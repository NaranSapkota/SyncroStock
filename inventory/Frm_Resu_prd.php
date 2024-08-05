<?php
session_start();
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}

$Systemalert = $_SESSION['message'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
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

        /* Main container for content */
        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        /* Main content area */
        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }

        /* Header styling */
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

        /* Breadcrumbs styling */
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

        /* Controls area styling */
        .S-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 5px;
        }

        .S-controls .search {
            padding: 12px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 25px;
            z-index: 3;
        }

        .S-controls .search:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .S-controls a.S-btn-add {
            display: inline-block;
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            z-index: 3;
        }

        .S-controls a.S-btn-add:hover {
            background-color: #0056b3;
        }

        .S-controls button.S-btn-find {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s, color 0.3s;
            z-index: 3;
        }

        .S-controls button.S-btn-find:hover {
            background-color: #0056b3;
            color: white;
        }

        .S-controls select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 25px;
            z-index: 3;
        }

        .S-controls select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        /* Table styling */
        .S-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .S-table thead {
            background: #2596be;
            color: white;
        }

        .S-table th,
        .S-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        /* Establecemos colores alternos para las filas */
        .S-table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Color de fila par */
        }

        .S-table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Color de fila impar */
        }

        /* Pagination styling */
        .S-pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .S-pagination a {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s, color 0.3s;
            text-decoration: none;
            color: #007bff;
        }

        .S-pagination a.active,
        .S-pagination a:hover {
            background-color: #0056b3;
            color: white;
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .modal-content p {
            margin: 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .record-count {
            margin-bottom: 10px; /* Espacio entre el número de registros y la tabla */
            font-weight: bold;
            font-size: 16px;
        }

        .w-1 {
        width: 9% !important; 
        }

	#productModal {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    padding: 10px;
    z-index: 1000;
    width: 300px; /* Cambia el ancho según sea necesario */
    height: 150px; /* Cambia la altura según sea necesario */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    border-radius: 4px;
	}


	 .highlightable {
        transition: background-color 0.3s; /* Suavizar la transición */
    }

    .highlightable:hover {
        background-color: #f0f8ff; /* Cambia el color de fondo al pasar el mouse */
    }
    </style>
</head>
<body>

<div class="S-container">
    <!-- Contenedor del iframe -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <div class="S-main-content">
        <div class="S-breadcrumbs">
            <span>Home </span><span>/</span>
            <a href="./inventory.php">Inventory </a><span>/</span>
        </div>

        <h1>Products</h1>

        <!-- Controles para añadir y buscar productos -->
        <div class="S-controls py-1">
            <?php echo "<img src='../images/Products.jpeg' class='img-fluid w-1' alt='Orders'>"; ?>
            <a href="./Frm_Add_prd.php" class="S-btn-add">Add +</a>
            <form action="" method="GET">
                <input type="text" id="search" name="search" placeholder="Search by: Name, Category, Brand, Model, SKU" class="search">
                <select name="filter">
                    <option value="All">All</option>
                    <option value="Active" <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="S-btn-find">Find</button>
            </form>
        </div>



        <!-- Tabla de productos -->
        <table class="S-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Unit Price</th>
                    <th>Reorder</th>
                    <th>Status</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>

            <?php 
                if (isset($_SESSION['message'])): 
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $Systemalert; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php 
                unset($_SESSION['message']); // Unset the session variable after displaying the alert
                endif; 
            ?>
            

            <tbody>
                <?php
                // Incluimos las funciones y nos conectamos a la base de datos
                include '../inc/functions.php';
                $conn = connect();

                // Inicializamos las variables
                $condition = "";
                $filter = ""; // Inicializamos $filter vacío

                // Verificamos los filtros y el término de búsqueda
                if (isset($_GET['filter']) && $_GET['filter'] !== '' && $_GET['filter'] !== 'All') {
                    $filter = htmlspecialchars($_GET['filter']);
                    $condition = "AND Status='$filter'";
                } elseif (isset($_GET['filter']) && $_GET['filter'] === 'All') {
                    $filter = 'All';
                    // No establecemos $condition cuando $filter es 'All', dejándolo vacío
                } else {
                    $filter = 'Active'; // Asigno 'Active' si el filtro está vacío
                    $condition = "AND Status='$filter'"; // Construimos $condition solo si $filter no es 'All'
                }

                // Construimos la consulta SQL base
                $sql = "SELECT p.*, pc.Category_Name
                        FROM Products p
                        JOIN Product_Categories pc ON p.Category_ID = pc.Category_id
                        WHERE 1=1 $condition";

                // Verificamos el término de búsqueda
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_term = $_GET['search'];
                    $sql .= " AND (SKU LIKE '%$search_term%'
                            OR Product_Name LIKE '%$search_term%'
                            OR Category_Name LIKE '%$search_term%'
                            OR Brand LIKE '%$search_term%'
                            OR Model LIKE '%$search_term%')";
                }


                // Contar Items
                $sql2 = "SELECT COUNT(*) As Items
                        FROM Products p
                        JOIN Product_Categories pc ON p.Category_ID = pc.Category_id
                        WHERE 1=1 $condition";

                // Verificamos el término de búsqueda para $sql2
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_term = $_GET['search'];
                    $sql2 .= " AND (SKU LIKE '%$search_term%'
                            OR Product_Name LIKE '%$search_term%'
                            OR Category_Name LIKE '%$search_term%'
                            OR Brand LIKE '%$search_term%'
                            OR Model LIKE '%$search_term%')";
                }

                $result2 = $conn->query($sql2);
                $row = $result2->fetch_assoc();

                echo "<div class='record-count'>";
                echo $row['Items'] . " Record(s) In Total"; 
                echo "</div>";

                

                // Obtenemos el total de registros
                $result = $conn->query($sql);
                $total_records = $result->num_rows;

                // registros por página
                $records_per_page = 10;

                // calcular total de páginas
                $total_pages = ceil($total_records / $records_per_page);

                // obtener página actual
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

                // Calcular offset para la consulta SQL según la página actual
                $offset = ($current_page - 1) * $records_per_page;

                // incluir LIMIT y OFFSET en la consulta
                $sql .= " LIMIT $records_per_page OFFSET $offset";

                // Ejecutar la consulta modificada
                $result = $conn->query($sql);

                // Mostrar los resultados en la tabla
               // Mostrar los resultados en la tabla
		// Mostrar los resultados en la tabla
