<?php

session_start();
include("../connection.php");

if (isset($_POST['submit'])) {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Query to fetch user from the database (User, roles, Warehouses, Suppliers)
    $username = $conn->real_escape_string($username);

    $sql = "SELECT 
                u.user_id AS UserID,
                u.FirstName AS FirstName,
                u.Lastname AS LastName,
                r.role_name AS RoleName,
                r.role_id AS Role,
                CONCAT(u.Lastname, ', ', u.FirstName) AS FullName,
                w.Warehouse_Name AS WarehouseName,
                w.Warehouse_ID AS WarehouseID,
                s.Company_Name AS SupplierCompany,
                u.password AS PasswordHash,
                u.Status AS Status
            FROM 
                user u
            JOIN 
                user_roles ur ON u.user_id = ur.user_id
            JOIN 
                roles r ON ur.role_id = r.role_id
            LEFT JOIN 
                Warehouse_by_user Whuser ON u.user_id = Whuser.user_id
            LEFT JOIN 
                Warehouses w ON Whuser.warehouse_id = w.Warehouse_ID
            LEFT JOIN 
                Suppliers s ON u.user_id = s.Supplier_ID
            WHERE 
                u.username = '$username'";

    $result = $conn->query($sql);

    if ($result === false) {
        die('Error en la consulta: ' . $conn->error);
    }

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $hashedPassword = $row['PasswordHash'];
        $status = $row['Status'];

        if ($status === 'OFF') {
            $_SESSION['message'] = "Login failed. User account is deactivated!";
            echo '<script>window.location.href = "../login.php";</script>';
            exit();
        }
          
        if (password_verify($password, $hashedPassword)) {
            // Almacenar datos en variables de sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['UserID'] = $row['UserID'];
            $_SESSION['FirstName'] = $row['FirstName'];
            $_SESSION['LastName'] = $row['LastName'];
            $_SESSION['Role'] = $row['Role'];
            $_SESSION['FullName'] = $row['FullName'];
            $_SESSION['WarehouseName'] = $row['WarehouseName'];
            $_SESSION['WarehouseID'] = $row['WarehouseID'];
            $_SESSION['SupplierCompany'] = $row['SupplierCompany'];
            $_SESSION['username'] = $username;

            // Detectar si el usuario está en un dispositivo móvil
            $isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT']);

            if ($isMobile) {
                // Redireccionar a alert.php si es un móvil
                //echo '<script>window.location.href = "../alerts_Mobile.php";</script>';
		echo '<script>window.location.href = "../home.php";</script>';
            } else {
                // Redireccionar a home.php si no es un móvil
                echo '<script>window.location.href = "../home.php";</script>';
            }
            exit();
        } else {
            $_SESSION['message'] = "Login failed. Invalid Password!";
            echo '<script>window.location.href = "../login.php";</script>';
            exit();
        }
    } else {
        $_SESSION['message'] = "Login failed. Invalid Username or Password!";
        echo '<script>window.location.href = "../login.php";</script>';
        exit();
    }

    $conn->close();
}
?>
