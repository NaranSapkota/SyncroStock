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

// $servername = "localhost";
// $username = "root";
// $password = "";
// $db_name = "uitilneuxt_syncro";

// // Create connection
// $connection = new mysqli($servername, $username, $password, $db_name);
include 'connection.php'; // Ensure this file sets $conn
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Supplier_ID = "";
$Company_Name = "";
$Contact_Name = "";
$Contact_Title = "";
$Address = "";
$City = "";
$Province = "";
$Postal_Code = "";
$Country = "";
$Phone = "";
$Email = "";

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Supplier_ID = $_POST['Supplier_ID'];
    $Company_Name = $_POST['Company_Name'];
    $Contact_Name = $_POST['Contact_Name'];
    $Contact_Title = $_POST['Contact_Title'];
    $Address = $_POST['Address'];
    $City = $_POST['City'];
    $Province = $_POST['Province'];
    $Postal_Code = $_POST['Postal_Code'];
    $Country = $_POST['Country'];
    $Phone = $_POST['Phone'];
    $Email = $_POST['Email'];

    do {
        if (
            empty($Company_Name) || empty($Contact_Name) || empty($Contact_Title) || empty($Address) || empty($City)
            || empty($Province) || empty($Postal_Code) || empty($Country) || empty($Phone) || empty($Email)
        ) {
            $error_message = "All Fields are required";
            break;
        }

        $stmt = $conn->prepare("INSERT INTO suppliers (Company_Name, Contact_Name, Contact_Title, Address, City, Province, Postal_Code, Country, Phone, Email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $Company_Name, $Contact_Name, $Contact_Title, $Address, $City, $Province, $Postal_Code, $Country, $Phone, $Email);

        if (!$stmt->execute()) {
            $error_message = "Invalid query: " . $stmt->error;
            break;
        }

        // Clear form fields after successful insert
        $Company_Name = "";
        $Contact_Name = "";
        $Contact_Title = "";
        $Address = "";
        $City = "";
        $Province = "";
        $Postal_Code = "";
        $Country = "";
        $Phone = "";
        $Email = "";

        // Set success message in session
        $_SESSION['success_message'] = "Supplier added correctly";

        // Redirect to suppliers.php after successful insertion
        header("Location: ../project/suppliers.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@github/details-dialog-element@0.9.4/dist/index.css">
    <link rel="stylesheet" href="toastr.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin-top: 50px;
            background-color: #f2f2f2;
            color: #333;
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
            z-index: 0;
        }

        #S-header {
            width: 100%;
            background: #2B597A;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
            color: #fff;
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px;
            box-sizing: border-box;
            z-index: 1;
        }

        .S-breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .S-breadcrumb a {
            text-decoration: none;
            color: #2B597A;
            transition: color 0.3s ease;
        }

        .S-breadcrumb a:hover {
            color: #007bff;
        }

        .S-breadcrumb span {
            margin-right: 5px;
        }

        .S-breadcrumb a::after {
            content: '>';
            margin-left: 5px;
        }

        .S-breadcrumb a:last-child::after {
            content: '';
        }

        h1 {
            color: #2B597A;
        }

        .S-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-row {
            margin-bottom: 15px;
        }

        .form-row label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-row input,
        .form-row textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-row textarea {
            resize: vertical;
        }

        .form-row input[type="submit"] {
            background-color: #FFD847;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .form-row input[type="submit"]:hover {
            background-color: #d4a000;
        }

        .form-row .col-md-6 {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="S-iframe-container">
        <iframe id="iframe1" src="navbar.php?n=1"></iframe>
    </div>
    <div class="S-main-content" id="mainContent">
        <div class="S-breadcrumb">
            <a href="home.php">Home</a><span>/</span>
            <a href="suppliers.php">Manage Supplier</a><span>/</span>
            <span>Add Supplier</span>
        </div>
        <h1>Add Supplier</h1>
        <form class="S-form" method="post">
            <div class="form-row row">
                <div class="col-md-6">
                    <label for="Company_Name">Company Name:</label>
                    <input type="text" id="Company_Name" value="<?php echo htmlspecialchars($Company_Name); ?>" name="Company_Name" required>
                </div>
                <div class="col-md-6">
                    <label for="Contact_Name">Contact Name:</label>
                    <input type="text" id="Contact_Name" value="<?php echo htmlspecialchars($Contact_Name); ?>" name="Contact_Name" required>
                </div>
            </div>
            <div class="form-row row">
                <div class="col-md-6">
                    <label for="Contact_Title">Contact Title:</label>
                    <input type="text" id="Contact_Title" value="<?php echo htmlspecialchars($Contact_Title); ?>" name="Contact_Title" required>
                </div>
                <div class="col-md-6">
                    <label for="Address">Address:</label>
                    <textarea id="Address" name="Address" rows="1" required><?php echo htmlspecialchars($Address); ?></textarea>
                </div>
            </div>
            <div class="form-row row">
                <div class="col-md-6">
                    <label for="City">City:</label>
                    <input type="text" id="City" value="<?php echo htmlspecialchars($City); ?>" name="City" required>
                </div>
                <div class="col-md-6">
                    <label for="Province">Province:</label>
                    <input type="text" id="Province" value="<?php echo htmlspecialchars($Province); ?>" name="Province" required>
                </div>
            </div>
            <div class="form-row row">
                <div class="col-md-6">
                    <label for="Postal_Code">Postal Code:</label>
                    <input type="text" id="Postal_Code" value="<?php echo htmlspecialchars($Postal_Code); ?>" name="Postal_Code" required>
                </div>
                <div class="col-md-6">
                    <label for="Country">Country:</label>
                    <input type="text" id="Country" value="<?php echo htmlspecialchars($Country); ?>" name="Country" required>
                </div>
            </div>
            <div class="form-row row">
                <div class="col-md-6">
                    <label for="Phone">Phone:</label>
                    <input type="text" id="Phone" value="<?php echo htmlspecialchars($Phone); ?>" name="Phone" required>
                </div>
                <div class="col-md-6">
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" value="<?php echo htmlspecialchars($Email); ?>" name="Email" required>
                </div>
            </div>

            <div class="mb-3 text-center">
                <button type="submit" onclick="success()" class="btn btn-primary">Submit</button>
                <a href="suppliers.php" class="btn btn-outline-primary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="toastr.min.js"></script>
    <script>
        function success() {
            toastr.success("Supplier added successfully");
        }
    </script>
</body>

</html>
