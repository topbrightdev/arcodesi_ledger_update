
           <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " id="navigation-example">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:void(0)"> 
                        <?php  
                            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                                $url = "https://";   
                            else  
                                $url = "http://";   
                            $url.= $_SERVER['HTTP_HOST'];   
                            
                            $url.= $_SERVER['REQUEST_URI'];    
                            if (strpos($url, "demo3") == true)
                                echo "Entry";  
                            else if (strpos($url, "index") == true)
                                echo "Dashboard";  
                            else if (strpos($url, "addsuppliers") == true)
                                echo "Report";  
                            else if (strpos($url, "profile") == true)
                                echo "Settings";  
                        ?>   
                        
                        </a>
                        

                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    
                    
                    
                    
                    <div class="list-unstyled topnav-menu topnav-menu-left mb-0" style="float: right !important;">
                        <div class="btn-group" >
                            <button type="button" class="btn  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: transparent !important; color: black; padding: 10px 20px !important;">
                                <?php  if (isset($_SESSION['fullname'])) : ?><?php echo $_SESSION['fullname']; ?><?php endif ?>
                            </button>
                            <div class="dropdown-menu" style="display: inline">
                                <li class="nav-item" style="display: flex; align-items: center; justify-content: center;">
                                    <a href="profile.php"><i class="fa fa-gear" aria-hidden="true"></i>&nbsp &nbsp Profile</a>
                                </li>
                                <li class="nav-item" style="display: flex; align-items: center; justify-content: center;">
                                    <a href="?logout='1'"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp &nbsp Logout</a>
                                </li>
                            </div>
                        </div>
                        <!-- <ul class="dropdown navbar-nav">
                           
                            <li class="nav-item">
                                <a href="profile.php"><i class="fa fa-gear" aria-hidden="true"></i></a>
                            </li>
                            <li class="nav-item">
                                <a href="?logout='1'"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
                            </li>
                        </ul> -->
                    </div>
                   
                </div>
            </nav>