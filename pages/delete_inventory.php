<?php

require_once "../includes/db_connect.php";

// Only allow deletion through a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: inventory.php");
    exit;
}

// Get and validate the inventory ID
$itemId = filter_input(INPUT_POST, "item_id", FILTER_VALIDATE_INT);

if (!$itemId || $itemId < 1) {
    die("Invalid inventory item ID.");
}

// Delete the selected inventory item
$sql = "DELETE FROM inventory WHERE item_id = ?";

$statement = $conn->prepare($sql);
$statement->bind_param("i", $itemId);
$statement->execute();

// Redirect depending on whether an item was deleted
if ($statement->affected_rows > 0) {
    header("Location: inventory.php?deleted=1");
    exit;
} else {
    header("Location: inventory.php?deleted=0");
    exit;
}

?>