<?php

require_once "../includes/db_connect.php";

$sql = "SELECT
            customers.customer_id,
            customers.name,
            customers.email,
            customers.phone,
            COUNT(orders.order_id) AS total_orders
        FROM customers
        LEFT JOIN orders
            ON customers.customer_id = orders.customer_id
        GROUP BY
            customers.customer_id,
            customers.name,
            customers.email,
            customers.phone
        ORDER BY customers.name ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Customers</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Customers</h1>
                <p>View and manage your Belle Boutique customers.</p>
            </header>

            <section class="overview">

                <h2>All Customers</h2>

                <div class="table-container">
                    <table>

                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Orders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($result && $result->num_rows > 0): ?>

                                <?php while ($customer = $result->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($customer['customer_id']); ?>
                                        </td>

                                        <td>
                                            <span class="customer-name">
                                                <?php echo htmlspecialchars($customer['name']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($customer['total_orders']); ?>
                                        </td>
                                        <td>
                                            <a
                                                href="customer_details.php?id=<?php echo $customer['customer_id']; ?>"
                                                class="action-button edit-button"
                                            >
                                                View Orders
                                            </a>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6">No customers found yet.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>
            </section>

        </div>
    </div>
</body>
</html>