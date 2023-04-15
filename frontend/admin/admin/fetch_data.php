<!DOCTYPE html>  
<html>  
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>How to use Jquery DataTables in PHP?- Nicesnippets.com</title>  
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.4.2/css/searchBuilder.dataTables.min.css"/>
    
</head>
<body>  
    <?php  
        $connect = mysqli_connect("localhost", "root", "", "arcodesi_ledger");  
        $query ="SELECT * FROM suppliers ORDER BY ID DESC";  
        $result = mysqli_query($connect, $query);  
    ?>  
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center text-white" style="background: #1867ab;">
                        <h3>How to use Jquery DataTables in PHP? - Nicesnippets.com</h3>  
                    </div>
                    <div class="card-body">  
                        <table id="employee_data" class="table table-bordered table-striped">  
                            <thead>  
                                <tr>
                                    <th>Id</th>  
                                    <th>Name</th>
                                    <th>Beat</th>  
                                </tr>  
                            </thead>
                            <tbody>
                                <?php  
                                    while($row = mysqli_fetch_array($result))  
                                    {  
                                        echo'<tr>  
                                                <td>'.$row["id"].'</td>  
                                                <td>'.$row["name"].'</td>  
                                                <td>'.$row["beat"].'</td>  
                                            </tr>  
                                        ';  
                                    }  
                                ?>  
                            </tbody>  
                        </table>    
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <!-- Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.4.2/js/dataTables.searchBuilder.min.js"></script>
    <script>  
        $(document).ready(function(){  
            $('#employee_data').DataTable({
                searching: true,  
            }); 
        });  
    </script>
</body>  
</html> 