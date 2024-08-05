<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Management</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <style>
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
            position: relative;
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

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 35%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="file"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .button {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button-save {
            background-color: #4CAF50;
        }

        .button-save:hover {
            background-color: #45a049;
        }

        .button-reset {
            background-color: #f0ad4e;
        }

        .button-reset:hover {
            background-color: #ec971f;
        }

        .button-cancel {
            background-color: #d9534f;
        }

        .button-cancel:hover {
            background-color: #c9302c;
        }
    </style>
</head>

<body>
    <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    <div id="S-main-content">
        <div class="breadcrumb">
            <a href="../home.php">Home</a><span>/</span>
            <span>Company</span>
        </div>
        <br>

        <div class="container">
            <div class="form_body">
                <h1>Add Company</h1>
                <!-- Display error messages -->
                <?php
                if (isset($_GET['error'])) {
                    if ($_GET['error'] == 'emptyfields') {
                        echo '<p style="color:red;">Please fill in all fields.</p>';
                    }
                }

                // Display success message
                if (isset($_GET['success'])) {
                    if ($_GET['success'] == 'companyadded') {
                        echo '<p style="color:green;">Company successfully added.</p>';
                    }
                }
                ?>
                <form action="main/add_company_handler.php" method="POST" enctype="multipart/form-data">
                    <label for="company_name">Company Name:</label>
                    <input type="text" id="company_name" name="company_name">

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address">

                    <label for="city">City:</label>
                    <input type="text" id="city" name="city">

                    <label for="state">State/Province:</label>
                    <input type="text" id="state" name="state">

                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" name="postal_code">

                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country">

                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone">

                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email">

                    <label for="logo">Company Logo:</label>
                    <input type="file" id="logo" name="logo" placeholder="(JPG, JPEG or PNG)" accept="image/*">

                    <div class="button-container">
                        <button class="button button-save" type="submit">Save</button>
                        <button class="button button-reset" type="reset">Reset</button>
                        <button class="button button-cancel" type="button" onclick="javascript:window.location='../company.php';">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

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
