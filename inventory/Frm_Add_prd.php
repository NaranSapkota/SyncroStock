<?php
session_start(); // Iniciar sesión para usar $_SESSION

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}


include '../inc/functions.php';
$sql_category = "SELECT Category_ID, Category_Name
		FROM Product_Categories
		ORDER BY
  		CASE
    		WHEN Category_Name = 'Others' THEN 1
    		ELSE 0
  		END,
  		Category_Name;";
$conn = connect();
$result1 = $conn->query($sql_category);

// Variable para almacenar mensajes de error o éxito
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Limpiar mensajes de sesión después de mostrarlos
unset($_SESSION['message']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Items</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
    </style>
</head>

<body>

<div class="S-container">
    <!-- Contenedor del iframe -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <!-- Área principal de contenido -->
    <div class="S-main-content">
        <div>
            <!-- Contenedor de migas de pan -->
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="./inventory.php">Inventory </a><span>/</span>
                <a href="./Frm_Resu_prd.php">Products </a><span>/</span>
            </div>
            
            <h2>Add Items</h2>

            <!-- Controles del formulario -->
            <form id="addItemForm" action="./Frm_Add_prd_run.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()"> 

                <!-- Parte 1: Nombre y Foto -->
                <div class="form-group row py-4">
               	     <label for="SKU" class="col-sm-1 col-form-label"><span class="S-required">* </span> Name:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="Name" name="Name" autofocus required autocomplete="off">
                    </div>

                    <label for="foto" class="col-sm-1 col-form-label">Photo:</label>
                    <div class="col-sm-2">
                        <div id="imageContainer" class="image-container" onclick="openFileOption()">
                            <img id="preview" src="#" alt="Preview">
                            <span class="choose-image-text" id="chooseImageText">Click to load image<br>(JPG, JPEG or PNG)</span>
                        </div>
                        <button type="button" class="cancel-button" id="cancelButton" onclick="cancelPreview()">Cancel</button>
                        <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*" onchange="previewImage(event)" style="opacity: 0;">
                    </div>
                </div>
                <!-- Fin Parte 1 -->   

                <!-- Parte 2: SKU, Unidad, Modelo -->
                <div class="form-group row">
                    <label for="SKU" class="col-sm-1 col-form-label"><span class="S-required">* </span> SKU:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="SKU" name="SKU" required autocomplete="off">
                    </div>
                    <label for="Unit" class="col-sm-1 col-form-label"><span class="S-required">* </span> Unit:</label>
                    <div class="col-sm-2">
                        <div class="input-group" >
                            <select class="form-control m-0" id="Unit" name="Unit" style="border-radius: 0 8px 0 0; width: 40%; height: 38px;" required>
                                <option value=""></option>
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
                    </div>
                    <label for="model" class="col-sm-1 col-form-label">Model:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="model" name="model">
                    </div>
                </div>
                <!-- Fin Parte 2 --> 

                <!-- Parte 3: Espacio -->
                <div class="form-group row ">
                    <div class="col-sm-12">
                        &nbsp; <!-- Espacio no rompible para separación visual -->
                    </div>
                </div>
                <!-- Fin Parte 3 -->

                <!-- Parte 4: Marca, Categoría, Número de serie -->
                <div class="form-group row">
                    <label for="brand" class="col-sm-1 col-form-label">Brand:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="brand" name="brand">
                    </div>

                    <label for="category" class="col-sm-1 col-form-label"><span class="S-required">* </span> Category:</label>
                    <div class="col-sm-2">
                        <div class="d-flex align-items-center">
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php while ($row1 = $result1->fetch_assoc()) { ?>
                                    <option value="<?php echo $row1['Category_ID']; ?>"><?php echo $row1['Category_Name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <label for="serial" class="col-sm-1 col-form-label">Serial:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="serial" name="serial">
                    </div>
                </div>
                <!-- Fin Parte 4 --> 

                <!-- Parte 5: Costo, Reorden y Estado -->
                <div class="form-group row">
                    <label for="cost" class="col-sm-1 col-form-label"><span class="S-required">* </span>Cost:</label>
                    <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" id="cost" name="cost" value="1" required>
                    </div>

                    <label for="reorder" class="col-sm-1 col-form-label"><span class="S-required">* </span>Reorder:</label>
                    <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" id="reorder" name="reorder" value="1" required>
                    </div>

                    <label for="status" class="col-sm-1 col-form-label">Status:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    
                </div>
                <!-- Fin Parte 5 -->  

                <!-- Parte 6: Descripción y botones -->
                <div class="form-group row">
                    <label for="description" class="col-sm-1 col-form-label">Description:</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Maximum 100 Characters"></textarea>
                    </div>
                </div>

                <div class="form-group row py-3">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" >Save</button>
                        <button type="reset" class="btn btn-success">Clear</button>
                        <a href="./Frm_Resu_prd.php" class="btn btn-secondary">Cancel</a>

                    </div>
                </div>
                <!-- Fin Parte 6 -->  
            </form>
        </div>
    </div>

    <!-- Modal de notificación -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="notificationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para el manejo del modal y validación -->
    <script>
        // Función para mostrar el modal con el mensaje recibido
        function showModal(message) {
            var notificationMessage = document.getElementById('notificationMessage');
            notificationMessage.textContent = message;
            $('#notificationModal').modal('show');
        }

        // Función para validar el formulario
        function validateForm() {
            var name = document.getElementById('Name').value;
            var sku = document.getElementById('SKU').value;
            var unit = document.getElementById('Unit').value;
            var category = document.getElementById('category').value;

            // Ejemplo de validación simple, puedes agregar más según tus necesidades
            if (name.trim() === '') {
                showModal('Please enter Name');
                return false;
            }

            if (sku.trim() === '') {
                showModal('Please enter SKU');
                return false;
            }

            if (unit.trim() === '') {
                showModal('Please select Unit');
                return false;
            }

            if (category.trim() === '') {
                showModal('Please select Category');
                return false;
            }

            // Si todo está bien, el formulario se enviará
            return true;
        }

        // Función para previsualizar la imagen seleccionada
        function previewImage(event) {
            var reader = new FileReader();
            var imageContainer = document.getElementById('imageContainer');
            var preview = document.getElementById('preview');
            var chooseImageText = document.getElementById('chooseImageText');
            var cancelButton = document.getElementById('cancelButton');

            reader.onload = function() {
                if (reader.readyState == 2) {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    chooseImageText.style.display = 'none';
                    cancelButton.style.display = 'block';
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

            preview.src = '#';
            preview.style.display = 'none';
            chooseImageText.style.display = 'block';
            cancelButton.style.display = 'none';
            foto.value = '';
        }

        // Función para abrir la opción de selección de archivo
        function openFileOption() {
            document.getElementById('foto').click();
        }
        
        // Verificar si hay un mensaje para mostrar
        $(document).ready(function() {
            // Obtener el mensaje de éxito o error de la sesión PHP
            var message = "<?php echo $message; ?>";
            var error = "<?php echo $error; ?>";

            // Mostrar el modal según el tipo de mensaje
            if (message.trim() !== '') {
                showModal(message);
            } else if (error.trim() !== '') {
                showModal(error);
            }
        });
    </script>
    <!-- Fin del script -->
</div>
</body>
</html>
