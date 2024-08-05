<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f8f9fa;
}

.logo {
    font-size: 1.5em;
    font-weight: bold;
}

.search-bar input {
    width: 200px;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.icons {
    display: flex;
    align-items: center;
}

.notification-icon, .profile-icon {
    font-size: 1.5em;
    margin-left: 20px;
}

.container {
    display: flex;
}

aside {
    width: 200px;
    background-color: #f1f1f1;
    padding: 20px;
}

.menu {
    list-style-type: none;
    padding: 0;
}

.menu li {
    margin: 10px 0;
    cursor: pointer;
}

.menu li.active {
    font-weight: bold;
}

main {
    flex: 1;
    padding: 20px;
}

.breadcrumb {
    margin-bottom: 20px;
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

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.toolbar input {
    width: 60%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.toolbar button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.toolbar button:hover {
    background-color: #0056b3;
}

.table-container {
    overflow-x: auto;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.order-table th, .order-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.order-table th {
    background-color: #f2f2f2;
}

.pagination {
    display: flex;
    justify-content: center;
}

.pagination button {
    padding: 10px;
    margin: 0 5px;
    border: 1px solid #ccc;
    background-color: white;
    cursor: pointer;
}

.pagination button:hover {
    background-color: #f2f2f2;
}

    </style>
    <link rel="stylesheet" href="manage_order.css">
</head>
<body>
    <header>
        <div class="logo">Company Logo</div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
        </div>
        <div class="icons">
            <span class="notification-icon">🔔</span>
            <span class="profile-icon">👤</span>
        </div>
    </header>
    <div class="container">
        <aside>
            <ul class="menu">
                <li>Dashboard</li>
                <li>dolor sit amet</li>
                <li>Integer nec odio</li>
                <li>Lorem ipsum</li>
                <li class="active">Item A</li>
                <li>Praesent libero</li>
            </ul>
        </aside>
        <main>
            <div class="order-section">
                <nav class="breadcrumb">
                    <a href="#">Home</a> > Manage Order
                </nav>
                <h1>Manage Order</h1>
                <div class="stats">
                    <div class="stat open-orders">
                        <h2>Open Orders</h2>
                        <p>7,156</p>
                        <p>+4% last month</p>
                    </div>
                    <div class="stat booked-orders">
                        <h2>Booked Orders</h2>
                        <p>514</p>
                        <p>+2% last month</p>
                    </div>
                    <div class="stat deleted-orders">
                        <h2>Deleted Orders</h2>
                        <p>317</p>
                        <p>-12% last month</p>
                    </div>
                </div>
                <div class="toolbar">
                    <input type="text" placeholder="Search Orders by #, status, date, etc.">
                    <button class="search-btn">🔍</button>
                    <button class="create">+ Create New Order</button>
                </div>
                <div class="table-container">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Order #</th>
                                <th>City / State</th>
                                <th>Shipping Company</th>
                                <th>Last Updated At</th>
                                <th>Sales Person</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>67564664</td>
                                <td>Chicago, IL</td>
                                <td>UPS</td>
                                <td>Nov 11th, 2019, 6:00 AM EST</td>
                                <td>Carlson</td>
                                <td>Nov 11th, 2019</td>
                                <td>Dec 21st, 2019</td>
                                <td>Open</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>123646</td>
                                <td>Smalltown, ME</td>
                                <td>FedEx</td>
                                <td>Nov 11th, 2019, 6:01 AM EST</td>
                                <td>Carlson</td>
                                <td>Dec 11th, 2019</td>
                                <td>Dec 21st, 2019</td>
                                <td>Open</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>087642</td>
                                <td>Aurora, IL</td>
                                <td>DHL</td>
                                <td>Nov 11th, 2019, 6:02 AM EST</td>
                                <td>Barond</td>
                                <td>Dec 11th, 2019</td>
                                <td>Dec 21st, 2019</td>
                                <td>Open</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>2456832</td>
                                <td>Tempe, AZ</td>
                                <td>UPS</td>
                                <td>Nov 11th, 2019, 6:03 AM EST</td>
                                <td>Carlson</td>
                                <td>Nov 11th, 2019</td>
                                <td>Nov 21st, 2019</td>
                                <td>Booked</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>3464536</td>
                                <td>Phoenix, AZ</td>
                                <td>FedEx</td>
                                <td>Nov 11th, 2019, 6:04 AM EST</td>
                                <td>Trent Jr.</td>
                                <td>Oct 11th, 2019</td>
                                <td>Oct 21st, 2019</td>
                                <td>Open</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>9762352</td>
                                <td>Dallas, TX</td>
                                <td>DHL</td>
                                <td>Nov 11th, 2019, 6:05 AM EST</td>
                                <td>Trent Jr.</td>
                                <td>Jan 11th, 2020</td>
                                <td>Jan 21st, 2020</td>
                                <td>Deleted</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>8764236</td>
                                <td>Palo Alto, CA</td>
                                <td>FedEx</td>
                                <td>Nov 11th, 2019, 6:06 AM EST</td>
                                <td>Barond</td>
                                <td>Nov 11th, 2019</td>
                                <td>Nov 21st, 2019</td>
                                <td>Booked</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button>&lt;&lt;</button>
                    <button>&lt;</button>
                    <button>1</button>
                    <button>2</button>
                    <button>3</button>
                    <button>4</button>
                    <button>5</button>
                    <button>6</button>
                    <button>7</button>
                    <button>8</button>
                    <button>9</button>
                    <button>10</button>
                    <button>&gt;</button>
                    <button>&gt;&gt;</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
