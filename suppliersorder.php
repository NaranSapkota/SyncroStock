
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Order</title>
    <style>
        /* Reset and basic styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f0f0f0;
      
        }

        .iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        #iframe1 {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            padding: 20px;
            flex: 1;
            /* Fill remaining space */
            background-color: #ffffff;
            /* Added background color */
            border-left: 1px solid #ddd;
            /* Added a left border */
            overflow: auto;
            /* Enable scrolling if content exceeds viewport */
         
            border-radius: 0 10px 10px 0;
            /* Rounded corners on the right side */
        }

        /* Header section */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border-radius: 10px 0 0 10px;
            /* Rounded corners on the left side */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Added shadow for depth */
            z-index: 1;
            /* Ensure header is above content */
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
            /* Accent color for logo */
        }

        .search-bar input {
            width: 200px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
    
        }

        .search-bar input:focus {
            border-color: #007bff;
            outline: none;
        }

        .icons {
            display: flex;
            align-items: center;
        }

        .notification-icon,
        .profile-icon {
            font-size: 1.5em;
            margin-left: 20px;
            cursor: pointer;
      
        }

        .notification-icon:hover,
        .profile-icon:hover {
            color: #007bff;
        }

        /* Sidebar styling */
        aside {
            width: 200px;
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            /* Rounded corners on the left side */
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            /* Added shadow for depth */
            z-index: 1;
            /* Ensure sidebar is above content */
        }

        .menu {
            list-style-type: none;
            padding: 0;
        }

        .menu li {
            margin: 10px 0;
            cursor: pointer;
     
            padding: 8px 16px;
            /* Increased padding for better touch area */
            border-radius: 4px;
            /* Rounded corners for menu items */
        }

        .menu li:hover {
            background-color: #e9ecef;
            color: #007bff;
        }

        .menu li.active {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }

        /* Main content section */
        main {
            flex: 1;
            padding: 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #777;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
 
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        h1 {
            margin-top: 0;
        }

        /* Toolbar and action buttons */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 10px 20px;
            background-color: #2596be; /* Changed background color to #2596be */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        
        }

        .filter-btn:hover {
            background-color: #f4d65e; /* Changed hover color to #f4d65e */
        }

        .search-box {
            display: flex;
            align-items: center;
        }

        .search-box input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
        
        }

        .search-box input:focus {
            border-color: #007bff;
            outline: none;
        }

        .search-box button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
    
        }

        .search-box button:hover {
            background-color: #0056b3;
        }

        /* Tabs for different order statuses */
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #f1f1f1;
            border: none;
            border-radius: 4px;
            cursor: pointer;
       
        }

        .tab:hover,
        .tab.active {
            background-color: #007bff;
            color: white;
        }

        /* Cards displaying order information */
        .order-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Added gap between order cards */
            justify-content: flex-start;
            /* Align cards to the start */
        }

        .order-card {
            width: calc(33.333% - 20px);
            margin-bottom: 20px;
            /* Added margin bottom for spacing */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2596be; /* Changed background color to #2596be */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
       
            overflow: hidden;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .order-info {
            flex: 1;
        }

        .order-info h2 {
            margin: 0 0 10px;
            color: #ffffff; /* Changed text color to white */
        }

        .order-info p {
            margin: 5px 0;
            color: #ffffff; /* Changed text color to white */
        }

        .order-status {
            font-size: 1.2em;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
            min-width: 80px;
        }

        .order-status.green {
            background-color: #d4edda;
            color: #155724;
        }

        .order-status.red {
            background-color: #f8d7da;
            color: #721c24;
        }

        .order-status.orange {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>

<div class="iframe-container">
        <iframe id="iframe1" src="navbar.php?n=1"></iframe>
    </div>

    <div class="main-content" id="mainContent">

        <header>
            <div class="logo">Supplier Order</div>
            <div class="icons">
                <div class="notification-icon">🔔</div>
                <div class="profile-icon">
                </div>
            </div>
        </header>

        <main>
            <nav class="breadcrumb">
                <a href="#">Home</a> > <a href="#">Manage Order</a> > Supplier Order
            </nav>

            <div class="toolbar">
                <button class="filter-btn">Filter</button>
                <div class="search-box">
                    <input type="text" placeholder="Search Orders">
                    <button class="search-btn">🔍</button>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active">All</button>
                <button class="tab">Open</button>
                <button class="tab">Booked</button>
                <button class="tab">Deleted</button>
            </div>

            <div class="order-cards">
                <div class="order-card">
                    <div class="order-info">
                        <h2>Robby Anderson</h2>
                        <p>Projected Points: 21.46</p>
                        <p>Bye Week: 6</p>
                        <p>% Started: 57%</p>
                    </div>
                    <div class="order-status green">16.90</div>
                </div>
                <div class="order-card">
                    <div class="order-info">
                        <h2>Malcolm Brown</h2>
                        <p>Projected Points: 21.46</p>
                        <p>Bye Week: 6</p>
                        <p>% Started: 57%</p>
                    </div>
                    <div class="order-status red">4.50</div>
                </div>
                <div class="order-card">
                    <div class="order-info">
                        <h2>Eagles</h2>
                        <p>Projected Points: 21.46</p>
                        <p>Bye Week: 6</p>
                        <p>% Started: 57%</p>
                    </div>
                    <div class="order-status orange">7.89</div>
                </div>
                <!-- Repeat for other orders -->
            </div>
        </main>

        <!-- JavaScript for sidebar navigation -->
        <script>
            function openNav() {
                window.parent.postMessage({ action: 'openNav' }, '*');
            }

            window.addEventListener('message', function (event) {
                if (event.data.action === 'openNav') {
                    document.getElementById("mainContent").style.marginLeft = "250px";
                    document.getElementById("mainContent").style.width = "calc(100% - 250px)";
                } else if (event.data.action === 'closeNav') {
                    document.getElementById("mainContent").style.marginLeft = "0";
                    document.getElementById("mainContent").style.width = "100%";
                }
            });
        </script>
    </div>

</body>

</html>