<?php
require_once('../db.php');
require_once('../../model/response.php');

// note: never cache login or token http requests/responses
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

// check if sessionid is in the url e.g. /sessions/1
if (array_key_exists("sessionid",$_GET)) {
  // get sessions id from query string
  $sessionid = $_GET['sessionid'];

  // check to see if sessions id in query string is not empty and is number, if not return json error
  if($sessionid == '' || !is_numeric($sessionid)) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    ($sessionid == '' ? $response->addMessage("Session ID cannot be blank") : false);
    (!is_numeric($sessionid) ? $response->addMessage("Session ID must be numeric") : false);
    $response->send();
    exit;
  }

  // check to see if access token is provided in the HTTP Authorization header and that the value is longer than 0 chars
  // don't forget the Apache fix in .htaccess file
  // 401 error is for authentication failed or has not yet been provided
  if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1)
  {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    (!isset($_SERVER['HTTP_AUTHORIZATION']) ? $response->addMessage("Access token is missing from the header") : false);
    (strlen(isset($_SERVER['HTTP_AUTHORIZATION'])) < 1 ? $response->addMessage("Access token cannot be blank") : false);
    $response->send();
    exit;
  }

  // get supplied access token from authorisation header - used for delete (log out) and patch (refresh)
  $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

  // if request is a DELETE, e.g. delete session
  if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // attempt to query the database to check token details - use write connection as it needs to be synchronous for token
    try {
      // create db query to delete session where access token is equal to the one provided (leave other sessions active)
      // doesn't matter about if access token has expired as we are deleting the session
      $query = $writeDB->prepare('delete from tblsessions where id = :sessionid and accesstoken = :accesstoken');
      $query->bindParam(':sessionid', $sessionid, PDO::PARAM_INT);
      $query->bindParam(':accesstoken', $accesstoken, PDO::PARAM_STR);
      $query->execute();

      // get row count
      $rowCount = $query->rowCount();

      if($rowCount === 0) {
        // set up response for unsuccessful log out response
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Failed to log out of this session using access token provided");
        $response->send();
        exit;
      }

      // build response data array which contains the session id that has been deleted (logged out)
      $returnData = array();
      $returnData['session_id'] = intval($sessionid);

      // send successful response for log out
      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->setData($returnData);
      $response->addMessage("Successfully Logged Out");
      $response->send();
      exit;
    }
    catch(PDOException $ex) {
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("There was an issue logging out - please try again");
      $response->send();
      exit;
    }
  }
  else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
  }
}