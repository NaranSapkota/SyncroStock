<?php
session_start();


// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ./login.php"); 
    exit;
}




// Business variables

//$_SESSION['Company'] = 'SuperStore';
$alert =$_SESSION['Alert'] = '2';
$version = '1.0.0';

$a=$_SESSION['UserID'];
$b=$_SESSION['FirstName'];
$c=$_SESSION['LastName'];
$d=$_SESSION['Role'];
$e=$_SESSION['FullName'];
$f=$_SESSION['WarehouseName'];
$g=$_SESSION['WarehouseID'];
$h=$_SESSION['SupplierCompany'];
$i=$_SESSION['username'];

//
include './inc/functions.php';

$sql_company ="SELECT * FROM companies";
// 

$conn = connect();
$result_company = $conn->query($sql_company);
if ($result_company->num_rows > 0) {
    $row_company = $result_company->fetch_assoc();
    $Company = $row_company["company_name"];
} else {
   $Company= "Data no Founded";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGE</title>
    <style>
        /* General reset and styling */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%; /* Height set to 300% of viewport height */
        }

        /* Fixed header styles */
        header {
            display: flex;
            justify-content: flex-end; /* Align header content to the right */
            align-items: center;
            background-color: #FFCE33;
            padding: 10px 20px;
            color: red;
            position: fixed;
            top: 0;
            width: calc(100% - 270px); /* Adjust width to account for sidebar */
            z-index: 100; /* Ensure header is on top */
        }

        /* Input field in header */
        header input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        /* Icons container in header */
        header .icons {
            display: flex;
            align-items: center;
            margin-left: auto; /* Push icons to the right */
            margin-right: 50px; /* Space before the end */
        }

        /* Sidebar styles */
        .S-sidebar {
            height: 1200px;
            width: 270px;
            position: fixed;
            z-index: 1;
            left: 0; /* Sidebar is initially open */
            background-color: #2B597A; /* Navbar Color */
            display: flex;
            flex-direction: column;
            border-right: 0px solid #2B597A;
            /*  overflow-y: auto; Allow scrolling if content exceeds height */
        }

        /* Logo styles */
        .S-sidebar .logo img {
            width: 150px; /* Adjusted smaller size */
            height: auto; /* Maintain aspect ratio */
            margin-right: 5px; /* Optional: Adjust margin if needed */
        }

        /* Sidebar menu link styles */
        .S-sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 19px;
            font-family: Arial, sans-serif;
            color: white;
            display: flex;
            align-items: center; /* Vertical alignment for icons */
        }

        /* Hover effect on sidebar links */
        .S-sidebar a:hover {
            color: #1a1a1a; /* Text color on hover */
            background-color: #ffffff; /* Background color on hover */
            font-weight: bold; /* Bold font on hover */
        }

        /* Close button in sidebar */
        .S-sidebar .closebtn {
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 36px;
            color: #fff;
            cursor: pointer;
        }

        /* Quit button in sidebar */
        .S-sidebar .quit-btn {
            position: absolute;
            bottom: 20px;
            left: 40px;
            font-size: 18px;
            color: #2596be;
        }

        /* Open button style (hidden in this example) */
        .openbtn {
            display: none; /* Sidebar always visible, so hide the open button */
        }

        /* Additional utility classes (adjusted) */
        .S-py-4 {
            padding-top: 1rem;
            padding-bottom: 2.5rem;
        }

        .S-py-2 {
            padding-bottom: 1rem;
        }

        .S-tm-color-business2 {
            color: #FFCE33;
            font-family: Arial Black, sans-serif;
            margin-bottom: -25px;
            padding-left: 20px;
        }
 	.S-tm-color-warehouse {
            color: white;
            font-family: Arial Black, sans-serif;
            margin-bottom: 5px;
            padding-left: 20px;
        }

        .warehouse-text {
            color: white;
            padding-left: 20px;
            font-family: Arial, sans-serif;
        }

        /* Menu icon styles */
        .menu-icon {
            margin-right: 10px;
            font-size: 20px; /* Adjust icon size */
        }

        .icon-alert {
            padding: 5px;
        }

	 .menu-item {
        margin-top: 2rem; /* Ajusta este valor para el espaciado deseado */
    	}
        
    </style>
