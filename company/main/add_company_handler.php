<?php

require_once("databasehandler.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $country = $_POST['country'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $logo = $_FILES['logo'] ?? null;

    // Validate form data
    if (empty($company_name) || empty($address) || empty($city) || empty($state) ||
        empty($postal_code) || empty($country) || empty($phone) || empty($email)) {
        header("Location: ../addcompany.php?error=emptyfields");
        exit();
    }

    // Default image path
    $default_image_path = '../../images/Company.png'; // Adjust the path as necessary

    // Handle file upload
    $upload_ok = 1;
    $logo_data = null;

    if ($logo && $logo['error'] == UPLOAD_ERR_OK) {
        $image_file_type = strtolower(pathinfo($logo["name"], PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($logo["tmp_name"]);
        if ($check !== false) {
            $upload_ok = 1;
        } else {
            echo "File is not an image.";
            $upload_ok = 0;
        }

        // Check file size (5MB limit)
        if ($logo["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $upload_ok = 0;
        }

        // Allow certain file formats
        if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $upload_ok = 0;
        }

        if ($upload_ok == 0) {
            echo "Sorry, your file was not uploaded.";
            exit();
        }

        // Read the image file into a variable
        $logo_data = file_get_contents($logo["tmp_name"]);
    } else {
        // Use default image if no file is uploaded
        $logo_data = file_get_contents($default_image_path);
    }

    try {
        $sql = "INSERT INTO companies (company_name, address, city, state, postal_code, country, phone, email, logo) 
                VALUES (:company_name, :address, :city, :state, :postal_code, :country, :phone, :email, :logo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':company_name' => $company_name,
            ':address' => $address,
            ':city' => $city,
            ':state' => $state,
            ':postal_code' => $postal_code,
            ':country' => $country,
            ':phone' => $phone,
            ':email' => $email,
            ':logo' => $logo_data
        ]);
        header("Location: ../addcompany.php?success=companyadded");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
