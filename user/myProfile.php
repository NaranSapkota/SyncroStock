<?php
session_start();
include("dbConnection.php");
$conn = connect();

//$Systemalert= $_SESSION['message'];

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Fetch user details using the session user ID
$userID = $_SESSION['UserID'];

$sql = "SELECT 
            u.user_id AS UserID,
            u.FirstName AS FirstName,
            u.Lastname AS LastName,
            u.Useractivacion As Activation,
            u.email AS email,
            u.Cellphone AS phone,
            u.Address AS address,
            r.role_name AS RoleName,
            w.Warehouse_Name AS WarehouseName,
            w.Address AS waddress,
            w.Phone AS wphone,
            u.username AS Username
        FROM 
            user u
        JOIN 
            user_roles ur ON u.user_id = ur.user_id
        JOIN 
            roles r ON ur.role_id = r.role_id
         JOIN 
            Warehouse_by_user uw ON u.user_id = uw.user_id
        JOIN 
            Warehouses w ON uw.Warehouse_ID = w.Warehouse_ID
        WHERE 
            u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID); // Bind parameter securely
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Error in query: ' . $conn->error);
}

$userDetails = $result->fetch_assoc();

$stmt->close();
// Handle password change
//$passwordChangeMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['currentPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if new password and confirm password match
   // if ($newPassword !== $confirmPassword) {
       // $passwordChangeMessage = "New password and confirm password do2 not match.";
   // } else {
        // Retrieve the current hashed password from the database
        $sql = "SELECT password FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if ($stmt->num_rows === 1) {
            // Verify the current password
            if (password_verify($currentPassword, $hashedPassword)) {
                // Hash the new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update the password in the database
                $updateSql = "UPDATE user SET password = ? WHERE user_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("si", $newHashedPassword, $userID);
                if ($updateStmt->execute()) {
                    $_SESSION['message'] = "Password successfully changed.";
                } else {
                    $_SESSION['message_invalid'] = "Error updating password: " . $conn->error;
                }
                $updateStmt->close();
            } else {
                $_SESSION['message_invalid'] = "Current Password is incorrect.";
            }
        } else {
            $_SESSION['message_invalid'] = "User not found.";
        }

        $stmt->close();
    }
