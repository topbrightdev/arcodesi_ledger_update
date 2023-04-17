  <?php 
include("../../db.php");
 
  ?>
    <div class="row" style="padding: 20vh 10vh 0vh 10vh; align-items: left; ">
        <a type = "button" class="btn btn-primary w-50 mb-4" href="index.php">
            <h5 style = "margin-bottom: 0px !important;">Refresh</h5>
        </a>    
    </div>
    <div class="row" style="padding: 0vh 10vh 0vh 10vh; margin-top: 20px; ">
        
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6" style="padding-left: 0px; ">
          <div class="card card-stats">
              <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                      <i class="material-icons">content_copy</i>
                  </div>
                  <p class="card-category">Total Suppliers</p>
                  <h3 class="card-title">
                      <?php  $query = "SELECT id FROM suppliers"; 
                                      $result = mysqli_query($con, $query);  
                                       if ($result) 
                        { 
                            // it return number of rows in the table. 
                            $row = mysqli_num_rows($result); 
                              
                            printf(" " . $row); 
                        
                            // close the result. 
                        }  ?>
                  </h3>
              </div>

          </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6" style="padding-left: 0px; ">
          <div class="card card-stats">
              <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                      <i class="material-icons">store</i>
                  </div>
                  <p class="card-category">Total Amount Due</p>
                  <h3 class="card-title"> 
                    <?php  
                    $query = "SELECT SUM(debit) AS total FROM supply"; 
                    $query2 = "SELECT SUM(credit) AS total2 FROM supply"; 
                    $result = mysqli_query($con, $query); 
                    $result2 = mysqli_query($con, $query2); 
                    if ($result && $result2) 
                    { 
                        $row = mysqli_fetch_assoc($result);
                        $row2 = mysqli_fetch_assoc($result2);
                        $total = $row['total'] - $row2['total2']; 
                        print_r($total);  
                    } 
                    ?></h3>
              </div>

          </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6" style="padding-left: 0px; ">
          <div class="card card-stats">
              <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                      <i class="material-icons">info_outline</i>
                  </div>
                  <p class="card-category" style = "font-size: 15px !important">Total Amount Received</p>
                  <h3 class="card-title">
                    <?php  
                    $query = "SELECT SUM(debit) AS total FROM supply"; 
                    $query2 = "SELECT SUM(credit) AS total2 FROM supply"; 
                    $result = mysqli_query($con, $query); 
                    $result2 = mysqli_query($con, $query2); 
                    if ($result && $result2) 
                    { 
                        $row = mysqli_fetch_assoc($result);
                        $row2 = mysqli_fetch_assoc($result2);
                        $total = $row2['total2']; 
                        print_r($total);  
                    } 
                    ?></h3>
              </div>

          </div>
      </div>
  </div>