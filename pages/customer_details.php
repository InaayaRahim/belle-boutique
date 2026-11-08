<?php

require_once "../includes/db_connect.php";

// Get the customer ID from the URL
$customerId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$customerId || $customerId < 1) {
    die("Invalid customer ID.");
}

// Retrieve customer details
$sql = "SELECT * FROM customers WHERE customer_id = ?";

$statement = $conn->prepare($sql);
$statement->bind_param("i", $customerId);
$statement->execute();

$result = $statement->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Customer not found.");
}

// Retrieve all orders belonging to this customer
$orderSql = "SELECT * FROM orders
             WHERE customer_id = ?
             ORDER BY order_date DESC";

$orderStatement = $conn->prepare($orderSql);
$orderStatement->bind_param("i", $customerId);
$orderStatement->execute();

$orders = $orderStatement->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Customer Details</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1><?php echo htmlspecialchars($customer['name']); ?></h1>
                <p>Customer details and order history.</p>
            </header>

            <section class="overview">

                <h2>Contact Information</h2>

                <div class="summary-cards">

                    <div class="summary-card">
                        <h3>Email</h3>
                        <p class="contact-detail">
                            <?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?>
                        </p>
                    </div>

                    <div class="summary-card">
                        <h3>Phone</h3>
                        <p class="contact-detail">
                            <?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?>
                        </p>
                    </div>

                    <div class="summary-card">
                        <h3>Total Orders</h3>
                        <p><?php echo $orders->num_rows; ?></p>
                    </div>

                </div>

            </section>

            <section class="overview">

                <h2>Order History</h2>

                <div class="table-container">
                    <table>

                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Bouquet Description</th>
                                <th>Quantity</th>
                                <th>Price (£)</th>
                                <th>Order Date</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($orders->num_rows > 0): ?>

                                <?php while ($order = $orders->fetch_assoc()): ?>

                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['bouquet_description']); ?></td>
                                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                        <td><?php echo number_format((float) $order['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['deadline'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="7">This customer has not placed any orders yet.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>
                <p class="black-link">
                    <a href="customers.php">← Back to Customers</a>
                </p>

            </section>

        </div>
    </div>
</body>
</html>