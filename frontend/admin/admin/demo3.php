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
        $connect = mysqli_connect("localhost", "root", "", "arcodesi_ledger");  
        $query ="SELECT * FROM suppliers ORDER BY id DESC";  
        $result1111 = mysqli_query($connect, $query);  

        include("../../db.php");

        require_once('functions/validate.php');
        require_once('functions/phpexport/vendor/autoload.php');

        use PhpOffice\PhpSpreadsheet\IOFactory;
        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

        // load data from excel file.
        $name1 = ''; 
        $name2 = ''; 
        $date_from = '';
        $date_to = '';
        // customer add
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_excel'])) {
        $target_dir = "files/"; 
        $target_file = $target_dir . basename($_FILES["excel_file"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        $filename = $_FILES["excel_file"]["name"];

        // Check if file is an .xlsx file
        if($fileType != "xlsx") {
            echo "Sorry, only .xlsx files are allowed.";
            $uploadOk = 0;
        }
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        // Check if there were any errors with the file upload
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } 
        else {
            if (move_uploaded_file($_FILES["excel_file"]["tmp_name"], $target_file)) {
                //   echo "The file ". htmlspecialchars( basename( $_FILES["excel_file"]["name"])). " has been uploaded.";
                $index = 0; 
                $num_of_errors = 0; 
                $inputFilePath = 'files/'.$filename;
                $spreadsheet = IOFactory::load($inputFilePath);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $rowData = array();
                for ($row = 2; $row <= $highestRow; $row++){
                    for ($col = 'A'; $col <= $highestColumn; $col++){
                        $cellValue = $sheet->getCell($col.$row)->getValue();
                        $rowData[$index] = $cellValue;
                        $index++; 
                    }
                    // echo "<script>alert('$rowData[0]'); </script>";
                    if (preg_match('/[\'^£$%*}{@#~?><>,|=_+¬-]/', $rowData[1])){
                        echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>CustomerCode:$rowData[0] contains special character. Please modify it!</div>";
                        exit(); 
                    }
                    if (preg_match('/[\'^£$%*}{@#~?><>,|=_+¬-]/', $rowData[2])){
                        echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Customername:$rowData[1] contains special character. Please modify it!</div>";
                        exit(); 
                    }
                    $result1 = mysqli_query($con,"select * from suppliers where CustomerCode='$rowData[1]' and name ='$rowData[2]'") or die ("query 1 incorrect.....");
                    if (mysqli_num_rows($result1) == 0) {
                        $num_of_errors++; 
                    }
                    else {
                        $re = mysqli_fetch_assoc($result1); 
                        list($id) = mysqli_fetch_array($result1); 
                        $te_id = $re !== null ? $re['id'] : $id; 
                        $start_date = "1899-12-30"; 
                        $date_real = date("Y-m-d", strtotime($start_date.'+'.$rowData[0].'days')); 
                        mysqli_query($con, "insert into supply (suppliers_id, particulars, quantity, debit, credit, date, CustomerCode) values ('$te_id','$rowData[3]','$rowData[4]','$rowData[5]','$rowData[6]','$date_real','$rowData[1]')") or die ("query incorrect"); // 5:credit 6: description 8:date 4: debit  1: suppliers_id
                        $index = 0; 
                    }
                }
                if ($num_of_errors !== 0) {
                    echo "<div class='alert alert-danger' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>There are unregistered customers. Please register them first!</div>";
                }
                else
                    echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>New Customer Entry is added successfully!</div>";
            } 
            else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        }

        // entry add

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_excel_customer'])) {
        $target_dir = "files/"; 
        $target_file = $target_dir . basename($_FILES["customer_add_file"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        $filename = $_FILES["customer_add_file"]["name"];

        // Check if file is an .xlsx file
        if($fileType != "xlsx") {
            echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Sorry, only .xlsx files are allowed!</div>";
            $uploadOk = 0;
        }
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        // Check if there were any errors with the file upload
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } 
        else {
            if (move_uploaded_file($_FILES["customer_add_file"]["tmp_name"], $target_file)) {
                // echo "The file ". htmlspecialchars( basename( $_FILES["customer_add_file"]["name"])). " has been uploaded.";
                $index = 0; 
                $inputFilePath = 'files/'.$filename;
                $spreadsheet = IOFactory::load($inputFilePath);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $rowData = array();
                for ($row = 2; $row <= $highestRow; $row++){
                    for ($col = 'A'; $col <= $highestColumn; $col++){
                        $cellValue = $sheet->getCell($col.$row)->getValue();
                        $rowData[$index] = $cellValue;
                        $index++; 
                    }
                    if (preg_match('/[\'^£$%*}{@#~?><>,|=_+¬-]/', $rowData[0])){
                        echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>CustomerCode:$rowData[0] contains special character. Please modify it!</div>";
                        exit(); 
                    }
                    if (preg_match('/[\'^£$%*}{@#~?><>,|=_+¬-]/', $rowData[1])){
                        echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Customername:$rowData[1] contains special character. Please modify it!</div>";
                        exit(); 
                    }
                    $result1 = mysqli_query($con,"select * from suppliers where CustomerCode='$rowData[0]' and name ='$rowData[1]'") or die ("query 1 incorrect.....");
                    if (mysqli_num_rows($result1) !== 0) {
                        
                        echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Already exists!</div>";
                        exit();
                    }
                    mysqli_query($con, "insert into suppliers (name, beat, CustomerCode) values ('$rowData[1]', '$rowData[2]', '$rowData[0]')") or die ("query incorrect");
                    $index = 0; 
                }
                echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>New Customer is added successfully!</div>";
                
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        }

        $result1 = mysqli_query($con,"select * from suppliers Order By id DESC") or die ("query 1 incorrect.....");
        $real_id = 0; 
        $filter_result = []; 
        $style = 0; $name1 = ''; $to = ''; $from = ''; 

        
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save'])) {
        
        $customer_name = $_POST['customer_name']; 
        $customer_code = $_POST['customer_code']; 
        $beat = $_POST['beat']; 
        mysqli_query($con, "insert into suppliers (name, beat, CustomerCode) values ('$customer_name', '$beat', '$customer_code')") or die ("query incorrect");
        echo "<div class='success-message'>Operation completed successfully!</div>"; 
        header('Location: demo1.php');
        exit();
        }

        //credit, debit adds to database
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save1'])) {
        $create_date = $_POST['create_date']; 
        $name = $_POST['myselect']; 
        $query = "SELECT id FROM suppliers WHERE name = '$name'"; 
        $filter_id = mysqli_query($con, $query);
        $row = $filter_id->fetch_assoc(); 
        $real_id = $row['id']; 
        $description = $_POST['description']; 
        $salesman = $_POST['salesman']; 
        $credit = $_POST['credit']; 
        $cus_code_entry = $_POST['cus_code_entry']; 
        $debit = $_POST['debit']; 
        
        $result = mysqli_query($con, "select id from suppliers where name = '$name' and CustomerCode = '$cus_code_entry'");
        if (mysqli_num_rows($result) == 0) {
            echo "<div class='alert alert-success' id = 'alert_warning' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Costomer code and customer name do not match!</div>";
            echo "<script>alert(''); </script>";
            
        }
        else {
            mysqli_query($con, "insert into supply (suppliers_id, particulars, quantity, debit, credit, date, CustomerCode) values ('$real_id', '$description', '$salesman', '$debit', '$credit','$create_date', '$cus_code_entry')") or die ("query incorrect");
            echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>A new entry added successfully!</div>";
            
            // header('Location: demo1.php');
            // exit();
        }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save2'])) {
        $name1 = $_POST['myselect1']; 
        $date_from = $_POST['date_from']; 
        $c_code = $_POST['customer_code_filter']; 
        $date_to = $_POST['date_to']; 
        $filter_id = $name1; 
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_restore'])) {
            
            $restore = $_POST['restore_name']; 
            $result = mysqli_query($con, "select id from suppliers where name = '$restore'");
            if (mysqli_num_rows($result) == 0) {
                echo "<div class='alert alert-warning' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Customer name doesn't exits!</div>";
                
            }
            else {
                $query = "UPDATE suppliers SET deleted = 'N' WHERE name = '$restore'";
                mysqli_query($con, $query); 
                echo "<div class='alert alert-success' id = 'alert_message' style = 'width: 200px; right: 0px; top: 100px; position: absolute;'>Restored successfully!</div>";
                header('Location: demo1.php'); 
                exit(); 
            }

        }

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_result'])){
        $name3 = $_POST['search_keyword']; 
        
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update'])) {
        
        $edit_beat = $_POST['edit_beat']; 
        $edit_name = $_POST['edit_name']; 
        $temp_id = $_POST['temp_id'];
        $query = "UPDATE suppliers SET name = '$edit_name', beat = '$edit_beat' WHERE id = '$temp_id'"; 
        mysqli_query($con, $query);
        header('Location: demo1.php'); 
        exit(); 
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_delete'])){
        $temp_id = $_POST['del_id'];
        $tem_del_id = $_POST['del_id']; 
        $query = "UPDATE suppliers SET deleted = 'Y' WHERE id = '$temp_id'"; 

        //   $query = "DELETE FROM suppliers WHERE id = '$temp_id'"; 
        mysqli_query($con, $query); 
        header('Location: demo1.php'); 
        exit(); 
        }; 

        // update entry 
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_entry_update'])) {

        $t_update_date = $_POST['update_date']; 
        $t_sal = $_POST['update_sal']; 
        $t_credit = $_POST['update_credit'];
        $t_debit = $_POST['update_debit'];
        $t_id = $_POST['tem_update_id'];
        $query = "UPDATE supply SET particulars = '$t_sal', date = '$t_update_date', credit = '$t_credit', debit = '$t_debit' WHERE id = '$t_id'"; 
        mysqli_query($con, $query);
        header('Location: demo1.php'); 
        exit(); 
        }
        // transaction history generation. 
        include "sidenav.php";
        include "topheader.php";
        // include "footer.php";
    ?>  
    <div class="container mt-5">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                
            <!-- upload excel data -->     

            <form action="" method="post" type="form" name="form3" enctype="multipart/form-data">
                    <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class = "col-md-12">
                            <label style = "color: #343a00; font-weight: 600; margin-bottom: 20px;  ">Upload Excel Data</label>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-control" type="file" id="customer_add_file" name="customer_add_file" >
                            </div>
                            </div>
                            <div class = "col-md-12" style="display: flex; align-items: center; justify-content: center; ">
                            <button type="submit" id="btn_save_excel_customer" name="btn_save_excel_customer" required class="btn btn-fill btn-primary" style="padding: 12px 10px !important">Insert Customer From Excel</button>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-control" type="file" id="excel_file" name="excel_file" <?php if ($_SESSION['is_admin'] == 'NO') echo 'disabled' ?>>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group" >
                                <button type="submit" id="btn_save_excel" name="btn_save_excel" required <?php if ($_SESSION['is_admin'] == 'NO') echo 'disabled' ?>  class="btn btn-fill btn-primary" style="padding: 12px 10px !important">Upload Entry From Excel</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </form>

                <form action="" method="post" type="form" name="form" enctype="multipart/form-data">
                    <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class = "col-md-12">
                            <label style = "color: #343a00; font-weight: 600; margin-bottom: 20px;  ">Create New Customer</label>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Code(optional)</label>
                                    <input type="text" id="customer_code" name="customer_code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label>Customer's Name</label>
                                <input type="text" id="customer_name" name="customer_name" class="form-control">
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label>Beat(optional)</label>
                                <input type="text" id="beat" name="beat" class="form-control" >
                            </div>
                            </div>


                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="btn_save" name="btn_save" required class="btn btn-fill btn-primary">Create</button>
                    </div>
                    </div>
                </form> 

                <form action="" method="post" type="form" name="form1">
                    <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class = "col-md-12">
                            <label style = "color: #343a00; font-weight: 600; margin-bottom: 20px;  ">Create Entry Date</label>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" id="create_date" name="create_date" required class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label for="">CustomerCode(optional)</label>
                                <input type="text" id="cus_code_entry" name="cus_code_entry" class="form-control">
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group select-wrapper">
                                <label for="">Customer Name</label>
                                <select name="myselect" class = "form-control" style = "padding-bottom: 0px" id = "myselect">
                                <?php foreach ($result1 as $option) { ?>
                                    <option value="" disabled selected  >Select Customer</option>
                                    <option value="<?php echo $option['name']; ?>"><?php echo $option['name']; ?></option>
                                <?php } ?>
                                </select>
                                <span class="select-icon">&nbsp;</span>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Salesman(optional)</label>
                                <input type="text" id="salesman" name="salesman" class="form-control">
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label>Description(optional)</label>
                                <input type="text" id="description" name="description" class="form-control" >
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Credit</label>
                                <input type="number" id="credit" name="credit" required class="form-control">
                            </div>
                            </div>
                            <div class="col-md-12" style = "display: <?php if ($_SESSION['is_admin'] == 'NO') echo 'none' ?> ">
                                <div class="form-group">
                                    <label for="">Debit</label>
                                    <input type="number" id="debit" name="debit" class="form-control" <?php if ($_SESSION['is_admin'] == 'YES') echo 'required' ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="btn_save1" name="btn_save1" required class="btn btn-fill btn-primary">Submit</button>
                    </div>
                    </div>
                </form>

                <form action="" method="post" type="form" name="form2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                        <div class = "col-md-12">
                            <label style = "color: #343a00; font-weight: 600; margin-bottom: 20px;  ">Show Customer Ledger</label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                            <label for="">Customer Name</label>
                            <select id = "myselect1" name="myselect1" class = "form-control" style = "padding-bottom: 0px" id = "myselect">
                                <?php foreach ($result1 as $option) { ?>
                                <option value="" disabled selected  >Select Customer</option>
                                <option value="<?php echo $option['name']; ?>"><?php echo $option['name']; ?></option>
                                <?php } ?>
                            </select>
                            <span class="select-icon">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Customer Code(optional)</label>
                                <input type="text" id="customer_code_filter" name="customer_code_filter" class="form-control">
                            </div>
                        </div>            
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>From</label>
                            <input type="date" id="date_from" name="date_from" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>To</label>
                            <input type="date" id="date_to" name="date_to" required class="form-control">
                            <input type="text" id="filter_name" name="filter_name" class="form-control" hidden>
                            <input type="text" id="filter_from" name="filter_from" class="form-control" hidden>
                            <input type="text" id="filter_to" name="filter_to" class="form-control" hidden>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button onclick="set_filter_name()" type="submit" id="btn_save2" name="btn_save2" required class="btn btn-fill btn-primary">Show</button>
                    </div>
                </div>
                </form>  
            </div>

            <div class="col-xl-9 col-lg-8">
                <button
                    class="btn btn-primary w-50 mb-4"
                    type="button"
                    onclick="show_restore()"
                >
                    Restore Customer
                </button>
                <div class="card">
                    <div class="card-body">
                    <div class="row d-flex justify-content-between entry-head" id = "re_title">
                        <div class="col-1 text-center bg-primary" id = "re_no">
                        <h5 class="text-white">NO</h5>
                        </div>
                        <div class="col-3 text-center bg-primary" id = "re_name">
                        <h5 class="text-white">Customer Name</h5>
                        </div>
                        <div class="col-3 text-center bg-primary" id = "re_beat">
                        <h5 class="text-white">Beat</h5>
                        </div>
                        <div class="col-2 text-center bg-primary" id = "re_amount">
                        <h5 class="text-white">Amount</h5>
                        </div>
                        <div class="col-3 text-center bg-primary" id = "re_action">
                        <h5 class="text-white">Action</h5>
                        </div>
                    </div>

                    <div class="table-responsive ps">
                        <table class="table table-hover tablesorter " id="hello">
                        <tbody>
                            <?php 
                            $result = mysqli_query($con,"select * from suppliers where deleted = 'N' Order By id DESC") or die ("query 1 incorrect.....");
                            $num = mysqli_num_rows($result); 
                            $no = 1; 
                            while($num > 0)
                            {	
                                $amount = 0; 
                                $row = mysqli_fetch_assoc($result); 
                                $tem_id = $row['id']; 
                                $tem_name = $row['name']; 
                                $tem_beat = $row['beat']; 
                                    
                                $result2 = mysqli_query($con,"select * from supply where suppliers_id = '$tem_id'") or die ("query 1 incorrect.....");
                                $num_entry = mysqli_num_rows($result2); 
                                while ($num_entry > 0) {
                                $row2 = mysqli_fetch_assoc($result2); 
                                $tem_c = $row2['credit']; 
                                $tem_d = $row2['debit'];
                                $amount = $amount - $tem_c + $tem_d;  
                                // $amount = $amount + $tem_d;  
                                $num_entry--; 
                                }
                                echo "<tr id = 'click_row$no' ><td id = 'row_no'>$no</td><td id = 'row_name'>$tem_name</td><td id = 'row_beat'>$tem_beat</td><td id = 'row_amount'>$amount</td>
                                <td id = 'row_action'>
                                <a class = 'btn btn-warning' onClick = 'openModal(`$tem_name`,`$tem_beat`,`$tem_id`)'>Edit</a>
                                <a class = 'btn btn-danger' onClick = 'confirm_del(`$tem_id`)'>Delete</a></td>
                                </tr>";
                                $num--; 
                                $no++; 
                            }
                            ?>
                        </tbody>
                        </table>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;">
                        </div>
                    </div>  
                </div>
                    <div class="row d-flex justify-content-between entry-head">
                        <div class="col-5 text-center bg-primary" style="margin: 0px 10px 10px 10px ; ">
                            <h5 class="text-white">Total Balance</h5>
                        </div>
                        <div class="col-5 text-center bg-primary" style="margin: 0px 10px 10px 10px; ">
                            <h5 class="text-white">
                                <?php 
                                    $result1 = mysqli_query($con,"select sum(credit) as credit from supply") or die ("query 1 incorrect.....");
                                    $result2 = mysqli_query($con,"select sum(debit) as debit from supply") or die ("query 1 incorrect.....");
                                    $dis_tra1 = mysqli_fetch_assoc($result1); 
                                    $dis_tra2 = mysqli_fetch_assoc($result2); 
                                    $d = $dis_tra2['debit'] - $dis_tra1['credit'] ; 
                                    echo $d; 
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>                       
                
                
                <div class="modal" id = "myModal" style="display: none; ">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                    <form action="" method="post" type = "form" name = "form4">
                        <div class="mb-3">
                        <label for="username" class="form-label"
                            >Supplier Name</label
                        >
                        <input
                            class="form-control"
                            type="text"
                            id="edit_name"
                            name="edit_name"
                            required=""
                        />
                        </div>

                        <div class="mb-2">
                        <label for="beat" class="form-label">Beat</label>
                        <input
                            type="text"
                            class="form-control"
                            id="edit_beat"
                            name="edit_beat"
                            required=""
                        />
                        <input
                            type="text"
                            class="form-control"
                            id="temp_id"
                            name="temp_id"
                            hidden
                        />
                        
                        </div>

                        <div class="mb-3 text-center">
                        <div
                            class="spinner-border text-primary m-2 text-center m-auto d-block mb-4 mt-4"
                            role="status"
                        >
                        </div>
                            <button
                            class="btn btn-primary"
                            type="submit"
                            id = "btn_update"
                            name = "btn_update"
                            >
                            Update
                            </button>
                        
                        <button
                            class="btn btn-primary"
                            type="button"
                            onclick="closeModal()"
                        >
                            Close
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
                </div>
            </div>
            <!-- delte entry main -->     
            <div class="modal" id = "myModal_delete" style="display: none; ">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                    <form action="" method="post" type = "form" name = "form5">
                        <div class="mb-2">
                        <label for="del" class="form-label">Do you really want to delete it? </label>
                        </div>
                        <div class="mb-3 text-center">
                        <input
                            type="text"
                            class="form-control"
                            id="del_id"
                            name="del_id"
                            hidden
                        />
                        <button
                            class="btn btn-primary"
                            type="submit"
                            id = "btn_delete"
                            name = "btn_delete"
                        >
                            Delete
                        </button>
                        
                        <button
                            class="btn btn-primary"
                            type="button"
                            onclick="close_del_modal()"
                        >
                            Cancel
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
                </div>
            </div>

            <!-- update entry modal -->     

            <div class="modal" id = "edit_entry" style="display: none; ">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                    <form action="" method="post" type = "form" name = "form4">
                        <label for="update" class="form-label"
                            >Update Entry</label
                        >
                        <div class="mb-3">
                        <label for="username" class="form-label"
                            >Date</label
                        >
                        <input
                            class="form-control"
                            type="date"
                            id="update_date"
                            name="update_date"
                            required=""
                        />
                        </div>

                        <div class="mb-2">
                        
                        <input
                            type="text"
                            class="form-control"
                            id="tem_update_id"
                            name="tem_update_id"
                            hidden
                        />
                        </div>
                        <div class="mb-3">
                        <label for="salesman" class="form-label"
                            >Salesman(optional)</label
                        >
                        <input
                            class="form-control"
                            type="text"
                            id="update_sal"
                            name="update_sal"
                            required=""
                        />
                        </div>

                        <div class="mb-3">
                        <label for="credit" class="form-label"
                            >Credit</label
                        >
                        <input
                            class="form-control"
                            type="number"
                            id="update_credit"
                            name="update_credit"
                            required=""
                        />
                        </div>
                        <div class="mb-3">
                        <label for="debit" class="form-label"
                            >Debit</label
                        >
                        <input
                            class="form-control"
                            type="number"
                            id="update_debit"
                            name="update_debit"
                            required=""
                        />
                        </div>
                        <div class="mb-3 text-center">
                        </div>
                            <button
                            class="btn btn-primary"
                            type="submit"
                            id = "btn_entry_update"
                            name = "btn_entry_update"
                            >
                            Update
                            </button>
                        
                        <button
                            class="btn btn-primary"
                            type="button"
                            onclick="close_entry_modal()"
                        >
                            Close
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
                </div>
            </div>            
            <!-- restore entry modal -->       
            <div class="modal" id = "restoreModal" style="display: none; ">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                    <form action="" method="post" type = "form" name = "form4">
                        <div class="mb-3">
                        <label for="username" class="form-label"
                            >Input Supplier Name to restore</label
                        >
                        <input
                            class="form-control"
                            type="text"
                            id="restore_name"
                            name="restore_name"
                            required=""
                        />
                        </div>

                        
                        <div class="mb-3 text-center">
                        <div
                            class="spinner-border text-primary m-2 text-center m-auto d-block mb-4 mt-4"
                            role="status"
                        >
                        </div>
                            <button
                            class="btn btn-primary"
                            type="submit"
                            id = "btn_restore"
                            name = "btn_restore"
                            >
                            Restore
                            </button>
                        
                        <button
                            class="btn btn-primary"
                            type="button"
                            onclick="closeRestoreModal()"
                        >
                            Close
                        </button>
                        </div>
                    </form>
                    </div>
                </div>
                </div>
            </div>  
            <?php
                if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save2'])){
                    include "demo4.php";
                }
            ?>
        </div>
    </div>

    <!-- Script -->
    <script>
        
    function openModal(a, b, c) {
        document.getElementById("myModal").style.display = "block";
        document.getElementById("edit_name").value = a; 
        document.getElementById("edit_beat").value = b; 
        document.getElementById("temp_id").value = c; 
    }
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
        document.getElementById("edit_name").value = ''; 
        document.getElementById("edit_beat").value = ''; 
        document.getElementById("temp_id").value = ''; 
    }
    function confirm_del(a) {
        document.getElementById("myModal_delete").style.display = "block";
        document.getElementById("del_id").value = a; 
    }
    function close_del_modal() {
        document.getElementById("myModal_delete").style.display = "none";
        document.getElementById("del_id").value = ''; 
    }


    // update entry
    function open_entry_modal(id, date, description, salesman, debit, credit) {
        document.getElementById("edit_entry").style.display = "block";
        document.getElementById("update_date").value = date; 
        document.getElementById("update_sal").value = description; 
        document.getElementById("update_credit").value = credit; 
        document.getElementById("update_debit").value = debit; 
        document.getElementById("tem_update_id").value = id; 

    }
    function close_entry_modal() {
        document.getElementById("edit_entry").style.display = "none";
        document.getElementById("tem_update_id").value = id; 
    }
    //restore 
    function show_restore() {
        document.getElementById("restoreModal").style.display = "block";
    }
    function closeRestoreModal() {
        document.getElementById("restoreModal").style.display = "none";
    }
    function set_filter_name() {
        const tem = document.getElementById("myselect1").value; 
        const tem1 = document.getElementById("date_from").value; 
        const tem2 = document.getElementById("date_to").value; 
        document.getElementById("filter_name").value = tem; 
        document.getElementById("filter_from").value = tem1; 
        document.getElementById("filter_to").value = tem2; 
    }
    function confirm_entry_del(a) {
        document.getElementById("myModal_delete").style.display = "block";
        // document.getElementById("del_id").value = a; 
    }
    function get_search_result() {
        const input = document.getElementById("search_key").value;
        const rem1 = document.getElementById("main_id").innerHTML;
        document.getElementById("search_keyword").value = rem1;
        alert(document.getElementById("filter_name").value);
    }
    const input = document.getElementById("search_key");
    input.addEventListener("keydown", function(event) {
        if (event.key == "Enter") {
        const inputValue = input.value;
        document.getElementById("filter_name").value = inputValue; 
        const rem = document.getElementById("main_id").innerHTML;
        const rem1 = document.getElementById("filter_from").value;
        const rem2 = document.getElementById("filter_to").value;
        document.getElementById("search_keyword").value = rem; 
        document.getElementById("search_from").value = rem1; 
        document.getElementById("search_to").value = rem2; 
        }
    });
    
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
    <script>  
        $(document).ready(function(){  
            $('#employee_data').DataTable();  
            $('#alert_message').fadeOut(5000);  
            document.getElementById('create_date').valueAsDate = new Date();
        });  
        const rows = document.querySelectorAll('#hello tbody tr');
        rows.forEach(row => {
            row.addEventListener('click', () => {
                var sync_state = false; 
                var id = row.id; 
                var element = document.getElementById(id); 
                const cells = row.cells;
                const no = cells[0].innerText;
                const name = cells[1].innerText;
                const beat = cells[2].innerText;
                const amount = cells[3].innerText;
                // const action = cell[4].innerHTML; 
                newrow = document.createElement("tr");
                newrow.setAttribute("id", 'tr' + id);  
                newrow.innerHTML = "<label>beat: "+ beat + "</label><label>amount: " + amount + "</label><label>Action: </label><a class = 'btn btn-warning' onClick = 'openModal("+name+","+beat+","+no+")'>Edit</a><a class = 'btn btn-danger' onClick = 'confirm_del("+ no +")'>Delete</a>" ;
                row.getAttribute("disabled") == 'false' || row.getAttribute("disabled") == undefined ? 
                (element.parentNode.insertBefore(newrow, element.nextSibling), row.setAttribute("disabled", true)) :
                ($("#tr" + id).remove(), row.setAttribute("disabled", false));
            });
        });
        
        // automatic fill of corresponding customercode for customername
        // automatic fill of corresponding customername for customercode
        const mySelect = document.getElementById("myselect1");
        const mySelect_first = document.getElementById("myselect");
        const co_value = document.getElementById("customer_code_filter"); 
        const co_value2 = document.getElementById("cus_code_entry"); 
        mySelect.addEventListener("change", function() {
            $.ajax({
                url: 'temp.php', // Specify the path to your PHP script here
                type: 'POST',
                data: {
                    param: mySelect.value
                }, 
                dataType: 'json',
                success: function(data) {
                    co_value.value = data; 
                }
            });
        });
        mySelect_first.addEventListener("change", function() {
            $.ajax({
                url: 'temp.php', // Specify the path to your PHP script here
                type: 'POST',
                data: {
                    param: mySelect_first.value
                }, 
                dataType: 'json',
                success: function(data) {
                    co_value2.value = data; 
                }
            });
        });


        let inputElement = document.getElementById("cus_code_entry");
        let inputElement2 = document.getElementById("customer_code_filter");

        inputElement.addEventListener("input", function() {
            let inputText = inputElement.value.toLowerCase();
            let options = mySelect_first.options;
            $.ajax({
                url: 'temp1.php', // Specify the path to your PHP script here
                type: 'POST',
                data: {
                    param1: inputText
                }, 
                dataType: 'json',
                success: function(data) {
                    mySelect_first.value = data ? data : "";
                }
            });
        });
        inputElement2.addEventListener("input", function() {
            let inputText = inputElement2.value.toLowerCase();
            let options = mySelect.options;
            $.ajax({
                url: 'temp1.php', // Specify the path to your PHP script here
                type: 'POST',
                data: {
                    param1: inputText
                }, 
                dataType: 'json',
                success: function(data) {
                    mySelect.value = data ? data : "";
                }
            });
        });
    </script>
    <script>
        const search_row = document.querySelectorAll("#employee_data tbody tr");
        search_row.forEach(row => {
            row.addEventListener('click', () => {
                var id = row.id; 
                var element = document.getElementById(id); 
                const cells = row.cells;
                const id1 = cells[0].innerText;
                const date = cells[1].innerText;
                const salesman = cells[2].innerText;
                const beat = cells[3].innerText;
                const debit = cells[4].innerText;
                const credit = cells[5].innerText;
                const amount = cells[6].innerText;
                // const action = cell[4].innerHTML; 
                newrow = document.createElement("tr");
                newrow.setAttribute("id", 'tr1' + id);  
                document.getElementById(id); 

                newrow.innerHTML = "<td>salesman: " + salesman + "</td><td>beat: " + beat + "</td><td>Action: </label><a class = 'btn btn-warning' onClick = 'open_entry_modal("+id1+","+date+","+salesman+","+beat+","+debit+","+credit+")'>Edit</a><a class = 'btn btn-danger' onClick = 'confirm_entry_del("+ id1 +")'>Del</a>" ;
                row.getAttribute("disabled") == 'false' || row.getAttribute("disabled") == undefined ? 
                (element.parentNode.insertBefore(newrow, element.nextSibling), row.setAttribute("disabled", true)) :
                ($("#tr1" + id).remove(), row.setAttribute("disabled", false));
            });
        });

    </script>

    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap-material-design.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/js/material-dashboard.js?v=2.1.0"></script>
</body>  
</html> 