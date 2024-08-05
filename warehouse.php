<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}

$authorizedRoles = ['1', '2'];

// Verificar si el rol del usuario está en la lista de roles autorizados
if (!in_array($_SESSION['Role'], $authorizedRoles)) {
    
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: ./home.php?redirect=" . urlencode($currentUrl));  // 
    exit;

}

require_once "warehouse/main/databasehandler.inc.php";

// Fetch active warehouses
try {
    $query = "SELECT w.Warehouse_ID, w.Warehouse_Name, w.Address, w.City, w.Province, w.Postal_Code, w.Country, w.Phone, w.Fax, w.Status, u.FirstName, u.Lastname, u.email, u.Cellphone
              FROM Warehouses AS w LEFT JOIN user AS u ON w.wh_user_manager_id = u.user_id WHERE w.Status = 'Active';";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error - for simplicity, you might want to log the error or handle it gracefully
    echo "Error fetching warehouses: " . $e->getMessage();
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

            flex: 1;
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

        .transfer-logs {
            margin-top: 20px;
        }

        .transfer-logs table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transfer-logs th,
        .transfer-logs td {
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

        .transfer-logs th {
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
    </style>
</head>

<body>

    <iframe id="S-iframe1" src="navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="container">
            <div class="S-breadcrumb">
                <a href="home.php">Home</a><span>/</span>
                <span>Warehouse</span>
            </div>
            <br>
            <div class="container">
                <div class="button-section">
                    <div class="add-button">
                        <a href="warehouse/addwarehouse/addwarehouse.php" class="button-link">
                            <button class="green-button">Add Warehouse</button>
                        </a>
                    </div>
                    <div>
                        <a href="warehouse/managewarehouse/managewarehouse.php" class="button-link">
                            <button class="red-button">Manage Warehouse</button>
                        </a>
                    </div>
                    <div>
                        <a href="warehouse/transfers/transferlogs.php" class="button-link">
                            <button class="blue-button">View Transfer Logs</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="selection">
                <form method="post">
                    <label for="warehouse">Select Warehouse:</label>
                    <select name="warehouse" id="warehouse">
                        <?php foreach ($warehouses as $index => $warehouse): ?>
                            <option value="<?= htmlspecialchars($warehouse['Warehouse_ID']) ?>"
                                data-address="<?= htmlspecialchars($warehouse['Address']) ?>"
                                data-city="<?= htmlspecialchars($warehouse['City']) ?>"
                                data-province="<?= htmlspecialchars($warehouse['Province']) ?>"
                                data-postalcode="<?= htmlspecialchars($warehouse['Postal_Code']) ?>"
                                data-country="<?= htmlspecialchars($warehouse['Country']) ?>"
                                data-phone="<?= htmlspecialchars($warehouse['Phone']) ?>"
                                data-fax="<?= htmlspecialchars($warehouse['Fax']) ?>"
                                data-firstname="<?= htmlspecialchars($warehouse['FirstName'] ?? 'N/A') ?>"
                                data-lastname="<?= htmlspecialchars($warehouse['Lastname'] ?? 'N/A') ?>"
                                data-email="<?= htmlspecialchars($warehouse['email'] ?? 'N/A') ?>"
                                data-cellphone="<?= htmlspecialchars($warehouse['Cellphone'] ?? 'N/A') ?>" <?= $index === 0 ? 'selected' : '' ?>>
                                <?= htmlspecialchars($warehouse['Warehouse_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="container">
            <div class="supplier-cards">
                <div class="card">
                    <h2 id="displayWarehouse"></h2>
                    <p id="displayAddress"></p>
                    <p id="displayCity"></p>
                    <p id="displayCountry"></p>
                    <p id="displayPhone"></p>
                </div>

                <div class="card">
                    <h2>Warehouse Manager</h2>
                    <p id="displayManagerName"></p>
                    <p id="displayEmail"></p>
                    <p id="displayCellphone"></p>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="stats">
                <div class="prevent-click">
                    <p>Transfers In</p>
                    <h2 id="transferInCounts"></h2>
                </div>
                <div class="prevent-click">
                    <p>Transfers Out</p>
                    <h2 id="transferOutCounts"></h2>
                </div>
                <div class="prevent-click">
                    <p>Products In Stock</p>
                    <h2 id="inStockCounts"></h2>
                </div>
                <div class="prevent-click">
                    <p>Products Out Of Stock</p>
                    <h2 id="outOfStockCounts"></h2>
                </div>
            </div>
        </div>

        <div class="section-overview">
            <div class="export-add">
                <div class="pie-chart">
                    <canvas id="pieChart" width="300" height="300"></canvas>
                    <h2>Warehouse Occupancy</h2>
                </div>
            </div>
            <div class="abc">
                <h2>Transfer Logs</h2>
                <!-- Search and Filter Box Container -->
                <div class="search-filter-container">
                    <!-- Search Box -->
                    <div class="search-box">
                        <form action="warehouse.php" method="GET">
                            <input type="text" name="search" placeholder="Search by Transaction Number or Warehouse"
                                value="<?= htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '') ?>">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                    <!-- Filter Box -->
                    <div class="filter-box">
                        <form action="warehouse.php" method="GET">
                            <select name="filter">
                                <option value="">Filter by Type</option>
                                <option value="PO" <?= (isset($_GET['filter']) && $_GET['filter'] == 'PO') ? 'selected' : '' ?>>PO</option>
                                <option value="TRF" <?= (isset($_GET['filter']) && $_GET['filter'] == 'TRF') ? 'selected' : '' ?>>TRF</option>
                            </select>
                            <button type="submit">Filter</button>
                        </form>
                    </div>
                </div>
                <!-- Transfer Logs Table -->
                <div class="transfer-logs">
                    <?php require_once 'warehouse/transfers/fetch_transfer_logs_by_warehouse.php' ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Transaction Number</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>From</th>
                                <th>To</th>
                            </tr>
                        </thead>
                        <tbody id="transferLogsTableBody">
                            <?php if (!empty($transfers)): ?>
                                <?php foreach ($transfers as $transfer): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transfer['Type']) ?></td>
                                        <td><?= htmlspecialchars($transfer['Transactions_Number']) ?></td>
                                        <td><?= htmlspecialchars($transfer['Date']) ?></td>
                                        <td><?= htmlspecialchars($transfer['Quantity']) ?></td>
                                        <td><?= htmlspecialchars($transfer['Price']) ?></td>
                                        <td><?= htmlspecialchars($transfer['origin_warehouse']) ?></td>
                                        <td><?= htmlspecialchars($transfer['destination_warehouse']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No transfer logs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination">
                        <?php
                        $total_pages = ceil($total_records / $records_per_page);

                        if ($total_pages > 1) {
                            // "First" link
                            if ($page > 1) {
                                echo '<a href="warehouse.php?page=1&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">First</a>';
                            }

                            // Previous link
                            if ($page > 1) {
                                echo '<a href="warehouse.php?page=' . ($page - 1) . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Prev</a>';
                            }

                            // Page number links
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            if ($start > 1) {
                                echo '<a href="warehouse.php?page=1&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">1</a>';
                                if ($start > 2) {
                                    echo '<span>...</span>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $page) {
                                    echo '<span class="current-page">' . $i . '</span>';
                                } else {
                                    echo '<a href="warehouse.php?page=' . $i . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">' . $i . '</a>';
                                }
                            }

                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<span>...</span>';
                                }
                                echo '<a href="warehouse.php?page=' . $total_pages . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">' . $total_pages . '</a>';
                            }

                            // Next link
                            if ($page < $total_pages) {
                                echo '<a href="warehouse.php?page=' . ($page + 1) . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Next</a>';
                            }

                            // "Last" link
                            if ($page < $total_pages) {
                                echo '<a href="warehouse.php?page=' . $total_pages . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Last</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const selectElement = document.getElementById('warehouse');

                const data = {
                    labels: ['In Stock', 'Out of Stock'],
                    datasets: [{
                        label: 'Product Availability',
                        data: [0, 0], // Initial data
                        backgroundColor: [
                            '#2596be', // Green for In Stock
                            '#F4D65E'  // Grey for Out of Stock
                        ],
                        hoverOffset: 4
                    }]
                };

                const config = {
                    type: 'pie',
                    data: data,
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

                const ctx = document.getElementById('pieChart').getContext('2d');
                const myChart = new Chart(ctx, config);

                // Fetch and update chart for the initially selected warehouse
                updateAllData(selectElement.value, myChart);

                // Update chart and other data on warehouse selection change
                selectElement.addEventListener('change', function () {
                    const warehouseId = selectElement.value;
                    updateAllData(warehouseId, myChart);
                    fetchTransferLogs(warehouseId); // Fetch transfer logs based on the selected warehouse
                });

                // Adjust main content width and margin on navigation open/close
                window.addEventListener('message', function (event) {
                    if (event.data.action === 'openNav') {
                        document.getElementById("S-main-content").style.marginLeft = "270px";
                        document.getElementById("S-main-content").style.width = "calc(100% - 270px)";
                    } else if (event.data.action === 'closeNav') {
                        document.getElementById("S-main-content").style.marginLeft = "0";
                        document.getElementById("S-main-content").style.width = "100%";
                    }
                });

                // Prevent default action for elements with the class 'prevent-click'
                document.querySelectorAll('.prevent-click').forEach(function (element) {
                    element.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                });

                function updateDisplay() {
                    var selectedOption = selectElement.options[selectElement.selectedIndex];
                    var selectedWarehouseName = selectedOption.text;
                    var selectedWarehouseAddress = selectedOption.getAttribute('data-address');
                    var selectedWarehouseCity = selectedOption.getAttribute('data-city');
                    var selectedWarehouseProvince = selectedOption.getAttribute('data-province');
                    var selectedWarehousePostalCode = selectedOption.getAttribute('data-postalcode');
                    var selectedWarehouseCountry = selectedOption.getAttribute('data-country');
                    var selectedWarehousePhone = selectedOption.getAttribute('data-phone');
                    var selectedWarehouseFax = selectedOption.getAttribute('data-fax');
                    var selectedFirstName = selectedOption.getAttribute('data-firstname');
                    var selectedLastname = selectedOption.getAttribute('data-lastname');
                    var selectedEmail = selectedOption.getAttribute('data-email');
                    var selectedCellphone = selectedOption.getAttribute('data-cellphone');

                    document.getElementById('displayWarehouse').innerText = selectedWarehouseName;
                    document.getElementById('displayAddress').innerText = selectedWarehouseAddress;
                    document.getElementById('displayCity').innerText = selectedWarehouseCity + ', ' + selectedWarehouseProvince + ', ' + selectedWarehousePostalCode;
                    document.getElementById('displayCountry').innerText = selectedWarehouseCountry;
                    document.getElementById('displayPhone').innerText = selectedWarehousePhone;
                    document.getElementById('displayManagerName').innerText = selectedFirstName + ' ' + selectedLastname;
                    document.getElementById('displayEmail').innerText = selectedEmail;
                    document.getElementById('displayCellphone').innerText = selectedCellphone;
                }

                async function fetchData(url, warehouseId) {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({ warehouse_id: warehouseId })
                    });
                    return await response.json();
                }

                async function updateChart(chart, warehouseId) {
                    const inStockData = await fetchData('warehouse/stocks/fetch_in_stock_products.php', warehouseId);
                    const outOfStockData = await fetchData('warehouse/stocks/fetch_out_of_stock_products.php', warehouseId);

                    chart.data.datasets[0].data = [inStockData.in_stock_count, outOfStockData.out_of_stock_count];
                    chart.update();
                }

                async function updateAllData(warehouseId, chart) {
                    const inStockData = await fetchData('warehouse/stocks/fetch_in_stock_products.php', warehouseId);
                    const outOfStockData = await fetchData('warehouse/stocks/fetch_out_of_stock_products.php', warehouseId);
                    const transferInData = await fetchData('warehouse/transfers/fetch_transfer_in_counts.php', warehouseId);
                    const transferOutData = await fetchData('warehouse/transfers/fetch_transfer_out_counts.php', warehouseId);

                    document.getElementById('inStockCounts').innerText = inStockData.in_stock_count;
                    document.getElementById('outOfStockCounts').innerText = outOfStockData.out_of_stock_count;
                    document.getElementById('transferInCounts').innerText = transferInData.transfer_in_count;
                    document.getElementById('transferOutCounts').innerText = transferOutData.transfer_out_count;

                    updateChart(chart, warehouseId);
                    updateDisplay();
                }

                async function fetchTransferLogs(warehouseId) {
                    try {
                        const response = await fetch('warehouse/transfers/fetch_transfer_logs_by_warehouse.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({ warehouse_id: warehouseId })
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        console.log('Transfer Logs Data:', data); // Log data for debugging

                        const tableBody = document.getElementById('transferLogsTableBody');
                        const pagination = document.getElementById('pagination');

                        tableBody.innerHTML = '';
                        data.transfers.forEach(function (transfer) {
                            // Display log only if the selected warehouse is in either the 'From' or 'To' column
                            if (transfer.origin_warehouse_id === warehouseId || transfer.destination_warehouse_id === warehouseId) {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                            <td>${transfer.Type}</td>
                            <td>${transfer.Transactions_Number}</td>
                            <td>${transfer.Date}</td>
                            <td>${transfer.Quantity}</td>
                            <td>${transfer.Price}</td>
                            <td>${transfer.origin_warehouse}</td>
                            <td>${transfer.destination_warehouse}</td>
                        `;
                                tableBody.appendChild(row);
                            }
                        });

                        pagination.innerHTML = data.pagination;
                    } catch (error) {
                        console.error('Error fetching transfer logs:', error); // Log errors for debugging
                    }
                }

                // Initial display update
                updateDisplay();
            });

            async function fetchTransferLogs(warehouseId) {
                const response = await fetch('warehouse/transfers/fetch_transfer_logs_by_warehouse.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({ warehouse_id: warehouseId })
                });
                const data = await response.json();
                return data;
            }

            async function updateTransferLogs(warehouseId) {
                const data = await fetchTransferLogs(warehouseId);

                if (data.transfers && Array.isArray(data.transfers)) {
                    const tableBody = document.getElementById('transferLogsTableBody');
                    tableBody.innerHTML = '';

                    data.transfers.forEach(log => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${log.Type}</td>
                <td>${log.Transactions_Number}</td>
                <td>${log.Date}</td>
                <td>${log.Quantity}</td>
                <td>${log.Price}</td>
                <td>${log.origin_warehouse}</td>
                <td>${log.destination_warehouse}</td>
            `;
                        tableBody.appendChild(row);
                    });

                    // Handle pagination if needed
                    // For example: update pagination controls
                }
            }

            // Call this function on warehouse selection change
            selectElement.addEventListener('change', function () {
                const warehouseId = selectElement.value;
                updateAllData(warehouseId, myChart); // Update chart and other data
                updateTransferLogs(warehouseId); // Update transfer logs
            });
        </script>

    </div>
    </div>
</body>

</html>