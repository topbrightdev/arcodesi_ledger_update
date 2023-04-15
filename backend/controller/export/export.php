<?php

require_once('../db.php');
require_once('../../model/response.php');
require_once('../functions/validate.php');
require_once('../functions/phpexport/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// check to make sure the request is GET only - else exit with error response
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


// check if type is set

elseif(isset($jsonData->type) == "" || !isset($jsonData->type)) {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("Type Not Supplied");
    $response->send();
    exit;
}
//all date functions
function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
 function dateDiffInDays($date1, $date2) 
  {
      // Calculating the difference in timestamps
      $diff = strtotime($date2) - strtotime($date1);
  
      // 1 day = 24 hours
      // 24 * 60 * 60 = 86400 seconds
      return abs(round($diff / 86400));
  }
  
  $datetoday = date('Y-m-d');
//all date functions ends here 

        // check if type is transaction
if ($jsonData->type == "transaction"){
    
// check if GET request contains from and to in body as they are mandatory
  if (!isset($jsonData->from) || !isset($jsonData->to) ) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!isset($jsonData->from) ? $response->addMessage("from value not supplied") : false);
    (!isset($jsonData->to) ? $response->addMessage("to value not supplied") : false);
    $response->send();
    exit;
  }


  // check to make sure that from and to are not empty and not greater than 255 characters
  if (!validateDate($jsonData->from) || !validateDate($jsonData->to) ) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!validateDate($jsonData->from) ? $response->addMessage("from date not valid") : false);
    (!validateDate($jsonData->to) ? $response->addMessage("to date not valid") : false);
    
    $response->send();
    exit;
  }



  try {

    $from = validate($jsonData->from);
    $to = validate($jsonData->to);
    // create db query
    $query = $writeDB->prepare('SELECT * FROM supply WHERE deleted="N" AND date BETWEEN :from AND :to ORDER BY suppliers_id ASC');
    $query->bindParam(':from', $from, PDO::PARAM_STR);
    $query->bindParam(':to', $to, PDO::PARAM_STR);
    $query->execute();
    // get row count
    $rowCount = $query->rowCount();

    if($rowCount == 0) {
      // set response
  $response = new Response();
  $response->setHttpStatusCode(404);
  $response->setSuccess(false);
  $response->addMessage("Records Not Found");
  $response->send();
  exit;
  }


    $fileName = "Transactions-" . date('YmdHisA') ; 
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
   // create db query
   $querys = $writeDB->prepare('SELECT name,beat  from suppliers WHERE id =:id LIMIT 1');
   $querys->bindParam(':id', $row['suppliers_id'], PDO::PARAM_STR);
   $querys->execute();
   $rows = $querys->fetch();
   $q = 1;
  $name = $rows['name'];
  $supply = array("date"=> $row['date'],"beat"=> $rows['beat'], "Supplier_name"=>$name,"quantity" =>$row['quantity'],"desc" =>$row['particulars'],"debit" =>$row['debit'],"credit" =>$row['credit'],"amount"=>($row['credit']-$row['debit'])  );
  $supplyArray[] =  $supply;

      if($rowCount > 0) {
     
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
      
          $sheet->setCellValue('A1', 'NO');
          $sheet->setCellValue('B1', 'Customer Name');
          $sheet->setCellValue('C1', 'Beat/Route');
          $sheet->setCellValue('D1', 'Salesman');
          $sheet->setCellValue('E1', 'Debit');
          $sheet->setCellValue('F1', 'Credit');
          $sheet->setCellValue('G1', 'Description');
          $sheet->setCellValue('H1', 'Amount(Credit - Debit)');
          $sheet->setCellValue('I1', 'Date');
           
      
          $rowCount = 2;
      
          foreach($supplyArray as $data){
      
              $sheet->setCellValue('A'.$rowCount, $q++);
              $sheet->setCellValue('B'.$rowCount, $data['Supplier_name']);
              $sheet->setCellValue('C'.$rowCount, $data['beat']);
              $sheet->setCellValue('D'.$rowCount, $data['quantity']);
              $sheet->setCellValue('E'.$rowCount, $data['debit']);
              $sheet->setCellValue('F'.$rowCount, $data['credit']);
              $sheet->setCellValue('G'.$rowCount, $data['desc']);
              $sheet->setCellValue('H'.$rowCount, $data['amount']);
              $sheet->setCellValue('I'.$rowCount, $data['date']);
               
              
              $rowCount++;
          }
      
          $writer = new Xlsx($spreadsheet);
          $final_filename = $fileName.'.xlsx';
          $writer->save('files/'.$final_filename);
          
      
        }
  }

    

    $url =$url."controller/export/files/".$final_filename;

    
    $returnData = array();
  //   $returnData['supplier_name'] = $name;
    $returnData['url'] = $url;
    // set response
    $response = new Response();
    $response->setHttpStatusCode(200);
    $response->setSuccess(true);
    $response->setData($returnData);
    $response->addMessage("Transaction Data Successfully fetched");
    $response->send();
    exit;

  } 

  // catch error

  catch (PDOException $ex) {
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("There was an issue while fetching transaction data - please try again");
    $response->send();
    exit;
  }

}
// check transaction  ends here



