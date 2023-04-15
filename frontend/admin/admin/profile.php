
  <?php
 session_start();
include("./includes/db.php");

$email=$_SESSION['admin_name'];
$full_name = mysqli_query($con,"select * from tblusers where email = '$email'");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['e_password'])) {
  $new_e = $_POST['new_email']; 
  $new_f = $_POST['new_full']; 
  $update_email = mysqli_query($con,"UPDATE tblusers set email = '$new_e', fullname = '$new_f'  where email ='$email'");
  echo "<script>alert('Updated successfully!'); </script>";
  // header('Location:profile.php'); 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['re_password']))
  {
  $email=$_SESSION['admin_name'];
  $old_pass = $_POST['old_pass'];
  $op = md5($old_pass);
  $new_pass = $_POST['new_pass'];
  $re_pass = $_POST['re_pass'];
  $password_query = mysqli_query($con,"select * from tblusers where email = '$email'");
  $password_row = mysqli_fetch_assoc($password_query);
  $n = $password_row['password'];
  

  if ($n == $op)
    {
    if ($new_pass !== null && $new_pass == $re_pass)
      {
        $pass = md5($re_pass);
        $update_pwd = mysqli_query($con,"UPDATE tblusers set password = '$pass' where email ='$email'");
        echo "<script>alert('Update Sucessfully'); </script>";
      }
      else
      {
      echo "<script>alert('Your new and Retype Password is not match or no password'); </script>";
      }
    }
    else
    {
    echo "<script>alert('Your old password is wrong'); </script>";
    }
  }
  // $update_email = mysqli_query($con,"UPDATE tblusers set email = '$new_e', fullname = '$new_f'  where email ='$email'");


include "sidenav.php";
include "topheader.php";

?>
      <!-- End Navbar -->
   <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Update Profile</h4>
                </div>
                <div class="card-body">
                  
                <form method="post" action="">
                    <div id="basicwizard">
                      <ul
                        class="nav nav-pills bg-light nav-justified form-wizard-header mb-4"
                      >
                        <li class="nav-item">
                          <a
                            href="#basictab1"
                            data-bs-toggle="tab"
                            data-toggle="tab"
                            class="nav-link rounded-0 pt-2 pb-2 active"
                          >
                            <i class="mdi mdi-account-circle me-1"></i>
                            <span class="d-none d-sm-inline">Account</span>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a
                            href="#basictab2"
                            data-bs-toggle="tab"
                            data-toggle="tab"
                            class="nav-link rounded-0 pt-2 pb-2"
                          >
                            <i class="mdi mdi-key me-1"></i>
                            <span class="d-none d-sm-inline"
                              >Change Password</span
                            >
                          </a>
                        </li>
                      </ul>

                      <div class="tab-content b-0 mb-0 pt-0">
                        <div class="tab-pane active" id="basictab1">
                          <div class="row">
                            <div class="col-12">
                              <div class="row mb-3">
                                <label
                                  class="col-md-3 col-form-label"
                                  for="name"
                                >
                                  Full name</label
                                >
                                <div class="col-md-9">
                                  <input
                                    type="text"
                                    id="new_full"
                                    name="new_full"
                                    class="form-control"
                                    value = <?php  if (isset($_SESSION['admin_name'])) : ?>
                                        <?php echo $_SESSION['fullname']; ?>
                                    <?php endif ?>
                                  />
                                </div>
                              </div>
                              <div class="row mb-3">
                                <label
                                  class="col-md-3 col-form-label"
                                  for="name"
                                >
                                  Email</label
                                >
                                <div class="col-md-9">
                                  <input
                                    type="text"
                                    id="new_email"
                                    name="new_email"
                                    class="form-control"
                                    value = <?php  if (isset($_SESSION['admin_name'])) : ?>
                                        <?php echo $_SESSION['admin_name']; ?>
                                    <?php endif ?>
                                  />
                                </div>
                              </div>

                              <div
                                class="spinner-border text-primary m-2 text-center m-auto d-block mb-4"
                                role="status"
                              ></div>
                              <button
                                class="btn btn-primary w-100 mb-4"
                                type="submit"
                                name = "e_password"
                                
                              >
                                Update
                              </button>
                            </div>
                          </div>
                        </div>

                        <div class="tab-pane" id="basictab2">
                          <div class="row">
                            <div class="col-12">
                              <div class="row mb-3">
                                <label
                                  class="col-md-3 col-form-label"
                                  for="password"
                                >
                                  Old Password</label
                                >
                                <div class="col-md-9">
                                  <input
                                    type="password"
                                    id="old_pass"
                                    name="old_pass"
                                    class="form-control"
                                  />
                                </div>
                              </div>

                              <div class="row mb-3">
                                <label
                                  class="col-md-3 col-form-label"
                                  for="confirm"
                                  >New Password</label
                                >
                                <div class="col-md-9">
                                  <input
                                    type="password"
                                    id="new_pass"
                                    name="new_pass"
                                    class="form-control"
                                  />
                                </div>
                              </div>
                              <div class="row mb-3">
                                <label
                                  class="col-md-3 col-form-label"
                                  for="confirm"
                                  >Confirm Password</label
                                >
                                <div class="col-md-9">
                                  <input
                                    type="password"
                                    id="re_pass"
                                    name="re_pass"
                                    class="form-control"
                                  />
                                </div>
                              </div>
                              <div
                                class="spinner-border text-primary m-2 text-center m-auto d-block mb-4"
                                role="status"
                              ></div>
                              <button
                                class="btn btn-primary w-100 mb-4"
                                type="submit"
                                name = "re_password"
                              >
                                Update
                              </button>
                            </div>
                            <!-- end col -->
                          </div>
                          <!-- end row -->
                        </div>
                      </div>
                    </div>
                  </form>

                  <!-- <form method="post" action="profile.php">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="form-group bmd-form-group">
                          <label class="bmd-label-floating">
                            <?php  if (isset($_SESSION['admin_name'])) : ?><?php echo $_SESSION['admin_name']; ?>
                            <?php endif ?>
                          
                        </label>
                          <input type="text" class="form-control" disabled="">
                        </div>
                      </div>
                     <div class="col-md-4">
                        <div class="form-group bmd-form-group">
                          <label class="bmd-label-floating">enter old password</label>
                          <input type="text" class="form-control" name="old_pass" id="npwd">
                        </div>
                      </div>
                    
                  
                      <div class="col-md-4">
                        <div class="form-group bmd-form-group">
                          <label class="bmd-label-floating">Change Password Here</label>
                          <input type="text" class="form-control" name="new_pass" id="npwd">
                        </div>
                      </div>
                     
                      <div class="col-md-4">
                        <div class="form-group bmd-form-group">
                          <label class="bmd-label-floating">confirm Password Here</label>
                          <input type="text" class="form-control" name="re_pass" id="npwd">
                        </div>
                      </div>
               
                    <button class="btn btn-primary pull-right" type="submit" name="re_password">Update Profile</button>
                   
                    <div class="clearfix"></div>
                  </form> -->
                </div>
              </div>
            </div>
         
          </div>
        </div>
      </div>
      <?php
include "footer.php";
?>