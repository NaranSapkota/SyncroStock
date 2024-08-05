<?php

session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}

include '../inc/functions.php';

$conn = connect();

$Product_ID = $_POST['product_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['Name']) && !empty($_POST['Unit']) && !empty($_POST['category']) && !empty($_POST['cost']) && !empty($_POST['reorder'])) {
       
        $Name = $_POST['Name'];
        $category = $_POST['category'];
        $note = $_POST['note'];
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
        $supplier = "1";
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
                $uploadFileDir = './img_products/'; // Carpeta
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

        $sql_update = "UPDATE Products
                        SET Product_Name = '$Name',
                        Category_ID = '$category',
                        Description = '$note',
                        Brand = '$brand',
                        Model = '$model',
                        Price = '$cost',
                        Supplier_ID = '$supplier',
                        Minimum_Level = '$reorder',
                        SKU = '$SKU',
                        UPC = '$UPC',
                        EAN = '$EAN',
                        ISBN = '$ISBN',
                        UNIT = '$Unit',
                        Barcode = '$barcode',
                        image = '$imagenPath',
                        SERIAL = '$serial',
                        Status = '$Status',
                        Max_Stock_Time = '0'
                       WHERE Product_ID = '$Product_ID'";
                       
        //echo $sql_update;
        //die('');

        if ($conn->query($sql_update) === TRUE) {

            // Redirect to Frm_edit_prd.php
	$conn->commit();
	$_SESSION['message'] = "Product <b> $Name </b> Updated Successfully";
	echo '<script type="text/javascript">window.location.href = "./Frm_Resu_prd.php";</script>';

        } else {
            // Error: Set error message
            $_SESSION['error'] = 'Error updating product: ' . $conn->error;
        }
    } else {
        $_SESSION['error'] = 'Please fill in all required fields.';
    }
}

function saveImage($file) {
    // Function to handle image upload, unchanged from your original code
    // Ensure to adjust as per your server environment and requirements
}

$conn->close();
?>
