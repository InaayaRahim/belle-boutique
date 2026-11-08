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

// Get order counts for the status chart
$orderStatusQuery = "
    SELECT status, COUNT(*) AS total
    FROM orders
    GROUP BY status
";

$orderStatusResult = $conn->query($orderStatusQuery);

$orderStatusCounts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Completed' => 0
];

while ($row = $orderStatusResult->fetch_assoc()) {
    $orderStatusCounts[$row['status']] = $row['total'];
}

// Count low stock items
$lowStockQuery = "SELECT COUNT(*) AS total FROM inventory
                  WHERE quantity_in_stock <= low_stock_threshold";
$lowStockResult = $conn->query($lowStockQuery);
$lowStockItems = $lowStockResult->fetch_assoc()['total'];

// Get the five most recent orders
$recentOrdersQuery = "
    SELECT orders.order_id,
           customers.name AS customer_name,
           orders.bouquet_description,
           orders.price,
           orders.order_date,
           orders.status
    FROM orders
    INNER JOIN customers
        ON orders.customer_id = customers.customer_id
    ORDER BY orders.order_date DESC, orders.order_id DESC
    LIMIT 5
";

$recentOrdersResult = $conn->query($recentOrdersQuery);

// Get materials that are low in stock
$lowStockOverviewQuery = "
    SELECT item_name, colour, quantity_in_stock, unit, low_stock_threshold
    FROM inventory
    WHERE quantity_in_stock <= low_stock_threshold
    ORDER BY quantity_in_stock ASC
";

$lowStockOverviewResult = $conn->query($lowStockOverviewQuery);
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

            <section class="overview dashboard-section">
                <h2>Order Status Overview</h2>

                <div class="order-status-chart">

                    <div class="chart-item">
                        <div class="chart-label">
                            <span>Pending</span>
                            <strong>
                                <?php echo $orderStatusCounts['Pending']; ?> orders
                                (<span class="chart-percentage">0%</span>)
                            </strong>
                        </div>
                        <div class="chart-bar">
                            <div class="chart-fill pending-fill"
                                data-status="Pending"></div>
                        </div>
                    </div>

                    <div class="chart-item">
                        <div class="chart-label">
                            <span>In Progress</span>
                            <strong>
                                <?php echo $orderStatusCounts['In Progress']; ?> orders
                                (<span class="chart-percentage">0%</span>)
                            </strong>
                        </div>
                        <div class="chart-bar">
                            <div class="chart-fill progress-fill"
                                data-status="In Progress"></div>
                        </div>
                    </div>

                    <div class="chart-item">
                        <div class="chart-label">
                            <span>Completed</span>
                            <strong>
                                <?php echo $orderStatusCounts['Completed']; ?> orders
                                (<span class="chart-percentage">0%</span>)
                            </strong>
                        </div>
                        <div class="chart-bar">
                            <div class="chart-fill completed-fill"
                                data-status="Completed"></div>
                        </div>
                    </div>

                </div>
            </section>

            <section class="overview dashboard-section">

                <div class="section-header">
                    <h2>Recent Orders</h2>
                    <a href="pages/orders.php" class="primary-button">View All Orders</a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Bouquet</th>
                                <th>Price</th>
                                <th>Order Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($recentOrdersResult->num_rows > 0): ?>

                                <?php while ($order = $recentOrdersResult->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>

                                        <td>
                                            <?php echo htmlspecialchars($order['customer_name']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($order['bouquet_description']); ?>
                                        </td>

                                        <td>
                                            £<?php echo number_format($order['price'], 2); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($order['order_date']); ?>
                                        </td>

                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6">No orders have been added yet.</td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>

            <section class="overview dashboard-section">

                <div class="section-header">
                    <h2>Low Stock Overview</h2>
                    <a href="pages/inventory.php" class="primary-button">View Inventory</a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Colour</th>
                                <th>Quantity Remaining</th>
                                <th>Low Stock Threshold</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($lowStockOverviewResult->num_rows > 0): ?>

                                <?php while ($item = $lowStockOverviewResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>

                                        <td><?php echo htmlspecialchars($item['colour'] ?? ''); ?></td>

                                        <td>
                                            <?php echo htmlspecialchars($item['quantity_in_stock']); ?>
                                            <?php echo htmlspecialchars($item['unit']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($item['low_stock_threshold']); ?>
                                            <?php echo htmlspecialchars($item['unit']); ?>
                                        </td>

                                        <td>
                                            <span class="stock-badge stock-low">Low Stock</span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5">All materials are sufficiently stocked!</td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>


            <section class="overview dashboard-section">

                <h2>Quick Actions</h2>

                <div class="quick-actions">

                    <a href="pages/add_order.php" class="quick-action">
                        <span>+</span>
                        <h3>Add New Order</h3>
                        <p>Record a new customer order.</p>
                    </a>

                    <a href="pages/add_inventory.php" class="quick-action">
                        <span>+</span>
                        <h3>Add Inventory</h3>
                        <p>Add new materials or update your stock list.</p>
                    </a>

                    <a href="pages/material_usage.php" class="quick-action">
                        <span>+</span>
                        <h3>Record Material Usage</h3>
                        <p>Record materials used and update inventory.</p>
                    </a>

                </div>

            </section>
        </main>
    </div>


    <script>
        const orderCounts = {
            pending: <?php echo (int)$orderStatusCounts['Pending']; ?>,
            progress: <?php echo (int)$orderStatusCounts['In Progress']; ?>,
            completed: <?php echo (int)$orderStatusCounts['Completed']; ?>
        };

        const total = Object.values(orderCounts)
            .reduce((sum, count) => sum + count, 0);

        document.querySelectorAll('.chart-fill').forEach(bar => {
            const status = bar.dataset.status;

            let count = 0;

            if (status === 'Pending') count = orderCounts.pending;
            if (status === 'In Progress') count = orderCounts.progress;
            if (status === 'Completed') count = orderCounts.completed;

            const percentage = total > 0
                ? (count / total) * 100
                : 0;

            bar.style.width = percentage + '%';

            const percentageLabel = bar
                .closest('.chart-item')
                .querySelector('.chart-percentage');

            percentageLabel.textContent =
                percentage.toFixed(1) + '%';
        });
    </script>
</body>
</body>
</html>