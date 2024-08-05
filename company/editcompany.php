<?php
// editcompany.php

// Check if the warehouse ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: managecompany.php"); // Redirect if ID is missing
    exit();
}

$companyId = $_GET['id'];

// Fetch warehouse details from database
require_once "main/databasehandler.php";

try {
    $query = "SELECT company_id, company_name, address, city, state, postal_code, country, phone, email, logo 
              FROM companies 
              WHERE company_id = :companyId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':companyId', $companyId);
    $stmt->execute();
    $company = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$company) {
        header("Location: managecompany.php"); // Redirect if company not found
        exit();
    }

    // Convert the logo BLOB data to base64
    if ($company['logo']) {
        $logoBase64 = base64_encode($company['logo']);
        $logoSrc = 'data:image/jpeg;base64,' . $logoBase64;
    } else {
        $logoSrc = null;
    }




} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <title>Edit Company</title>

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
            pointer-events: unset;
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

        .container {
            width: 40%;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
    </style>

    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('logoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>

<body>
    <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="container">
            <h2>Edit Company</h2>
            <form action="main/edit_company_handler.php" method="post" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td><input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="companyname">Company Name:</label></td>
                        <td><input type="text" id="company_name" name="companyname"
                                value="<?php echo $company['company_name']; ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="address">Address:</label></td>
                        <td><input type="text" id="address" name="address" value="<?php echo $company['address']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="city">City:</label></td>
                        <td><input type="text" id="city" name="city" value="<?php echo $company['city']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="state">State/Province:</label></td>
                        <td><input type="text" id="state" name="state" value="<?php echo $company['state']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="postal_code">Postal Code:</label></td>
                        <td><input type="text" id="postal_code" name="postal_code"
                                value="<?php echo $company['postal_code']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="country">Country:</label></td>
                        <td><input type="text" id="country" name="country" value="<?php echo $company['country']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="phone">Phone:</label></td>
                        <td><input type="text" id="phone" name="phone" value="<?php echo $company['phone']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input type="text" id="email" name="email" value="<?php echo $company['email']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="logo">Logo:</label></td>
                        <td>
                            <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(this)">
                            <?php if ($logoSrc): ?>
                                <img id="logoPreview" src="<?php echo $logoSrc; ?>" alt="Company Logo"
                                    style="max-width: 100px; max-height: 100px;">
                            <?php else: ?>
                                <img id="logoPreview" alt="No Logo" style="max-width: 100px; max-height: 100px;">
                            <?php endif; ?>
                        </td>
                    </tr>



                </table>
                <button class="button" type="submit">Save</button>
                <button class="button" type="cancel"
                    onclick="javascript:window.location='managecompany.php';">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>