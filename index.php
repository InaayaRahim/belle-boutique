<?php
require_once "includes/db_connect.php";

// Count all orders
$totalOrdersQuery = "SELECT COUNT(*) AS total FROM orders";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['total'];

// Count pending orders
$pendingOrdersQuery = "SELECT COUNT(*) AS total FROM orders WHERE status = 'Pending'";
$pendingOrdersResult = $conn->query($pendingOrdersQuery);
$pendingOrders = $pendingOrdersResult->fetch_assoc()['total'];

// Count low stock items
$lowStockQuery = "SELECT COUNT(*) AS total FROM inventory
                  WHERE quantity_in_stock <= low_stock_threshold";
$lowStockResult = $conn->query($lowStockQuery);
$lowStockItems = $lowStockResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belle Boutique | Dashboard</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <header class="top-header">
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome back, Admin!</p>
                </div>
            </header>

            <section class="overview">
                <h2>Business Overview</h2>

                <div class="summary-cards">
                    <div class="summary-card">
                        <h3>Total Orders</h3>
                        <p><?php echo $totalOrders; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Pending Orders</h3>
                        <p><?php echo $pendingOrders; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Low Stock Items</h3>
                        <p><?php echo $lowStockItems; ?></p>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>