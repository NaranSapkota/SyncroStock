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
            padding: 8px 16px;
            /* Adjust padding */
            cursor: pointer;
            border: 0px solid #fff;
            background-color: #2596be;
            color: #fff;
            border-radius: 5px;
            width: auto;
            /* Let the button width adjust to content */
            font-size: 14px;
            /* Adjust font size */
        }

        .button-link button:hover {
            background-color: #2596be;
            border-color: #2596be;
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
            background-color: #2596be;
            padding: 20px;
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

        .transfer-logs {
            margin-top: 20px;
            align-self: center;
            display: block;
        }

        .transfer-logs h2 {
            text-align: center;
        }

        .transfer-logs table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            align-self: center;
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
            align-items: center;
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
    </style>
</head>

<body>

    <iframe id="S-iframe1" src="../../navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="container">
            <div class="S-breadcrumb">
                <a href="../../home.php">Home</a><span>/</span>
                <a href="../../warehouse.php">Warehouse</a><span>/</span>
                <span>View Transfer Logs</span>
            </div>
            <!-- Search and Filter Box Container -->
            <div class="search-filter-container">
                <!-- Search Box -->
                <div class="search-box">
                    <form action="transferlogs.php" method="GET">
                        <input type="text" name="search" placeholder="Search by Transaction Number or Warehouse"
                            value="<?= htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '') ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>
                <!-- Filter Box -->
                <div class="filter-box">
                    <form action="transferlogs.php" method="GET">
                        <select name="filter">
                            <option value="">Filter by Type or Warehouse</option>
                            <option value="PO" <?= (isset($_GET['filter']) && $_GET['filter'] == 'PO') ? 'selected' : '' ?>>PO</option>
                            <option value="TRF" <?= (isset($_GET['filter']) && $_GET['filter'] == 'TRF') ? 'selected' : '' ?>>TRF</option>
                            <?php
                            include '../main/databasehandler.inc.php';
                            $stmt = $pdo->prepare("SELECT Warehouse_Name FROM Warehouses");
                            $stmt->execute();
                            $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($warehouses as $warehouse):
                                ?>
                                <option value="<?= htmlspecialchars($warehouse['Warehouse_Name']) ?>"
                                    <?= (isset($_GET['filter']) && $_GET['filter'] == $warehouse['Warehouse_Name']) ? 'selected' : '' ?>><?= htmlspecialchars($warehouse['Warehouse_Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Filter</button>
                    </form>
                </div>
            </div>
            <!-- Transfer Logs Table -->
            <div class="transfer-logs">
                <h2>Transfer Logs</h2>
                <?php include 'fetch_transfer_logs.php'; ?>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Transaction Number</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>From Warehouse</th>
                            <th>To Warehouse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transfers)): ?>
                            <?php foreach ($transfers as $transfer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($transfer['Transaction_ID']) ?></td>
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
                                <td colspan="8">No transfer logs found.</td>
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
                            echo '<a href="transferlogs.php?page=1&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">First</a>';
                        }

                        // Previous link
                        if ($page > 1) {
                            echo '<a href="transferlogs.php?page=' . ($page - 1) . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Prev</a>';
                        }

                        // Page number links
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);

                        if ($start > 1) {
                            echo '<a href="transferlogs.php?page=1&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">1</a>';
                            if ($start > 2) {
                                echo '<span>...</span>';
                            }
                        }

                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $page) {
                                echo '<span class="current-page">' . $i . '</span>';
                            } else {
                                echo '<a href="transferlogs.php?page=' . $i . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">' . $i . '</a>';
                            }
                        }

                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) {
                                echo '<span>...</span>';
                            }
                            echo '<a href="transferlogs.php?page=' . $total_pages . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">' . $total_pages . '</a>';
                        }

                        // Next link
                        if ($page < $total_pages) {
                            echo '<a href="transferlogs.php?page=' . ($page + 1) . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Next</a>';
                        }

                        // "Last" link
                        if ($page < $total_pages) {
                            echo '<a href="transferlogs.php?page=' . $total_pages . '&search=' . htmlspecialchars($search) . '&filter=' . htmlspecialchars($filter) . '">Last</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>