// check if type is balance

elseif ($jsonData->type == "balance"){


if (!isset($jsonData->from) || !isset($jsonData->to) ) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!isset($jsonData->from) ? $response->addMessage("from value not supplied") : false);
  (!isset($jsonData->to) ? $response->addMessage("to value not supplied") : false);
  $response->send();
  exit;
}

// function validateDate($date, $format = 'Y-m-d'){
//     $d = DateTime::createFromFormat($format, $date);
//     return $d && $d->format($format) === $date;
// }


// check to make sure that from and to are not empty and not greater than 255 characters
if (!validateDate($jsonData->from) || !validateDate($jsonData->to) ) {
  $response = new Response();
  $response->setHttpStatusCode(400);
  $response->setSuccess(false);
  (!validateDate($jsonData->from) ? $response->addMessage("from date not valid") : false);
  (!validateDate($jsonData->to) ? $response->addMessage("to date not valid") : false);
  
  $response->send();
  exit;
}

    try {
    
      $from = validate($jsonData->from);
      $to = validate($jsonData->to);
      // create db query
  $query = $writeDB->prepare('SELECT SUM(debit),SUM(credit),suppliers_id  FROM supply WHERE deleted="N" AND date BETWEEN :from AND :to  GROUP BY suppliers_id');
  $query->bindParam(':from', $from, PDO::PARAM_STR);
  $query->bindParam(':to', $to, PDO::PARAM_STR);
  $query->execute();
      // get row count
      $rowCount = $query->rowCount();

      if($rowCount == 0) {
        // set response
$response = new Response();
$response->setHttpStatusCode(404);
$response->setSuccess(false);
$response->addMessage("Records Not Found");
$response->send();
exit;
}



      $fileName = "Balance-" . date('YmdHisA') ; 
      while($row = $query->fetch($writeDB::FETCH_ASSOC)) {
     // create db query
     $querys = $writeDB->prepare('SELECT name,beat  from suppliers WHERE id =:id LIMIT 1');
     $querys->bindParam(':id', $row['suppliers_id'], PDO::PARAM_STR);
     $querys->execute();
     $rows = $querys->fetch();
     $s = 1;
    $name = $rows['name'];
    $supply = array( "Supplier_name"=>$name,"beat"=>$rows['beat'],"amount"=>($row['SUM(credit)']-$row['SUM(debit)'])  );
    $supplyArray[] =  $supply;
    
        if($rowCount > 0) {
       
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
             $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'Customer Name');
            $sheet->setCellValue('C1', 'Beat/Route');
            $sheet->setCellValue('D1', 'Balance');
            $rowCount = 2;
        
            foreach($supplyArray as $data){
        
                $sheet->setCellValue('A'.$rowCount, $s++);
                $sheet->setCellValue('B'.$rowCount, $data['Supplier_name']);
                $sheet->setCellValue('C'.$rowCount, $data['beat']);
                $sheet->setCellValue('D'.$rowCount, $data['amount']);
                $rowCount++;
            }
        
            $writer = new Xlsx($spreadsheet);
            $final_filename = $fileName.'.xlsx';
            $writer->save('files/'.$final_filename);
            
        
          }
    }
    
      
    
      $url =$url."controller/export/files/".$final_filename;
    
      
      $returnData = array();
    //   $returnData['supplier_name'] = $name;
      $returnData['url'] = $url;
      // set response
      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->setData($returnData);
      $response->addMessage("Balance Data Successfully fetched");
      $response->send();
      exit;
    
    } 
    
    // catch error
    
    catch (PDOException $ex) {
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("There was an issue while fetching Balance data - please try again");
      $response->send();
      exit;
    }
    
    }
