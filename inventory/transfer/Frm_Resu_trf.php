<?php
session_start();


// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}



// Business variables

$user=$_SESSION['UserID'];
$userSystem =$_SESSION['FullName'];
$alert =$_SESSION['Alert'] = '2';
$today = date('M-d-Y');
$currentWarehouse=$_SESSION['WarehouseID'];
$role=$_SESSION['Role'];


$Systemalert = $_SESSION['message'];



// Verificar si las variables de sesión están definidas
if (isset($_SESSION['UserID'], $_SESSION['Role'],)) {

    $userSystem =$_SESSION['FullName'];
    $rol=$_SESSION['Role'];

      // Determinar si el usuario tiene permisos basado en el rol
      $User_permitido = ($rol != '3'); // true or false
    

} else {
    header('Location: login.php');
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>


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

        .record-count {
            margin-bottom: 10px; 
            font-weight: bold;
            font-size: 16px;
        }
        .w-1 {
        width: 9% !important; 
        }
    </style>
</head>

<body>

<?php
    // Incluir archivo de funciones
    include '../../inc/functions.php';

    // Establecer la cantidad de registros por página
    $records_per_page = 10;

    // Determinar la página actual
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $records_per_page;

// Determinar la página actual
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

If ($currentWarehouse !=1)
{
  $WHERE = "WHERE t.Warehouse_Destination=$currentWarehouse";
}
Else{
  $WHERE = "WHERE 1=1";
}

// Consulta base para obtener las órdenes con paginación
$base_query = " SELECT 
        t.Transfer_ID,
        t.Transfer_Number, 
        t.Transfer_Date, 
        t.Transfer_Status, 
        t.Warehouse_Origin, 
        wo.Warehouse_Name AS whOriginName,
        t.User_ID_Responsable, 
        t.Warehouse_Destination, 
        wd.Warehouse_Name AS whDestinationName,
        t.Delivery_Date, 
        COUNT(td.Product_ID) AS Items,
        SUM(td.amount) AS total_amount
    FROM transfers t
    JOIN details_transfer td ON t.Transfer_Number = td.Transfer_Number
    JOIN Warehouses wd ON t.Warehouse_Destination = wd.Warehouse_ID
    JOIN Warehouses wo ON t.Warehouse_Origin = wo.Warehouse_ID
    $WHERE

	";

// Si se ha enviado un término de búsqueda
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $base_query .= " AND 
            t.Transfer_Number LIKE '%$search_term%' OR 
            t.Transfer_Date LIKE '%$search_term%' OR 
            t.Transfer_Status LIKE '%$search_term%' OR 
            t.Warehouse_Destination LIKE '%$search_term%' OR 
            t.User_ID_Responsable LIKE '%$search_term%'";
}

// Agrupar y ordenar la consulta
$base_query .= " 

	GROUP BY 
        t.Transfer_ID, 
        t.Transfer_Number, 
        t.Transfer_Date, 
        t.Transfer_Status, 
        t.Warehouse_Origin, 
	wo.Warehouse_Name,
        t.User_ID_Responsable, 
        t.Warehouse_Destination,
	wd.Warehouse_Name,
        t.Delivery_Date
    ORDER BY t.Transfer_ID DESC, 
    FIELD(t.Transfer_Status, 'In-Progress', 'Open', 'Completed','Cancelled') ASC ";

    //echo $base_query;
    //die('');


// Añadir LIMIT y OFFSET para paginación
$paginated_query = "$base_query LIMIT $records_per_page OFFSET $offset";

// Conectar a la base de datos y ejecutar la consulta para obtener los resultados paginados
$conn = connect();
$result = $conn->query($paginated_query);

// Contar el número total de registros sin LIMIT y OFFSET
$count_query = "SELECT COUNT(*) AS total_records FROM ($base_query) AS count_table";
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total_records'];

// Calcular el número total de páginas
$total_pages = ceil($total_records / $records_per_page);

?>

