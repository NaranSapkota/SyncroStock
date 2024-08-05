<?php
// fetch_manage_company.php
require_once "main/databasehandler.php";

try {
    $query = "SELECT company_id, company_name, address, city, state, postal_code, country, phone, email FROM companies;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $company = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($company);
} catch (PDOException $e) {
    // Handle database error - for simplicity, return an empty array in case of error
    echo json_encode([]);
}
?>
