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

// check if postED request contains supplier  in body as they are mandatory
if (!isset($jsonData->supplierId) || !isset($jsonData->name) ) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!isset($jsonData->supplierId) ? $response->addMessage("supplier Id not set") : false);
  (!isset($jsonData->name) ? $response->addMessage("Supplier name not supplied") : false);
  $response->send();
  exit;
}

// check to make sure that supplier Id are not empty and not greater than 255 characters
if (strlen($jsonData->supplierId) < 1 || strlen($jsonData->supplierId) > 255|| strlen($jsonData->name) < 1 || strlen($jsonData->name) > 255 ) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (strlen($jsonData->supplierId) < 1 ? $response->addMessage("supplier Id cannot be blank") : false);
  (strlen($jsonData->supplierId) > 255 ? $response->addMessage("supplier Id must be less than 255 characters") : false);
  (strlen($jsonData->name) < 1 ? $response->addMessage("Supplier name cannot be blank") : false);
  (strlen($jsonData->name) > 255 ? $response->addMessage("Supplier name must be less than 255 characters") : false);
  $response->send();
  exit;
}

if (!isset($jsonData->beat)) {
  $jsonData->beat = "";
}

// attempt to query the database to update supplier - use write connection as it needs to be synchronous for oldsupplier/token
try {

  $supplierId = validate($jsonData->supplierId);
  $suppliername = validate($jsonData->name);
  
  $beat = validate($jsonData->beat);


  $query = $writeDB->prepare('UPDATE suppliers SET name=:name,beat=:beat WHERE deleted="N" AND id = :supplierid ');
      
      $query->bindParam(':supplierid', $supplierId, PDO::PARAM_STR);
      $query->bindParam(':name', $suppliername, PDO::PARAM_STR);
      $query->bindParam(':beat', $beat, PDO::PARAM_STR);
      $query->execute();

           $rowCount = $query->rowCount();
if ($rowCount == 0 ){
      // set response
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an  error  while Updating supply - please try again");
  $response->send();
  exit;
}


  $returnData['id'] = $supplierId;
  $returnData['Supplier_name'] = $suppliername;
  $returnData['beat'] = $beat;
  // set response
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("Supplier Successfully updated");
  $response->send();
  exit;

} 

// catch error

catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an  error  while Updating supply - please try again");
  $response->send();
  exit;
}
