<?php

require_once('../db.php');
require_once('../../model/response.php');



// check to make sure the request is post only - else exit with error response
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
  }



// attempt to set up connections to read and write db connections
try {
    $readDB = DB::connectReadDB();
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













  

// if request is a GET e.g. get tasks
if($_SERVER['REQUEST_METHOD'] === 'GET') {


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
    $returned_userid = $row['userid'];
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



    // attempt to query the database
    try {
      // create db query
      $query = $readDB->prepare('SELECT id, name from suppliers WHERE deleted="N"');
      $query->execute();
       
      // get row count
      $rowCount = $query->rowCount();

      // create task array to store returned tasks
      $taskArray = array();


        // create db query
       $querys = $readDB->prepare('SELECT SUM(debit),SUM(credit)  from supply WHERE deleted="N" ');
       $querys->execute();
       $total = $querys->fetch(PDO::FETCH_NUM);
       $debit = $total[0];
       $credit = $total[1]; // 0 is the first array. 



      // bundle tasks and rows returned into an array to return in the json data
      $returnData = array();
      $returnData['total_debit'] = $debit;
      $returnData['total_credit'] = $credit;
      $returnData['total_supplier'] =  $rowCount;

      // set up response for successful return
      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->toCache(true);
      $response->setData($returnData);
      $response->addMessage("Statistics Fetched");
      $response->send();
      exit;
    }
    // if error with sql query return a json error

    catch(PDOException $ex) {
      error_log("Database Query Error: ".$ex, 0);
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("Failed to get statistics");
      $response->send();
      exit;
    }
  }
?>