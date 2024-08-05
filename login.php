<?php

session_start();
//$_SESSION['message']='Hola';
$Systemalert = $_SESSION['message'];


if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncroStock Login</title>
    <link rel="icon" href="./images/company/syncrostock.ico" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
            background-image: url('images/1.png');
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
            height: 380px;
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
            color: #red; /* Note: this might need correction, should it be red? */
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
    </style>
</head>
<body>


    <div class="right-section" id="form">
        <h2>Login</h2>

        <form name="form" action="./apicall/loginapi.php" onsubmit="return isValid()" method="POST">
            <div class="form-group">
                <label for="user">Username</label>
                <input type="text" id="user" name="user" placeholder="Enter your username" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="pass">Password</label>
                <input type="password" id="pass" name="pass" placeholder="Enter your password" utocomplete="off" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Login">
            </div>

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

            <?php 
                if (isset($_SESSION['message_success'])): //alert after restting the password
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message_success']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php 
                unset($_SESSION['message_success']); // Unset the session variable after displaying the alert
                endif; 
            ?>

            <a href="./user/forgotPassword.php" class="forgot-password">Forgot password?</a>
        </form>
    </div>


<script>
    function isValid() {
        var user = document.getElementById("user").value;
        var pass = document.getElementById("pass").value;
        if (user.length === 0 || pass.length === 0) {
            alert("Please fill in both username and password fields.");
            return false;
        }
        return true;
    }

    $(document).ready(function() {
        // Close alert after 3 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });
</script>

</body>
</html>