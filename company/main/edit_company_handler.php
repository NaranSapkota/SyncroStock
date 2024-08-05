<?php
// edit_company_handler.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $companyId = $_POST["company_id"];
    $companyname = $_POST["companyname"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $postalcode = $_POST["postal_code"];
    $country = $_POST["country"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    if (empty($companyname) || empty($address) || empty($city) || empty($state) || empty($postalcode) || empty($country) || empty($phone) || empty($email)) {
        header("Location: ../editcompany.php?id=$companyId&error=emptyfields");
        exit();
    }

    // Check if a file is uploaded
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Read the file content
        $fileContent = file_get_contents($fileTmpPath);
    } else {
        // If no file is uploaded, keep the existing logo
        $fileContent = null;
    }

    // Update company in database
    require_once "databasehandler.php";

    try {
        if ($fileContent !== null) {
            $query = "UPDATE companies 
                      SET company_name = :companyname, address = :address, city = :city, state = :state, postal_code = :postalcode, country = :country, phone = :phone, email = :email, logo = :logo
                      WHERE company_id = :companyId";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':logo', $fileContent, PDO::PARAM_LOB);
        } else {
            $query = "UPDATE companies 
                      SET company_name = :companyname, address = :address, city = :city, state = :state, postal_code = :postalcode, country = :country, phone = :phone, email = :email
                      WHERE company_id = :companyId";
            $stmt = $pdo->prepare($query);
        }

        $stmt->bindParam(':companyname', $companyname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':postalcode', $postalcode);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':companyId', $companyId);
        $stmt->execute();

        header("Location: ../managecompany.php?success=companyupdated");
        exit();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../managecompany.php");
    exit();
}
?>
