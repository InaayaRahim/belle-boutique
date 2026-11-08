<?php

require_once "../includes/db_connect.php";

$sql = "SELECT 
            orders.order_id,
            customers.name,
            orders.bouquet_description,
            orders.quantity,
            orders.price,
            orders.order_date,
            orders.deadline,
            orders.status
        FROM orders
        INNER JOIN customers
        ON orders.customer_id = customers.customer_id
        ORDER BY orders.order_date DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belle Boutique | Orders</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="main-content">

        <header class="top-header">
            <h1>Orders</h1>
            <p>Manage bouquet orders.</p>
        </header>

        <section class="overview">

            <h2>All Orders</h2>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Bouquet Description</th>
                        <th>Quantity</th>
                        <th>Price (£)</th>
                        <th>Order Date</th>
                        <th>Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>

                        <?php while ($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td><?php echo htmlspecialchars($order['bouquet_description']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td>£<?php echo number_format($order['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['deadline'] ?? 'Not set'); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8">No orders found yet.</td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>

        </section>

    </div>

</body>
</html>