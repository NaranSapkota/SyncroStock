<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Alineación ajustada a la derecha */
            background-color: #ffffff;
        }

        #S-iframe-container {
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

        /* Main content area */
        #S-main-content {
            margin-left: 275px; 
            padding: 20px;
            flex: 1;
            width: calc(100% - 275px);
            background-color: #fff;
            margin-top: 60px;
            max-width: 1200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        header {
            background-color: #333333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .S-logo {
            font-size: 1.5em;
        }

        .S-search-bar {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .S-search-bar input {
            padding: 8px;
            width: 200px;
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .S-search-bar input:focus {
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        .S-search-btn {
            padding: 8px 12px;
            background-color: #333333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .S-search-btn:hover {
            background-color: #555555;
        }

        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #777777;
        }

        .S-breadcrumbs a {
            text-decoration: none;
            color: #2596be;
        }

        .S-breadcrumbs a:hover {
            text-decoration: underline;
        }

        .S-action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .S-action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .S-create {
            background-color: #2596be;
            color: white;
        }

        .S-edit {
            background-color: #007bff;
            color: white;
        }

        .S-delete {
            background-color: #dc3545;
            color: white;
        }

        .S-filter {
            margin-bottom: 20px;
        }

        .S-filter label {
            font-weight: bold;
            margin-right: 10px;
            color: #2596be;
        }

        .S-filter select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .S-filter select:focus {
            outline: none;
            border-color: #2596be;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .S-table-container {
            overflow-x: auto;
        }

        .S-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .S-thead {
            background: #2596be;
            color: white;
        }

        .S-th,
        .S-td {
            padding: 12px;
            border: 1px solid #2596be;
            text-align: center;
        }

        .S-pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .S-pagination button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .S-pagination button:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>

<body>

    <div id="S-iframe-container">
        <iframe id="S-iframe1" src="navbar.php?n=1"></iframe>
    </div>

    <div id="S-main-content">
        <!-- Main content here -->

       <div>
                 <div class="S-breadcrumb">
                    <a href="#">Home</a><span>/</span>
                    <span>Manage Supplier</span>
                </div>
            
                <h1>Inventory</h1>

        <div class="S-action-buttons">
            <button class="S-create">Create</button>
            <button class="S-edit">Edit</button>
            <button class="S-delete">Delete</button>
        </div>

        <div class="S-filter">
            <label for="S-filter">Filter by:</label>
            <select id="S-filter">
                <option>SKU</option>
                <option>Date</option>
                <option>Name</option>
            </select>
        </div>

        <div class="S-table-container">
            <table class="S-table">
                <thead class="S-thead">
                    <tr>
                        <th class="S-th">Name</th>
                        <th class="S-th">Age</th>
                        <th class="S-th">Nickname</th>
                        <th class="S-th">Employee</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="S-td">Giada Mile</td>
                        <td class="S-td">36</td>
                        <td class="S-td">Peldi</td>
                        <td class="S-td"><input type="checkbox" checked></td>
                    </tr>
                    <tr>
                        <td class="S-td">Giandomeo Guilizzoni</td>
                        <td class="S-td">34</td>
                        <td class="S-td">Potato</td>
                        <td class="S-td"><input type="checkbox"></td>
                    </tr>
                    <!-- More rows -->
                </tbody>
            </table>
        </div>

        <div class="S-pagination">
            <button>&lt;&lt;</button>
            <button>&lt;</button>
            <button>1</button>
            <button>2</button>
            <!-- More page numbers -->
            <button>&gt;</button>
            <button>&gt;&gt;</button>
        </div>
    </div>

    <script>
        function openNav() {
            const mainContent = document.getElementById("S-main-content");
            mainContent.style.marginLeft = "275px"; /* Ancho del sidebar + 20px de margen */
            mainContent.style.width = "calc(100% - 275px)";
        }

        function closeNav() {
            const mainContent = document.getElementById("S-main-content");
            mainContent.style.marginLeft = "0";
            mainContent.style.width = "100%";
        }

        window.addEventListener('message', function (event) {
            if (event.data.action === 'openNav') {
                openNav();
            } else if (event.data.action === 'closeNav') {
                closeNav();
            }
        });
    </script>

</body>

</html>