//}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Profile</title>

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

        .card-header {
            background-color: #2596be; /* Blue header for cards */
            color: #fff;
            padding: 12px 20px; /* Larger padding */
            border-radius: 8px 8px 0 0; /* Rounded top corners */
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-body {
            background-color: #fff; /* White background for card bodies */
            padding: 20px;
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
        hr {
    -moz-box-sizing: border-box;
    -moz-float-edge: margin-box;
    border: 1px inset;
    color: gray;
    display: block;
    height: 1px;
    margin: 0.1em auto;
}
    </style>
</head>

<body>
    <div class="S-container">
        <div class="S-iframe-container">
            <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
        </div>

        <div class="S-main-content">
            <div>
                <div class="S-breadcrumbs">
                    <a href="../home.php">Home </a><span>/</span>
                    <a href="../user.php">Control Panel </a><span>/</span>
                    <span>View My Profile</span>
                </div>

                <h2>My Profile</h2>
                <!--message alert -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                <?php endif; ?>

                <!-- message_invalid alert-->
                <?php if (isset($_SESSION['message_invalid'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['message_invalid']; unset($_SESSION['message_invalid']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!--user information -->
                    <div class="col-md-6">
                        <div class="card border-secondary mb-3">
                            <div class="card-header">User Information </div>
                                <div class="card-body text-secondary">
                                    <h5 class="card-title">Hello <?php echo htmlspecialchars($userDetails['FirstName']); ?>!</h5>
                                    <p class="card-text">You can view the user information details here. If you need to modify any user details, please contact the administrator.</p>
                                    <ul class="list-group list-group-flush">
                                        <?php if ($userDetails) : ?>
                                            <li class="list-group-item"><strong>First Name:</strong> <?php echo htmlspecialchars($userDetails['FirstName']); ?></li><hr>
                                            <li class="list-group-item"><strong>Last Name:</strong> <?php echo htmlspecialchars($userDetails['LastName']); ?></li><hr>
                                            <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($userDetails['Username']); ?></li><hr>
                                            <li class="list-group-item"><strong>Role:</strong> <?php echo htmlspecialchars($userDetails['RoleName']); ?></li><hr>
                                            <li class="list-group-item"><strong>User Activation Date:</strong> <?php echo htmlspecialchars($userDetails['Activation']); ?></li><hr>
                                            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($userDetails['email']); ?></li><hr>
                                            <li class="list-group-item"><strong>Phone Number:</strong> <?php echo htmlspecialchars($userDetails['phone']); ?></li><hr>
                                            <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($userDetails['address']); ?></li><hr>
                                        <?php else : ?>
                                            <p>User details not found.</p>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                        </div>
                    </div>

                    <!-- warehouse information and change password -->
                    <div class="col-md-6">
                        <!-- warehouse information -->
                        <div class="card border-secondary mb-3">
                            <div class="card-header">Warehouse Information</div>
                            <div class="card-body text-secondary">
                                <ul class="list-group list-group-flush">
                                    <?php if ($userDetails) : ?>
                                    <p class="card-text">You can view the warehouse information details here. If you need to modify any warehouse details, please contact the administrator.</p>
                                    <li class="list-group-item"><strong>Warehouse Name:</strong> <?php echo htmlspecialchars($userDetails['WarehouseName']); ?></li><hr>
                                        <li class="list-group-item"><strong>Warehouse Phone Number:</strong> <?php echo htmlspecialchars($userDetails['wphone']); ?></li><hr>
                                        <li class="list-group-item"><strong>Warehouse Address:</strong> <?php echo htmlspecialchars($userDetails['waddress']); ?></li><hr>
                                        <?php else : ?>
                                        <p>Warehouse details not found.</p>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <!--  change password -->
                        <div class="card border-secondary mb-3">
                            <div class="card-header">Change Password</div>
                            <div class="card-body text-secondary">
                                <ul class="list-group list-group-flush">
                                   <button id="changePasswordBtn" class="btn btn-warning" type="button" onclick="showChangePasswordForm()"> Change Password </button>

                                    <!-- Change Password Form -->
                                    <div class="mt-3" id="changePasswordForm" style="display: none;">
                                        <form  method="POST" onsubmit="return validatePasswords()">
                                            <div class="form-row">
                                                <!-- Current Password -->
                                                <div class="form-group col-md-6">
                                                    <label for="currentPassword">Current Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter your current password" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="toggleCurrentPassword" style="cursor: pointer;">
                                                                <i class="far fa-eye"></i>
                                                            </span>
                                                         </div>
                                                    </div>
                                                </div>
                                                <!-- New Password -->
                                                <div class="form-group col-md-6">
                                                    <label for="newPassword">New Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter your new password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required oninput="validatePasswordMatch()">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="toggleNewPassword" style="cursor: pointer;">
                                                                <i class="far fa-eye"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Confirm Password -->
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="confirmPassword">Confirm New Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Enter your confirm new password"required oninput="validatePasswordMatch()">
                                                        <div id="passwordMatchMessage" class="invalid-feedback">Passwords do not match.</div>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="toggleConfirmNewPassword" style="cursor: pointer;">
                                                                <i class="far fa-eye"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                            <button type="button" class="btn btn-secondary" onclick="cancelChangePassword()">Cancel</button>
                                        </form>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>
<script>
    function showChangePasswordForm() {
        // Hide the button
        document.getElementById('changePasswordBtn').style.display = 'none';
        // Show the form
        document.getElementById('changePasswordForm').style.display = 'block';
    }

    function cancelChangePassword() {
        // Show the button
        document.getElementById('changePasswordBtn').style.display = 'block';
        // Hide the form
        document.getElementById('changePasswordForm').style.display = 'none';
    }
        /*function validatePasswordMatch() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const passwordMatchMessage = document.getElementById('passwordMatchMessage');

            if (newPassword === confirmPassword) {
                confirmPassword.setCustomValidity('');
                passwordMatchMessage.style.display = 'none';
            } else {
                confirmPassword.setCustomValidity('Passwords do0 not match');
                passwordMatchMessage.style.display = 'block';
            }
        }*/

    //toggle for current password
    document.getElementById('toggleCurrentPassword').addEventListener('click', function () {
        const passwordField = document.getElementById('currentPassword');
        const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', passwordFieldType);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    //toggle for new password
    document.getElementById('toggleNewPassword').addEventListener('click', function () {
        const passwordField = document.getElementById('newPassword');
        const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', passwordFieldType);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    //toggle for confirm new password
    document.getElementById('toggleConfirmNewPassword').addEventListener('click', function () {
        const passwordField = document.getElementById('confirmPassword');
        const passwordFieldType = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', passwordFieldType);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    function validatePasswordMatch() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const passwordMatchMessage = document.getElementById('passwordMatchMessage');

    if (newPassword === confirmPassword) {
        confirmPassword.setCustomValidity('');
        passwordMatchMessage.style.display = 'none';
    } else {
        confirmPassword.setCustomValidity('Passwords do not match');
        passwordMatchMessage.style.display = 'block';
    }
    }

    function validatePasswords() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            alert('New Password and Confirm New Password do not match.');
            return false;
        }
        return true;
    }


    // function validatePasswords() {
    //     const newPassword = document.getElementById('newPassword').value;
    //     const confirmPassword = document.getElementById('confirmPassword').value;
        
    //     if (newPassword !== confirmPassword) {
    //         $_SESSION['message_invalid'] ="New password and Confirm new password do not match";
    //         return false;
    //     }
    //     return true;
    // }
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($passwordChangeMessage)) : ?>
            alert('<?php echo addslashes($passwordChangeMessage); ?>');
        <?php endif; ?>
    });
</script>

</body>

</html>