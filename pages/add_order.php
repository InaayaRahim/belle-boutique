<?php

require_once "../includes/db_connect.php";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get and clean the submitted form values
    $customerName = trim($_POST["customer_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    $description = trim($_POST["bouquet_description"] ?? "");
    $quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);
    $price = $_POST["price"] ?? "";
    $orderDate = $_POST["order_date"] ?? "";
    $deadline = $_POST["deadline"] ?? "";

    // Check the submitted information
    if (
        $customerName === "" ||
        $description === "" ||
        $quantity === false ||
        $quantity < 1 ||
        !is_numeric($price) ||
        $price < 0 ||
        $orderDate === ""
    ) {
        $errorMessage = "Please enter valid information in all required fields.";

    } elseif (
        $email !== "" &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $errorMessage = "Please enter a valid email address.";

    } else {

        try {

            // Start a database transaction
            $conn->begin_transaction();

            // Insert the customer
            $customerSql = "INSERT INTO customers (name, email, phone)
                            VALUES (?, ?, ?)";

            $customerStatement = $conn->prepare($customerSql);
            $customerStatement->bind_param(
                "sss",
                $customerName,
                $email,
                $phone
            );

            $customerStatement->execute();

            // Get the ID of the newly created customer
            $customerId = $conn->insert_id;

            // Insert the order using the customer's ID
            $orderSql = "INSERT INTO orders
                         (customer_id, bouquet_description, quantity,
                          price, order_date, deadline)
                         VALUES (?, ?, ?, ?, ?, ?)";

            $orderStatement = $conn->prepare($orderSql);

            $orderStatement->bind_param(
                "isidss",
                $customerId,
                $description,
                $quantity,
                $price,
                $orderDate,
                $deadline
            );

            $orderStatement->execute();

            // Save both database changes
            $conn->commit();

            // Return to the orders page
            header("Location: orders.php?added=1");
            exit;

        } catch (Throwable $error) {

            // Undo changes if something goes wrong
            $conn->rollback();

            $errorMessage = "Something went wrong while saving the order. Please try again.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belle Boutique | Add Order</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Add New Order</h1>
                <p>Enter the details of a new bouquet order.</p>
            </header>

            <section class="overview">
                <div class="form-container">
                    <form action="" method="POST">

                        <h2>Customer Details</h2>

                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" id="customer_name" name="customer_name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>

                        <h2>Order Details</h2>

                        <div class="form-group">
                            <label for="bouquet_description">Bouquet Description</label>
                            <textarea id="bouquet_description" name="bouquet_description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (£)</label>
                            <input type="number" id="price" name="price" min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="order_date">Order Date</label>
                            <input type="date" id="order_date" name="order_date" required>
                        </div>

                        <div class="form-group">
                            <label for="deadline">Completion Deadline</label>
                            <input type="date" id="deadline" name="deadline">
                        </div>

                        <button type="submit" name="add_order">Save Order</button>

                    </form>
                </div>

                <a href="orders.php" class="back-link">← Back to Orders</a>

            </section>

        </div>
    </div>
</body>
</html>