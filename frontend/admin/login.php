<?php include("./server/server.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>

  
    <!-- <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="./assets/css/style.css"> -->
    <link href='./assets/assets/css/boxicons.css' rel='stylesheet'>
    <!--[if IE]><link rel="icon" href="/favicon.ico"><![endif]-->
    <title>customerledger</title>
    
    <link href="./assets/assets/libs/mohithg-switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <link href="./assets/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="./assets/assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <link href="./assets/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
		<link href="./assets/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

</head>
<body class = "authentication-bg authentication-bg-pattern">
    <div class="account-pages my-5">
        <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-4">
            <div class="text-center">
                <h1>Customer Ledger</h1>
            </div>
            <div class="card">
                <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="text-uppercase mt-0">Sign In</h4>
                </div>

                <form id="login-form" action="login.php" method="post">
                    <div class="mb-3">
                    <label for="emailaddress" class="form-label"
                        >Email address</label
                    >
                    <input
                        class="form-control"
                        type="text"
                        name="admin_username" 
                        id="your_name"
                        placeholder="Enter your email"
                    />
                    </div>

                    <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        class="form-control"
                        type="password"
                        placeholder="Enter your password"
                        name="password" 
                        id="your_pass"
                    />
                    </div>

                    <div class="mb-3 d-grid text-center">
                    <button
                        class="btn btn-primary"
                        type="submit"
                        name="login_admin" 
                        id="signin" 
                        class="form-submit" 
                        value="Log in"
                    >
                        Log In
                    </button>
                    <!-- <div
                        class="spinner-border text-primary m-2 text-center m-auto"
                        role="status"
                    ></div> -->
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    <!-- <div class="main" style="padding-top: 90px;">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div>
                        <h2>Customer Ledger</h2>
                    </div>
                    <div class="signin-form">
                        <h3 class="form-title">SIGN IN</h3>
                        <form  class="register-form" id="login-form" action="login.php" method="post">
                            <div class="alert alert-danger"><h4 id="e_msg"><?php include('./server/errors.php'); ?></h4></div>
                            <div class="form-group">
                                <label for="your_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="admin_username" id="your_name" placeholder="Admin Email"/>
                            </div>
                            <div class="form-group">
                                <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="your_pass" placeholder="Password"/>
                            </div>
                           
                            <div class="form-group form-button">
                                <input type="submit" name="login_admin" id="signin" class="form-submit" value="Log in"/>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </section>

    </div> -->

    <!-- JS -->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
    <script src="js/main.js"></script>


    <script src="./assets/assets/libs/jquery/jquery.min.js"></script>
    <script src="./assets/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="./assets/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/assets/libs/node-waves/waves.min.js"></script>
    <script src="./assets/assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="./assets/assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="./assets/assets/libs/feather-icons/feather.min.js"></script>
    
    <script src="./assets/assets/libs/selectize/js/standalone/selectize.min.js"></script>
    <script src="./assets/assets/libs/mohithg-switchery/switchery.min.js"></script>
    <script src="./assets/assets/libs/multiselect/js/jquery.multi-select.js"></script>
    <script src="./assets/assets/libs/select2/js/select2.min.js"></script>
    <script src="./assets/assets/libs/jquery-mockjax/jquery.mockjax.min.js"></script>
    <script src="./assets/assets/libs/devbridge-autocomplete/jquery.autocomplete.min.js"></script>
    <script src="./assets/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <script src="./assets/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

    <script src="./assets/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="./assets/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
    <script src="./assets/assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="./assets/assets/libs/pdfmake/build/vfs_fonts.js"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="./assets/assets/js/pages/datatables.init.js"></script>
    
    <!-- Init js-->
    <script src="./assets/assets/js/pages/form-advanced.init.js" defer></script>
    
    <script src="/assets/js/app.min.js"></script>

  <script type="text/javascript" src="/js/chunk-vendors.js"></script><script type="text/javascript" src="/js/app.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
    <script>  
        $(document).ready(function(){  
            $('#alert_message').fadeOut(4000);  
        });  
    </script>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>