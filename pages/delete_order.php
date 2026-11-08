<?php

require_once "../includes/db_connect.php";

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}

// Get the order ID
$orderId = filter_input(INPUT_POST, "order_id", FILTER_VALIDATE_INT);

// Check that the ID is valid
if (!$orderId || $orderId < 1) {
    die("Invalid order ID.");
}

// Delete the selected order
$sql = "DELETE FROM orders WHERE order_id = ?";

$statement = $conn->prepare($sql);
$statement->bind_param("i", $orderId);
$statement->execute();

// Check whether an order was deleted
if ($statement->affected_rows > 0) {

    header("Location: orders.php?deleted=1");
    exit;

} else {

    header("Location: orders.php?deleted=0");
    exit;

}

?>