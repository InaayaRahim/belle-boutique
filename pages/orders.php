<?php

session_start();

require_once "../includes/db_connect.php";

// Generate a CSRF token if one doesn't already exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

$queryError = false;

try {
    $result = $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    error_log("Orders query failed: " . $e->getMessage());
    $queryError = true;
    $result = null;
}

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

    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

        <header class="top-header">
            <h1>Orders</h1>
            <p>Manage bouquet orders.</p>

            <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                <p class="success-message">
                    Order added successfully!
                </p>
            <?php elseif (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                <p class="success-message">
                    Order updated successfully!
                </p>
            <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                <p class="success-message">
                    Order deleted successfully!
                </p>
            <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] == 0): ?>
                <p class="success-message">
                    No order was deleted.
                </p>
            <?php endif; ?>
        </header>

        <section class="overview">
            <div class="section-header">
                <h2>All Orders</h2>

                <a href="add_order.php" class="primary-button">
                    + Add New Order
                </a>
            </div>

            <div class="order-filters">

                <input
                    type="text"
                    id="orderSearch"
                    placeholder="Search by customer or bouquet..."
                >

                <select id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>

            </div>

            <div class="table-container">

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
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($queryError): ?>

                            <tr>
                                <td colspan="9">
                                    Unable to load orders right now. Please try again later.
                                </td>
                            </tr>

                        <?php elseif ($result->num_rows > 0): ?>

                            <?php while ($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['bouquet_description']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                    <td>£<?php echo number_format($order['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                    <td><?php echo htmlspecialchars($order['deadline'] ?? 'Not set'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">

                                            <a
                                                href="edit_order.php?id=<?php echo $order['order_id']; ?>"
                                                class="action-button edit-button"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="delete_order.php"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this order?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="order_id"
                                                    value="<?php echo $order['order_id']; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="delete_order"
                                                    class="action-button delete-button"
                                                >
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="9">No orders found yet.</td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </section>
        </div> <!-- End of main-content -->
    </div>

    <script src="../assets/javascript/orders.js"></script>
</body>
</html>