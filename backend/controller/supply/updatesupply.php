<?php

require_once('../db.php');
require_once('../../model/response.php');
require_once('../functions/validate.php');
// note: never cache login or token http requests/responses
// (our response model defaults to no cache unless specifically set)

// attempt to set up connections to db connections
try {
  $writeDB = DB::connectWriteDB();
} catch (PDOException $ex) {
  // log connection error for troubleshooting and return a json error response
  error_log("Connection Error: " . $ex, 0);
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("Database connection error");
  $response->send();
  exit;
}

// check if supplyid is in the url e.g. /supplys/1
if (array_key_exists("supplyid",$_GET)) {
  // get supply id from query string
  $supplyid = $_GET['supplyid'];

  //check to see if supply id in query string is not empty and is number, if not return json error
  if($supplyid == '' || !is_numeric($supplyid)) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Supply ID cannot be blank or must be numeric");
    $response->send();
    exit;
  }
}
  


// check to make sure the request comes with HTTP_AUTHORIZATION
// Authenticate user with access token
if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
  $response = new Response();
  $response->setHttpStatusCode(401);
  $response->setSuccess(false);
  (!isset($_SERVER['HTTP_AUTHORIZATION']) ? $response->addMessage("Access token is missing from the header") : false);
  ((strlen(isset($_SERVER['HTTP_AUTHORIZATION']))) < 1 ? $response->addMessage("Access token cannot be blank") : false);
  $response->send();
  exit;
}

// get supplied access token from authorisation header
$accesstoken = $_SERVER['HTTP_AUTHORIZATION'];


// attempt to query the database to check token details - use write connection as it needs to be synchronous for token
try {
  // create db query to check access token is equal to the one provided
  $query = $writeDB->prepare('select userid, accesstokenexpiry from tblsessions, tblusers where tblsessions.userid = tblusers.id and accesstoken = :accesstoken');
  $query->bindParam(':accesstoken', $accesstoken, PDO::PARAM_STR);
  $query->execute();

  // get row count
  $rowCount = $query->rowCount();

  if($rowCount === 0) {
    // set up response for unsuccessful log out response
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("Invalid access token");
    $response->send();
    exit;
  }

  // get returned row
  $row = $query->fetch(PDO::FETCH_ASSOC);

  // save returned details into variables
  $returned_accesstokenexpiry = $row['accesstokenexpiry'];


  // check if access token has expired
  if(strtotime($returned_accesstokenexpiry) < time()) {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("Access token has expired");
    $response->send();
    exit;
  }
}
catch(PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue authenticating - please try again");
  $response->send();
  exit;
}




// delay action by 1 second to slow down any potential brute force attacks
sleep(1);

// check request's content type header is JSON
if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
  // set up response for unsuccessful request
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  $response->addMessage("Content Type header not set to JSON");
  $response->send();
  exit;
}

// check to make sure the request is post only - else exit with error response
if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
  }

// get post request body as the posted data will be JSON format
$rawPostData = file_get_contents('php://input');

if (!$jsonData = json_decode($rawPostData)) {
  // set up response for unsuccessful request
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  $response->addMessage("Request body is not valid JSON");
  $response->send();
  exit;
}

// check if postED request contains supply details in body as they are mandatory
if (!isset($jsonData->supplier)  || !isset($jsonData->credit) || !isset($jsonData->debit) || !isset($jsonData->date)) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!isset($jsonData->supplier) ? $response->addMessage("supplier id not set") : false);
  (!isset($jsonData->credit) ? $response->addMessage("credit amount  not set") : false);
  (!isset($jsonData->debit) ? $response->addMessage("debit amount not set") : false);
  (!isset($jsonData->date) ? $response->addMessage("date not set") : false);
  $response->send();
  exit;
}

if (!isset($jsonData->particulars)) {
  $jsonData->particulars = "";
}
if (!isset($jsonData->quantity)) {
  $jsonData->quantity = "";
}



// check to make sure that supply details are not empty and not greater than 255 & 20 characters
if (strlen($jsonData->supplier) < 1 || strlen($jsonData->supplier) > 255 || strlen($jsonData->particulars) > 255 || strlen($jsonData->debit) < 1 || strlen($jsonData->debit) > 20 || strlen($jsonData->credit) < 1 || strlen($jsonData->credit) > 20 || strlen($jsonData->quantity) > 20 )  {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (strlen($jsonData->supplier) < 1 ? $response->addMessage("supplier name cannot be blank") : false);
  (strlen($jsonData->supplier) > 255 ? $response->addMessage("supplier name must be less than 255 characters") : false);
  (strlen($jsonData->particulars) > 255 ? $response->addMessage("particulars name must be less than 255 characters") : false);
  (strlen($jsonData->credit) < 1 ? $response->addMessage("credit cannot be blank") : false);
  (strlen($jsonData->credit) > 20 ? $response->addMessage("credit amount must be less than 20 characters") : false);
  (strlen($jsonData->debit) < 1 ? $response->addMessage("debit cannot be blank") : false);
  (strlen($jsonData->debit) > 20 ? $response->addMessage("debit amount must be less than 20 characters") : false);
  (strlen($jsonData->quantity) > 20 ? $response->addMessage("quantity amount must be less than 20 characters") : false);
  $response->send();
  exit;
}

if (isset($jsonData->date)){
  function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if (!validateDate($jsonData->date)){
 // set up response for Date Format Error
 $response = new Response();
 $response->setHttpStatusCode(400);
 $response->setSuccess(false);
 $response->addMessage("Date Format Error ");
 $response->send();
 exit;
}

}

// attempt to query the database to add new supply - use write connection as it needs to be synchronous for oldsupplier/token
try {
$supplier = validate($jsonData->supplier);
$particulars = validate($jsonData->particulars);
$quantity = validate($jsonData->quantity);
$debit = validate($jsonData->debit);
$credit = validate($jsonData->credit);
$date = validate($jsonData->date);
$supplyid =validate($supplyid);

  // create db query
  $query = $writeDB->prepare('UPDATE supply SET suppliers_id=:supplier_id, particulars=:particulars, credit=:credit, debit=:debit, quantity=:quantity, date=:date where id= :supplyid ');
  $query->bindParam(':supplier_id', $supplier, PDO::PARAM_STR);
  $query->bindParam(':particulars', $particulars, PDO::PARAM_STR);
  $query->bindParam(':credit', $credit, PDO::PARAM_STR);
  $query->bindParam(':debit', $debit, PDO::PARAM_STR);
  $query->bindParam(':quantity', $quantity, PDO::PARAM_STR);
  $query->bindParam(':date', $date, PDO::PARAM_STR);
  $query->bindParam(':supplyid', $supplyid, PDO::PARAM_STR);
  $query->execute();

  // get last user id so we can return the user id in the json
  // $lastSupplierID = $writeDB->lastInsertId();
  $returnData['id'] =$supplyid;
  $returnData['Supplier_id'] = $supplier;
   $returnData['particulars'] = $particulars;
   $returnData['quantity'] = intval($quantity);
   $returnData['debit'] = intval($debit);
   $returnData['credit'] = intval($credit);
   $returnData['date'] = $date;
   

  // set response
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("Supply Successfully Updated");
  $response->send();
  exit;

} 

// catch error

catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an error while updating supply - please try again");
  $response->send();
  exit;
}