//check if type is balance ends




// check if type is all_balance

elseif ($jsonData->type == "all_balance"){
    
    
    try {
    
      // create db query
  $query = $writeDB->prepare('SELECT SUM(debit),SUM(credit),suppliers_id  FROM supply WHERE deleted="N"  GROUP BY suppliers_id');
  $query->execute();
      // get row count
      $rowCount = $query->rowCount();

      if($rowCount == 0) {
        // set response
$response = new Response();
$response->setHttpStatusCode(404);
$response->setSuccess(false);
$response->addMessage("Records Not Found");
$response->send();
exit;
}



      $fileName = "All_Balance-" . date('YmdHisA') ; 
      while($row = $query->fetch($writeDB::FETCH_ASSOC)) {
     // create db query
     $querys = $writeDB->prepare('SELECT name,beat  from suppliers WHERE id =:id LIMIT 1');
     $querys->bindParam(':id', $row['suppliers_id'], PDO::PARAM_STR);
     $querys->execute();
     $rows = $querys->fetch();
     $s = 1;
    $name = $rows['name'];
    $supply = array( "Supplier_name"=>$name,"beat"=>$rows['beat'],"amount"=>($row['SUM(credit)']-$row['SUM(debit)'])  );
    $supplyArray[] =  $supply;
    
        if($rowCount > 0) {
       
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'Customer Name');
            $sheet->setCellValue('C1', 'Beat/Route');
            $sheet->setCellValue('D1', 'Balance');
            $rowCount = 2;
        
            foreach($supplyArray as $data){
        
                $sheet->setCellValue('A'.$rowCount, $s++);
                $sheet->setCellValue('B'.$rowCount, $data['Supplier_name']);
                $sheet->setCellValue('C'.$rowCount, $data['beat']);
                $sheet->setCellValue('D'.$rowCount, $data['amount']);
                $rowCount++;
            }
        
            $writer = new Xlsx($spreadsheet);
            $final_filename = $fileName.'.xlsx';
            $writer->save('files/'.$final_filename);
            
        
          }
    }
    
      
    
      $url =$url."controller/export/files/".$final_filename;
    
      
      $returnData = array();
    //   $returnData['supplier_name'] = $name;
      $returnData['url'] = $url;
      // set response
      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->setData($returnData);
      $response->addMessage("All Balance Data Successfully fetched");
      $response->send();
      exit;
    
    } 
    
    // catch error
    
    catch (PDOException $ex) {
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("There was an issue while fetching All Balance data - please try again");
      $response->send();
      exit;
    }
    
    }
//check if type is all balance ends here






       // check if type is all transaction
elseif ($jsonData->type == "all_transaction"){

try {

  // create db query
  $query = $writeDB->prepare('SELECT * FROM supply WHERE deleted="N"  ORDER BY suppliers_id ASC');
  $query->execute();
  // get row count
  $rowCount = $query->rowCount();

  if($rowCount == 0) {
    // set response
$response = new Response();
$response->setHttpStatusCode(404);
$response->setSuccess(false);
$response->addMessage("Records Not Found");
$response->send();
exit;
}


  $fileName = "All_Transactions-" . date('YmdHisA') ; 
  while($row = $query->fetch(PDO::FETCH_ASSOC)) {
 // create db query
 $querys = $writeDB->prepare('SELECT name,beat  from suppliers WHERE id =:id LIMIT 1');
 $querys->bindParam(':id', $row['suppliers_id'], PDO::PARAM_STR);
 $querys->execute();
 $rows = $querys->fetch();
 $q = 1;
$name = $rows['name'];
$supply = array("date"=> $row['date'],"beat"=> $rows['beat'], "Supplier_name"=>$name,"quantity" =>$row['quantity'],"desc" =>$row['particulars'],"debit" =>$row['debit'],"credit" =>$row['credit'],"amount"=>($row['credit']-$row['debit'])  );
$supplyArray[] =  $supply;

    if($rowCount > 0) {
   
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Beat/Route');
        $sheet->setCellValue('D1', 'Salesman');
        $sheet->setCellValue('E1', 'Debit');
        $sheet->setCellValue('F1', 'Credit');
        $sheet->setCellValue('G1', 'Description');
        $sheet->setCellValue('H1', 'Amount(Credit - Debit)');
        $sheet->setCellValue('I1', 'Date');
         
        
        $rowCount = 2;
    
        foreach($supplyArray as $data){
    
            $sheet->setCellValue('A'.$rowCount, $q++);
            $sheet->setCellValue('B'.$rowCount, $data['Supplier_name']);
            $sheet->setCellValue('C'.$rowCount, $data['beat']);
            $sheet->setCellValue('D'.$rowCount, $data['quantity']);
            $sheet->setCellValue('E'.$rowCount, $data['debit']);
            $sheet->setCellValue('F'.$rowCount, $data['credit']);
            $sheet->setCellValue('G'.$rowCount, $data['desc']);
            $sheet->setCellValue('H'.$rowCount, $data['amount']);
            $sheet->setCellValue('I'.$rowCount, $data['date']);
             
            
            $rowCount++;
        }
    
        $writer = new Xlsx($spreadsheet);
        $final_filename = $fileName.'.xlsx';
        $writer->save('files/'.$final_filename);
        
    
      }
}

  

  $url =$url."controller/export/files/".$final_filename;

  
  $returnData = array();
//   $returnData['supplier_name'] = $name;
  $returnData['url'] = $url;
  // set response
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("All Transaction  Successfully fetched");
  $response->send();
  exit;

} 

// catch error

catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue while fetching  All transaction  - please try again");
  $response->send();
  exit;
}

}
// check if type is all transaction ends here

















       // check if type is all time detailed Report
