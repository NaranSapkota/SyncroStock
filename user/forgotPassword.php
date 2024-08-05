<?php
session_start();
ob_start(); // Start output buffering at the very beginning

include("dbConnection.php");

require '../PHPMailer/PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer/PHPMailer.php';
require '../PHPMailer/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = connect();

// Ensure no output before this point
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Prepare and execute the query to check if the user exists
    $sql = "SELECT * FROM user WHERE username = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists
        $verification_code = rand(100000, 999999); // Generate a 6-digit verification code
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        // Send the verification code to the user's email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.mi.com.co'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'info@itilnet.net.co'; // SMTP username
            $mail->Password = 'Colombia.2024'; // SMTP password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('info@itilnet.net.co', 'SyncroStock');
            $mail->addAddress($email, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body = "Your verification code is: $verification_code";

            $mail->send();
            $_SESSION['message'] = "A verification code has been sent to your email.";

            // Clear output buffer before redirect
            ob_end_clean();

            // Redirect to verifyCode.php
            header('Location: verifyCode.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['message_invalid'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // User not found
        $_SESSION['message_invalid'] = "No user found with the provided username and email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncroStock Forgot Password</title>
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
    </style>
</head>
<body>
    <div class="right-section" id="form">
        <h2>Forgot Password</h2>

        <form name="form" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label for="pass">Email</label>
                <input type="email" id="pass" name="email" placeholder="Enter your email" autocomplete="off" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Submit">
            </div>
            <div class="form-group">
                <input type="submit" name="submit" onclick="window.location.href='../login.php'" value="Go Back">
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
     document.addEventListener('DOMContentLoaded', function() {
        var alertElement = document.getElementById('autoDismissAlert');
        if (alertElement) {
            setTimeout(function() {
                $(alertElement).alert('close');
            }, 4000); // 4000 milliseconds = 4 seconds
        }
    });
</script>
</body>
</html>
