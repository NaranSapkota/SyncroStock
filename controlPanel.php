<?php
session_start();


// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}

$authorizedRoles = ['1'];

// Verificar si el rol del usuario está en la lista de roles autorizados
if (!in_array($_SESSION['Role'], $authorizedRoles)) {
    
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: ./home.php?redirect=" . urlencode($currentUrl));  // 
    exit;

}


// Business variables

$userSystem =$_SESSION['FullName'];
$UserLogin=$_SESSION['username'];
$warehouseName= $_SESSION['WarehouseName'];

$alert =$_SESSION['Alert'] = '2';
$today = date('M-d-Y');


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel</title>
    <link rel="icon" href="./images/company/syncrostock.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
        }

        .S-iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        #S-iframe1 {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px; /* Adjusted top margin */
        }

        .S-breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .S-breadcrumb a {
            text-decoration: none;
            color: #007bff;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px; /* Increased bottom margin for better spacing */
            margin-top: 10px; /* Increased top margin */
        }

        .card,
        .card2,
        .card3,
        .card4,
        .card5 {
            flex: 1;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.125);
            height: 250px; /* Increased card height */
            margin-right: 10px; /* Added margin between cards */
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); /* Increased shadow size */
        }

        /* End White cards */
        .card6 {
            flex: 1;
            padding: 20px;
            border-radius: 0px;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 0px solid rgba(0, 0, 0, 0.125);
            height: 250px; /* Increased card height */
            margin-right: 10px; /* Added margin between cards */


        }

        .card:hover,
        .card2:hover,
        .card3:hover,
        .card4:hover,
        .card5:hover {
            transform: translateY(-8px); /* Shadow effect on hover */
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2); /* Increased shadow size */
        }

        .card a,
        .card2 a,
        .card3 a,
        .card4 a,
        .card5 a {
            color: #F7F6F6;
            text-decoration: none;
        }

        .card h2,
        .card2 h2,
        .card3 h2,
        .card4 h2,
        .card5 h2 {
            margin-bottom: 10px;
        }

        .card img,
        .card2 img,
        .card3 img,
        .card4 img,
        .card5 img {
            max-width: 100px; /* Example size for images */
            margin-bottom: 10px; /* Adjust as necessary */
        }

        .card-header,
        .card-header2,
        .card-header3,
        .card-header4,
        .card-header5 {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding: 10px; /* Added padding to align heights */
            height: 60px; /* Adjust the height as per your design */
        }

        .card p,
        .card2 p,
        .card3 p,
        .card4 p,
        .card5 p {
            font-size: 12px;
        }

        .card-header {
            background-color: #13AE12;
        }
        .card-header2 {
            background-color: #F45672;
        }
        .card-header3 {
            background-color: #2B597A;
        }
        .card-header4 {
            background-color: #EB7405;
        }
        .card-header5 {
            background-color: #6C6E6C;
        }

        .card {
            background-color: #FCFCFB;
            color: #000000;
        }

        .card2 {
            background-color: #FCFCFB;
            color: #000000;
        }

        .card3 {
            background-color: #FCFCFB;
            color: #000000;
        }

        .card4 {
            background-color: #FCFCFB;
            color: #000000;
        }

        .card5 {
            background-color: #FCFCFB;
            color: #000000;
        }
        .card6 {
            background-color: white;
            color: #000000;
        }

        .add-button-container {
            margin-bottom: 20px; /* Space between button and table */
        }

        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="S-container">
        <!-- Iframe container -->
        <div class="S-iframe-container">
            <iframe id="S-iframe1" src="./navbar.php?n=1"></iframe>
        </div>

        <!-- Main content area -->
        <div class="S-main-content">

            <div class="S-breadcrumbs">
                <a href="./home.php">Home </a><span>/</span>
                <span>Control Panel </span><span>/</span>
            </div>

            <!-- Adjusted bottom -->
            <h2 style="margin-bottom: 20px;">Control Panel</h2>

            <div class="summary-cards py-3">

                <div class="card" title="View details">
                    <div class="card-header"><h4><a href="./user.php">Users</h4></div>
                    <div>
                        <img src="./images/user2.png" class="img-fluid"></a>
                    </div>
                    <p>Manage: Users, Roles and Modules </p>
                </div>

                <div class="card4" title="View details">
                    <div class="card-header4"><h4><a href="./warehouse.php">Warehouses</h4></div>
                    <div>
                        <img src="./images/Warehouse.png" class="img-fluid"></a>
                    </div>
                    <p>Manage Business Warehouses </p>
                </div>

                <div class="card5" title="View details">
                <div class="card-header5"><h4><a href="company.php">Company</h4></div>
                    <div>
                        <img src="./images/Company.png" class="img-fluid"></a>
                    </div>
                    <p>Set your Company Name, Logo and More.</p>
                </div>
            </div>

            <div class="summary-cards py-3">

                <div class="card2" title="View details">
                    <div class="card-header2"><h4><a href="./Audit_Resu.php">Event Log</h4></div>
                    <div>
                        <img src="./images/log.png" class="img-fluid"></a>
                    </div>
                    <p>View Database Events </p>
                </div>

                <div class="card3" title="View details">
                <div class="card-header3"><h4><a href="./orders/Frm_Resu_order.php">Export Data</h4></div>
                    <div>
                        <img src="./images/ExportData.png" class="img-fluid"></a>
                    </div>
                    <p>Export Data to: CSV , Excel, PDF File </p>
                </div>
                    
                <div class="card6">
                    <div ><h4><a ></a></h4></div>
                    <img>
                    <h2><a ></a></h2>
                </div>
                    
               
            </div>

        </div>
    </div>

</body>

</html>





