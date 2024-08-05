<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Alerts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            margin-top: 80px;
        }

        .S-iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        #iframe1 {
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
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        .alert-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 1px solid #ddd;
            z-index: 3;
        }

        .alert-heading {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
            z-index: 2;
        }

        .alert-section {
            margin-bottom: 40px;
        }

        .alert-section h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .alert {
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            font-size: 18px;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 8px solid #28a745;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 8px solid #dc3545;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert i {
            margin-right: 15px;
            font-size: 24px;
        }

        .alert-success i {
            color: #28a745;
        }

        .alert-danger i {
            color: #dc3545;
        }

        .alert p {
            margin: 0;
        }

        .empty-message {
            text-align: center;
            font-size: 20px;
            color: #6c757d;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div>
        <div class="S-iframe-container">
            <iframe id="iframe1" src="navbar.php?n=1"></iframe>
        </div>

        <div class="S-main-content" id="mainContent">
            <div class="alert-container">
                <h2 class="alert-heading">Synchro Alerts</h2>

                <div class="alert-section" id="product-alerts-section">
                    <h3>Product Alerts</h3>
                    <div id="product-alerts-content">
                        <!-- Product alerts will be injected here by JavaScript -->
                    </div>
                </div>

                <div class="alert-section" id="order-alerts-section">
                    <h3>Order Alerts</h3>
                    <div id="order-alerts-content">
                        <!-- Order alerts will be injected here by JavaScript -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (Notification.permission !== 'granted') {
                Notification.requestPermission();
            }
        });

        function showNotification(message) {
            if (Notification.permission === 'granted') {
                new Notification('Inventory Alert', {
                    body: message,
                    icon: 'https://cdn-icons-png.flaticon.com/512/330/330189.png'
                });
            }
        }

        function getPreviousAlerts() {
            const previousAlerts = localStorage.getItem('previousAlerts');
            return previousAlerts ? new Set(JSON.parse(previousAlerts)) : new Set();
        }

        function savePreviousAlerts(alerts) {
            localStorage.setItem('previousAlerts', JSON.stringify(Array.from(alerts)));
        }

        let previousAlerts = getPreviousAlerts();

        function fetchAlerts() {
            $.ajax({
                url: 'fetch_alerts.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    const productAlertsContent = $('#product-alerts-content');
                    const orderAlertsContent = $('#order-alerts-content');

                    if (data.productAlerts.length === 0) {
                        productAlertsContent.html('<div class="alert alert-success" role="alert"><i class="fas fa-check-circle"></i><p class="empty-message">All products are sufficiently stocked.</p></div>');
                    } else {
                        let productAlertsHtml = '';
                        data.productAlerts.forEach(alert => {
                            productAlertsHtml += `
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p>${alert}</p>
                                </div>
                            `;

                            if (!previousAlerts.has(alert)) {
                                showNotification(alert);
                                previousAlerts.add(alert);
                            }
                        });
                        productAlertsContent.html(productAlertsHtml);
                    }

                    if (data.orderAlerts.length === 0) {
                        orderAlertsContent.html('<div class="alert alert-success" role="alert"><i class="fas fa-check-circle"></i><p class="empty-message">No orders older than 5 days.</p></div>');
                    } else {
                        let orderAlertsHtml = '';
                        data.orderAlerts.forEach(alert => {
                            orderAlertsHtml += `
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p>${alert}</p>
                                </div>
                            `;

                            if (!previousAlerts.has(alert)) {
                                showNotification(alert);
                                previousAlerts.add(alert);
                            }
                        });
                        orderAlertsContent.html(orderAlertsHtml);
                    }

                    savePreviousAlerts(previousAlerts);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching alerts:', error);
                }
            });
        }

        fetchAlerts();
        setInterval(fetchAlerts, 1000);

        window.addEventListener('message', function (event) {
            if (event.data.action === 'openNav') {
                document.getElementById("mainContent").style.marginLeft = "270px";
               
                document.getElementById("mainContent").style.width = "calc(100% - 270px)";
            } else if (event.data.action === 'closeNav') {
                document.getElementById("mainContent").style.marginLeft = "0";
                document.getElementById("mainContent").style.width = "100%";
            }
        });
    </script>
</body>
</html>