elseif ($jsonData->type == "all_time_detailed"){

try {

  // create db query
  $query = $writeDB->prepare('SELECT * FROM suppliers ORDER BY id ASC');
  $query->execute();
  // get row count
  $rowCount = $query->rowCount();

  if($rowCount == 0) {
    // set response
$response = new Response();
$response->setHttpStatusCode(404);
$response->setSuccess(false);
$response->addMessage("Records Not Found");
$response->send();
exit;
}


  $fileName = "All_Time_Report-" . date('YmdHisA') ; 
  while($row = $query->fetch(PDO::FETCH_ASSOC)) {
 // create db query
 $querys = $writeDB->prepare('SELECT * FROM supply WHERE deleted="N" AND credit>debit AND suppliers_id=:id ORDER BY id ASC LIMIT 1');
 $querys->bindParam(':id', $row['id'], PDO::PARAM_STR);
 $querys->execute();
 $rows = $querys->fetch();
 $rowCount = $querys->rowCount();
 $q = 1;
$name = $row['name'];
$supply = array("date"=> $rows['date'],"beat"=> $row['beat'], "Supplier_name"=>$name,"amount"=>($rows['credit']-$rows['debit'])  );
$supplyArray[] =  $supply;

    if($rowCount > 0) {
   
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Customer Name');
        $sheet->setCellValue('D1', 'Beat');
        $sheet->setCellValue('E1', 'Balance');
        $sheet->setCellValue('F1', 'Due Days');
        
         
        
        $rowCount = 2;
    
        foreach($supplyArray as $data){
    
            $sheet->setCellValue('A'.$rowCount, $q++);
            $sheet->setCellValue('B'.$rowCount, $data['date']);
            $sheet->setCellValue('C'.$rowCount, $data['Supplier_name']);
            $sheet->setCellValue('D'.$rowCount, $data['beat']);
            $sheet->setCellValue('E'.$rowCount, $data['amount']);
            $sheet->setCellValue('F'.$rowCount, dateDiffInDays($datetoday, $data['date']));
             
            
            $rowCount++;
        }
    
        $writer = new Xlsx($spreadsheet);
        $final_filename = $fileName.'.xlsx';
        $writer->save('files/'.$final_filename);
        
    
      }
}

  

  $url =$url."controller/export/files/".$final_filename;

  
  $returnData = array();
//   $returnData['supplier_name'] = $name;
  $returnData['url'] = $url;
  // set response
  $response = new Response();
  $response->setHttpStatusCode(200);
  $response->setSuccess(true);
  $response->setData($returnData);
  $response->addMessage("All Time Report Balance  Successfully fetched");
  $response->send();
  exit;

} 

// catch error

catch (PDOException $ex) {
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("There was an issue while fetching  All time report balance  - please try again");
  $response->send();
  exit;
}

}
// check if type is all transaction ends here


else {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("Type Not Found");
    $response->send();
    exit;
}