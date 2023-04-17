<?php

  if (!isset($_SESSION['admin_name'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: .././login.php');
  }
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['admin_name']);
    header("location: .././login.php");
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
        customerledger
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="assets/demo/demo.css" rel="stylesheet" />


    
    <link href='assets/assets/css/boxicons.css' rel='stylesheet'>
    <!--[if IE]><link rel="icon" href="/favicon.ico"><![endif]-->
    <title>customerledger</title>
    
    <link href="assets/assets/libs/mohithg-switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
	<link href="assets/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- <link href="/js/404.js" rel="prefetch"><link href="/js/account.js" rel="prefetch">
    <link href="/js/account~entry~home~report~settings.js" rel="prefetch">
    <link href="/js/account~entry~report~settings.js" rel="prefetch">
    <link href="/js/entry.js" rel="prefetch">
    <link href="/js/home.js" rel="prefetch">
    <link href="/js/report.js" rel="prefetch">
    <link href="/js/settings.js" rel="prefetch">
    <link href="/js/app.js" rel="preload" as="script">
    <link href="/js/chunk-vendors.js" rel="preload" as="script">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/icons/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4DBA87">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="customerledger">
    <link rel="apple-touch-icon" href="/img/icons/apple-touch-icon-152x152.png">
    <link rel="mask-icon" href="/img/icons/safari-pinned-tab.svg" color="#4DBA87">
    <meta name="msapplication-TileImage" content="/img/icons/msapplication-icon-144x144.png">
    <meta name="msapplication-TileColor" content="#000000"> -->
</head>

<body>
    <div class="wrapper ">
        <div class="sidebar">
            <div class="logo-box">
                <a href="index.php" class="logo logo-light text-center">
                    <h3>Customer Ledger</h3>
                </a>
                
            </div>
            <!-- <div class="left-side-menu">
                <div class="h-100" data-simplebar>
                <div id="sidebar-menu">
                    <ul id="side-menu">
                    <li class="menu-title">Navigation</li>

                    <li>
                        <a href="/account">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> Dashboard </span>
                        </a>
                    </li>

                    <li>
                        <a href="/entry">
                        <i class="mdi mdi-table"></i>
                        <span> Entry </span>
                        </a>
                    </li>

                    <li>
                        <a href="/report">
                        <i class="mdi mdi-book-open-page-variant-outline"></i>
                        <span> Report </span>
                        </a>
                    </li>

                    <li>
                        <a href="/settings">
                        <i class="fe-settings noti-icon"></i>
                        <span> Settings </span>
                        </a>
                    </li>
                    </ul>
                </div>

                <div class="clearfix"></div>
            </div>
        </div> -->
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class = "nav-item-navigation" style = "margin-left: 20px; font-size: 14px">NAVIGATION</li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="demo3.php">
                            <i class="material-icons">table </i>
                            <p>Entry</p>
                        </a>
                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addsuppliers.php">
                            <i class="material-icons">library_books</i>
                            <p>Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                        <i class="material-icons">settings</i>
                        <p>Settings</p>
                        </a>
                    </li>
                </ul>
            </div>
    </div>
</body>
<script>  
    $(document).ready(function(){  
        $('#employee_data').DataTable();  
    });  
</script>
</html>