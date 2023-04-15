<div class = "card">
    <div class = "card-body">
    <label style = "color: #343a00; font-weight: 600; margin-bottom: 20px;  ">Show Customer Ledger</label>
        <div class="row" style="display: flex; justify-content: space-between; ">
        <div class="col-md-6 col-sm-12 transaction_header" style="display: flex">
            <label>Show</label>
            <select name = "datatable_length" aria-controls="datatable" class="form-control form-select-sm">
            <option value="10">10</option>
            <option value="10">25</option>
            <option value="10">50</option>
            <option value="10">100</option>
            </select>
            <label>entries</label>
        </div>
        <div class="col-md-6 col-sm-12">
            <form action="" method = "post" type = "form" name = "form7">
                <label>Search:</label>
                <input type = "text" id = "search_key" name = "search_key"/>
                <input type = "text" id = "search_keyword" name = "search_keyword" hidden >
                <input type = "text" id = "search_from" name = "search_from" hidden >
                <input type = "text" id = "search_to" name = "search_to" hidden >
                <button onclick="get_search_result()" type="submit" id="search_result" name="search_result" required class="btn btn-fill btn-primary">Search</button>
            </form>
        </div>
        </div>

        <?php 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_result'])){
            ?>
            <label style = "color: #343a00; font-weight: 600; margin:0px 20px;  "><?php echo $name1 ?></label>
            
            <?php
            if (isset($_POST['search_key'])) {
                print_r($_POST['search_key']); 
            }
            $search_key = $_POST['search_key'];
            $total_amount = 0; 
            $query = "SELECT id FROM suppliers WHERE name = '$name3'"; 
            $filter_id = mysqli_query($con, $query);
            $row = $filter_id->fetch_assoc(); 
            $real_id = $row['id']; 
            $result = mysqli_query($con,"select * from supply where suppliers_id = '$real_id' and date between '$date_from' and '$date_to'") or die ("query 1 incorrect.....");
            list($id, $suppliers_id, $particulars, $quantity, $debit, $credit, $date) = mysqli_fetch_array($result);
            $num = mysqli_num_rows($result); 
            if ($num < 1) 
                exit(); 
            ?>
            <table class="table table-hover tablesorter " id="transaction_detail">
                <tbody>
                <tr><th>ID</th><th>Date</th><th>Salesman</th><th>Beat</th><th>Debit</th><th>Credit</th><th>Amount</th><th>Action</th></tr>
            <!-- modified -->
            
            <?php
            while($num > 0){
                $row = mysqli_fetch_assoc($result); 
                $t_id = $row !== null ? $row['suppliers_id'] : $suppliers_id; 
                $temp = "SELECT beat FROM suppliers WHERE id = '$t_id'"; 
                $temp_id = mysqli_query($con, $temp); 
                $tem_row = $temp_id->fetch_assoc(); 

                $tem_beat = $tem_row['beat']; 
                $tem_id = $row !== null ? $row['id'] : $id; 
                $t_date = $row !== null ? $row['date'] : $date; 
                $t_particulars = $row !== null ? $row['particulars'] : $particulars; 
                $t_quantity = $row !== null ? $row['quantity'] : $quantity; 
                $t_debit = $row !== null ? $row['debit'] : $debit; 
                $t_credit = $row !== null ? $row['credit'] : $credit; 
                $t_amount = $t_credit - $t_debit; 
                
                echo "<tr><td>$tem_id</td><td>$t_date</td><td>$t_quantity</td><td>$tem_beat</td><td>$t_debit</td><td>$t_credit</td><td>$t_amount</td>
                <td>
                <a class = 'btn btn-warning' onClick = 'open_entry_modal(`$tem_id`,`$t_date`,`$t_particulars`, `$t_quantity`, `$t_debit`, `$t_credit`)'>Edit</a>
                <a class = 'btn btn-danger' onClick = 'confirm_entry_del(`$tem_id`)'>Delete</a></td>
                </td>
                </tr>";
                $num--; 
                $total_amount += $t_amount; 
            }
            
            ?> 
                </tbody>
            </table>
            <label>Total Amount: </label><?php echo $total_amount ?>
            <?php
            }
            
        ?>

            
    </div>
</div>