</head>
<body>

<?php


// Definir los menús según el rol

// Basic
$menuRole3 = [
    ['name' => 'Home', 'link' => 'home.php?n=1', 'icon' => './images/casa.png', 'title' => 'Home'],
    ['name' => 'Inventory', 'link' => './inventory/inventory.php?n=1', 'icon' => './images/product.png', 'title' => 'Inventory'],
    ['name' => 'Suppliers', 'link' => 'suppliers.php?n=1', 'icon' => './images/prove.png', 'title' => 'Suppliers'],
    ['name' => 'Reports', 'link' => 'report.php?n=1', 'icon' => './images/report20.png', 'title' => 'Reports'],
    ['name' => 'Help', 'link' => 'help.php?n=1', 'icon' => './images/help25.png', 'title' => 'Help'],
    ['name' => 'Alert', 'link' => 'alerts.php?n=1', 'icon' => './images/bell35_2.png', 'title' => 'Alert Inventory'],
    ['name' => 'User Profile', 'link' => './user/myProfile.php?n=1', 'icon' => './images/profile.png', 'title' => 'User Profile'],
    ['name' => 'Logout', 'link' => 'logout.php?n=1', 'icon' => './images/out25.png', 'title' => 'Close application']
];

// Manager
$menuRole2 = [
    ['name' => 'Home', 'link' => 'home.php?n=1', 'icon' => './images/casa.png', 'title' => 'Home'],
    ['name' => 'Inventory', 'link' => './inventory/inventory.php?n=1', 'icon' => './images/product.png', 'title' => 'Inventory'],
    ['name' => 'Warehouse', 'link' => 'warehouse.php?n=1', 'icon' => './images/panel.png', 'title' => 'Warehouse'],
    ['name' => 'Suppliers', 'link' => 'suppliers.php?n=1', 'icon' => './images/prove.png', 'title' => 'Suppliers'],
    ['name' => 'Reports', 'link' => 'report.php?n=1', 'icon' => './images/report20.png', 'title' => 'Reports'],
    ['name' => 'Help', 'link' => 'help.php?n=1', 'icon' => './images/help25.png', 'title' => 'Help'],
    ['name' => 'Alert', 'link' => 'alerts.php?n=1', 'icon' => './images/bell35_2.png', 'title' => 'Alert Inventory'],
    ['name' => 'User Profile', 'link' => './user/myProfile.php?n=1', 'icon' => './images/profile.png', 'title' => 'User Profile'],
    ['name' => 'Logout', 'link' => 'logout.php?n=1', 'icon' => './images/out25.png', 'title' => 'Close application']
];

// Admin
$menuRole1 = [
    ['name' => 'Home', 'link' => 'home.php?n=1', 'icon' => './images/casa.png', 'title' => 'Home'],
    ['name' => 'Inventory', 'link' => './inventory/inventory.php?n=1', 'icon' => './images/product.png', 'title' => 'Inventory'],
    ['name' => 'Suppliers', 'link' => 'suppliers.php?n=1', 'icon' => './images/prove.png', 'title' => 'Suppliers'],
    ['name' => 'Reports', 'link' => 'report.php?n=1', 'icon' => './images/report20.png', 'title' => 'Reports'],
    ['name' => 'Help', 'link' => 'help.php?n=1', 'icon' => './images/help25.png', 'title' => 'Help'],
    ['name' => 'Alert', 'link' => 'alerts.php?n=1', 'icon' => './images/bell35_2.png', 'title' => 'Alert Inventory'],
    ['name' => 'User Profile', 'link' => './user/myProfile.php?n=1', 'icon' => './images/profile.png', 'title' => 'User Profile'],
    ['name' => 'Control Panel', 'link' => 'controlPanel.php?n=1', 'icon' => './images/tools.png', 'title' => 'Tools'],
    ['name' => 'Logout', 'link' => 'logout.php?n=1', 'icon' => './images/out25.png', 'title' => 'Close application']
];

