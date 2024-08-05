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

// Pagination variables
$results_per_page = 6; // Number of results per page
$suppliers = [];
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Current page, default to page 1

// Database connection
include 'connection.php';

// Count total number of suppliers
$sql_count = "SELECT COUNT(*) AS total FROM Suppliers";
$count_result = $conn->query($sql_count);
$total_results = $count_result->fetch_assoc()['total'];

// Calculate number of pages
$total_pages = ceil($total_results / $results_per_page);

// Validate current page value
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Calculate SQL LIMIT starting position for the results on the current page
$start_limit = ($current_page - 1) * $results_per_page;

// Query to fetch suppliers with pagination
$sql = "SELECT * FROM Suppliers LIMIT $start_limit, $results_per_page";
$result = $conn->query($sql);

// Check if results exist and fetch data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = [
            'Supplier_ID' => $row['Supplier_ID'],
            'Company_Name' => $row['Company_Name'],
            'Contact_Name' => $row['Contact_Name'],
            'Contact_Title' => $row['Contact_Title'],
            'Address' => $row['Address'],
            'City' => $row['City'],
            'Province' => $row['Province'],
            'Postal_Code' => $row['Postal_Code'],
            'Country' => $row['Country'],
            'Phone' => $row['Phone'],
            'Email' => $row['Email']
        ];
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
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
            position: absolute;
            z-index: 1;
            max-width: 100%;
        }

        /* Breadcrumbs styling */
        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .S-breadcrumbs a {
            text-decoration: none;
            color: #333;
        }

        .S-breadcrumbs a:hover {
            color: #007bff;
        }

        .S-breadcrumbs span {
            margin-right: 5px;
        }

        .S-breadcrumbs a::after {
            content: '>';
            margin-left: 5px;
        }

        .S-breadcrumbs a:last-child::after {
            content: '';
        }

        .S-pagination {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            /* Center-align the pagination */
            flex-wrap: wrap;
        }

        .buttoncenter {
            margin-top: 10px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Style for buttons */
        .buttoncenter a button,
        .buttoncenter button {
            margin: 5px;
            padding: 12px 20px;
            background-color: #4CAF50;
            /* Green background */
            color: white;
            /* White text */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .buttoncenter a button:hover,
        .buttoncenter button:hover {
            background-color: #45a049;
            /* Darker green on hover */
            transform: scale(1.05);
            /* Slightly enlarge the button on hover */
        }

        .buttoncenter a button:focus,
        .buttoncenter button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.5);
            /* Blue outline on focus */
        }

        /* Specific styles for the "Insert" button */
        .buttoncenter a button {
            background-color: #007bff;
            /* Blue background */
        }

        .buttoncenter a button:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
        }


        .S-pagination button {
            margin: 5px;
            padding: 10px 15px;
            background-color: #2596be;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .S-pagination button:hover {
            background-color: #f4d65e;
        }

        .S-pagination a {
            text-decoration: none;
        }

        .S-pagination .page-number {
            margin: 5px;
            padding: 10px 15px;
            background-color: #2596be;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .S-pagination .page-number:hover {
            background-color: #f4d65e;
        }

        /* Supplier cards grid */
        .S-supplier-cards {
            display: grid;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* Three equal columns */
        }

        .S-card {
            background-color: #2B597A;
            /* Dark blue card background */
            padding: 20px;
            display: flex;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            /* Adjusted for responsiveness */
            position: relative;
        }

        .S-card .info {
            flex: 1;
        }

        .S-card h2 {
            font-size: 20px;
            margin: 0 0 10px;
            color: #fff;
            /* White text */
        }

        .S-card p {
            font-size: 14px;
            color: #fff;
            /* White text */
            margin: 0 0 5px;
            line-height: 1.5;
        }

        .S-card .details p {
            margin: 0;
        }

        .S-card .image-placeholder {
            width: 80px;
            height: 80px;
            background-color: #cccccc;
            margin-left: 20px;
            background-image: url('images/company.png');
            background-size: cover;
            /* Ensure the image covers the entire background */
            background-position: 100%;
            /* Center the background image */
            border-radius: 8px;
            margin-top: 40px;
            /* Adjusted margin top */
        }

        .S-card .S-card-icons {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .S-card .S-card-icons a {
            color: #fff;
            margin-left: 10px;
        }

        /* Floating button */
        .S-openbtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
            background-color: #2596be;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .S-openbtn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
<div class="S-container">
    <div class="S-iframe-container">
        <iframe id="iframe1" src="navbar.php?n=1"></iframe>
    </div>

    <div class="S-main-content" id="mainContent">

       <!-- Breadcrumbs container -->
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="./suppliers.php">Suppliers </a><span>/</span>
            </div>
            <!-- End breadcrumbs container -->


        <h1>Manage Supplier</h1>
        <div class="buttoncenter">
            <a href="suppliersform.php">
                <button>Insert</button>
            </a>
            <!-- Removed Edit and Delete buttons from here -->
            <button onclick="fetchAndDisplaySuppliers()">Refresh</button>
        </div>
        <div id="supplierCards" class="S-supplier-cards">
            <?php foreach ($suppliers as $supplier): ?>
                <div class="S-card">
                    <div class="info">
                        <h2><?php echo $supplier['Company_Name']; ?></h2>
                        <div class="details">
                            <p><strong>Vendor Name:</strong> <?php echo $supplier['Contact_Name']; ?></p>
                            <p><strong>Title:</strong> <?php echo $supplier['Contact_Title']; ?></p>
                            <p><strong>Address:</strong> <?php echo $supplier['Address']; ?></p>
                            <p><strong>City:</strong> <?php echo $supplier['City']; ?></p>
                            <p><strong>Province:</strong> <?php echo $supplier['Province']; ?></p>
                            <p><strong>Postal Code:</strong> <?php echo $supplier['Postal_Code']; ?></p>
                            <p><strong>Country:</strong> <?php echo $supplier['Country']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $supplier['Phone']; ?></p>
                            <p><strong>Email:</strong> <?php echo $supplier['Email']; ?></p>
                        </div>
                    </div>
                    <div class="S-card-icons">
                        <a href="suppliersedit.php?Supplier_ID=<?php echo $supplier['Supplier_ID']; ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="suppliersdelete.php?action=delete&Supplier_ID=<?php echo $supplier['Supplier_ID']; ?>">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    <div class="image-placeholder"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Pagination Controls -->
        <div class="S-pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="page-number">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a href="?page=<?php echo $page; ?>"
                    class="page-number <?php echo ($page == $current_page) ? 'active' : ''; ?>">
                    <?php echo $page; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="page-number">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
    <script>
        function fetchAndDisplaySuppliers() {
            window.location.reload();
        }
        // Function to fetch suppliers from PHP endpoint
        function fetchSuppliers() {
            fetch('apicall/suppliersapi.php')
                .then(response => response.json())
                .then(data => {
                    const supplierCardsContainer = document.getElementById('supplierCards');
                    supplierCardsContainer.innerHTML = ''; // Clear previous content
                    data.forEach(supplier => {
                        const card = `
                        <div class="S-card">
                            <div class="info">
                                <h2>${supplier.Company_Name}</h2>
                                <div class="details">
                                    <p><strong>Vendor Name:</strong> ${supplier.Contact_Name}</p>
                                    <p><strong>Title:</strong> ${supplier.Contact_Title}</p>
                                    <p><strong>Address:</strong> ${supplier.Address}</p>
                                    <p><strong>City:</strong> ${supplier.City}</p>
                                    <p><strong>Province:</strong> ${supplier.Province}</p>
                                    <p><strong>Postal Code:</strong> ${supplier.Postal_Code}</p>
                                    <p><strong>Country:</strong> ${supplier.Country}</p>
                                    <p><strong>Phone:</strong> ${supplier.Phone}</p>
                                    <p><strong>Email:</strong> ${supplier.Email}</p>
                                </div>
                            </div>
                            <div class="S-card-icons">
                                <a href="suppliers/suppliersedit.php?Supplier_ID=${supplier.Supplier_ID}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="suppliersdelete.php?action=delete&Supplier_ID=${supplier.Supplier_ID}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                            <div class="image-placeholder"></div>
                        </div>`;
                        supplierCardsContainer.innerHTML += card;
                    });
                })
                .catch(error => {
                    console.error('Error fetching suppliers:', error);
                });
        }
        // Call fetchSuppliers initially to populate the cards
        fetchSuppliers();
        // Function to open navigation
        function openNav() {
            window.parent.postMessage({ action: 'openNav' }, '*');
        }
        // Adjust main content width and margin on navigation open/close
        window.addEventListener('message', function (event) {
            if (event.data.action === 'openNav') {
                document.getElementById("mainContent").style.marginLeft = "270px"; /* Sidebar width */
                document.getElementById("mainContent").style.width = "calc(100% - 270px)"; /* Adjusted width */
            } else if (event.data.action === 'closeNav') {
                document.getElementById("mainContent").style.marginLeft = "0";
                document.getElementById("mainContent").style.width = "100%";
            }
        });
    </script>
</body>

</html>