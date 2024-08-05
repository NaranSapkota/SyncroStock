<?php
// editwarehouse.php

// Check if the warehouse ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: managewarehouse.php"); // Redirect if ID is missing
    exit();
}

$warehouseId = $_GET['id'];

// Fetch warehouse details from database
require_once "../main/databasehandler.inc.php";

try {
    $query = "SELECT Warehouse_ID, Warehouse_Name, Address, City, Province, Postal_Code, Country, Phone, Fax, Status, wh_user_manager_id 
              FROM Warehouses 
              WHERE Warehouse_ID = :warehouseId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':warehouseId', $warehouseId);
    $stmt->execute();
    $warehouse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$warehouse) {
        header("Location: managewarehouse.php"); // Redirect if warehouse not found
        exit();
    }

    // Fetch all users for the dropdown menu
    $queryUsers = "SELECT user_id, FirstName, Lastname FROM user";
    $stmtUsers = $pdo->prepare($queryUsers);
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);




} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/styles.css">
    <title>Edit Warehouse</title>
</head>
<style>
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
        pointer-events:unset;
        z-index: 1;
    }

    #S-main-content {
        padding: 40px;
        flex: 1;
        background-color: #ffffff;
        border-left: 0px solid #ddd;
        overflow: auto;
        margin-left: 270px;
        margin-top: 25px;
        z-index: 2;
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
        margin: 20px 0;
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

    .stats div:hover {
        transform: translateY(-5px);
    }

    .stats h2 {
        font-size: 36px;
        margin: 10px 0;
    }

    .stats p {
        font-size: 18px;
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
        justify-content: space-between;
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
        padding: 10px 20px;
        cursor: pointer;
        border: 0px solid #fff;
        background-color: #2596be;
        color: #fff;
        border-radius: 5px;
        width: 100%;
    }

    .button-link button:hover {
        background-color: #2596be;
        border-color: #0056b3;
    }

    .warehouse-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .warehouse-table th,
    .warehouse-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .warehouse-table th {
        background-color: #2596be;
        color: white;
    }

    .warehouse-table td {
        text-align: left;
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
        border: 1px solid #ddd;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .container {
    width: 40%;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    }
</style>

<body>
    <iframe id="S-iframe1" src="../../navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="container">
            <h2>Edit Warehouse</h2>
            <form action="../main/edit_warehouse_handler.inc.php" method="post">
                <table>
                    <tr>
                        <td><input type="hidden" name="warehouse_id" value="<?php echo $warehouse['Warehouse_ID']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="warehousename">Warehouse Name:</label></td>
                        <td><input type="text" id="warehousename" name="warehousename" value="<?php echo $warehouse['Warehouse_Name']; ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="address">Address:</label></td>
                        <td><input type="text" id="address" name="address" value="<?php echo $warehouse['Address']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="city">City:</label></td>
                        <td><input type="text" id="city" name="city" value="<?php echo $warehouse['City']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="province">Province:</label></td>
                        <td><input type="text" id="province" name="province" value="<?php echo $warehouse['Province']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="postalcode">Postal Code:</label></td>
                        <td><input type="text" id="postalcode" name="postalcode" value="<?php echo $warehouse['Postal_Code']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="country">Country:</label></td>
                        <td><input type="text" id="country" name="country" value="<?php echo $warehouse['Country']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="phone">Phone:</label></td>
                        <td><input type="text" id="phone" name="phone" value="<?php echo $warehouse['Phone']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="fax">Fax:</label></td>
                        <td><input type="text" id="fax" name="fax" value="<?php echo $warehouse['Fax']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="status">Status:</label></td>
                        <td>
                            <div class="selection">
                                <select name="status" id="status">
                                    <option value="Active" <?php if ($warehouse['Status'] == 'Active') echo 'selected'; ?>>
                                        Active</option>
                                    <option value="Inactive"
                                        <?php if ($warehouse['Status'] == 'Inactive') echo 'selected'; ?>>Inactive
                                    </option>
                                </select>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="warehousemanager">Warehouse Manager:</label></td>
                        <td>
                            <div class="selection">
                                <select name="warehousemanager" id="warehousemanager">
                                    <option value="">Select Warehouse Manager</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['user_id']; ?>" <?php if ($user['user_id'] == $warehouse['wh_user_manager_id'])
                                               echo 'selected'; ?>>
                                            <?php echo $user['FirstName'] . ' ' . $user['Lastname']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
                <button class="button" type="submit">Save</button>
                <button class="button" type="cancel" onclick="javascript:window.location='managewarehouse.php';">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>