<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Manage Company</title>
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

        .company-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .company-table th,
        .company-table td {
            border: 1px solid #ddd;
            padding: 8px;
            white-space: nowrap;
            /* Prevent text from wrapping */
            overflow: hidden;
            /* Hide overflow text */
            text-overflow: ellipsis;
            /* Show ellipsis if text overflows */
        }

        .company-table th {
            background-color: #2596be;
            color: white;
        }

        .company-table td {
            text-align: center;
        }
    </style>
</head>

<body>
    <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="S-breadcrumb">
            <a href="../../home.php">Home</a><span>/</span>
            <a href="../company.php">Company</a><span>/</span>
            <span>Manage Warehouse</span>
        </div>
        <div class="container">
            <div class="form_body">
                <!-- Display error messages -->
                <?php
                if (isset($_GET['error'])) {
                    if ($_GET['error'] == 'emptyfields') {
                        echo '<p style="color:red;">Please fill in all fields.</p>';
                    }
                } elseif (isset($_GET['success'])) {
                    if ($_GET['success'] == 'companyupdated') {
                        echo '<p style="color:green;">Company updated successfully.</p>';
                    }
                }
                ?>
                <!-- Existing Company Table -->
                <h2>Existing Company</h2>
                <table class="company-table">
                    <thead>
                        <tr>
                            
                            <th>Company Name</th>
                            <th>Company Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Postal Code</th>
                            <th>Country</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Action</th> <!-- Added Action column for Edit button -->
                        </tr>
                    </thead>
                    <tbody id="companyTableBody">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Function to handle edit button click
        function editCompany(companyId) {
            window.location.href = `editcompany.php?id=${companyId}`;
        }

        document.addEventListener("DOMContentLoaded", function () {
            fetch('fetch_manage_company.php')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('companyTableBody');
                    tableBody.innerHTML = ""; // Clear existing table data
                    data.forEach(company => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            
                            <td>${company.company_name}</td>
                            <td>${company.address}</td>
                            <td>${company.city}</td>
                            <td>${company.state}</td>
                            <td>${company.postal_code}</td>
                            <td>${company.country}</td>
                            <td>${company.phone}</td>
                            <td>${company.email}</td>
                            <td><button class="button" onclick="editCompany(${company.company_id})">Edit</button></td> <!-- Edit button -->
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching data:', error));

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

        });
    </script>
</body>

</html>