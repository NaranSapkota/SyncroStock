<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Items</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- Optional JavaScript -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>-->
    
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
    <!--Alert input email already exits -->
    <script>
    $(document).ready(function() {
        $('#inputEmail').on('blur', function() {
            var email = $(this).val();
            $.ajax({
                type: 'POST',
                url: 'checkEmail.php',
                data: { email: email },
                success: function(response) {
                    if (response === 'exists') {
                        alert('The entered email already exists. Please enter another email.');
                        $('#inputEmail').val(''); // Clear the input field
                    }
                }
            });
        });
      });
    </script>
</head>

<body>

<div class="S-container">
    <!-- Contenedor del iframe -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <!-- Área principal de contenido -->
    <div class="S-main-content">
        <div>
            <!-- Contenedor de migas de pan -->
            <div class="S-breadcrumbs">
                <a href="../home.php">Home </a><span>/</span>
                <a href="../user.php">Control Panel </a><span>/</span>
                <span>Create New User</span>
            </div>
            
            <h2>Create New User</h2>

            <!-- Controles del formulario -->
            <form method="post"> 

                <!-- Start Part 1 -->
                <div class="form-group row py-2">
                    <label for="inputfName4" class="col-sm-1 col-form-label"><span class="S-required">* </span>First Name:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="firstName" id="inputfName4" placeholder="First Name" required autofocus required autocomplete="off">
                    </div>
                    <label for="inputlName4" class="col-sm-1 col-form-label"><span class="S-required">* </span>Last Name:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="lastName" id="inputlName4" placeholder="Last Name" required autofocus required autocomplete="off">
                    </div>                    
                </div>
                <!-- End Part 1-->   

                <!-- Parte 2:-->
                <div class="form-group row py-2">
                    <label for="inputContactNumber" class="col-sm-1 col-form-label"><span class="S-required">* </span>Contact Number:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="contactNumber" id="inputContactNumber"  pattern="^[\+]?[0-9]{0,3}\W?+[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$" title="Enter a valid phone number" required autofocus required autocomplete="off">
                    </div>
                    <label for="inputEmail" class="col-sm-1 col-form-label"><span class="S-required">* </span>Email:</label>
                    <div class="col-sm-3">
                        <input type="email" class="form-control"  name="email" id="inputEmail" placeholder="john@gmail.com"required autofocus required autocomplete="off">
                    </div>                    
                </div>
                <!-- Fin Parte 2 --> 

                <!-- Parte 3: Espacio -->
                <div class="form-group row py-2">
                    <label for="inputAddress" class="col-sm-1 col-form-label"><span class="S-required">* </span>Address:</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="address" id="inputAddress" placeholder="1234 Main St" required autofocus required autocomplete="off">
                    </div>                   
                </div>
                <!-- Fin Parte 3 -->

                <!-- Parte 4:  -->
                <div class="form-group row">
                    <label for="inputUserActivation4" class="col-sm-1 col-form-label">Activation Date:</label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="activationDate" id="inputUserActivation4" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <label for="inputUserDeactivation4" class="col-sm-1 col-form-label">Deactivation Date:</label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="deactivationDate" id="inputUserDeactivation4">
                    </div>
                </div>
                <!-- Fin Parte 4 --> 

                <!-- Parte 5: Costo, Reorden y Estado -->
                <div class="form-group row">
                    <label for="inputGroupSelect01" class="col-sm-1 col-form-label">Status:</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="inputGroupSelect01" name="status">
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                        </select>
                    </div>
                    <label for="inputUserName" class="col-sm-1 col-form-label"><span class="S-required">* </span>Username:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="userName" id="inputUserName" placeholder="johnd"required autofocus required autocomplete="off">
                    </div> 
                    <label for="inputRole" class="col-sm-1 col-form-label"><span class="S-required">* </span>Select Role:</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="role_id" id="inputRole" required>
                            <option value="">Select Role</option>
                            <?php
                                // Include database connection
                                include('dbConnection.php');
                                $conn = connect();

                                // Fetch roles from database
                                $sql = "SELECT * FROM roles";
                                $result = mysqli_query($conn, $sql);

                                // Check if there are roles
                                if (mysqli_num_rows($result) > 0) {
                                    // Display each role as an option in the select dropdown
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row['role_id'] . "'>" . $row['role_name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No roles found</option>";
                                }

                                // Close the database connection
                                mysqli_close($conn);
                            ?>                
                        </select>
                    </div>  
                </div>
                <!-- Fin Parte 5 -->  

                <!-- Parte 6 -->
                <div class="form-group row">
                <label for="psw" class="col-sm-1 col-form-label"><span class="S-required">* </span>Password:</label>
                    <div class="col-sm-3">
                        <input type="password" class="form-control" name="psw" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required autofocus required autocomplete="off">
                    </div>
                    <div id="message">
                        <p>Password must contain the following:</p>
                            <ul>
                                <li id="letter" class="invalid">A <b>lowercase</b> letter</li>
                                <li id="capital" class="invalid">A <b>capital (uppercase)</b> letter</li>
                                <li id="number" class="invalid">A <b>number</b></li>
                                <li id="length" class="invalid">Minimum <b>8 characters</b></li>
                            </ul>
                    </div>  
                </div>
                
                 <!-- Part 7 -->
                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="submit" name="submit" class="btn btn-primary" >Save</button>
                        <button type="reset" class="btn btn-success">Clear</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='../user.php'">Cancel</button>

                    </div>
                </div>
                <!-- Fin Parte 6 -->  
            </form>
        </div>
    </div>
</div>
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="notificationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php
    if (isset($_POST['submit'])) {
    include('dbConnection.php');
    $conn = connect();

    // Set the timezone to a specific Canadian timezone
    // date_default_timezone_set('America/Toronto');
    //$current_date = date('Y-m-d');

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber =  $_POST['contactNumber'];
    $status = $_POST['status'];
    $activationDate = $_POST['activationDate'];
    $deactivationDate = $_POST['deactivationDate'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $userName = $_POST['userName'];
    $password = $_POST['psw']; //plain text password

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $role_id = $_POST['role_id'];

    // Map status value to ENUM values
    if ($status == '1') {
        $status = 'ON';
    } else if ($status == '2') {
        $status = 'OFF';
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert the new user into the user table
        $sql1 = "INSERT INTO user (FirstName, LastName, Cellphone, Status, Useractivacion, Userdeactivacion, Address, email, username, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt1 = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt1, "ssssssssss", $firstName, $lastName, $contactNumber, $status, $activationDate, $deactivationDate, $address, $email, $userName, $hashed_password);

        if (!mysqli_stmt_execute($stmt1)) {
            throw new Exception("Error inserting user: " . mysqli_error($conn));
        }

        // Get the last inserted user_id
        $user_id = mysqli_insert_id($conn);

        // Insert the user-role relationship into the user_roles table
        $sql2 = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";

        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "ii", $user_id, $role_id);

        if (!mysqli_stmt_execute($stmt2)) {
            throw new Exception("Error inserting user role: " . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);
        echo "<script>showNotification('User created successfully!');</script>";

        //header("Location: user.php?status=success");
        //exit();
        
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        mysqli_rollback($conn);
        echo "<script>showNotification('Error creating user: " . $e->getMessage() . "');</script>";
    }

    // Close statements if they were successfully prepared
    if (isset($stmt1)) {
        mysqli_stmt_close($stmt1);
    }
    if (isset($stmt2)) {
        mysqli_stmt_close($stmt2);
    }

    // Close the connection
    mysqli_close($conn);
}
?>

</body>
</html>
