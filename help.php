<?php   
$session_duration = 3600; // 1 hour in seconds
$session_path = '/'; // Adjust according to your application's needs

// Set session cookie parameters
session_set_cookie_params($session_duration, $session_path);

// Start or resume session
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synchro Stock FAQ</title>
    <link rel="icon" href="./images/company/syncrostock.ico" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            width: 100%;
            background: #f8f9fa;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #S-main-content {
            margin-left: 270px;
            padding: 20px;
            color: #2596be;
            max-width: 1500px;
            margin: 0 auto;
            transition: none;
            margin-top: 20px;
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

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 30px;
        }

        .faq-item {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
            transition: background-color 0.3s ease;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-item:hover {
            background-color: #f9f9f9;
        }

        .faq-question {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #274c70;
        }

        .faq-answer {
            font-size: 16px;
            color: #333;
            max-height: 200px; /* Adjust as needed */
            overflow-y: auto; /* Enable vertical scroll */
            padding-right: 15px; /* Compensate for scrollbar space */
        }

        .faq-answer p {
            margin-bottom: 10px;
        }

        .faq-answer a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .faq-answer a:hover {
            color: #0056b3;
        }

        .faq-answer strong {
            font-weight: bold;
            color: #274c70;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #274c70;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="S-iframe-container">
        <iframe id="iframe1" src="navbar.php?m=1"></iframe>
    </div>

    <div id="S-main-content">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p>Welcome to the FAQ section of the Synchro Stock system. Here, you'll find answers to some of the most commonly asked questions about our inventory management system. If you have any other questions or need further assistance, please feel free to reach out to our support team.</p>

            <div class="faq-item">
                <div class="faq-question">1. How does the inventory system manage product inventory?</div>
                <div class="faq-answer">
                    <p>The inventory system tracks product inventory across multiple warehouses, allowing for real-time
                        updates and stock management.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">2. Can the system transfer inventory between warehouses?</div>
                <div class="faq-answer">
                    <p>Yes, the system supports transfer of inventory between warehouses, facilitating efficient
                        distribution and stock balancing.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">3. How does the system handle adding and deleting users?</div>
                <div class="faq-answer">
                    <p>The system allows administrators to add and delete users with specified roles and permissions,
                        ensuring secure access control.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">4. How can the system generate reports?</div>
                <div class="faq-answer">
                    <p>The system generates comprehensive reports on inventory levels, sales trends, supplier
                        performance, and more, aiding in strategic decision-making.</p>
                </div>
            </div>

            <!--
            <div class="faq-item">
                <div class="faq-question">6. Can the system automate replenishment of low-stock items?</div>
                <div class="faq-answer">
                    <p>Yes, the system automates replenishment by setting reorder points and generating purchase orders
                        based on inventory thresholds.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">7. How does the system handle order management?</div>
                <div class="faq-answer">
                    <p>The system efficiently manages orders from creation to fulfillment, tracking order status and
                        ensuring accurate inventory allocation.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">8. What tools does the system offer for inventory tracking and auditing?</div>
                <div class="faq-answer">
                    <p>The system provides tools for real-time inventory tracking, auditing, and reconciliation of stock
                        movements across warehouses.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">9. How does the system handle compliance with regulatory requirements?</div>
                <div class="faq-answer">
                    <p>The system ensures compliance with regulatory standards by implementing checks, maintaining audit
                        trails, and securing data access.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">10. Can the system integrate with barcode or RFID systems?</div>
                <div class="faq-answer">
                    <p>Yes, the system integrates with barcode and RFID systems for efficient inventory tracking,
                        reducing manual errors and improving accuracy.</p>
                </div>
            </div>
            -->
        </div>
    </div>
</body>

</html>
