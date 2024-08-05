<?php
     // Start output buffering
     ob_start();
     session_start();
     // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: ../login.php");
        exit();
    }
     // Establish database connection
     include('dbConnection.php');
     $conn = connect();

    if (isset($_POST['updateUser'])) {
        $user_id_new = $_GET['user_id'];  // Fetch user_id from the URL parameters

        //fetch the user details
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $contactNumber = $_POST['contactNumber'];
        $activationDate = $_POST['activationDate'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $userName = $_POST['userName'];
        $role_id = $_POST['role_id'];
        $status = $_POST['status'] == 1 ? 'ON' : 'OFF';
        $warehouse_id = $_POST['warehouse_id'];

         // Check if the email or username already exists for another user
         $sql_check = "SELECT * FROM user WHERE (email = '$email' OR username = '$userName') AND user_id != '$user_id_new'";
         $result_check = mysqli_query($conn, $sql_check);

         if (mysqli_num_rows($result_check) > 0) {
            $_SESSION['message'] = "Email or Username already exists. Please choose another.";
            echo '<script>window.location.href = "./updateUser.php?user_id='.$user_id_new.'";</script>';
            exit();
        } else {
            //update the user
            $sql2 = "UPDATE user SET 
                FirstName = '$firstName',
                Lastname = '$lastName',
                Cellphone = '$contactNumber',
                Useractivacion = '$activationDate',
                Address = '$address',
                email = '$email',
                username = '$userName',
                Status = '$status'
                WHERE user_id = '$user_id_new'"; 

            $result = mysqli_query($conn, $sql2);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }

        // Update the user's role in the user_roles table
        $sql3 = "UPDATE user_roles SET role_id = '$role_id' WHERE user_id = '$user_id_new'";
        $result = mysqli_query($conn, $sql3);

        // Update the user's role in the user_roles table
        $sql4 = "UPDATE Warehouse_by_user SET warehouse_id = '$warehouse_id' WHERE user_id = '$user_id_new'";
        $result = mysqli_query($conn, $sql4);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        } else {
            $_SESSION['message'] = "You have updated the user data successfully!";
            echo '<script>window.location.href = "../user.php";</script>';
            exit();
        }

        // Close connection
        mysqli_close($conn);
    }
    }
    // End output buffering and flush output
    ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
 
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
            font-size: 14px;
        }

        .S-iframe-container {
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
            z-index: 0;
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 0px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 60px;
            position: relative;
            z-index: 1;
        }

        #S-header {
            width: 100%;
            background: #007bff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .S-breadcrumbs a {
            text-decoration: none;
            color: #007bff;
        }

        .S-breadcrumbs a:hover {
            text-decoration: underline;
        }

        .image-container {
            border: 1px solid #ccc;
            padding: 10px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            display: none;
        }

        .image-container .choose-image-text {
            display: block;
            color: #666;
            font-size: 14px;
            text-align: center;
        }

        .image-container .cancel-button {
            display: none;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 5px;
        }

        .image-container .cancel-button:hover {
            background-color: #cc0000;
        }

        .S-required {
            color: red;
            font-size: 17px;
        }

        .placeholder {
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .S-container {
                margin-left: 0;
                width: 100%;
            }

            .S-main-content {
                margin-top: 0;
                border-radius: 0;
                padding: 15px;
            }
        }
        #message {
            font-family: Arial, sans-serif;
        }

        #message p {
            font-weight: bold;
            margin-bottom: 5px; /* Reduce space between the paragraph and the list */
        }

        #message ul {
            list-style-type: disc; /* Use bullet points */
            padding-left: 20px; /* Adjust padding for list items */
            margin-top: 0; /* Remove top margin to reduce space */
        }

        #message ul li {
            margin-bottom: 5px; /* Reduce space between list items */
        }

        #message ul li.invalid {
            color: red;
        }

        #message ul li.valid {
            color: green;
        }
    </style>
</head>

<body>
<?php
   

    //Fetch the user details
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        $sql = "SELECT * FROM user WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $sql);    

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        } else {
            $row = mysqli_fetch_assoc($result);
        }
    } else {
        die("User ID not provided.");
    }
?>

