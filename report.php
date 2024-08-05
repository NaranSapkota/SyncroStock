<?php
$session_duration = 3600; // 1 hour in seconds
$session_path = '/'; // Adjust according to your application's needs

// Set session cookie parameters
session_set_cookie_params($session_duration, $session_path);

// Start or resume session
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'connection.php';

// $servername = "localhost";
// $username = "root";
// $password = "";
// $db_name = "uitilneuxt_syncro";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $db_name);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Function to fetch data based on selected table with pagination
function fetchDataWithPagination($conn, $table, $limit, $offset)
{
    $query = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
    return $conn->query($query);
}

// Function to count total rows for pagination
function countTotalRows($conn, $table)
{
    $query = "SELECT COUNT(*) AS total FROM $table";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to download data as CSV
function downloadCSV($data, $filename)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    $first = true;
    while ($row = $data->fetch_assoc()) {
        if ($first) {
            fputcsv($output, array_keys($row));
            $first = false;
        }
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Function to download data as Excel
function downloadExcel($data, $filename)
{
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    $first = true;
    while ($row = $data->fetch_assoc()) {
        if ($first) {
            fputcsv($output, array_keys($row), "\t");
            $first = false;
        }
        fputcsv($output, $row, "\t");
    }
    fclose($output);
    exit();
}

// Handle download request
if (isset($_POST['download']) && isset($_POST['tables'])) {
    $tables = $_POST['tables'];
    $format = $_POST['format'];

    $filename = "data";
    $first_table = true;

    foreach ($tables as $table) {
        $data = fetchDataWithPagination($conn, $table, PHP_INT_MAX, 0); // Fetch all data
        if ($format === 'csv') {
            if ($first_table) {
                downloadCSV($data, "$filename.csv");
                $first_table = false;
            } else {
                // Append additional tables to the existing CSV file
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'a');
                while ($row = $data->fetch_assoc()) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }
        } else {
            if ($first_table) {
                downloadExcel($data, "$filename.xls");
                $first_table = false;
            } else {
                // Append additional tables to the existing Excel file
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'a');
                while ($row = $data->fetch_assoc()) {
                    fputcsv($output, $row, "\t");
                }
                fclose($output);
            }
        }
    }
    exit();
}

// Set default table to 'suppliers' if no table is selected
$default_table = 'Suppliers';
$table_to_display = isset($_POST['tables']) && !empty($_POST['tables']) ? $_POST['tables'][0] : $default_table;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #f4f4f9;
        }

        .S-iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        #iframe1 {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Main container for content */
        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        /* Main content area */
        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            
            margin-top: 60px;
            position: relative;
            z-index: 1;
        }

        /* Header styling */
        #S-header {
            width: 100%;
            background: #4a90e2;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        /* Logo styling */
        .S-logo {
            font-size: 1.8em;
            color: white;
            text-transform: uppercase;
            text-align: center;
            width: 100%;
        }

        /* Breadcrumbs styling */
        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .S-breadcrumbs a {
            text-decoration: none;
            color: #4a90e2;
        }

        .S-breadcrumbs a:hover {
            text-decoration: underline;
        }

        /* Controls area styling */
        .S-controls {
            display: flex;
            align-items: center;
            margin-top: 80px;
            margin-left: 20px;
            margin-bottom: 20px;
        }

        .S-controls button {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            margin-left: 20px;
            border-radius: 25px;
            transition: background-color 0.3s, color 0.3s;
            background-color: #4a90e2;
            color: white;
        }

        .S-controls button:hover {
            background-color: #357ABD;
            color: white;
        }

        .S-controls select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 25px;
            width: 250px; /* Adjust width as needed */
            height: auto; /* Allow the height to expand based on content */
            min-height: 40px; /* Ensure it has a minimum height to show single selection */
            overflow: visible; /* Ensure dropdown shows all options */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }

        .S-controls select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.3);
        }

        /* Table styling */
        .S-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            font-size: 0.9em;
            background-color: #fff;
        }

        .S-table th,
        .S-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
        }

        .S-table th {
            background: #274c70;
            color: #ffffff;
        }

        .S-table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .S-table tbody tr:hover {
            background-color: #eaeaea;
        }

        /* Pagination styling */
        .S-pagination {
            display: flex;
            justify-content: center;
        }