// Report
$menuRole4 = [
    ['name' => 'Home', 'link' => 'home.php?n=1', 'icon' => './images/casa.png', 'title' => 'Home'],
    ['name' => 'Reports', 'link' => 'report.php?n=1', 'icon' => './images/report20.png', 'title' => 'Reports'],
    ['name' => 'Help', 'link' => 'help.php?n=1', 'icon' => './images/help25.png', 'title' => 'Help'],
    ['name' => 'Alert', 'link' => 'alerts.php?n=1', 'icon' => './images/bell35_2.png', 'title' => 'Alert Inventory'],
    ['name' => 'User Profile', 'link' => './user/myProfile.php?n=1', 'icon' => './images/profile.png', 'title' => 'User Profile'],
    ['name' => 'Logout', 'link' => 'logout.php?n=1', 'icon' => './images/out25.png', 'title' => 'Close application'],
];

// Seleccionar el menú según el rol
if ( $_SESSION['Role']== "1") {
    $menu = $menuRole1;
} elseif ($_SESSION['Role'] == "2") {
    $menu = $menuRole2;
} elseif ($_SESSION['Role'] == "3") {
    $menu = $menuRole3;
} elseif ($_SESSION['Role'] == "4") {
    $menu = $menuRole4;
} else {
    // En caso de un rol no reconocido, manejar de acuerdo a los requerimientos
    $menu = []; // Otra acción o menú por defecto
}
?>

<div id="mySidebar" class="S-sidebar">
    <div>
        <div class="logo">
            <!-- Conditional logo based on 'n' parameter -->
            <?php if (isset($_GET['n']) && $_GET['n'] == '1'): ?>
                <img src="./images/logo3.png" alt="Warehouse Logo" title="Warehouse Logo">
		
            <?php endif; ?>
        </div>
        <div>
            <!-- Company name -->
            <h2 id="companyText" class="S-tm-color-business2 S-py-2 mb-5"><?php echo $Company; ?></h2>
            <!-- Warehouse text -->
       	
	<h3 class="S-tm-color-warehouse"><?php echo $f; ?></h3>
	    
        </div>
        <div class="S-py-1">
            <!-- Sidebar menu links -->
            <?php foreach ($menu as $item): ?>
                <div class="S-py-1">
                    <a href="<?php echo $item['link']; ?>" target="_parent" class="S-menu-link">
                        <span class="menu-icon"><img src="<?php echo $item['icon']; ?>" alt="<?php echo $item['title']; ?>" title="<?php echo $item['title']; ?>"></span>
                        <div class="S-py-1"><?php echo $item['name']; ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
 <p class="mt-5 warehouse-text">
    <?php echo "Version: " . $version; ?>
</p>
</div>

<!-- JavaScript for sidebar functionality -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Automatically open sidebar on page load
        openNav();

        // Select all menu links
        const menuLinks = document.querySelectorAll('.S-sidebar a');

        // Add mouseover event to each menu link
        menuLinks.forEach(function(menuLink) {
            menuLink.addEventListener('mouseover', function() {
                // Change text color and background on mouseover
                this.style.color = '#1a1a1a';
                this.style.backgroundColor = '#ffffff';
                this.style.fontWeight = 'bold';
            });

            // Mouseout event
            menuLink.addEventListener('mouseout', function() {
                this.style.color = '#ffffff';
                this.style.backgroundColor = 'transparent';
                this.style.fontWeight = 'normal';
            });
        });
    });

    // Function to open sidebar
    function openNav() {
        document.getElementById("mySidebar").style.left = "0";
        document.querySelector('.S-main-content').style.marginLeft = "270px"; // Adjusted width of sidebar
        document.querySelector('header').style.width = "calc(100% - 270px)"; // Adjusted width for header
        window.parent.postMessage({ action: 'openNav' }, '*');
    }

    // Function to close sidebar
    function closeNav() {
        // No need to close sidebar as it should always be open
    }
</script>

</body>
</html>
