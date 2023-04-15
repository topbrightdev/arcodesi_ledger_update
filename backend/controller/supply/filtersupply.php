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

// check to make sure the request is POST only - else exit with error response
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $response = new Response();
  $response->setHttpStatusCode(405);
  $response->setSuccess(false);
  $response->addMessage("Request method not allowed");
  $response->send();
  exit;
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

// get supplied access token from authorisation header - used for and GET (refresh)
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




// delay login by 1 second to slow down any potential brute force attacks
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

// get GET request body as the GET data will be JSON format
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

// check if GET request contains from and fullname in body as they are mandatory
if (!isset($jsonData->from) || !isset($jsonData->to) || !isset($jsonData->supplierId)) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!isset($jsonData->from) ? $response->addMessage("from value not supplied") : false);
  (!isset($jsonData->to) ? $response->addMessage("to value not supplied") : false);
  (!isset($jsonData->supplierId) ? $response->addMessage("supplier Id not set") : false);
  $response->send();
  exit;
}

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


// check to make sure that from and to are not empty and not greater than 255 characters
if (!validateDate($jsonData->from) || !validateDate($jsonData->to) || strlen($jsonData->supplierId) < 1 || strlen($jsonData->supplierId) > 255) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!validateDate($jsonData->from) ? $response->addMessage("from date not valid") : false);
  (!validateDate($jsonData->to) ? $response->addMessage("to date not valid") : false);
  (strlen($jsonData->supplierId) < 1 ? $response->addMessage("supplier Id cannot be blank") : false);
  (strlen($jsonData->supplierId) > 255 ? $response->addMessage("supplier Id must be less than 255 characters") : false);
  $response->send();
  exit;
}

// attempt to query the database to fetch data - use write connection as it needs to be synchronous for to/token
try {

  $from = validate($jsonData->from);
  $to = validate($jsonData->to);
  $id = validate($jsonData->supplierId);
  
        // create db query
$query = $writeDB->prepare('SELECT * from suppliers WHERE deleted="N" AND id =:id');

  $query->bindParam(':id', $id, PDO::PARAM_STR);
      $query->execute();
       
      // get row count
      $rowCount = $query->rowCount();
  
  if ($rowCount == 0 ){
      // set response
  $response = new Response();
  $response->setHttpStatusCode(404);
  $response->setSuccess(false);
  $response->addMessage("Supplier Not Found");
  $response->send();
  exit;
}
  // create db query
  $query = $writeDB->prepare('SELECT SUM(debit),SUM(credit) FROM supply WHERE deleted="N" AND suppliers_id=:id AND date BETWEEN :from AND :to');
  $query->bindParam(':from', $from, PDO::PARAM_STR);
  $query->bindParam(':id', $id, PDO::PARAM_STR);
  $query->bindParam(':to', $to, PDO::PARAM_STR);
  $query->execute();
  $total = $query->fetch(PDO::FETCH_NUM);
       $sumtotal = $total[1]-$total[0]; 
  // create db query
  $query = $writeDB->prepare('SELECT * FROM supply WHERE deleted="N" AND suppliers_id=:id AND date BETWEEN :from AND :to');
  $query->bindParam(':from', $from, PDO::PARAM_STR);
  $query->bindParam(':id', $id, PDO::PARAM_STR);
  $query->bindParam(':to', $to, PDO::PARAM_STR);
  $query->execute();
  while($row = $query->fetch(PDO::FETCH_ASSOC)) {
 // create db query
 $querys = $writeDB->prepare('SELECT name,beat  from suppliers  WHERE deleted="N" AND id =:id LIMIT 1');
 $querys->bindParam(':id', $row['suppliers_id'], PDO::PARAM_STR);
 $querys->execute();
 $rows = $querys->fetch();

$name = $rows['name'];
    $supply = array("id"=> $row['id'],"date"=> $row['date'],"quantity" =>$row['quantity'],"desc" =>$rows['beat'],"debit" =>$row['debit'],"credit" =>$row['credit'],"amount"=>($row['credit']-$row['debit'])  );
    // create supply and store in array for return in json data
    $supplyArray[] =  $supply;
  }

  
  $returnData = array();
  $returnData['supplier_name'] = $name;
    $returnData['total_amount'] = $sumtotal;
  $returnData['filter'] = $supplyArray;


  // set response
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("Data Successfully fetched");
  $response->send();
  exit;

} 

// catch error

catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue while fetching data - please try again");
  $response->send();
  exit;
}

