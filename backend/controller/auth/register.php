<?php

require_once('../db.php');
require_once('../functions/validate.php');
require_once('../../model/response.php');

// note: never cache user http requests/responses
// (our response model defaults to no cache unless specifically set)

// attempt to set up connections to db connections
try {

  $writeDB = DB::connectWriteDB();
  
}
catch(PDOException $ex) {
  // log connection error for troubleshooting and return a json error response
  error_log("Connection Error: ".$ex, 0);
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("Database connection error");
  $response->send();
  exit;
}

// handle creating new user
// check to make sure the request is POST only - else exit with error response
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $response = new Response();
  $response->setHttpStatusCode(405);
  $response->setSuccess(false);
  $response->addMessage("Request method not allowed");
  $response->send();
  exit;
}

// check request's content type header is JSON
if($_SERVER['CONTENT_TYPE'] !== 'application/json') {
  // set up response for unsuccessful request
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  $response->addMessage("Content Type header not set to JSON");
  $response->send();
  exit;
}

// get POST request body as the POSTed data will be JSON format
$rawPostData = file_get_contents('php://input');

if(!$jsonData = json_decode($rawPostData)) {
  // set up response for unsuccessful request
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  $response->addMessage("Request body is not valid JSON");
  $response->send();
  exit;
}

// check if post request contains full name, email and password in body as they are mandatory
if(!isset($jsonData->fullname) || !isset($jsonData->email) || !isset($jsonData->password)) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  // add message to message array where necessary
  (!isset($jsonData->fullname) ? $response->addMessage("Full name not supplied") : false);
  (!isset($jsonData->email) ? $response->addMessage("email not supplied") : false);
  (!isset($jsonData->password) ? $response->addMessage("Password not supplied") : false);
  $response->send();
  exit;
}

// check to make sure that full name email and password are not empty and less than 255 long
if(strlen($jsonData->fullname) < 1 || strlen($jsonData->fullname) > 255 || strlen($jsonData->email) < 1 || strlen($jsonData->email) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 100) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (strlen($jsonData->fullname) < 1 ? $response->addMessage("Full name cannot be blank") : false);
  (strlen($jsonData->fullname) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
  (strlen($jsonData->email) < 1 ? $response->addMessage("email cannot be blank") : false);
  (strlen($jsonData->email) > 255 ? $response->addMessage("email cannot be greater than 255 characters") : false);
  (strlen($jsonData->password) < 1 ? $response->addMessage("Password cannot be blank") : false);
  (strlen($jsonData->password) > 100 ? $response->addMessage("Password cannot be greater than 100 characters") : false);
  $response->send();
  exit;
}

//check if email is valid 
if (!filter_var($jsonData->email, FILTER_VALIDATE_EMAIL)) {
  // set up response for unsuccessful request
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  $response->addMessage("Email is Not valid");
  $response->send();
  exit;
}
// trim any leading and trailing blank spaces from full name and email only - password may contain a leading or trailing space
$fullname = validate($jsonData->fullname);
$email = validate($jsonData->email);
$password = $jsonData->password;

// attempt to query the database to check if email already exists
try {
  // create db query
  $query = $writeDB->prepare('SELECT id from tblusers where email = :email');
  $query->bindParam(':email', $email, PDO::PARAM_STR);
  $query->execute();

  // get row count
  $rowCount = $query->rowCount();

  if($rowCount !== 0) {
    // set up response for email already exists
    $response = new Response();
    $response->setHttpStatusCode(409);
    $response->setSuccess(false);
    $response->addMessage("email already exists");
    $response->send();
    exit;
  }
  
  // hash the password to store in the DB as plain text password stored in DB is bad practice
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  
  // create db query to create user
  $query = $writeDB->prepare('INSERT into tblusers (fullname, email, password) values (:fullname, :email, :password)');
  $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
  $query->bindParam(':email', $email, PDO::PARAM_STR);
  $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
  $query->execute();

  // get row count
  $rowCount = $query->rowCount();

  if($rowCount === 0) {
    // set up response for error
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("There was an error creating the user account - please try again");
    $response->send();
    exit;
  }
  
  // get last user id so we can return the user id in the json
  $lastUserID = $writeDB->lastInsertId();
  
  // build response data array which contains basic user details
  $returnData = array();
  $returnData['user_id'] = $lastUserID;
  $returnData['fullname'] = $fullname;
  $returnData['email'] = $email;
  $returnData['is_admin'] ="NO";

  $response = new Response();
  $response->setHttpStatusCode(201);
  $response->setSuccess(true);
  $response->addMessage("User created");
  $response->setData($returnData);
  $response->send();
  exit;
}
catch(PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue creating a user account - please try again");
  $response->send();
  exit;
}