<div class="S-container">
    <!-- Iframe container -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../../navbar.php?n=1"></iframe>
    </div>

    <!-- Main content area -->
    <div class="S-main-content">
        <div>
            <!-- Breadcrumbs container -->
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="../../inventory/inventory.php">Inventory </a><span>/</span>
            </div>
            <!-- End breadcrumbs container -->

            <h1>Transfers</h1>

            <!-- Controls section -->
            <div class="S-controls py-1">
                <?php
                 echo "<img src='../../images/Receive.jpeg' class='img-fluid w-1' alt='Orders'>";
                    // Mostrar el botón Add + solo si $User_permitido es verdadero
                    if ($User_permitido) {
                        echo "<a href='./Frm_Add_transfer.php' class='S-btn-add'>Add +</a>";
                    } else {
                        
                    }
                ?>

                <form action="" method="GET">
                    <input type="text" id="search" name="search" placeholder="Search by: Number, Status, Responsible, Supplier" class="search">
                    <button type="submit" class="S-btn-find">Find</button>
                </form>
            </div>

            <!------------ Alert section ------------->

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
            
            <!------------ contar Items ------------->
            <?php
               
                $sql2 = "SELECT COUNT(*) AS Items
                FROM ($base_query) AS count_table
                ";
                $result2 = $conn->query($sql2);
                $row2 = $result2->fetch_assoc();

                echo "<div class='record-count'>";
                echo $row2['Items'] . " Record(s) In Total"; // Items de la Tabla
                echo "</div>";
            ?>

            <!-- Table section -->
            <table class="S-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transfer #</th>
                        <th>Responsible</th>
                        <th>Warehouse Origin</th>
                        <th>Date</th>
                        <th>Warehouse Destination</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>PDF</th>
                        <th>Receive Transfer</th>

                        <?php
                        // Encabezado de Acciones solo para Administrativos
                             if ($User_permitido){
                               echo" <th colspan='2'>Actions</th> ";
                             }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Verificar si hay resultados de la consulta
                    if ($result->num_rows > 0) {
                        // Iterar sobre cada fila de resultados
                        $it=1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $it++ . "</td>";
                            echo "<td>" . $row["Transfer_Number"] . "</td>";
                            echo "<td>" . $userSystem . "</td>";
                            echo "<td>" . $row["whOriginName"] . "</td>";
                            echo "<td>" . $row["Transfer_Date"] . "</td>";
                            echo "<td>" . $row["whDestinationName"] . "</td>";
                            $status = $row["Transfer_Status"];
                            echo "<td>";

                            // **************************   Mostrar ícono según el estado de la orden  ***************************
                            if ($status == "Open") {
                                echo '<div class="text-center">';
                                    echo '<img src="../../images/blue25.png" alt="Open" title="Open" class="img-fluid mb-0" style="max-width: 50px;">';
                                    echo '<p class="text-muted mb-0" style="font-size: 8px;">Open</p>';
                                echo '</div>';

                            } elseif ($status == "Cancelled") {
                                echo '<div class="text-center">';
                                    echo '<img src="../../images/red25.png" alt="Cancelled" title="Cancelled" class="img-fluid mb-0" style="max-width: 50px;">';
                                    echo '<p class="text-muted mb-0" style="font-size: 8px;">Cancelled</p>';
                                echo '</div>';

                            } elseif ($status == "In-Progress") {
                                echo '<div class="text-center">';
                                    echo '<img src="../../images/yellow25.png" alt="In-Progress" title="In-Progress" class="img-fluid mb-0" style="max-width: 50px;">';
                                    echo '<p class="text-muted mb-0" style="font-size: 8px;">In-Progress</p>';
                                echo '</div>';

                            } elseif ($status == "Completed") {
                                echo '<div class="text-center">';
                                    echo '<img src="../../images/green25.png" alt="Completed" title="Completed" class="img-fluid mb-0" style="max-width: 50px;">';
                                    echo '<p class="text-muted mb-0" style="font-size: 8px;">Completed</p>';
                                echo '</div>';

                            }

                            // **************************   Icono Status   ***************************

                            echo "</td>";
                            echo "<td>" . $row["Items"] . "</td>";
                            echo "<td>$" . number_format($row["total_amount"], 1) . "</td>";

                            // Boton Exportar PDF
                            echo "<td>";
                                echo "<form action='../../fpdf/transfer_pdf.php' method='post' target='new_window' onsubmit=\"window.open('', 'new_window', 'width=800,height=600');\">";
                                    echo "<input type='hidden' name='Transfernumber' value='" . $row["Transfer_Number"] . "'>";
                                    echo "<button type='submit' name='pdf' style='border: none; background: none; padding: 0;'><img src='../../images/pdf2_15.png' title='Exportar'></button>";
                                echo "</form>";
                            echo "</td>";

                             // Boton Recibir Orden
                            echo "<td>";
                                echo "<form action='./receive_transfer.php' method='post';\">";
                                    echo "<input type='hidden' name='Transfernumber' value='" . $row["Transfer_Number"] . "'>";

                                    if ($status != 'Cancelled' &&  $status != 'Completed') {
                                        echo "<button type='submit' name='receive' class='btn btn-primary'> Receive</button>";
                                    }else {
                                        // Si el estado es Cancelled, mostrar el botón deshabilitado
                                        echo "<button type='submit' name='receive' class='btn btn-primary' disabled> Receive</button>";
                                    }


                                echo "</form>";
                            echo "</td>";
                
                            if ($User_permitido){
                                // Boton Eliminar
                                echo "<td>";
                                    echo "<form id='deleteForm_" . $row["Transfer_Number"] . "' action='./Del_Transfer.php' method='post'>";
                                    echo "<input type='hidden' name='Transfernumber' value='" . $row["Transfer_Number"] . "'>";

                                    // Verificar si el estado no es Cancelled
                                    if ($status != 'Cancelled' &&  $status != 'Completed') {
                                        echo "<button type='button' onclick='confirmDelete(\"" . htmlspecialchars($row["Transfer_Number"]) . "\")' class='btn btn-danger'>Cancel</button>";
                                    } else {
                                        // Si el estado es Cancelled, mostrar el botón deshabilitado
                                        echo "<button type='button' class='btn btn-secondary' disabled>Cancel</button>";
                                    }
                                    echo "</form>";
                                echo "</td>";

                                
                                }else{

                                }    
                                  
                                echo "</td>";

                            echo "</tr>";
                        }
                    } else {
                        // Mostrar fila vacía si no hay resultados
                        echo "<tr><td colspan='11'>No products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Paginación -->
            <?php
            // Consulta para contar el número total de registros
            $count_query = "SELECT COUNT(*) AS total_records FROM ($base_query) AS count_table";
            $count_result = $conn->query($count_query);
            $count_row = $count_result->fetch_assoc();
            $total_records = $count_row['total_records'];

            // Calcular el número total de páginas
            $total_pages = ceil($total_records / $records_per_page);

            // <!-- Controles de paginación -->
            echo "<div class='S-pagination'>";
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_class = ($i == $current_page) ? 'active' : '';
                echo "<a href='?page=$i&filter=$filter&search=$search_term' class='S-btn-pagination $active_class'>$i</a>";
            }
            echo "</div>";           
            
            ?>
        </div>
    </div>
</div>

<!-- Script para manejar la comunicación del iframe -->
<script>

    $(document).ready(function() {
        // Close alert after 3 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });

    // Mostrar la alerta automáticamente cuando se cargue la página
    $(document).ready(function() {
        $('.alert').alert();
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });

    function openNav() {
        window.parent.postMessage({ action: 'openNav' }, '*');
    }

    window.addEventListener('message', function(event) {
        if (event.data.action === 'openNav') {
            document.querySelector(".S-container").style.marginLeft = "270px";
            document.querySelector(".S-container").style.width = "calc(100% - 270px)";
        } else if (event.data.action === 'closeNav') {
            document.querySelector(".S-container").style.marginLeft = "0";
            document.querySelector(".S-container").style.width = "100%";
        }
    });

    function confirmDelete(orderNumber) {
        if (confirm("¿Are you sure you want to delete the Transfer #" + orderNumber + "?")) {
            var formId = "deleteForm_" + orderNumber;
            document.getElementById(formId).submit();
        } else {
        
        }
    }
</script>

</body>

</html>
