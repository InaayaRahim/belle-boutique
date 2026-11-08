<?php

session_start();

require_once "../includes/db_connect.php";

// Only allow deletion through a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: inventory.php");
    exit;
}

// Verify the CSRF token
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Invalid security token. Please return to the inventory page and try again.");
}

// Get and validate the inventory ID
$itemId = filter_input(INPUT_POST, "item_id", FILTER_VALIDATE_INT);

if (!$itemId || $itemId < 1) {
    die("Invalid inventory item ID.");
}

// Delete the selected inventory item
$sql = "DELETE FROM inventory WHERE item_id = ?";

try {
    $statement = $conn->prepare($sql);
    $statement->bind_param("i", $itemId);
    $statement->execute();

} catch (mysqli_sql_exception $e) {
    error_log("Inventory deletion failed: " . $e->getMessage());
    die("Unable to delete this inventory item right now. Please try again.");
}

// Redirect depending on whether an item was deleted
if ($statement->affected_rows > 0) {
    header("Location: inventory.php?deleted=1");
    exit;
} else {
    header("Location: inventory.php?deleted=0");
    exit;
}

?>