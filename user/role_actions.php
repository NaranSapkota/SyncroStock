<?php
// Include database connection
include('dbConnection.php');
$conn = connect();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action == 'create') {
        $role_name = $_POST['role_name'];
        $sql = "INSERT INTO roles (role_name) VALUES ('$role_name')";

        if (mysqli_query($conn, $sql)) {
            $role_id = mysqli_insert_id($conn);
            echo json_encode(['id' => $role_id, 'name' => $role_name]);
        } else {
            http_response_code(500);
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($action == 'update') {
        $role_id = $_POST['role_id'];
        $role_name = $_POST['role_name'];
        $sql = "UPDATE roles SET role_name = '$role_name' WHERE role_id = '$role_id'";

        if (mysqli_query($conn, $sql)) {
            echo "Role updated successfully";
        } else {
            http_response_code(500);
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($action == 'delete') {
        $role_id = $_POST['role_id'];
        $sql = "DELETE FROM roles WHERE role_id = '$role_id'";

        if (mysqli_query($conn, $sql)) {
            echo "Role deleted successfully";
        } else {
            http_response_code(500);
            echo "Error deleting role: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'list') {
        $sql = "SELECT * FROM roles";
        $result = mysqli_query($conn, $sql);

        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }

        echo json_encode($roles);
        mysqli_close($conn);
    }
}
?>
