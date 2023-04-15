<?php

require_once('../db.php');
require_once('../../model/response.php');

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
// handle creating new session, e.g. log in

// handle cors
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
     header('Access-Control-Max-Age: 86400');
    
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->send();
  exit;
}

// handle creating new session, e.g. logging in
// check to make sure the request is POST only - else exit with error response
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $response = new Response();
  $response->setHttpStatusCode(405);
  $response->setSuccess(false);
  $response->addMessage("Request method not allowed");
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

// get POST request body as the POSTed data will be JSON format
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

// check if post request contains email and password in body as they are mandatory
if (!isset($jsonData->email) || !isset($jsonData->password)) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!isset($jsonData->email) ? $response->addMessage("email not supplied") : false);
  (!isset($jsonData->password) ? $response->addMessage("Password not supplied") : false);
  $response->send();
  exit;
}

// check to make sure that email and password are not empty and not greater than 255 characters
if (strlen($jsonData->email) < 1 || strlen($jsonData->email) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (strlen($jsonData->email) < 1 ? $response->addMessage("email cannot be blank") : false);
  (strlen($jsonData->email) > 255 ? $response->addMessage("email must be less than 255 characters") : false);
  (strlen($jsonData->password) < 1 ? $response->addMessage("Password cannot be blank") : false);
  (strlen($jsonData->password) > 255 ? $response->addMessage("Password must be less than 255 characters") : false);
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
// attempt to query the database to check user details - use write connection as it needs to be synchronous for password/token
try {
  $email = $jsonData->email;
  $password = $jsonData->password;
  // create db query
  $query = $writeDB->prepare('SELECT id, fullname, email, password,is_admin from tblusers where email = :email');
  $query->bindParam(':email', $email, PDO::PARAM_STR);
  $query->execute();

  // get row count
  $rowCount = $query->rowCount();

  if ($rowCount === 0) {
    // set up response for unsuccessful login attempt - obscure what is incorrect by saying email or password is wrong
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("email or password is incorrect");
    $response->send();
    exit;
  }

  // get first row returned
  $row = $query->fetch(PDO::FETCH_ASSOC);

  // save returned details into variables
  $returned_id = $row['id'];
  $returned_fullname = $row['fullname'];
  $returned_email = $row['email'];
  $returned_password = $row['password'];
  $returned_admin = $row['is_admin'];

  


  // check if password is the same using the hash
  if (!password_verify($password, $returned_password)) {

    // send response
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("email or password is incorrect");
    $response->send();
    exit;
  }

  // generate access token
  // use 24 random bytes to generate a token then encode this as base64
  // suffix with unix time stamp to guarantee uniqueness (stale tokens)
  $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());



  // set access token  expiry in seconds (access token 24hours lifetime )
  // send seconds rather than date/time as this is not affected by timezones
  $access_token_expiry_seconds = 86400;
} catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue logging in - please try again");
  $response->send();
  exit;
}
// new try catch as this is a transaction so should include roll back if error
try {
  // start transaction 
  $writeDB->beginTransaction();

  // create the query string to insert new session into sessions table and set the token and refresh token as well as their expiry dates and times
  $query = $writeDB->prepare('insert into tblsessions (userid, accesstoken, accesstokenexpiry) values (:userid, :accesstoken, date_add(NOW(), INTERVAL :accesstokenexpiryseconds SECOND))');
  // bind the user id
  $query->bindParam(':userid', $returned_id, PDO::PARAM_INT);
  // bind the access token
  $query->bindParam(':accesstoken', $accesstoken, PDO::PARAM_STR);
  // bind the access token expiry date
  $query->bindParam(':accesstokenexpiryseconds', $access_token_expiry_seconds, PDO::PARAM_INT);
  // run the query
  $query->execute();

  // get last session id so we can return the session id in the json
  $lastSessionID = $writeDB->lastInsertId();

  // commit new row and updates if successful
  $writeDB->commit();

  // build response data array which contains the access token and refresh tokens
  // session data array
  $sessionData = array();
  $sessionData['session_id'] = intval($lastSessionID);
  $sessionData['access_token'] = $accesstoken;
  $sessionData['access_token_expires_in'] = $access_token_expiry_seconds;
  $returnData['session'] = $sessionData;

  // userdata Array
  $userData = array();
  $userData['user_id'] =$returned_id;
  $userData['fullname'] =$returned_fullname;
  $userData['email'] =$returned_email;
   $userData['is_admin'] =$returned_admin;
  $returnData['user'] =$userData;
 

  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("Successfully Login");
  $response->send();
  exit;
} catch (PDOException $ex) {
  // roll back update/insert if error
  $writeDB->rollBack();
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue logging in - please try again");
  $response->send();
  exit;
}
