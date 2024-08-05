<?php
session_start();
$Systemalert = $_SESSION['message'];


if (isset($_POST['submit'])) {
    include('dbConnection.php');
    $conn = connect();

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber =  $_POST['contactNumber'];
    $status = $_POST['status'];
    $activationDate = $_POST['activationDate'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $userName = $_POST['userName'];
    $password = $_POST['psw']; //plain text password

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $role_id = $_POST['role_id'];

    // Map status value to ENUM values
    $status = ($status == '1') ? 'ON' : 'OFF';

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Check if email or username already exists
        $checkSql = "SELECT * FROM user WHERE email = '$email' OR username = '$userName'";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            // If exists, throw exception with custom message
            throw new Exception("Email or Username already exists. Please enter another.");
        }

        // Insert the new user into the user table
        $sql1 = "INSERT INTO user (FirstName, LastName, Cellphone, Status, Useractivacion, Address, email, username, password) 
                VALUES ('$firstName', '$lastName', '$contactNumber', '$status', '$activationDate', '$address', '$email', '$userName', '$hashed_password')";

        if (!mysqli_query($conn, $sql1)) {
            throw new Exception("Error inserting user: " . mysqli_error($conn));
        }

        // Get the last inserted user_id
        $user_id = mysqli_insert_id($conn);

        // Insert the user-role relationship into the user_roles table
        $sql2 = "INSERT INTO user_roles (user_id, role_id) VALUES ($user_id, $role_id)";

        if (!mysqli_query($conn, $sql2)) {
            throw new Exception("Error inserting user role: " . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect to user page with success message
        //echo '<script>window.location.href = "../user.php?create_msg=You have created a new user successfully!";</script>';
        $_SESSION['message'] = "You have updated the user data successfully!";
            echo '<script>window.location.href = "../user.php";</script>';
        exit();

    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        mysqli_rollback($conn);
        
        // Redirect to create user page with error message
        //$errorMessage = urlencode($e->getMessage());
        //echo '<script>window.location.href = "createUser.php?error_msg=' . $errorMessage . '";</script>';
        //exit();

	$_SESSION['message'] = "Email or Username already exists. Please choose another.";
        echo '<script>window.location.href = "./createUser.php";</script>';
        exit();

    }

    // Close the connection
    mysqli_close($conn);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNboafLQXz6eKFoA9scyQBOL9mN14jA6f1jf6f2by5T5zA+CZpQ/XK/pO8X8nH" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-CKZRZpKjt6n98sNc3uO8jWwF8PrOZs4noIskZhOH93KwF2yC8+lxzHb3x3xr1oZP" crossorigin="anonymous"></script>

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

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
            margin-top: 5px;
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

	 <?php // displaying the alert
                if (isset($_SESSION['message'])): 
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $Systemalert; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php 
                unset($_SESSION['message']); // Unset the session variable after displaying the alert
                endif; 
            ?>
	   

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
                        <div id="emailError" class="invalid-feedback"></div>
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
                <!-- end Part 3 -->

                <!-- Part 4:  -->
                <div class="form-group row">
                    <label for="inputUserActivation4" class="col-sm-1 col-form-label">Activation Date:</label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="activationDate" id="inputUserActivation4" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <label for="inputGroupSelect01" class="col-sm-1 col-form-label">Status:</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="inputGroupSelect01" name="status">
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                        </select>
                    </div>
                    <!--<label for="inputUserDeactivation4" class="col-sm-1 col-form-label">Deactivation Date:</label>
                    <div class="col-sm-3">
                        <input type="date" class="form-control" name="deactivationDate" id="inputUserDeactivation4">
                    </div>-->
                </div>
                <!-- end Part 4 --> 

                <!-- Part 5 -->
                <div class="form-group row">
                    <label for="inputUserName" class="col-sm-1 col-form-label"><span class="S-required">* </span>Username:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="userName" id="inputUserName" placeholder="johnd"required autofocus required autocomplete="off">
                        <div id="usernameError" class="invalid-feedback"></div>
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
                        <div class="input-group">
                            <input type="password" class="form-control" name="psw" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required autofocus required autocomplete="off">
                                <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="far fa-eye"></i>
                                </span>
                        </div>
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


    <script>       


$(document).ready(function() {
        // Close alert after 3 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });

 
 <!--Alert input email already exits -->
	
	$(document).ready(function() {
            $('#inputEmail').on('blur', function() {
                var email = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'checkEmail.php',
                    data: { email: email },
                    success: function(response) {
                        if (response === 'exists') {
                            displayError('The entered email already exists. Please enter another email.');
                            $('#inputEmail').val(''); // Clear the input field
                        } else {
                        clearError();
                        }
                    }
                });
            });
        

            function displayError(message) {
            // Display error message
            $('#emailError').text(message).show();

            // Add error class to input field and label
            $('#inputEmail').addClass('is-invalid');
            $('label[for="inputEmail"]').addClass('text-danger');
            }

            function clearError() {
            // Clear error message
            $('#emailError').text('').hide();

            // Remove error class from input field and label
            $('#inputEmail').removeClass('is-invalid');
            $('label[for="inputEmail"]').removeClass('text-danger');
            }
        });
    
      $(document).ready(function() {
            $('#inputUserName').on('blur', function() {
                var username = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'checkUserName.php',
                    data: { username: username },
                    success: function(response) {
                        if (response === 'exists') {
                            displayError('The entered username already exists. Please enter another username.');
                            $('#inputUserName').val(''); // Clear the input field
                        } else {
                            clearError();
                        }                  
                    }
                });
            });
            function displayError(message) {
            // Display error message
            $('#usernameError').text(message).show();

            // Add error class to input field and label
            $('#inputUserName').addClass('is-invalid');
            $('label[for="inputUserName"]').addClass('text-danger');
            }

            function clearError() {
            // Clear error message
            $('#usernameError').text('').hide();

            // Remove error class from input field and label
            $('#inputUserName').removeClass('is-invalid');
            $('label[for="inputUserName"]').removeClass('text-danger');
            }
      });       



        //toggle for  password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('psw');
            const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', passwordFieldType);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        //validate password
        var myInput = document.getElementById("psw");
        var letter = document.getElementById("letter");
        var capital = document.getElementById("capital");
        var number = document.getElementById("number");
        var length = document.getElementById("length");

        // When the user clicks on the password field, show the message box
        myInput.onfocus = function() {
            document.getElementById("message").style.display = "block";
        }

        // When the user clicks outside of the password field, hide the message box
        myInput.onblur = function() {
            document.getElementById("message").style.display = "none";
        }

        // When the user starts to type something inside the password field
        myInput.onkeyup = function() {
            // Validate lowercase letters
            var lowerCaseLetters = /[a-z]/g;
                if(myInput.value.match(lowerCaseLetters)) {  
                    letter.classList.remove("invalid");
                    letter.classList.add("valid");
                } else {
                    letter.classList.remove("valid");
                    letter.classList.add("invalid");
                }
    
            // Validate capital letters
            var upperCaseLetters = /[A-Z]/g;
                if(myInput.value.match(upperCaseLetters)) {  
                    capital.classList.remove("invalid");
                    capital.classList.add("valid");
                } else {
                    capital.classList.remove("valid");
                    capital.classList.add("invalid");
                }

            // Validate numbers
            var numbers = /[0-9]/g;
                if(myInput.value.match(numbers)) {  
                    number.classList.remove("invalid");
                    number.classList.add("valid");
                } else {
                    number.classList.remove("valid");
                    number.classList.add("invalid");
                }
    
            // Validate length
            if(myInput.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }
        }
       
    </script>

</body>
</html>
