<?php

session_start();

require_once "../includes/db_connect.php";

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}

// Verify the CSRF token
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Invalid security token. Please return to the orders page and try again.");
}

// Get the order ID
$orderId = filter_input(INPUT_POST, "order_id", FILTER_VALIDATE_INT);

// Check that the ID is valid
if (!$orderId || $orderId < 1) {
    die("Invalid order ID.");
}

// Delete the order and its related material records
try {
    $conn->begin_transaction();

    // Delete related material records first
    $sql = "DELETE FROM order_materials WHERE order_id = ?";
    $statement = $conn->prepare($sql);
    $statement->bind_param("i", $orderId);
    $statement->execute();

    // Delete the order
    $sql = "DELETE FROM orders WHERE order_id = ?";
    $statement = $conn->prepare($sql);
    $statement->bind_param("i", $orderId);
    $statement->execute();

    $conn->commit();

} catch (mysqli_sql_exception $e) {
    $conn->rollback();

    error_log("Order deletion failed: " . $e->getMessage());

    die("Unable to delete this order right now. Please try again.");
}

// Check whether an order was deleted
if ($statement->affected_rows > 0) {

    header("Location: orders.php?deleted=1");
    exit;

} else {

    header("Location: orders.php?deleted=0");
    exit;

}

?>