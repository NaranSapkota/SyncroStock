<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $warehousename = $_POST["warehousename"];
    $location = $_POST["location"];
    $city = $_POST["city"];
    $province = $_POST["province"];
    $postalcode = $_POST["postalcode"];
    $country = $_POST["country"];
    $phone = $_POST["phone"];
    $fax = $_POST["fax"];
    $warehousemanager = $_POST["warehousemanager"]; //  username of the selected manager

    // Validate input fields
    if (empty($warehousename) || empty($location) || empty($city) || empty($province) || empty($postalcode) || empty($country)|| empty($phone) || empty($warehousemanager)) {
        header("Location: ../addwarehouse/addwarehouse.php?error=emptyfields");
        exit();
    }

    try {
        require_once "databasehandler.inc.php";

        // Query to fetch user_id based on username
        $query_user = "SELECT user_id FROM user WHERE username = :username";
        $stmt_user = $pdo->prepare($query_user);
        $stmt_user->bindParam(":username", $warehousemanager);
        $stmt_user->execute();
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Handle case where username is not found in user table
            header("Location: ../addwarehouse/addwarehouse.php?error=usernotfound");
            exit();
        }

        $warehousemanager_id = $user['user_id'];

        // Insert into warehouses table
        $query = "INSERT INTO Warehouses(Warehouse_Name, Location, City, Province, Postal_Code, Country, Phone, Fax, wh_user_manager_id) 
                  VALUES (:warehousename, :location, :city, :province, :postalcode, :country, :phone, :fax, :warehousemanager_id);";

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":warehousename", $warehousename);
        $stmt->bindParam(":location", $location);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":province", $province);
        $stmt->bindParam(":postalcode", $postalcode);
        $stmt->bindParam(":country", $country);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":fax", $fax);
        $stmt->bindParam(":warehousemanager_id", $warehousemanager_id);

        $stmt->execute();

        // Redirect after successful insertion
        header("Location: ../addwarehouse/addwarehouse.php?success=warehouseadded");
        exit();

    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../addwarehouse/addwarehouse.php");
    exit();
}

?>
