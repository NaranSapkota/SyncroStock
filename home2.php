<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ./login.php"); 
    exit;
}

// Define las variables de sesión
$fullName = $_SESSION['FullName'];
$warehouseID = $_SESSION['WarehouseID'];
$today = date('M-d-Y');
$version = '1.0.0';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
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
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Ancho del sidebar */
            width: calc(100% - 270px);
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            overflow: auto;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin: 10px 0 40px; /* Margen ajustado */
        }

        .card {
            flex: 1;
            padding: 20px;
            text-align: center;
            border: none;
            box-sizing: border-box;
        }

        .card2 {
            position: absolute;
            top: 0;
            right: 20px;
            width: 40%;
            padding: 20px;
            text-align: right;
            border: none;
            box-sizing: border-box;
        }

        .card a,
        .card2 a {
            color: #F7F6F6;
            text-decoration: none;
        }

        .card h2,
        .card2 h2 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card p,
        .card2 p {
            font-size: 1rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 1200px) {
            .card h2,
            .card2 h2 {
                font-size: 1.25rem;
            }

            .card p,
            .card2 p {
                font-size: 0.875rem;
            }
        }

        @media (max-width: 768px) {
            .card h2,
            .card2 h2 {
                font-size: 1rem;
            }

            .card p,
            .card2 p {
                font-size: 0.55rem;
            }

            h1 {
                font-size: 1rem;
            }

            h3 {
                font-size: 0.5rem;
            }
        }

        .card img {
            border: none;
        }

        .S-tm-color-business2 {
            color: #FFCE33;
            font-family: Arial Black, sans-serif;
            margin-bottom: 5px;
            margin-top: 120px;
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
            <div class="summary-cards">
                <div class="card">
                    <img src="./images/1.png" class="img-fluid" alt="Image">
                    <div class="card2"> 
                        <h1 class="S-tm-color-business2 py-5">Welcome</h1>
                        <h3 class="text-white"><?php echo $fullName; ?></h3>
                        <p class="text-white">Warehouse # <?php echo $warehouseID; ?></p>
                        <p class="text-white"><?php echo $today; ?></p>
                        <p class="text-white fs-6 pt-5"><?php echo "Version: ".$version; ?></p>
                        <p class="text-white fs-6">© 2024 SyncroStock. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>