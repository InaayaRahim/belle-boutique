<?php

require_once "../includes/db_connect.php";

// Get the inventory ID from the URL
$itemId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$itemId || $itemId < 1) {
    die("Invalid inventory item ID.");
}

// Retrieve the selected inventory item
$sql = "SELECT * FROM inventory WHERE item_id = ?";

$statement = $conn->prepare($sql);
$statement->bind_param("i", $itemId);
$statement->execute();

$result = $statement->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Inventory item not found.");
}

// Update the inventory item when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $itemName = trim($_POST["item_name"] ?? "");
    $colour = trim($_POST["colour"] ?? "");
    $quantity = filter_var($_POST["quantity_in_stock"] ?? null, FILTER_VALIDATE_INT);
    $unit = $_POST["unit"] ?? "";
    $threshold = filter_var($_POST["low_stock_threshold"] ?? null, FILTER_VALIDATE_INT);

    $allowedUnits = ["pieces", "metres", "rolls", "packs", "sheets"];

    // Validate the submitted information
    if (
        $itemName === "" ||
        $quantity === false || $quantity < 0 ||
        $threshold === false || $threshold < 0 ||
        !in_array($unit, $allowedUnits, true)
    ) {
        die("Please enter valid inventory information.");
    }

    // Update the database
    $sql = "UPDATE inventory
            SET item_name = ?,
                colour = ?,
                quantity_in_stock = ?,
                unit = ?,
                low_stock_threshold = ?
            WHERE item_id = ?";

    $statement = $conn->prepare($sql);

    $statement->bind_param(
        "ssisii",
        $itemName,
        $colour,
        $quantity,
        $unit,
        $threshold,
        $itemId
    );

    if ($statement->execute()) {
        header("Location: inventory.php?updated=1");
        exit;
    } else {
        die("Unable to update inventory item.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belle Boutique | Edit Inventory</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Edit Inventory Item</h1>
                <p>Update the details of your material.</p>
            </header>

            <section class="overview">

                <div class="form-container">
                    <form action="" method="POST">

                        <div class="form-group">
                            <label>Item Name</label>
                            <input
                                type="text"
                                name="item_name"
                                value="<?php echo htmlspecialchars($item['item_name']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Colour</label>
                            <input
                                type="text"
                                name="colour"
                                value="<?php echo htmlspecialchars($item['colour'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label>Quantity in Stock</label>
                            <input
                                type="number"
                                name="quantity_in_stock"
                                min="0"
                                value="<?php echo htmlspecialchars($item['quantity_in_stock']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Unit</label>
                            <select name="unit" required>
                                <?php
                                $units = ["pieces", "metres", "rolls", "packs", "sheets"];

                                foreach ($units as $unit):
                                ?>
                                    <option
                                        value="<?php echo $unit; ?>"
                                        <?php echo ($item['unit'] === $unit) ? 'selected' : ''; ?>
                                    >
                                        <?php echo ucfirst($unit); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Low Stock Threshold</label>
                            <input
                                type="number"
                                name="low_stock_threshold"
                                min="0"
                                value="<?php echo htmlspecialchars($item['low_stock_threshold']); ?>"
                                required
                            >
                        </div>

                        <button type="submit" name="update_inventory">
                            Save Changes
                        </button>

                    </form>
                </div>

                <a href="inventory.php" class="back-link">← Back to Inventory</a>

            </section>

        </div>
    </div>
</body>
</html>