<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure no output is sent before this point
ob_start();

include("dbConnection.php"); // Adjust path if necessary

$conn = connect();
// Check if the user is verified
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    $_SESSION['message_invalid'] = "Unauthorized access.";
    header('Location: forgotPassword.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($new_password == $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $sql = "UPDATE user SET password = ? WHERE username = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("sss", $hashed_password, $_SESSION['username'], $_SESSION['email']);
        if ($stmt->execute()) {
            $_SESSION['message_success'] = "Your password has been successfully reset. You can now login with your new password.";

            // Clear session variables
            session_unset();
            session_destroy();

            // Clear output buffer before redirect
            ob_end_clean();

            // Redirect to login page
            header('Location: ../login.php');
            exit;
        } else {
            $_SESSION['message_invalid'] = "There was an error resetting your password. Please try again.";
        }

        $stmt->close();
    } else {
        // Passwords do not match
        $_SESSION['message_invalid'] = "Passwords do not match. Please try again.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncroStock Reset Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNboafLQXz6eKFoA9scyQBOL9mN14jA6f1jf6f2by5T5zA+CZpQ/XK/pO8X8nH" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-CKZRZpKjt6n98sNc3uO8jWwF8PrOZs4noIskZhOH93KwF2yC8+lxzHb3x3xr1oZP" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100vh;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url('../images/1.png');
            background-position: center center;
        }

        .right-section {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 80%;
            width: 400px;
            height: 480px;
            margin-right: 45px;
        }

        .right-section h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #274c70;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #1a3a4e;
        }

        .forgot-password {
            display: block;
            color: #666;
            text-decoration: none;
            text-align: right;
        }

        .forgot-password:hover {
            color: #333;
        }

        @media screen and (min-width: 768px) {
            .right-section {
                max-width: 450px;
            }
        }

        #S-main-content {
            margin-left: 270px;
            padding: 30px;
            color: red; /* Corrected */
            max-width: 1500px;
            margin: 0 auto;
            transition: none;
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
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
    <div class="right-section" id="form">
        <h2>Reset Password</h2>

        <form name="form" method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="input-group">
                    <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" required>
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
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" class="form-control" autocomplete="off" required>
                    <div class="input-group-append">
                            <span class="input-group-text" id="toggleCPassword" style="cursor: pointer;">
                                <i class="far fa-eye"></i>
                            </span>
                    </div>
                </div>    
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Submit">
            </div>

            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['message_invalid'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoDismissAlert">
                <?php echo $_SESSION['message_invalid']; unset($_SESSION['message_invalid']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
<script>
    //aler dissappear
     document.addEventListener('DOMContentLoaded', function() {
        var alertElement = document.getElementById('autoDismissAlert');
        if (alertElement) {
            setTimeout(function() {
                $(alertElement).alert('close');
            }, 4000); // 4000 milliseconds = 4 seconds
        }
    });

    //toggle for  password
    document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('new_password');
            const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', passwordFieldType);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleCPassword').addEventListener('click', function () {
            const passwordField = document.getElementById('confirm_password');
            const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', passwordFieldType);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        //validate password
        var myInput = document.getElementById("new_password");
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