.S-pagination a {
    text-decoration: none;
    color: #4a90e2;
    padding: 8px 16px;
    margin: 0 4px;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.S-pagination a:hover {
    background-color: #4a90e2;
    color: white;
}

/* Download section styling */
.download-section {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.download-section button {
    padding: 12px 20px;
    cursor: pointer;
    border: none;
    border-radius: 25px;
    transition: background-color 0.3s, color 0.3s;
    background-color: #4a90e2;
    color: white;
}

.download-section button:hover {
    background-color: #357ABD;
    color: white;
}

/* Center the table title */
.section h2 {
    text-align: center;
}

/* Center the main title */
h1 {
    text-align: center;
}

/* Make the table scrollable */
.table-container {
    overflow-x: auto;
}

/* Adjust the main content to center items */
.S-main-content {
    align-items: center;
}
</style>
</head>

<body>

<div class="S-container">
<!-- Iframe container -->
<div class="S-iframe-container">
    <iframe id="iframe1" src="navbar.php?n=1"></iframe>
</div>

<div class="S-main-content" id="mainContent">
    <h1>Synchro Stock Report</h1>

    <form method="post" id="tableForm">
        <div class="S-controls">
            <label for="tables">Select Table: </label>
            <select name="tables[]" id="tables" required>
                <option value="Suppliers" <?php if (in_array('Suppliers', $_POST['tables'] ?? [])) echo 'selected'; ?>>Suppliers</option>
                <option value="Products" <?php if (in_array('Products', $_POST['tables'] ?? [])) echo 'selected'; ?>>Products</option>
                <option value="Product_Categories" <?php if (in_array('Product_Categories', $_POST['tables'] ?? [])) echo 'selected'; ?>>Product Categories</option>
                <option value="orders" <?php if (in_array('orders', $_POST['tables'] ?? [])) echo 'selected'; ?>>Orders</option>
                <option value="Warehouses" <?php if (in_array('Warehouses', $_POST['tables'] ?? [])) echo 'selected'; ?>>Warehouses</option>
            </select>
            <button type="submit">Generate Report</button>
        </div>
    </form>

    <?php
    // Pagination variables
    $limit = 10; // Number of records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch data with pagination for the default or selected table
    $data = fetchDataWithPagination($conn, $table_to_display, $limit, $offset);

    echo '<div class="section">';
    echo '<h2>' . ucfirst(str_replace('_', ' ', $table_to_display)) . '</h2>';
    echo '<div class="table-container">';
    echo '<table class="S-table">';
    echo '<thead>';
    $first = true;
    while ($row = $data->fetch_assoc()) {
        if ($first) {
            echo '<tr>';
            foreach ($row as $key => $value) {
                echo '<th>' . $key . '</th>';
            }
            echo '</tr>';
            $first = false;
        }
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }
    echo '</thead>';
    echo '</table>';
    echo '</div>';
    echo '</div>';

    // Pagination links
    $total_rows = countTotalRows($conn, $table_to_display);
    $total_pages = ceil($total_rows / $limit);
    echo '<div class="S-pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="?table=' . $table_to_display . '&page=' . $i . '">' . $i . '</a>';
    }
    echo '</div>';
    ?>

    <div class="download-section">
        <form method="post">
            <input type="hidden" name="tables[]" value="<?php echo $table_to_display; ?>">
            <label for="format">Download as: </label>
            <select name="format" id="format" required>
                <option value="csv">CSV</option>
                <option value="excel">Excel</option>
            </select>
            <button type="submit" name="download">Download</button>
        </form>
    </div>
</div>
</div>
</body>

</html>
