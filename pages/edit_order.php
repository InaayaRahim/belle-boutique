<?php

require_once "../includes/db_connect.php";

// Get the order ID from the URL
$orderId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Check that a valid order ID was provided
if (!$orderId) {
    die("Invalid order ID.");
}

// Handle form submission for updating the order
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customerName = trim($_POST["customer_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    $description = trim($_POST["bouquet_description"] ?? "");
    $quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);
    $price = $_POST["price"] ?? "";
    $orderDate = $_POST["order_date"] ?? "";
    $deadline = $_POST["deadline"] ?? "";
    $status = $_POST["status"] ?? "";

    $allowedStatuses = ["Pending", "In Progress", "Completed"];

    if (
        $customerName === "" ||
        $description === "" ||
        $quantity === false ||
        $quantity < 1 ||
        !is_numeric($price) ||
        $price < 0 ||
        $orderDate === "" ||
        !in_array($status, $allowedStatuses, true)
    ) {
        die("Please enter valid order information.");

    } elseif (
        $email !== "" &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        die("Please enter a valid email address.");

    } else {

        try {
            $conn->begin_transaction();

            // Update customer details
            $customerSql = "UPDATE customers
                            SET name = ?, email = ?, phone = ?
                            WHERE customer_id = (
                                SELECT customer_id FROM orders
                                WHERE order_id = ?
                            )";

            $customerStatement = $conn->prepare($customerSql);
            $customerStatement->bind_param(
                "sssi",
                $customerName,
                $email,
                $phone,
                $orderId
            );
            $customerStatement->execute();

            // Update order details
            $orderSql = "UPDATE orders
                         SET bouquet_description = ?,
                             quantity = ?,
                             price = ?,
                             order_date = ?,
                             deadline = ?,
                             status = ?
                         WHERE order_id = ?";

            $orderStatement = $conn->prepare($orderSql);
            $orderStatement->bind_param(
                "sidsssi",
                $description,
                $quantity,
                $price,
                $orderDate,
                $deadline,
                $status,
                $orderId
            );
            $orderStatement->execute();

            $conn->commit();

            header("Location: orders.php?updated=1");
            exit;

        } catch (Throwable $error) {
            $conn->rollback();
            die("Unable to update the order. Please try again.");
        }
    }
}

// Retrieve the order and its customer details
$sql = "SELECT
            orders.order_id,
            orders.customer_id,
            customers.name,
            customers.email,
            customers.phone,
            orders.bouquet_description,
            orders.quantity,
            orders.price,
            orders.order_date,
            orders.deadline,
            orders.status
        FROM orders
        INNER JOIN customers
        ON orders.customer_id = customers.customer_id
        WHERE orders.order_id = ?";

$statement = $conn->prepare($sql);
$statement->bind_param("i", $orderId);
$statement->execute();

$result = $statement->get_result();
$order = $result->fetch_assoc();

// Check whether the order exists
if (!$order) {
    die("Order not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Edit Order</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Edit Order #<?php echo $order['order_id']; ?></h1>
                <p>Update the customer and bouquet order details.</p>
            </header>

            <section class="overview">

                <div class="form-container">

                    <form action="" method="POST">

                        <h2>Customer Details</h2>

                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                value="<?php echo htmlspecialchars($order['name']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($order['email'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?php echo htmlspecialchars($order['phone'] ?? ''); ?>"
                            >
                        </div>

                        <h2>Order Details</h2>

                        <div class="form-group">
                            <label for="bouquet_description">Bouquet Description</label>
                            <textarea
                                id="bouquet_description"
                                name="bouquet_description"
                                required
                            ><?php echo htmlspecialchars($order['bouquet_description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                min="1"
                                value="<?php echo htmlspecialchars($order['quantity']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="price">Price (£)</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                min="0"
                                step="0.01"
                                value="<?php echo htmlspecialchars($order['price']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="order_date">Order Date</label>
                            <input
                                type="date"
                                id="order_date"
                                name="order_date"
                                value="<?php echo htmlspecialchars($order['order_date']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="deadline">Completion Deadline</label>
                            <input
                                type="date"
                                id="deadline"
                                name="deadline"
                                value="<?php echo htmlspecialchars($order['deadline'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="status">Order Status</label>
                            <select id="status" name="status">
                                <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo $order['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>

                        <button type="submit" name="update_order">Update Order</button>

                    </form>
                </div>

                <a href="orders.php" class="back-link">← Back to Orders</a>

            </section>

        </div>
    </div>
</body>
</html>