<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}

include '../inc/functions.php';

$userSystem =$_SESSION['FullName'];
$user=$_SESSION['UserID'];
$currentWarehouse=$_SESSION['WarehouseID'];
$role=$_SESSION['Role'];
$alert =$_SESSION['Alert'] = '2';
$today = date('M-d-Y');

If ($currentWarehouse !=1)
{
  // Transfers
  $WHERE_Transfer = "WHERE Warehouse_Destination=$currentWarehouse";
  $AND_Transfer = "AND Warehouse_Destination=$currentWarehouse";
  
  // Orders
  $WHERE_Order = "WHERE Warehouse_ID=$currentWarehouse";
  $AND_Order = "AND Warehouse_ID=$currentWarehouse";
}
Else{
  $WHERE_Transfer ="WHERE 1=1";
  $AND_Order="";
  $AND_Transfer="";
}

// Consulta para obtener el número total de productos y productos en alerta
$sql_products = "SELECT 
                    (SELECT COUNT(*) FROM Products ) AS totalProducts,
                    (SELECT COUNT(*) FROM Products WHERE Minimum_Level <= 1) AS ProductAlert
                ";   

// Consulta para obtener el número total de órdenes y órdenes canceladas
$sql_orders = "SELECT 
                    (SELECT COUNT(*) FROM orders $WHERE_Order) AS Orders,
                    (SELECT COUNT(*) FROM orders WHERE Order_Status='In-Progress' $AND_Order) AS OrdersAlert
                ";

// Consulta para obtener el número de órdenes por mes
$sql_Order_by_Month = "SELECT 
                            DATE_FORMAT(Order_Date, '%Y-%m') AS Month,
                            MONTHNAME(Order_Date) AS Month_Name,
                            COUNT(Order_Number) AS Number_of_Orders
                            FROM 
                                orders
                            GROUP BY 
                                DATE_FORMAT(Order_Date, '%Y-%m'), MONTHNAME(Order_Date)
                            ORDER BY 
                                Month;
                            ";


// Consulta para obtener el número total de transferencias y transferencias canceladas
$sql_transfer = "SELECT 
                    (SELECT COUNT(*) FROM transfers $WHERE_Transfer) AS Transfer,
                    (SELECT COUNT(*) FROM transfers WHERE Transfer_Status='In-Progress' OR Transfer_Status='Open' 			     $AND_Transfer) 
		    AS TransferAlert
		     
                ";

//
// Consulta para obtener el número de transfer por Warehouse
$sql_transfer_by_warehouse = "SELECT 
                            w.Warehouse_Name AS Delivery,
                            COUNT(t.Transfer_Number) AS Number_of_Transfer
                            FROM 
                                transfers t
                            JOIN Warehouses w ON w.Warehouse_ID=t.Warehouse_Destination
                            WHERE 
                                MONTH(Transfer_Date) = MONTH(CURRENT_DATE())
                            GROUP BY 
                                Warehouse_Destination
                            ORDER BY 
                                Warehouse_Destination";

// Consulta disponibilidades por tipo

$sql_Prd_Available = "SELECT 'In_Stock' AS Type, COUNT(*) AS Quantity
                            FROM (
                                SELECT p.Product_ID
                                FROM Products p
                                LEFT JOIN item_availabilities a ON a.Product_ID = p.Product_ID
                                GROUP BY p.Product_ID, p.Minimum_level
                                HAVING COALESCE(SUM(a.Quantity), 0) > 0
                            ) AS InStock

                            UNION ALL

                            SELECT 'Out_Stock' AS Tipo, COUNT(*) AS Cantidad
                            FROM (
                                SELECT p.Product_ID
                                FROM Products p
                                LEFT JOIN item_availabilities a ON a.Product_ID = p.Product_ID
                                GROUP BY p.Product_ID, p.Minimum_level
                                HAVING COALESCE(SUM(a.Quantity), 0) <= 0
                            ) AS OutStock

                            UNION ALL

                            SELECT 'Minimum_Stock' AS Tipo, COUNT(*) AS Cantidad
                            FROM (
                                SELECT p.Product_ID
                                FROM Products p
                                LEFT JOIN item_availabilities a ON a.Product_ID = p.Product_ID
                                GROUP BY p.Product_ID, p.Minimum_level
                                HAVING COALESCE(SUM(a.Quantity), 0) <= p.Minimum_level
                            ) AS MinimumStock;
                            ";


// Establecer conexión y ejecutar consultas
$conn = connect();

// Consulta para productos
$result_products = $conn->query($sql_products);
if ($result_products->num_rows > 0) {
    $row_products = $result_products->fetch_assoc();
    $totalProducts = $row_products["totalProducts"];
    $totalAlert = $row_products["ProductAlert"];
} else {
    $totalProducts = 0;
    $totalAlert = 0;
}

