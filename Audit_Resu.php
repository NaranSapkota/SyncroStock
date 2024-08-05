<?php
session_start();
// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}


$authorizedRoles = ['1'];

// Verificar si el rol del usuario está en la lista de roles autorizados
if (!in_array($_SESSION['Role'], $authorizedRoles)) {
    
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: ./home.php?redirect=" . urlencode($currentUrl));  // 
    exit;

}


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
        <iframe id="S-iframe1" src="./navbar.php?n=1"></iframe>
    </div>

    <div class="S-main-content">
        <div class="S-breadcrumbs">
            <span>Home </span><span>/</span>
            <a href="./controlPanel.php">Control Panel </a><span>/</span>
        </div>

        <h1>Transaction Audit </h1>

        <!-- Controles para añadir y buscar productos -->
        <div class="S-controls py-1">
            <?php echo "<img src='./images/auditDB.jpeg' class='img-fluid w-1' alt='Orders'>"; ?>
            
        <form action="" method="GET">Select Type : 
                <select name="filter">
                    <option value="All">All</option>
                    <option value="INSERT" <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'Insert') echo 'selected'; ?>> Insert </option>
                    <option value="UPDATE" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'Update') echo 'selected'; ?>> Update </option>
 		    <option value="CANCEL" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'Cancelled') echo 'selected'; ?>> Cencelled</option>
		    <option value="DELETE" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'Delete') echo 'selected'; ?>> Delete</option>
                </select>
                <button type="submit" class="S-btn-find">Find</button>
            </form>
        </div>



        <!-- Tabla de productos -->
        <table class="S-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Module ID</th>
                    <th colspan='2'>Action Type</th>
                    <th>Table</th>
                    <th>Date</th>
                    <th>Details of the Transaction</th>
                </tr>
            </thead>

            <tbody>
                <?php
                // Incluimos las funciones y nos conectamos a la base de datos
                include './inc/functions.php';
                $conn = connect();

                // Inicializamos las variables
                $condition = "";
                $filter = ""; // Inicializamos $filter vacío

                // Verificamos los filtros y el término de búsqueda
                if (isset($_GET['filter']) && $_GET['filter'] !== '' && $_GET['filter'] !== 'All') {
                    $filter = htmlspecialchars($_GET['filter']);
                    $condition = "AND Action_Type ='$filter'";
                } elseif (isset($_GET['filter']) && $_GET['filter'] === 'All') {
                    $filter = 'All';

                } else {
                    $filter = 'INSERT'; // 
                    $condition = "AND Action_Type ='$filter'"; // Construimos $condition solo si $filter no es 'All'
                }

                // Construimos la consulta SQL base
                $sql = "SELECT t.*, u.username
                        FROM transaction_system t
                        JOIN user u ON t.user_id = u.USER_ID
                        WHERE 1=1 $condition
			ORDER BY Action_Date DESC";


                // Contar Items
                $sql2 = "SELECT COUNT(*) As Items
                        FROM transaction_system p
                        WHERE 1=1 $condition
			$condition";

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
	if ($result->num_rows > 0) {
    	$it = 1;
    	while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $it++ . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['Module_ID'] . "</td>";
        echo "<td>" . $row['Action_Type'] . "</td>";
        $type = $row["Action_Type"];
        echo "<td>";
        if ($type == 'INSERT') {
            echo '<img src="./images/green25.png" >';
        } elseif ($type == 'DELETE') {
            echo '<img src="./images/red25.png" >';
        }
	elseif ($type == 'UPDATE') {
            echo '<img src="./images/yellow25.png" >';
        }
	elseif ($type == 'CANCEL') {
            echo '<img src="./images/red25.png" >';
        }

        echo "</td>";
 	echo "<td>" . $row['Table_Name'] . "</td>";
        echo "<td>" . $row['Action_Date'] . "</td>";
        echo "<td class='text-left'>" . htmlspecialchars($row['Details']) . "</td>";

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

    </div>

</div>

</body>
</html>
