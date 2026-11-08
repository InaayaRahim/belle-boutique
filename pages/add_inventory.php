<?php

require_once "../includes/db_connect.php";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form information
    $itemName = trim($_POST["item_name"] ?? "");
    $colour = trim($_POST["colour"] ?? "");
    $quantity = filter_var($_POST["quantity_in_stock"] ?? null, FILTER_VALIDATE_INT);
    $unit = $_POST["unit"] ?? "";
    $threshold = filter_var($_POST["low_stock_threshold"] ?? null, FILTER_VALIDATE_INT);

    // Validate the information
    $allowedUnits = ["pieces", "metres", "rolls", "packs", "sheets"];

    if (
        $itemName === "" ||
        $quantity === false ||
        $quantity < 0 ||
        $threshold === false ||
        $threshold < 0 ||
        !in_array($unit, $allowedUnits, true)
    ) {

        $errorMessage = "Please enter valid inventory information.";

    } else {

        // Insert the new inventory item
        $sql = "INSERT INTO inventory
                (item_name, colour, quantity_in_stock, unit, low_stock_threshold)
                VALUES (?, ?, ?, ?, ?)";

        $statement = $conn->prepare($sql);

        $statement->bind_param(
            "ssisi",
            $itemName,
            $colour,
            $quantity,
            $unit,
            $threshold
        );

        if ($statement->execute()) {

            header("Location: inventory.php?added=1");
            exit;

        } else {

            $errorMessage = "Unable to save the inventory item. Please try again.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Add Inventory</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Add Inventory Item</h1>
                <p>Add a new material to your stock.</p>
            </header>

            <section class="overview">

                <?php if ($errorMessage !== ""): ?>
                    <p class="error-message">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </p>
                <?php endif; ?>

                <form action="" method="POST">

                    <label for="item_name">Item Name</label>
                    <input
                        type="text"
                        id="item_name"
                        name="item_name"
                        placeholder="e.g. Pipe Cleaners"
                        required
                    >

                    <label for="colour">Colour</label>
                    <input
                        type="text"
                        id="colour"
                        name="colour"
                        placeholder="e.g. Pink"
                    >

                    <label for="quantity_in_stock">Quantity in Stock</label>
                    <input
                        type="number"
                        id="quantity_in_stock"
                        name="quantity_in_stock"
                        min="0"
                        value="0"
                        required
                    >

                    <label for="unit">Unit</label>
                    <select id="unit" name="unit" required>
                        <option value="pieces">Pieces</option>
                        <option value="metres">Metres</option>
                        <option value="rolls">Rolls</option>
                        <option value="packs">Packs</option>
                        <option value="sheets">Sheets</option>
                    </select>

                    <label for="low_stock_threshold">Low Stock Threshold</label>
                    <input
                        type="number"
                        id="low_stock_threshold"
                        name="low_stock_threshold"
                        min="0"
                        value="5"
                        required
                    >

                    <button type="submit" name="add_inventory">
                        Save Inventory Item
                    </button>

                </form>

                <a href="inventory.php">Back to Inventory</a>

            </section>

        </div>
    </div>
</body>
</html>