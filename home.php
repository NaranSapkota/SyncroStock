<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}

// Define las variables de sesión
$fullName = $_SESSION['FullName'];
$warehouseID = $_SESSION['WarehouseID'];
$today = date('M-d-Y');
$urlError=$_GET['redirect'];

$alert = $_SESSION['Alert'] = '2';
$today = date('M-d-Y');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="./images/company/syncrostock.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
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
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px; /* Adjusted top margin */
        }


        .summary-cards {
            display: flex;
            flex-direction: column; /* Cambia la dirección de flex a columna */
            margin-bottom: 5px; /* Espacio entre elementos */
            margin-top: 5px; /* Espacio superior */
        }

        .card {
            width: 100%; /* Asegura que ocupe el ancho completo */
            text-align: center;
            position: relative;
            margin-bottom: 2px; /* Espacio entre la tarjeta de imagen y la tarjeta de texto */
            overflow: hidden; /* Oculta el contenido que se desborda */
            display: flex; /* Asegura que el contenido se ajuste al contenedor */
            align-items: flex-start; /* Eleva la imagen */
            justify-content: center; /* Centra la imagen horizontalmente */
            border: none; /* Elimina el borde */
            box-shadow: none; /* Elimina la sombra */
        }

        .card img {
            width: 100%; /* Ajusta el ancho al 100% del contenedor */
            height: auto; /* Ajusta la altura automáticamente */
            object-fit: cover; /* Ajusta la imagen para cubrir todo el contenedor */
            display: block; /* Elimina espacios debajo de la imagen */
            margin-top: -35px; /* Ajusta la posición vertical de la imagen */
        }

.card2 {
    padding: 30px; 
    text-align: right; /* Alineación del texto a la derecha */
    background-color: #ffffff;
    border: none; 

    display: flex; 
    flex-direction: column; 
    align-items: flex-end; 
}

        .card2 h2, .card2 h4, .card2 h6, .card2 p {
            margin: 0;
        }

        .card a,
        .card2 a {
            color: #F7F6F6;
            text-decoration: none;
        }

        .card p,
        .card2 p {
            font-size: 15px;
        }

    </style>
</head>

<body>

    <div class="S-container">
        <!-- Iframe container -->
        <div class="S-iframe-container">
            <iframe id="S-iframe1" src="./navbar.php?n=1"></iframe>
        </div>

        <!-- Main content area -->
        <div class="S-main-content">
            <div class="summary-cards">

    <?php if (empty($urlError)): ?>
        

        <div class="card2">
            <h2 class="fs-6"><?php echo $fullName; ?></h2>
            <p class="fs-4" style="display: inline-block; margin-right: 10px;">Warehouse # <?php echo "<b>".$warehouseID. "</b>"." , ". $today; ?></p>
	    
        </div>
	<div class="card">
            <img src="./images/home.jpeg" class="img-fluid" alt="Card Image">
        </div>

    	<?php else: ?>
        <div class="card">
            <img src="./images/Unauthorized.jpeg" class="img-fluid" alt="Card Image">
        </div>

        <div class="card2">
            <h3><?php echo $fullName; ?></h3>
	<p><?php echo $urlError; ?></p>
        </div>
    <?php endif; ?>
</div>

        </div>
    </div>

</body>

</html>
