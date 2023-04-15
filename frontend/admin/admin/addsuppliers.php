



<!DOCTYPE html>  
<html>  
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
     
    <!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css"/>
</head>
<body>  
<?php
session_start();
include("../../db.php");
include "sidenav.php";
include "topheader.php";
require_once('functions/validate.php');
require_once('functions/phpexport/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$save_name = ''; 
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save'])) {

  $from = $_POST['date_from']; 
  $to = $_POST['date_to']; 
  $down_type = $_POST['down_type'];
  if ($down_type == 'transaction') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'NO');
    $sheet->setCellValue('B1', 'CustomerCode');
    $sheet->setCellValue('C1', 'Customer Name');
    $sheet->setCellValue('D1', 'Beat/Route');
    $sheet->setCellValue('E1', 'Salesman');
    $sheet->setCellValue('F1', 'Debit');
    $sheet->setCellValue('G1', 'Credit');
    $sheet->setCellValue('H1', 'Description');
    $sheet->setCellValue('I1', 'Amount(Credit - Debit)');
    $sheet->setCellValue('J1', 'Date');
    $transaction_result = mysqli_query($con, "select * from supply where date between '$from' and '$to'") or die ("query incorrect");
    $rowCount = 2;
    $fileName = "Transactions -" . date('YmdHisA') ; 
    $num = mysqli_num_rows($transaction_result);
    while ( $num > 0) {
      $row = mysqli_fetch_assoc($transaction_result); 
      if ($row == null) {
        list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date, $CustomerCode) = mysqli_fetch_array($transaction_result); 
      }
      $sheet->setCellValue('A'.$rowCount, $rowCount - 1);
      $row !== null ? $sheet->setCellValue('B'.$rowCount, $row['CustomerCode']) : $sheet->setCellValue('B'.$rowCount, $CustomerCode);
      $row !== null ? $sheet->setCellValue('C'.$rowCount, $row['suppliers_id']) : $sheet->setCellValue('C'.$rowCount, $suppliers_id);
      $row !== null ? $sheet->setCellValue('D'.$rowCount, 'SDF') : $sheet->setCellValue('D'.$rowCount, 'Last');
      $row !== null ? $sheet->setCellValue('E'.$rowCount, $row['particulars']) : $sheet->setCellValue('E'.$rowCount, $particulars);
      $row !== null ? $sheet->setCellValue('F'.$rowCount, $row['debit']) : $sheet->setCellValue('F'.$rowCount, $debit);
      $row !== null ? $sheet->setCellValue('G'.$rowCount, $row['credit']) : $sheet->setCellValue('G'.$rowCount, $credit);
      $row !== null ? $sheet->setCellValue('H'.$rowCount, $row['quantity']) : $sheet->setCellValue('H'.$rowCount, $quantity);
      $row !== null ? $sheet->setCellValue('I'.$rowCount, $row['credit'] - $row['debit']) : $sheet->setCellValue('I'.$rowCount, $credit - $debit);
      $row !== null ? $sheet->setCellValue('J'.$rowCount, $row['date']) : $sheet->setCellValue('J'.$rowCount, $date);
      $rowCount++; 
      $num--; 
    }
    $writer = new Xlsx($spreadsheet);
    $save_name = $fileName.'.xlsx'; 
    $targetPath = 'files/'.$save_name; 
    $writer->save($targetPath);
    echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; bottom: 10px; position: absolute;'>Generated successfully!</div>";

  }
  else if ($down_type == 'balance') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'NO');
    $sheet->setCellValue('B1', 'CustomerCode');
    $sheet->setCellValue('C1', 'Customer Name');
    $sheet->setCellValue('D1', 'Beat/Route');
    $sheet->setCellValue('E1', 'Balance');
    $transaction_result = mysqli_query($con, "select * from supply where date between '$from' and '$to'") or die ("query incorrect");
    $transaction_id = mysqli_query($con, "select distinct suppliers_id from supply where date between '$from' and '$to'") or die ("query incorrect");

    $num = mysqli_num_rows($transaction_id);

    $rowCount = 2;
    $fileName = "Balance -" . date('YmdHisA') ; 
    while ( $num > 0) {
      $row = mysqli_fetch_assoc($transaction_id); 
      if ($row == null) {
        list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date, $CustomerCode) = mysqli_fetch_array($transaction_result); 
        $tra_name1 = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$suppliers_id'"); 
        $d_t_d = mysqli_fetch_assoc($tra_name1); 
        $dt = $d_t_d['name']; 
        $dt_beat = $d_t_d['beat']; 
        $dt_code = $d_t_d['CustomerCode']; 
      }
      $tem = $row['suppliers_id']; 
      $tra1 = mysqli_query($con, "select sum(credit) as total from supply where suppliers_id = '$tem' and date between '$from' and '$to'"); 
      $tra_name = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$tem'"); 
      $dis_tra_dis = mysqli_fetch_assoc($tra_name); 
      $dis_tra1 = mysqli_fetch_assoc($tra1); 
      $d = $dis_tra1['total']; 
      
      $tra11 = mysqli_query($con, "select sum(debit) as total from supply where suppliers_id = '$tem' and date between '$from' and '$to'"); 
      $dis_tra11 = mysqli_fetch_assoc($tra11); 
      $d1 = $dis_tra11['total']; 
      $real = $d - $d1; 
      
      list ($id, $name, $beat, $CustomerCode) = mysqli_fetch_array($tra_name);
      $d_name = $dis_tra_dis !== null ? $dis_tra_dis['name'] : $name;
      $d_beat = $dis_tra_dis !== null ? $dis_tra_dis['beat'] : $beat;
      $d_code = $dis_tra_dis !== null ? $dis_tra_dis['CustomerCode'] : $CustomerCode;

      $sheet->setCellValue('A'.$rowCount, $rowCount - 1);
      $row !== null ? $sheet->setCellValue('B'.$rowCount, $d_code) : $sheet->setCellValue('B'.$rowCount, $dt_code);
      $row !== null ? $sheet->setCellValue('C'.$rowCount, $d_name) : $sheet->setCellValue('C'.$rowCount, $dt);
      $row !== null ? $sheet->setCellValue('D'.$rowCount, $d_beat) : $sheet->setCellValue('D'.$rowCount, $dt_beat);
      $row !== null ? $sheet->setCellValue('E'.$rowCount, $real) : $sheet->setCellValue('E'.$rowCount, $real);
      $rowCount++; 
      $num--; 
    }
    $writer = new Xlsx($spreadsheet);
    $save_name = $fileName.'.xlsx'; 
    $targetPath = 'files/'.$save_name; 
    $writer->save($targetPath);
    echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; bottom: 10px; position: absolute;'>Generated successfully!</div>";
  }
  else if ($down_type == 'all_balance') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'NO');
    $sheet->setCellValue('B1', 'CustomerCode');
    $sheet->setCellValue('C1', 'Customer Name');
    $sheet->setCellValue('D1', 'Beat/Route');
    $sheet->setCellValue('E1', 'Balance');
    $transaction_result = mysqli_query($con, "select * from supply") or die ("query incorrect");
    $transaction_id = mysqli_query($con, "select distinct suppliers_id from supply") or die ("query incorrect");
    $num = mysqli_num_rows($transaction_id);

    $rowCount = 2;
    $fileName = "All balance -" . date('YmdHisA') ; 
    while ( $num > 0) {
      $row = mysqli_fetch_assoc($transaction_id); 
      if ($row == null) {
        list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date, $CustomerCode) = mysqli_fetch_array($transaction_result); 
        $tra_name1 = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$suppliers_id'"); 
        $d_t_d = mysqli_fetch_assoc($tra_name1); 
        $dt = $d_t_d['name']; 
        $dt_beat = $d_t_d['beat']; 
        $dt_code = $d_t_d['CustomerCode']; 
        $tem = $suppliers_id; 
      }
      else {
        $tem = $row['suppliers_id']; 
        $tra_name = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$tem'"); 
        $dis_tra_dis = mysqli_fetch_assoc($tra_name);
        if ($dis_tra_dis == null) {
          list($id, $name, $beat, $CustomerCode) = mysqli_fetch_array($tra_name); 
          $d_name = $name;
          $d_beat = $beat;
          $d_code = $CustomerCode; 
        }
        else {
          $d_name = $dis_tra_dis['name'];
          $d_beat = $dis_tra_dis['beat'];
          $d_code = $dis_tra_dis['CustomerCode'];
        }
        
      }

      $tra1 = mysqli_query($con, "select sum(credit) as total from supply where suppliers_id = '$tem'"); 
      $dis_tra1 = mysqli_fetch_assoc($tra1); 
      $d = $dis_tra1['total']; 

      $tra12 = mysqli_query($con, "select sum(debit) as total from supply where suppliers_id = '$tem'"); 
      $dis_tra12 = mysqli_fetch_assoc($tra12); 
      $d2 = $dis_tra12['total']; 
      $real = $d - $d2; 

      $sheet->setCellValue('A'.$rowCount, $rowCount - 1);
      $row !== null ? $sheet->setCellValue('B'.$rowCount, $d_code) : $sheet->setCellValue('B'.$rowCount, $dt_code);
      $row !== null ? $sheet->setCellValue('C'.$rowCount, $d_name) : $sheet->setCellValue('C'.$rowCount, $dt);
      $row !== null ? $sheet->setCellValue('D'.$rowCount, $d_beat) : $sheet->setCellValue('D'.$rowCount, $dt_beat);
      $row !== null ? $sheet->setCellValue('E'.$rowCount, $real) : $sheet->setCellValue('E'.$rowCount, $real);
      $rowCount++; 
      $num--; 
    }
    $writer = new Xlsx($spreadsheet);
    $save_name = $fileName.'.xlsx'; 
    $targetPath = 'files/'.$save_name; 
    $writer->save($targetPath);
    echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; bottom: 10px; position: absolute;'>Generated successfully!</div>";
  }


  else if ($down_type == 'all_time_detailed') {
    $currentDate = date('Y-m-d');
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'NO');
    $sheet->setCellValue('B1', 'Date');
    $sheet->setCellValue('C1', 'CustomerCode');
    $sheet->setCellValue('D1', 'Customer Name');
    $sheet->setCellValue('E1', 'Beat/Route');
    $sheet->setCellValue('F1', 'Balance');
    $sheet->setCellValue('G1', 'Due Days');
    $transaction_result = mysqli_query($con, "select * from supply") or die ("query incorrect");
    $transaction_id = mysqli_query($con, "select distinct suppliers_id from supply") or die ("query incorrect");
    $num = mysqli_num_rows($transaction_id);

    $rowCount = 2;
    $fileName = "All time balance report-" . date('YmdHisA') ; 
    while ( $num > 0) {
      $row = mysqli_fetch_assoc($transaction_id); 
      if ($row == null) {
        list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date, $CustomerCode) = mysqli_fetch_array($transaction_result); 
        $tra_name1 = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$suppliers_id'"); 
        $d_t_d = mysqli_fetch_assoc($tra_name1); 
        $dt = $d_t_d['name']; 
        $dt_beat = $d_t_d['beat']; 
        $dt_code = $d_t_d['CustomerCode']; 
        $tem = $suppliers_id; 
        $tem_num_id = mysqli_query($con, "select * from supply where suppliers_id = '$tem'"); 
        $num_id = mysqli_num_rows($tem_num_id); 
        $date_tem = mysqli_query($con, "select max(date) as max_date from supply where suppliers_id = '$tem'"); 
        $dis_date = mysqli_fetch_assoc($date_tem); 
        $dt_date = $d_t_d['max_date']; 
        $date_passed = mysqli_query($con, "select datediff('$currentDate', '$dt_date') as p_d from supply where suppliers_id = '$tem'"); 
        $passed_days = mysqli_fetch_assoc($date_passed); 
        $dt_dates = $passed_days['p_d']; 
        

      }
      else {
        $tem = $row['suppliers_id']; 
        $tem_num_id = mysqli_query($con, "select * from supply where suppliers_id = '$tem'"); 
        $num_id = mysqli_num_rows($tem_num_id); 
        $tra_name = mysqli_query($con, "select name, beat, CustomerCode from suppliers where id = '$tem'"); 
        $dis_tra_dis = mysqli_fetch_assoc($tra_name);
        $date_tem = mysqli_query($con, "select max(date) as max from supply where suppliers_id = '$tem'"); 
        $dis_date = mysqli_fetch_assoc($date_tem); 

        if ($dis_tra_dis == null) {
          $row !== null ? $sheet->setCellValue('E'.$rowCount, $num_id) : $sheet->setCellValue('E'.$rowCount, $num_id);
          list($id, $name, $beat, $CustomerCode) = mysqli_fetch_array($tra_name); 
          $d_name = $name;
          $d_beat = $beat;
          $d_code = $CustomerCode;
          list($max) = mysqli_fetch_array($date_tem); 
          $d_date = $max; 
        }
        else {
          $d_name = $dis_tra_dis['name'];
          $d_beat = $dis_tra_dis['beat'];
          $d_code = $dis_tra_dis['CustomerCode'];
          $d_date = $dis_date['max']; 
        }
        $date_passed = mysqli_query($con, "select datediff('$currentDate', '$d_date') as p_d from supply where suppliers_id = '$tem'"); 
        $dis_tra111 = mysqli_fetch_assoc($date_passed); 
        $d_dates = $dis_tra111['p_d']; 

      }

      $tra1 = mysqli_query($con, "select sum(credit) as total from supply where suppliers_id = '$tem'"); 
      $dis_tra1 = mysqli_fetch_assoc($tra1); 
      $d = $dis_tra1['total']; 

      $tra12 = mysqli_query($con, "select sum(debit) as total from supply where suppliers_id = '$tem'"); 
      $dis_tra12 = mysqli_fetch_assoc($tra12); 
      $d2 = $dis_tra12['total']; 
      $real = $d - $d2; 

      $sheet->setCellValue('A'.$rowCount, $rowCount - 1);
      $row !== null ? $sheet->setCellValue('B'.$rowCount, $d_date) : $sheet->setCellValue('B'.$rowCount, $dt_date);
      $row !== null ? $sheet->setCellValue('C'.$rowCount, $d_code) : $sheet->setCellValue('C'.$rowCount, $dt_code);
      $row !== null ? $sheet->setCellValue('D'.$rowCount, $d_name) : $sheet->setCellValue('D'.$rowCount, $dt);
      $row !== null ? $sheet->setCellValue('E'.$rowCount, $d_beat) : $sheet->setCellValue('E'.$rowCount, $dt_beat);
      $row !== null ? $sheet->setCellValue('F'.$rowCount, $real) : $sheet->setCellValue('F'.$rowCount, $real);
      $row !== null ? $sheet->setCellValue('G'.$rowCount, $d_dates) : $sheet->setCellValue('G'.$rowCount, $dt_dates);
      $rowCount++; 
      $num--; 
    }
    $writer = new Xlsx($spreadsheet);
    $save_name = $fileName.'.xlsx'; 
    $targetPath = 'files/'.$save_name; 
    $writer->save($targetPath);
    echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; bottom: 10px; position: absolute;'>Generated successfully!</div>";
  }
}

