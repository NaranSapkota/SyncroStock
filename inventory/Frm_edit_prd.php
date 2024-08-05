<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Items</title>
    <!-- Bootstrap CSS y otros estilos -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos CSS */
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
    </style>
</head>
<body>
    <?php    
       include '../inc/functions.php';

       // Verifica si se recibe el product_id por POST
       if (isset($_POST['product_id'])) {
           $Product_ID = intval($_POST['product_id']); // Aseguramos que sea un entero
           $Categ_Name = $_POST['categ_name'];

           // Conexión a la base de datos
           $conn = connect();

           // Escapar el ID del producto antes de la consulta
           $Product_ID = $conn->real_escape_string($Product_ID);

           // Consulta sin declaración preparada
           $query = "SELECT * FROM Products WHERE Product_ID = $Product_ID";
           $result = $conn->query($query);

           if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();

               // Asignar los valores a las variables
               $product_name = $row["Product_Name"];
               $category_id = $row["Category_ID"];
               $description = $row["Description"];
               $brand = $row["Brand"];
               $model = $row["Model"];
               $price = $row["Price"];
               $supplier = $row["Supplier_ID"];
               $stock_minimo = $row["Minimum_Level"];
               $stock_max = $row["Maximum_Level"]; // Corrige el nombre aquí
               $sku = $row["SKU"];
               $upc = $row["UPC"];
               $ean = $row["EAN"];
               $isbn = $row["ISBN"];
               $unit = $row["UNIT"];
               $barcode = $row["Barcode"];
               $image = $row["image"];
               $serial = $row["SERIAL"];
               $max_stock_time = $row["Max_Stock_Time"];
               $status = $row["Status"];

               // Obtener categorías
               $sql2 = "SELECT * FROM Product_Categories";
               $result1 = $conn->query($sql2);
           } else {
               // Manejo del error si no se encuentra el producto
               header("Location: ./Frm_Resu_prd.php?error=Product not found");
               exit();
           }
       } else {
           // Redireccionar si no se recibe product_id
           header("Location: ./Frm_Resu_prd.php");
           exit();
       }
    ?>

    <div class="S-container">

        <!-- Iframe container -->
        <div class="S-iframe-container">
            <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
        </div>

        <div class="S-main-content">
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="./inventory.php">Inventory </a><span>/</span>
                <a href="./Frm_Resu_prd.php">Products </a><span>/</span>
            </div>
            
            <h2>Edit Items</h2>

            <form id="addItemForm" action="Edit_prd_run.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmit()"> 
                <div class="form-group row py-4">
                    <label for="Name" class="col-sm-1 col-form-label"><span class="S-required">* </span>Name:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="Name" name="Name" autofocus required autocomplete="off" value="<?php echo htmlspecialchars($product_name); ?>">
                        <input type='hidden' class="form-control" name='product_id' value="<?php echo htmlspecialchars($Product_ID); ?>">
                    </div>

                    <label for="foto" class="col-sm-1 col-form-label">Photo:</label>
                    <div class="col-sm-2">
                        <div id="imageContainer" class="image-container" onclick="openFileOption()">
                            <?php $imagePath = !empty($image) ? './img_products/' . htmlspecialchars($image) : '#'; ?>
                            <img id="preview" src="<?php echo $imagePath; ?>" alt="Preview" style="max-width: 100%; height: auto; display: <?php echo !empty($image) ? 'block' : 'none'; ?>;">
                            <span class="choose-image-text" id="chooseImageText">
                                <?php echo !empty($image) ? 'Change image<br>(JPG, JPEG or PNG)' : 'Click to load image<br>(JPG, JPEG or PNG)'; ?>
                            </span>
                        </div>
                        <button type="button" class="cancel-button" id="cancelButton" onclick="cancelPreview()" style="display: <?php echo !empty($image) ? 'block' : 'none'; ?>;">Cancel</button>
                        <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*" onchange="previewImage(event)" style="opacity: 0;">
                        <input type="hidden" id="existingImage" name="existingImage" value="<?php echo htmlspecialchars($image); ?>">
                    </div>

                </div>

                <div class="form-group row">
                    <label for="SKU" class="col-sm-1 col-form-label"><span class="S-required">* </span> SKU:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="SKU" name="SKU" required autocomplete="off" value="<?php echo htmlspecialchars($sku); ?>">
                    </div>

                    <label for="Unit" class="col-sm-1 col-form-label"><span class="S-required">* </span> Unit:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="Unit" name="Unit">
                            <option value="<?php echo htmlspecialchars($unit); ?>"><?php echo htmlspecialchars($unit); ?></option>
                            <option value="Bx">Box</option>
                            <option value="Dz">Dozen</option>
                            <option value="Kl">Kilograms</option>
                            <option value="Mt">Meters</option>
                            <option value="Pa">Pairs</option>
                            <option value="Pc">Pieces</option>
                            <option value="Tb">Tablets</option>
                            <option value="Pc">Units</option>
                        </select>
                    </div>

                    <label for="model" class="col-sm-1 col-form-label">Model:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($model); ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="brand" class="col-sm-1 col-form-label">Brand:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
                    </div>

                    <label for="category" class="col-sm-1 col-form-label"><span class="S-required">* </span> Category:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="category" name="category">
                            <option value="<?php echo htmlspecialchars($category_id); ?>"><?php echo htmlspecialchars($Categ_Name); ?></option>
                            <?php
                            if ($result1->num_rows > 0) {
                                while ($row = $result1->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row["Category_ID"]) . '">' . htmlspecialchars($row["Category_Name"]) . '</option>';
                                }
                            } else {
                                echo '<option value="">There are no categories available</option>';
                            }
                            ?>
                        </select>
                        <a href="#" class="ml-1" title="Click to Add New Category"><img src="../images/conf_20.png"></a>
                    </div>

                    <label for="serial" class="col-sm-1 col-form-label">Serial:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="serial" name="serial" value="<?php echo htmlspecialchars($serial); ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cost" class="col-sm-1 col-form-label"><span class="S-required">* </span>Cost:</label>
                    <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" id="cost" name="cost" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>

                    <label for="reorder" class="col-sm-1 col-form-label"><span class="S-required">* </span>Reorder:</label>
                    <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" id="reorder" name="reorder" value="<?php echo htmlspecialchars($stock_minimo); ?>" required>
                    </div>
                    
                    <label for="status" class="col-sm-1 col-form-label">Status:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status" name="status">
                            <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="note" class="col-sm-1 col-form-label">Description:</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="note" name="note" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                </div>

                <div class="form-group row py-3">
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success col-sm-4">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href = './Frm_Resu_prd.php';">Cancel</button>
                    </div>
                </div>
            </form>   
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="updateSuccessModal" tabindex="-1" role="dialog" aria-labelledby="updateSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateSuccessModalLabel">Success!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    The item has been successfully updated.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmSubmit() {
            return confirm("Are you sure you want to Update the Product <?php echo htmlspecialchars($product_name); ?>?");
        }

        // Función para previsualizar la imagen seleccionada
        function previewImage(event) {
            var reader = new FileReader();
            var preview = document.getElementById('preview');
            var chooseImageText = document.getElementById('chooseImageText');
            var cancelButton = document.getElementById('cancelButton');
            var existingImageInput = document.getElementById('existingImage');

            reader.onload = function() {
                if (reader.readyState == 2) {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    chooseImageText.style.display = 'none';
                    cancelButton.style.display = 'block';
                    existingImageInput.value = ''; // Limpiar el campo oculto si se elige una nueva imagen
                }
            }

            reader.readAsDataURL(event.target.files[0]);
        }

        // Función para cancelar la previsualización de la imagen
        function cancelPreview() {
            var preview = document.getElementById('preview');
            var chooseImageText = document.getElementById('chooseImageText');
            var cancelButton = document.getElementById('cancelButton');
            var foto = document.getElementById('foto');
            var existingImageInput = document.getElementById('existingImage');

            // Restablecer la previsualización a la imagen existente
            preview.src = existingImageInput.value ? './img_products/' + existingImageInput.value : '#';
            preview.style.display = existingImageInput.value ? 'block' : 'none';
            chooseImageText.style.display = existingImageInput.value ? 'none' : 'block';
            cancelButton.style.display = existingImageInput.value ? 'block' : 'none';
            foto.value = ''; // Limpiar el campo de archivo

            // Si no hay una imagen existente, el mensaje predeterminado se muestra
            existingImageInput.value = ''; // Limpiar el campo oculto
        }

        // Función para abrir la opción de selección de archivo
        function openFileOption() {
            document.getElementById('foto').click();
        }

        // Mostrar el modal cuando se actualice el producto exitosamente
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            $(document).ready(function(){
                $('#updateSuccessModal').modal('show');
            });
        <?php endif; ?>
    </script>

    <?php
        // Cerrar conexión
        $conn->close();
    ?>
</body>


</html>
