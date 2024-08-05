<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

include '../connection.php';

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
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['Supplier_ID'])) {
        header("location:../suppliers.php");
        exit;
    }

    $Supplier_ID = $_GET['Supplier_ID'];
    $sql = "SELECT * FROM Suppliers WHERE Supplier_ID=$Supplier_ID";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if (!$row) {
        header("location:../suppliers.php");
        exit;
    }

    $Company_Name = $row['Company_Name'];
    $Contact_Name = $row['Contact_Name'];
    $Contact_Title = $row['Contact_Title'];
    $Address = $row['Address'];
    $City = $row['City'];
    $Province = $row['Province'];
    $Postal_Code = $row['Postal_Code'];
    $Country = $row['Country'];
    $Phone = $row['Phone'];
    $Email = $row['Email'];
} else {
    // POST method
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

    // Validate and update supplier information
    do {
        if (
            empty($Company_Name) || empty($Contact_Name) || empty($Contact_Title) || empty($Address) || empty($City)
            || empty($Province) || empty($Postal_Code) || empty($Country) || empty($Phone) || empty($Email)
        ) {
            $error_message = "All Fields are required";
        } else {
            $sql = "UPDATE Suppliers SET Company_Name='$Company_Name', Contact_Name='$Contact_Name', Contact_Title='$Contact_Title', Address='$Address', 
            City='$City', Province='$Province', Postal_Code='$Postal_Code', Country='$Country', Phone='$Phone', Email='$Email' WHERE Supplier_ID=$Supplier_ID";

            $result = $conn->query($sql);
            if (!$result) {
                $error_message = "Invalid query: " . $conn->error;
                break;
            } 
          $success_message = "Supplier updated correctly";
	  echo "<script>
        	alert('$success_message'); 
        	window.location.href = '../suppliers.php';
      	  </script>";
        }
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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
            width: 100%;
            max-width: 800px;
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
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .S-form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .S-form-column {
            flex: 1;
            min-width: calc(50% - 20px);
        }

        .S-form label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        .S-form input[type="text"],
        .S-form input[type="email"],
        .S-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .S-form textarea {
            resize: vertical;
        }

        .S-form input[type="submit"] {
            background-color: #FFD847;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: block;
            margin: 20px auto 0;
        }

        .S-form input[type="submit"]:hover {
            background-color: #d4a000;
        }

        @media (max-width: 768px) {
            .S-form {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="S-iframe-container">
        <iframe id="iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <div class="S-main-content" id="mainContent">
        <div class="S-breadcrumb">
            <a href="home.php">Home</a><span>/</span>
            <a href="../suppliers.php">Manage Supplier</a><span>/</span>
            <span>Edit Supplier</span>
        </div>

        <h1>Edit Supplier</h1>

        <form class="S-form" method="post">
            <input type="hidden" name="Supplier_ID" value="<?php echo $Supplier_ID; ?>">

            <div class="S-form-row">
                <div class="S-form-column">
                    <label for="company_name">Company Name:</label>
                    <input type="text" id="company_name" value="<?php echo $Company_Name; ?>" name="Company_Name" required>
                </div>

                <div class="S-form-column">
                    <label for="contact_name">Contact Name:</label>
                    <input type="text" id="contact_name" value="<?php echo $Contact_Name; ?>" name="Contact_Name" required>
                </div>
            </div>

            <div class="S-form-row">
                <div class="S-form-column">
                    <label for="contact_title">Contact Title:</label>
                    <input type="text" id="contact_title" value="<?php echo $Contact_Title; ?>" name="Contact_Title" required>
                </div>

                <div class="S-form-column">
                    <label for="address">Address:</label>
                    <textarea id="address" name="Address" rows="1" required><?php echo $Address; ?></textarea>
                </div>
            </div>

            <div class="S-form-row">
                <div class="S-form-column">
                    <label for="city">City:</label>
                    <input type="text" id="city" value="<?php echo $City; ?>" name="City" required>
                </div>

                <div class="S-form-column">
                    <label for="province">Province:</label>
                    <input type="text" id="province" value="<?php echo $Province; ?>" name="Province" required>
                </div>
            </div>

            <div class="S-form-row">
                <div class="S-form-column">
                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" value="<?php echo $Postal_Code; ?>" name="Postal_Code" required>
                </div>

                <div class="S-form-column">
                    <label for="country">Country:</label>
                    <input type="text" id="country" value="<?php echo $Country; ?>" name="Country" required>
                </div>
            </div>

            <div class="S-form-row">
                <div class="S-form-column">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" value="<?php echo $Phone; ?>" name="Phone" required>
                </div>

                <div class="S-form-column">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="<?php echo $Email; ?>" name="Email" required>
                </div>
            </div>

            <input type="submit" value="Update Supplier">
        </form>
    </div>
</body>

</html>
