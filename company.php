<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}

$authorizedRoles = ['1'];
if (!in_array($_SESSION['Role'], $authorizedRoles)) {
    
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: ./home.php?redirect=" . urlencode($currentUrl));  // 
    exit;

}


require_once "company/main/databasehandler.php";

// Fetch all companies
try {
    $query = "SELECT company_id, company_name, address, city, state, postal_code, country, phone, email FROM companies;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error - for simplicity, you might want to log the error or handle it gracefully
    echo "Error fetching companies: " . $e->getMessage();
    exit(); // Exit the script on error
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Management</title>
    <style>
        /* Existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #ffffff;
        }

        #S-iframe-container {
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
            z-index: 1;
        }

        #S-main-content {
            padding: 40px;
            flex: 1;
            background-color: #ffffff;
            border-left: 0px solid #ddd;
            overflow: auto;
            margin-left: 270px;
            /* Ancho del sidebar */
            margin-top: 25px;
            z-index: 2;
            /* Updated z-index */
            position: relative;
            /* Ensure this element is positioned relative to its normal position */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f4;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }

        .breadcrumb {
            margin: 20px 0;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #000;
            margin-right: 5px;
        }

        .breadcrumb span {
            margin-right: 5px;
        }

        .breadcrumb a::after {
            content: '>';
            margin-left: 5px;
        }

        .breadcrumb a:last-child::after {
            content: '';
        }

        h1 {
            font-size: 24px;
            margin: 15px 0;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tabs button {
            flex: 1;
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background-color: #2596be;
            color: #fff;
            border-radius: 5px;
        }

        .tabs button:hover {
            background-color: #0056b3;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stats div {
            background-color: #2596be;
            padding: 20px;
            flex: 1;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #fff;
        }

        .stats div:last-child {
            margin-right: 0;
        }

        .stats h2 {
            font-size: 36px;
            margin: 10px 0;
        }

        .stats p {
            font-size: 22px;
            margin: 0;
        }

        .stats p1 {
            font-size: 28px;
            margin: 0;
        }

        .supplier-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;

        }

        .card {
            background-color: #2596be;
            padding: 20px;
            flex: 1;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card .info {
            margin-bottom: 20px;
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 24px;
            color: #fff;
        }

        .card p {
            margin: 5px 0;
            color: #fff;
        }

        .card a {
            color: #2596be;
        }

        .card .image-placeholder {
            width: 100%;
            height: 100px;
            background-color: #ccc;
            margin-top: 10px;
            border-radius: 5px;
        }

        .section-overview {
            display: flex;
            justify-content: space-evenly;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }

        .aisles-rows {
            background-color: #2596be;
            padding: 20px;
            flex: 1;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }

        .aisles-rows input[type="text"] {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .export-add {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
            color: #2596be;
        }

        .export-add input[type="text"] {
            flex: 1;
            margin-right: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .export-add button {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #fff;
            background-color: #2596be;
            color: #fff;
            border-radius: 5px;
        }

        .export-add button:hover {
            background-color: #0056b3;
            border-color: #2596be;
        }

        .pie-chart {
            text-align: center;
            margin-top: 20px;
        }

        .pie-chart canvas {
            width: 300px;
            height: 300px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .button-link {
            text-decoration: none;
            display: block;
        }

        .button-link button {
            padding: 8px 16px;
            /* Adjust padding */
            cursor: pointer;
            border: 0px solid #fff;

            color: #fff;
            border-radius: 5px;
            width: auto;
            /* Let the button width adjust to content */
            font-size: 14px;
            /* Adjust font size */
        }


        .selection {
            margin-bottom: 15px;
        }

        .selection label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .selection select {
            width: auto;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .button-section {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
            width: 30%;
            white-space: nowrap;
        }

        .button-section div {
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #fff;
        }

        .button-section div:last-child {
            margin-right: 0;
        }

        .button-section div:hover {
            transform: translateY(-5px);
        }

        .button-section h2 {
            font-size: 36px;
            margin: 10px 0;
        }

        .button-section p {
            font-size: 18px;
            margin: 0;
        }

        .add-button {

            background-color: green;

        }

        .company-logs {
            margin-top: 20px;
        }

        .company-logs table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .company-logs th,
        .company-logs td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            white-space: nowrap;
            /* Prevent text from wrapping */
            overflow: hidden;
            /* Hide overflow text */
            text-overflow: ellipsis;
            /* Show ellipsis if text overflows */

        }

        .company-logs th {
            background-color: #2596be;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #2596be;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination a:hover {
            background-color: #0056b3;
            color: #fff;
        }

        .pagination .current-page {
            background-color: #2596be;
            color: #fff;
            border: 1px solid #2596be;
        }

        .pagination span {
            color: #000;
        }

        .search-filter-container {
            display: flex;
            justify-content: left;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .search-box,
        .filter-box {
            display: flex;
            align-items: left;
            margin-left: 10px;
        }

        .search-box input[type="text"],
        .filter-box select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 200px;
            margin-right: 10px;
        }

        .search-box button,
        .filter-box button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background-color: #2596be;
            color: #fff;
            border-radius: 5px;
        }

        .search-box button:hover,
        .filter-box button:hover {
            background-color: #0056b3;
        }

        .red-button {
            background-color: #bb5555;
        }

        .green-button {
            background-color: #32c732;
        }


        .blue-button {
            background-color: #2bb5a7;

        }

        .edit-button {
            display: inline-block;
            padding: 8px 16px;
            cursor: pointer;
            border: 1px solid #2596be;
            background-color: #2596be;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }

        .edit-button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>

    <iframe id="S-iframe1" src="navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="container">


 	 <div class="S-breadcrumbs">
                <a href="./home.php">Home </a><span>/</span>
                <span> Company </span><span>/</span>
        </div>

            <br>
            <div class="container">
                <div class="button-section">
                    <div class="add-button">
                        <a href="company/addcompany.php" class="button-link">
                            <button class="green-button">Add Company</button>
                        </a>
                    </div>
                    <div>
                        <a href="company/managecompany.php" class="button-link">
                            <button class="red-button">Manage Company</button>
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <div class="company-list">
                <table class="company-logs">
                <h1> Current List of Companies</h1>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Postal Code</th>
                            <th>Country</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><?= htmlspecialchars($company['company_name']) ?></td>
                                <td><?= htmlspecialchars($company['address']) ?></td>
                                <td><?= htmlspecialchars($company['city']) ?></td>
                                <td><?= htmlspecialchars($company['state']) ?></td>
                                <td><?= htmlspecialchars($company['postal_code']) ?></td>
                                <td><?= htmlspecialchars($company['country']) ?></td>
                                <td><?= htmlspecialchars($company['phone']) ?></td>
                                <td><?= htmlspecialchars($company['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>