?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <!-- your content here -->
          <div class="col-md-12">
              <div class="card">
                
              <div class="card-body">
                  <h5>Export Reports</h5>
                  <form action="" method="post" type="form" name="form" enctype="multipart/form-data">
                    <div class="mb-2">
                      <label for="acc_name" class="form-label"
                        >Report Type</label
                      >
                      <select class="form-control" id = "down_type" name = "down_type" onchange="togglebutton()">
                        <option value="transaction">Transaction Report</option>
                        <option value="balance">Balance Report</option>
                        <option value="all_balance">
                          All Time Balance Report
                        </option>
                        <option value="all_time_detailed">
                          All Time Balance Report Detailed
                        </option>
                      </select>
                    </div>
                    <div
                      class="mb-2"
                      id = "from_input"
                      >
                      <label for="date" class="form-label">From</label>
                      <input
                        type="date"
                        class="form-control"
                        id="date_from"
                        name="date_from"
                        placeholder="From"
                      />
                    </div>
                    <div
                      class="mb-2"
                      id = "to_input"
                    >
                      <label for="date" class="form-label">To</label>
                      <input
                        type="date"
                        class="form-control"
                        id="date_to"
                        name="date_to"
                        placeholder="To"
                      />
                    </div>
                    <div
                      class="spinner-border text-primary m-2 text-center m-auto d-block mb-4 mt-4"
                      role="status"
                    ></div>
                    <button
                      class="btn btn-primary w-100 mb-4"
                      type="submit"
                      id = "btn_save"
                      name = "btn_save"
                    >
                      Generate
                    </button>
                  </form>
                </div>
              </div>
            </div>
            
            <a href="<?php if(isset($save_name)) echo 'files/'.$save_name ?>" download><?php echo ($save_name !== null ) ? $save_name : 'No file to download' ?></a>                   
            
        </div>
      </div>
       <script>
        function togglebutton() {
          var options = document.getElementById("down_type");
          var from1 = document.getElementById("from_input");
          var to1 = document.getElementById("to_input");
          
          if (options.value == "transaction" || options.value == "balance") {
            from1.style.display = "block";
            to1.style.display = "block";
          } 
          else {
            from1.style.display = "none";
            to1.style.display = "none";
          }
        }
       </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

        <script>  
            $(document).ready(function(){  
                $('#alert_message').fadeOut(1000);  
            });  
        </script>
      <?php
include "footer.php";
?>
</body>  
</html> 
