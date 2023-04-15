<?php
   // Connect to database
   $connection = mysqli_connect("localhost", "root", "", "arcodesi_ledger");  

   if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
   }
   if (isset($_POST['param1'])) {
    $param  = $_POST['param1']; 
   }
   // Execute database query
   $query = "SELECT * FROM suppliers WHERE CustomerCode = '$param'";
   $result = $connection->query($query);
   
   // Process query results
   $re = mysqli_fetch_assoc($result); 
    list($name) = mysqli_fetch_array($result); 
    $te_id = $re !== null ? $re['name'] : $name; 
   
   // Return data as JSON response
   header('Content-Type: application/json');
   echo json_encode($te_id);
?>