// Query orders
$result_orders = $conn->query($sql_orders);
if ($result_orders->num_rows > 0) {
    $row_orders = $result_orders->fetch_assoc();
    $totalOrders = $row_orders["Orders"];
    $ordernalerts = $row_orders["OrdersAlert"];
} else {
    $totalOrders = 0;
    $ordernalerts = 0;
}

// Query orders by Month
$result_orders_by_month = $conn->query($sql_Order_by_Month);
$labels_month = [];
$data_orders_month = [];

if ($result_orders_by_month->num_rows > 0) {
    while ($row_month = $result_orders_by_month->fetch_assoc()) {
        $labels_month[] = $row_month["Month_Name"];
        $data_orders_month[] = $row_month["Number_of_Orders"];
    }
}

// Query Transfer
$result_transfer = $conn->query($sql_transfer);
if ($result_transfer->num_rows > 0) {
    $row_transfer = $result_transfer->fetch_assoc();
    $totalTransfer = $row_transfer["Transfer"];
    $transferalerts = $row_transfer["TransferAlert"];
} else {
    $totalTransfer = 0;
    $transferalerts = 0;
}

// Query orders by Warehouse
$result_transfer_by_warehouse = $conn->query($sql_transfer_by_warehouse);
$labels_warehouse = [];
$data_transfer_warehouse = [];

if ($result_transfer_by_warehouse->num_rows > 0) {
    while ($row_warehouse= $result_transfer_by_warehouse->fetch_assoc()) {
        $labels_warehouse[] = $row_warehouse["Delivery"];
        $data_transfer_warehouse[] = $row_warehouse["Number_of_Transfer"];
    }
}

// Query Product Available
$result_sql_Prd_Available = $conn->query($sql_Prd_Available);
$labels_Available = [];
$data_Prd_Available = [];

if ($result_sql_Prd_Available->num_rows > 0) {
    while ($row_month = $result_sql_Prd_Available->fetch_assoc()) {
        $labels_Available[] = $row_month["Type"];
        $data_Prd_Available[] = $row_month["Quantity"];
    }
}


// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="icon" href="../images/company/syncrostock.ico" type="image/x-icon">
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
            margin-bottom: 5px; /* Increased bottom margin */
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px; /* Increased top margin */
        }

        .card,
        .card2,
        .card3 {
            flex: 1;
            padding: 20px;
            margin-right: 20px;
            border-radius: 4px;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card:hover,
        .card2:hover,
        .card3:hover {
            transform: translateY(-8px); /* Shadow effect on hover */
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2); /* Increased shadow size */
        }

        .card a,
        .card2 a,
        .card3 a {
            color: inherit;
            text-decoration: none;
        }

        .card h2,
        .card2 h2,
        .card3 h2 {
            margin-bottom: 10px;
        }

        .card a:hover,
        .card2 a:hover,
        .card3 a:hover {
            text-decoration: none;
            color: inherit;
        }

        .card {
            background-color: #9ACB9A;
            color: #000000;
        }

        .card2 {
            background-color: #9298F7;
            color: #000000;
        }

        .card3 {
            background-color: #83C9F7;
            color: #000000;
        }

        .bk-alert {
            flex: 1;
            margin-right: 20px;
            border-radius: 4px;
            text-align: center;
            position: relative;
        }

        .alert {
            flex: 1; /* Takes up all available space */
            padding: 8px;
            background-color: #E87E5D17;
            color: #721c24;
            border: 0px solid #f5c6cb;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 20px; /* Adjusted top margin */
        }

        thead th {
            background-color: #2B597A;
            color: white;
            padding: 10px;
        }

        tbody td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        .add-button-container {
            margin-bottom: 20px; /* Space between button and table */
        }

        .pie-chart {
            text-align: center;
            margin-top: 20px;
            width: 65%; /* Adjusted size */
            margin-left: auto;
            margin-right: auto;
        }

        .pie-chart canvas {
            width: 100%; /* Full width inside the div */
            height: auto; /* Auto height to maintain aspect ratio */
            max-width: 300px; /* Maximum canvas width */
            max-height: 300px; /* Maximum canvas height */
            border-radius: 8px;
        }

        .bar-chart {
            margin-top: 40px; /* Space between charts */
            width: 100%;
            max-width: 600px; /* Maximum bar chart width */
            margin-left: auto;
            margin-right: auto;
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
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <!-- Main content area -->
    <div class="S-main-content">
    
        <div class="S-breadcrumbs">
                <a href="./home.php">Home </a><span>/</span>
                <span>Inventory </span><span>/</span>
        </div>

        <h2 style="margin-bottom: 20px;">Inventory Management </h2>

        <div class="summary-cards py-3">
            <div class="card" title="View details">
                <h2><a href="./Frm_Resu_prd.php">Products</a></h2>
                <p><?php echo number_format($totalProducts)." Item(s)"; ?></p>
            </div>

            <div class="card2" title="View details">
                <h2><a href="./orders/Frm_Resu_order.php">Orders</a></h2>
                <p><?php echo number_format($totalOrders)." Order(s)"; ?></p>
            </div>

            <div class="card3" title="View details">
                <h2><a href="./transfer/Frm_Resu_trf.php">Transfers</a></h2>
                <p><?php echo number_format($totalTransfer)." Transfer(s)"; ?></p>
            </div>
        </div>

        <!-- Alert area and Charts container -->

        <div class="summary-cards">
            <div class="bk-alert">
                <div class="alert">
                    <p title="Products with Stock < Reorder"><?php echo number_format($totalAlert)." - Product(s) need attention"; ?></p>
                </div>
                
                <div class="pie-chart">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <div class="bk-alert">
                <div class="alert">
                    <p title="Orders in Progress"><?php echo number_format($ordernalerts)." - Order(s) need attention"; ?></p>
                </div>

                <div class="bar-chart py-5">
                    <canvas id="barChart1"></canvas>
                </div>
            </div>

            <div class="bk-alert">
                <div class="alert">
                <p title="Transfers in Progress or Open"><?php echo number_format($transferalerts)." - Transfer(s) need attention"; ?></p>
                </div>

                <div class="bar-chart py-5">
                    <canvas id="barChart2"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Pie chart data
            const dataPie = {
                labels: <?php echo json_encode($labels_Available); ?>,
                datasets: [{
                    label: 'Product per Category',
                    data: <?php echo json_encode($data_Prd_Available); ?>,
                    backgroundColor: [
                        '#4BC0C0', // In Stock
                        '#FF6384',  // Out of Stock
                        '#F4D65E'  // Minimum Stock
                    ],
                    hoverOffset: 4
                }]
            };

            // Pie chart configuration
            const configPie = {
                type: 'pie',
                data: dataPie,
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    }
                }
            };

            // Orders per month
            const dataBar1 = {
                labels: <?php echo json_encode($labels_month); ?>,
                datasets: [{
                    label: 'Orders per Month',
                    data: <?php echo json_encode($data_orders_month); ?>,
                    backgroundColor: [
                        '#0556B7', // 1
                        '#9298F7', // 2
                        '#EA644F', // 3
                        '#4BC0C0', // 4
                        '#9966FF',  // 5
                        '#0556B7', // 6
                        '#36A2EB', // 7
                        '#FFCE56', // 8
                        '#4BC0C0', // 9
                        '#9966FF',  // 10
                        '#E57373',  // 11
                        '#F06292'  // 12ß
                    ],
                    borderWidth: 1
                }]
            };

            // Bar chart configuration
            const configBar1 = {
                type: 'bar',
                data: dataBar1,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            // Transfer by Warehouse
            const dataBar2 = {
                labels: <?php echo json_encode($labels_warehouse); ?>,
                datasets: [{
                    label: 'Transfer by Warehouse',
                    data: <?php echo json_encode($data_transfer_warehouse); ?>,
                    backgroundColor: [
                        '#148F77', // 1
                        '#83C9F7', // 2
                        '#AF7AC5', // 3
                        '#FF33A1', // 4
                        '#A1FF33', // 5
                        '#33FFF2', // 6
                        '#F2FF33', // 7
                        '#FF6F33', // 8
                        '#6F33FF', // 9
                        '#33FF6F', // 10
                        '#FF33F2', // 11
                        '#33F2FF', // 12
                        '#F2FF6F', // 13
                        '#FF6F6F', // 14
                        '#6F6FFF', // 15
                        '#6FFF6F', // 16
                        '#F26F6F', // 17
                        '#6FF2F2', // 18
                        '#F2A533', // 19
                        '#A533F2'  // 20
                    ],
                    borderWidth: 1
                }]
            };

            // Bar chart configuration
            const configBar2 = {
                type: 'bar',
                data: dataBar2,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            // Initialize charts
            var pieChart = new Chart(document.getElementById('pieChart').getContext('2d'), configPie);
            var barChart1 = new Chart(document.getElementById('barChart1').getContext('2d'), configBar1);
            var barChart2 = new Chart(document.getElementById('barChart2').getContext('2d'), configBar2);
        });
    </script>
</body>
</html>