if ($result->num_rows > 0) {
    $it = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $it++ . "</td>";
        echo "<td>" . $row['Category_Name'] . "</td>";
        echo "<td>" . $row['Product_ID'] . "</td>";
        echo "<td class='highlightable' style='text-align: left;' onmouseover='showModal(event, " . $row['Product_ID'] . ")' onmouseout='hideModal()'>
        	<img src='../images/eyes20.png' style='margin-right: 8px;'> 
        	<span>" . $row['Product_Name'] . "</span>
        </td>";
        echo "<td>" . $row['Brand'] . "</td>";
        echo "<td>" . $row['Model'] . "</td>";
        echo "<td>" . $row['SKU'] . "</td>";
        echo "<td>" . $row['Price'] . "</td>";
        echo "<td>" . $row['Minimum_Level'] . "</td>";

        $status = $row["Status"];
        echo "<td>";
        if ($status == 'Active') {
            echo '<img src="../images/green25.png" alt="Active" Title="Product Active">';
        } elseif ($status == 'Inactive') {
            echo '<img src="../images/red25.png" alt="Inactive" Title="Product Inactive">';
        }
        echo "</td>";

        // Botón de eliminar
        echo "<td>";
        echo "<form action='Del_prd.php' method='post' onsubmit='return confirmDelete(\"" . $row["Product_Name"] . "\", \"" . $status . "\")'>";
        echo "<input type='hidden' name='product_id' value='" . $row["Product_ID"] . "'>";
        echo "<input type='hidden' name='product_name' value='" . $row["Product_Name"] . "'>";

        // Mostrar el botón de eliminar según el estado del producto
        if ($status == 'Active') {
            echo "<button type='submit' name='delete_product' class='btn btn-danger'>Delete</button>";
        } else {
            echo "<button type='button' class='btn btn-secondary' disabled>Delete</button>";
        }
        echo "</form>";
        echo "</td>";

        // Botón de editar
        echo "<td>";
        echo "<form action='Frm_edit_prd.php' method='post'>";
        echo "<input type='hidden' name='product_id' value='" . $row["Product_ID"] . "'>";
        echo "<input type='hidden' name='categ_name' value='" . $row["Category_Name"] . "'>";
        echo "<button type='submit' name='edit_product' class='btn btn-warning'>Edit</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12'>No products found</td></tr>";
}
                // Cerrar la conexión a la base de datos
                $conn->close();
                ?>
            </tbody>
        </table>

<!-- Modal para mostrar detalles del producto -->
<div id="productModal" style="display: none; position: absolute; background-color: white; border: 1px solid #ccc; padding: 10px; z-index: 1000;">
    <div id="modalProductDetails"></div>
</div>


        <!-- Controles de paginación -->
        <div class="S-pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_class = ($i == $current_page) ? 'active' : '';
                echo "<a href='?page=$i&filter=$filter&search=$search_term' class='S-btn-pagination $active_class'>$i</a>";
            }
            ?>
        </div>

        <!-- Modal para mensajes -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div id='myModal' class='modal'>
                      <div class='modal-content'>
                          <span class='close'>&times;</span>
                          <p>" . $_SESSION['message'] . "</p>
                      </div>
                  </div>";
            unset($_SESSION['message']);
        }
        ?>


        <!-- Script para controlar el modal -->
        <script>

	function showModal(event, productId) {
    const modal = document.getElementById('productModal');
    modal.style.display = 'block'; // Mostrar el modal

    // Posicionar el modal cerca del cursor
    const x = event.pageX - 150; // Ajustar la posición del modal
    const y = event.pageY + 10; // Ajustar la posición del modal
    modal.style.left = x + 'px';
    modal.style.top = y + 'px';

    // Cargar detalles del producto desde el servidor
    $.ajax({
        url: './get_product_details.php', // Archivo que maneja la consulta
        type: 'POST',
        data: { id: productId }, // Enviar el ID del producto al servidor
        success: function(data) {
            // Suponiendo que `data` es un HTML que contiene los detalles del producto
            modal.innerHTML = data; // Insertar los detalles en el modal
        },
        error: function(xhr, status, error) {
            // Manejar errores aquí
            modal.innerHTML = "<strong>Loading Product Details...</strong>";
        }
    });
}

function hideModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none'; // Ocultar el modal
}

        $(document).ready(function() {
                // Close alert after 3 seconds
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 3000);
            });

            var modal = document.getElementById('myModal');
            var span = document.getElementsByClassName('close')[0];

            span.onclick = function() {
                modal.style.display = 'none';
            };

            window.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };

            function confirmDelete(productName, status) {
                if (status === 'Active') {
                    return confirm("Are you sure you want to delete the product '" + productName + "'?");
                } else {
                    alert("Cannot delete an inactive product.");
                    return false;
                }
            }
        </script>

    </div>

</div>

</body>
</html>
