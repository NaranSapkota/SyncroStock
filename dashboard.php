<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        #S-main-content {
            margin-left: 270px; /* Ancho del sidebar */
            margin-top: 75px; /* Ajuste de margen superior */
            padding: 20px;
            color: #2596be;
            width: calc(100% - 270px); /* Ajuste del ancho del contenido principal */
        }

        .S-iframe-container {
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

        header {
            width: 100%;
            background: #f8f9fa;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        main {
            flex-grow: 1;
            padding: 50px;
        }

        .S-highlights {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
            justify-content: center;
        }

        .S-highlights button {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #2596be;
            border: none;
            border-radius: 5px;
            color: white;
        }

        .S-highlights button:hover {
            background-color: #F4D65E;
            transform: scale(1.05);
        }

        .S-charts {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .S-chart {
            flex: 1;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
        }

        .S-year-filter {
            margin-bottom: 10px;
        }

        .S-bar-chart {
            height: 200px;
            background: #e9ecef;
        }

        .S-table-container {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #2596be;
            color: white;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #2596be;
            text-align: center;
        }

        .S-summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .S-card {
            flex: 1;
            padding: 20px;
            color: white;
            border-radius: 5px;
        }

        .S-card.blue {
            background-color: #2596be;
        }

        .S-card.green {
            background-color: #2596be;
        }

        .S-card.red {
            background-color: #2596be;
        }

        .S-card.orange {
            background-color: #2596be;
        }

        .S-detailed-chart {
            background: #2596be;
            padding: 20px;
            border: 1px solid #2596be;
            color: white;
        }

        .S-time-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .S-time-filter button {
            padding: 5px 10px;
            cursor: pointer;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .S-bar-chart {
            height: 300px;
            background: #e9ecef;
        }
    </style>

    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="S-iframe-container">
        <iframe id="iframe1" src="navbar.php?n=1"></iframe>
    </div>

    <main id="S-main-content">
        <div class="S-highlights">
            <button>Highlight 1</button>
            <button>Highlight 2</button>
            <button>Highlight 3</button>
            <button>Highlight 4</button>
        </div>
        <div class="S-charts">
            <div class="S-chart">
                <h2>Purchase & Sales</h2>
                <div class="S-year-filter">
                    <label for="year">Year</label>
                    <select id="year">
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                </div>
                <div class="S-bar-chart" id="purchase-sales"></div>
            </div>
            <div class="S-chart">
                <h2>Stock Levels</h2>
                <div class="S-year-filter">
                    <label for="year">Year</label>
                    <select id="year">
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                </div>
                <div class="S-bar-chart" id="stock-levels"></div>
            </div>
        </div>
        <div class="S-table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Nickname</th>
                        <th>Employee</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Row -->
                    <tr>
                        <td>Giada Mile</td>
                        <td>40</td>
                        <td>Peldi</td>
                        <td><input type="checkbox" checked></td>
                    </tr>
                    <tr>
                        <td>Giandomeo Guilizzoni</td>
                        <td>38</td>
                        <td>Potato</td>
                        <td><input type="checkbox"></td>
                    </tr>
                    <!-- More rows -->
                </tbody>
            </table>
        </div>
        <div class="S-summary-cards">
            <div class="S-card blue">
                <h3>Total Products</h3>
                <p>Data</p>
            </div>
            <div class="S-card green">
                <h3>Most Stock Products</h3>
                <p>Data</p>
            </div>
            <div class="S-card red">
                <h3>Out of Stock Products</h3>
                <p>Data</p>
            </div>
            <div class="S-card orange">
                <h3>Low Stock Products</h3>
                <p>Data</p>
            </div>
        </div>
        <div class="S-detailed-chart">
            <h2>Detailed Analysis</h2>
            <div class="S-time-filter">
                <button>1d</button>
                <button>1W</button>
                <button>1M</button>
                <button>3M</button>
                <button>All</button>
            </div>
            <div class="S-bar-chart" id="detailed-analysis"></div>
        </div>
    </main>

    <script>
        window.addEventListener('message', function (event) {
            if (event.data.action === 'openNav') {
                document.getElementById("S-main-content").style.marginLeft = "270px";
                document.getElementById("S-main-content").style.width = "calc(100% - 270px)";
            } else if (event.data.action === 'closeNav') {
                // No hay acción para cerrar el sidebar ya que siempre está abierto
            }
        });
    </script>
</body>

</html>