<div class="S-container">
    <!-- Contenedor del iframe -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <!-- main area -->
    <div class="S-main-content">
        <div>
            <!-- breadcrumbs -->
            <div class="S-breadcrumbs">
                <a href="../home.php">Home </a><span>/</span>
                <a href="../controlPanel.php">Control Panel </a><span>/</span>
                <a href="../user.php">Users </a><span>/</span>
                <span>Update User</span>
            </div>
            
            <h2>Update User</h2>
            <!-- Display session message if exists -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
            <?php unset($_SESSION['message']); endif; ?>

            <!-- Form starts -->
            <form action="updateUser.php?user_id=<?php echo $user_id; ?>" method="POST">


                <!-- Start Part 1 -->
                <div class="form-group row py-2">
                    <label for="inputfName4" class="col-sm-1 col-form-label"><span class="S-required">* </span>First Name:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="firstName" id="inputfName4" value="<?php echo $row['FirstName']; ?>" required autofocus required autocomplete="off">
                    </div>
                    <label for="inputlName4" class="col-sm-1 col-form-label"><span class="S-required">* </span>Last Name:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="lastName" id="inputlName4" value="<?php echo $row['Lastname']; ?>" required autofocus required autocomplete="off">
                    </div>                    
                </div>
                <!-- End Part 1-->   

                <!-- Parte 2:-->
                <div class="form-group row py-2">
                    <label for="inputContactNumber" class="col-sm-1 col-form-label"><span class="S-required">* </span>Contact Number:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="contactNumber" id="inputContactNumber" value="<?php echo $row['Cellphone']; ?>" pattern="^[\+]?[0-9]{0,3}\W?+[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$" title="Enter a valid phone number" required autofocus required autocomplete="off">
                    </div>
                    <label for="inputEmail" class="col-sm-1 col-form-label"><span class="S-required">* </span>Email:</label>
                    <div class="col-sm-3">
                        <input type="email" class="form-control"  name="email" id="inputEmail" value="<?php echo $row['email']; ?>" required autofocus required autocomplete="off">
                        <div id="emailError" class="invalid-feedback"></div>
                    </div>                    
                </div>
                <!-- Fin Parte 2 --> 

                <!-- Part 3:  -->
                <div class="form-group row py-2">
                    <label for="inputAddress" class="col-sm-1 col-form-label"><span class="S-required">* </span>Address:</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="address" id="inputAddress" value="<?php echo $row['Address']; ?>" required autofocus required autocomplete="off">
                    </div>                   
                </div>
                <!-- Fin Parte 3 -->

                <!-- Parte 4:  -->
                <div class="form-group row">
                    <label for="inputUserActivation4" class="col-sm-1 col-form-label">Activation Date:</label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="activationDate" id="inputUserActivation4" required value="<?php echo $row['Useractivacion']; ?>">
                    </div>
                    <label for="inputGroupSelect01" class="col-sm-1 col-form-label">Status:</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="inputGroupSelect01" name="status">
                            <option value="1"<?php echo ($row['Status'] == 'ON') ? 'selected' : ''; ?>>Active</option>
                            <option value="2"<?php echo ($row['Status'] == 'OFF') ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                    </div>
                </div>
                <!-- Fin Parte 4 --> 

                <!-- Parte 5: Costo, Reorden y Estado -->
                <div class="form-group row">
                    <label for="inputUserName" class="col-sm-1 col-form-label"><span class="S-required">* </span>Username:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="userName" id="inputUserName" value="<?php echo $row['username']; ?>" required autofocus required autocomplete="off">
                        <div id="usernameError" class="invalid-feedback"></div>
                    </div> 
                    <label for="inputRole" class="col-sm-1 col-form-label"><span class="S-required">* </span>Select Role:</label>
                    <div class="col-sm-3">
                    <select class="form-control" name="role_id" id="inputRole" required>
                            <option value="">Select Role</option>
                            <?php
                                

                                $user_id = $_GET['user_id']; // Or however you get the current user_id

                                // Fetch the current user's role from the user_roles table
                                $current_role_sql = "SELECT role_id FROM user_roles WHERE user_id = '$user_id'";
                                $current_role_result = mysqli_query($conn, $current_role_sql);
                                $current_role_id = null;

                                if (mysqli_num_rows($current_role_result) > 0) {
                                    $current_role_row = mysqli_fetch_assoc($current_role_result);
                                    $current_role_id = $current_role_row['role_id'];
                                }

                                // Fetch roles from the roles table
                                $roles_sql = "SELECT * FROM roles";
                                $roles_result = mysqli_query($conn, $roles_sql);

                                // Check if there are roles
                                if (mysqli_num_rows($roles_result) > 0) {
                                    // Display each role as an option in the select dropdown
                                    while($row = mysqli_fetch_assoc($roles_result)) {
                                        $selected = ($row['role_id'] == $current_role_id) ? "selected" : "";
                                        echo "<option value='" . $row['role_id'] . "' $selected>" . $row['role_name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No roles found</option>";
                                }
                            ?>              
                        </select>
                    </div>  
                </div>
                <!-- Fin Parte 5 -->  
                <div class="form-group row">
                <label for="inputWarehouse" class="col-sm-1 col-form-label"><span class="S-required">* </span>Select Warehouse:</label>
                    <div class="col-sm-3">
                    <select class="form-control" name="warehouse_id" id="inputWarehouse" required>
                            <option value="">Select Warehouse</option>
                            <?php
                                $user_id = $_GET['user_id']; //  get the current user_id

                                // Fetch the current user's warehouse 
                                $current_warehouse_sql = "SELECT Warehouse_ID FROM Warehouse_by_user WHERE user_id = '$user_id'";
                                $current_warehouse_result = mysqli_query($conn, $current_warehouse_sql);
                                $current_warehouse_id = null;

                                if (mysqli_num_rows($current_warehouse_result) > 0) {
                                    $current_warehouse_row = mysqli_fetch_assoc($current_warehouse_result);
                                    $current_warehouse_id = $current_warehouse_row['Warehouse_ID'];
                                }

                                // Fetch earehoses from the warehouses table
                                $warehouse_sql = "SELECT * FROM Warehouses";
                                $warehouse_result = mysqli_query($conn, $warehouse_sql);

                                // Check if there are warehouses
                                if (mysqli_num_rows($warehouse_result) > 0) {
                                    // Display each warehouse as an option in the select dropdown
                                    while($row = mysqli_fetch_assoc($warehouse_result)) {
                                        $selected = ($row['Warehouse_ID'] == $current_warehouse_id) ? "selected" : "";
                                        echo "<option value='" . $row['Warehouse_ID'] . "' $selected>" . $row['Warehouse_Name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No warehouses found</option>";
                                }

                                // Close the database connection
                                mysqli_close($conn);
                            ?>              
                        </select>
                    </div>               
                </div>
                
                 <!-- Part 7 -->
                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="submit" name="updateUser" class="btn btn-primary" >Update</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='../user.php'">Cancel</button>

                    </div>
                </div>
                <!-- Fin Parte 6 -->  
            </form>
        </div>
    </div>
</div>
       
</body>
</html>
