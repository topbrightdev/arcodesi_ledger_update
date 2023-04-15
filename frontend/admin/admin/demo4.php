    <!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css"/>
        <div class="card">
            <div class="card-header text-center text-white" style="background: #1867ab;">
                <h3>How to use Jquery DataTables in PHP? - Nicesnippets.com</h3>  
            </div>
            <div class="card-body">  
                <table id="employee_data" class="table table-bordered table-striped"> 
                    <label><?php echo $name1 ?></label>

                    <thead>  
                        <tr style="text-align: center;">
                            <th class="col-1">#ID</th>  
                            <th class="col-2">Date</th>
                            <th class="col-1">Salesman</th>  
                            <th class="col-1">Beat</th>  
                            <th class="col-1">Debit</th>  
                            <th class="col-1">Credit</th>  
                            <th class="col-1">Amount</th>    
                            <th class="col-4">Action</th>  
                        </tr>  
                    </thead>
                    <tbody>
                        <?php  
                            $total_amount = 0; 
                            $connect = mysqli_connect("localhost", "root", "", "arcodesi_ledger");  
                            $query ="SELECT * FROM suppliers WHERE name = '$name1'";  
                            $result = mysqli_query($connect, $query);
                            $row = $result->fetch_assoc(); 
                            $real_id = $row['id']; 
                            $result2 = mysqli_query($connect,"select * from supply where suppliers_id = '$real_id' and date between '$date_from' and '$date_to'") or die ("query 1 incorrect.....");
                            list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date) = mysqli_fetch_array($result2);
                            $num = mysqli_num_rows($result2); 
                            if ($num < 1) 
                                exit(); 
                            while ($num > 0) {
                                $display = mysqli_fetch_assoc($result2); 
                                $t_id = $display !== null ? $display['suppliers_id'] : $suppliers_id; 
                                $temp = "SELECT beat FROM suppliers WHERE id = '$t_id'"; 
                                $temp_id = mysqli_query($connect, $temp); 
                                $tem_display = $temp_id->fetch_assoc(); 
                                $tem_beat = $tem_display['beat']; 
                        
                                $tem_id = $display !== null ? $display['id'] : $id; 
                                $t_date = $display !== null ? $display['date'] : $date; 
                                $t_particulars = $display !== null ? $display['particulars'] : $particulars; 
                                $t_quantity = $display !== null ? $display['quantity'] : $quantity; 
                                $t_debit = $display !== null ? $display['debit'] : $debit; 
                                $t_credit = $display !== null ? $display['credit'] : $credit; 
                                $t_amount = $t_credit - $t_debit; 
                                echo"<tr style='text-align: center';>  
                                        <td>$tem_id</td>  
                                        <td>$t_date</td>  
                                        <td>$t_quantity</td>  
                                        <td>$tem_beat</td>  
                                        <td>$t_debit</td>  
                                        <td>$t_credit</td>  
                                        <td>$t_amount</td>  
                                        <td>
                                        <a class = 'btn btn-warning' onClick = 'open_entry_modal(`$tem_id`,`$t_date`,`$t_particulars`, `$t_quantity`, `$t_debit`, `$t_credit`)'>Edit</a>
                                        <a class = 'btn btn-danger' onClick = 'confirm_entry_del(`$tem_id`)'>Delete</a></td>
                                        </td>
                                    </tr>  
                                ";  
                                $num--; 
                                $total_amount += $t_amount; 
                            }
                    ?>  
                    </tbody>  
                </table>    
                <label>Total Amount: <?php echo $total_amount ?> </label>

            </div>
        </div>
            
    <!-- Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
    <script>  
        $(document).ready(function(){  
            $('#employee_data').DataTable();  
        });  
    </script>