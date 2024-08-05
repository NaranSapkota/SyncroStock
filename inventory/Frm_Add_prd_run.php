<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php"); 
    exit;
}

include '../inc/functions.php';
$conn = connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['Name']) && !empty($_POST['Unit']) && !empty($_POST['category']) && !empty($_POST['cost']) && !empty($_POST['reorder'])) {
        $Name = $_POST['Name'];
        $category = $_POST['category'];
        $note = $_POST['description'];
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $cost = $_POST['cost'];
        $reorder = $_POST['reorder'];
        $SKU = $_POST['SKU'];
        $serial = $_POST['serial'];
        $UPC = $_POST['UPC'];
        $EAN = $_POST['EAN'];
        $ISBN = $_POST['ISBN'];
        $Unit = $_POST['Unit'];
        $barcode = $_POST['barcode'];
        $Status = $_POST['status'];

        // Manejo del archivo de imagen
        $imagenPath = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            // Guardar la imagen
            $fileTmpPath = $_FILES['foto']['tmp_name'];
            $fileName = $_FILES['foto']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Generar un nombre único para evitar colisiones
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './img_products/'; // Asegúrate de que esta carpeta exista y tenga permisos de escritura
                $dest_path = $uploadFileDir . $newFileName;

                // Mover el archivo a la carpeta de destino
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imagenPath = $newFileName; // Guardar el nombre de la imagen para la base de datos
                } else {
                    $_SESSION['error'] = 'Error al mover el archivo a la carpeta de destino.';
                }
            } else {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Solo JPG, JPEG y PNG son aceptados.';
            }
        }

        // Consultar si el SKU o el nombre del producto ya existen
        $sql_check = "SELECT COUNT(*) as count FROM Products WHERE SKU = '$SKU' OR Product_Name = '$Name'";
        $result = $conn->query($sql_check);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                $_SESSION['error'] = 'SKU or Product Name already exists.';
            } else {
                // SKU y nombre del producto no existen, proceder con la inserción
                $sql_insert = "INSERT INTO Products (Product_Name, Category_ID, Description, Brand, Model, Price, Supplier_ID, Minimum_Level, Maximum_Level, SKU, UPC, EAN, ISBN, UNIT, Barcode, image, SERIAL, Status, Max_Stock_Time) 
                VALUES ('$Name', '$category', '$note', '$brand', '$model', '$cost', '1', '$reorder', 0, '$SKU', '$UPC', '$EAN', '$ISBN', '$Unit', '$barcode', '$imagenPath', '$serial', '$Status', 9999)";

                if ($conn->query($sql_insert) === TRUE) {
    			$_SESSION['message'] = "Product $Name Created Successfully.";
    			echo '<script type="text/javascript">window.location.href = "./Frm_Resu_prd.php";</script>';
    			exit();
		} else {
    			$_SESSION['error'] = 'Error inserting product: ' . $conn->error;
    			// Optionally, you can also redirect to an error page or show an error message
		}

            }
        } else {
            $_SESSION['error'] = 'Error checking product existence.';
        }
    } else {
        $_SESSION['error'] = 'Please fill in all required fields.';
    }
    
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

$conn->close();
?>
