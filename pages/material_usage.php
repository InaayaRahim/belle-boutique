<?php

session_start();

require_once "../includes/db_connect.php";

// Generate a CSRF token if one doesn't already exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify the CSRF token
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Invalid security token. Please return to the form and try again.");
    }

    // Collect submitted information
    $orderId = filter_var($_POST["order_id"] ?? null, FILTER_VALIDATE_INT);
    $itemId = filter_var($_POST["item_id"] ?? null, FILTER_VALIDATE_INT);
    $quantityUsed = filter_var($_POST["quantity_used"] ?? null, FILTER_VALIDATE_INT);

    // Validate the submitted information
    if (
        $orderId === false || $orderId < 1 ||
        $itemId === false || $itemId < 1 ||
        $quantityUsed === false || $quantityUsed < 1
    ) {

        $errorMessage = "Please enter valid material usage information.";

    } else {

        try {

            // Start a database transaction
            $conn->begin_transaction();

            // Retrieve the selected inventory item and lock its row
            $sql = "SELECT quantity_in_stock
                    FROM inventory
                    WHERE item_id = ?
                    FOR UPDATE";

            $statement = $conn->prepare($sql);
            $statement->bind_param("i", $itemId);
            $statement->execute();

            $result = $statement->get_result();
            $item = $result->fetch_assoc();

            if (!$item) {
                throw new Exception("Inventory item not found.");
            }

            // Check available stock
            if ($quantityUsed > $item["quantity_in_stock"]) {
                throw new Exception("Not enough stock available.");
            }

            // Deduct the quantity from inventory
            $updateSql = "UPDATE inventory
                          SET quantity_in_stock = quantity_in_stock - ?
                          WHERE item_id = ?";

            $updateStatement = $conn->prepare($updateSql);
            $updateStatement->bind_param("ii", $quantityUsed, $itemId);
            $updateStatement->execute();

            // Record the material usage
            $insertSql = "INSERT INTO order_materials
                          (order_id, item_id, quantity_used)
                          VALUES (?, ?, ?)";

            $insertStatement = $conn->prepare($insertSql);
            $insertStatement->bind_param(
                "iii",
                $orderId,
                $itemId,
                $quantityUsed
            );
            $insertStatement->execute();

            // Save both database changes
            $conn->commit();

            header("Location: material_usage.php?recorded=1");
            exit;

        } catch (Throwable $error) {

            // Undo database changes if anything fails
            $conn->rollback();

            $errorMessage = $error->getMessage();
        }
    }
}

// Retrieve all orders and inventory items
$queryError = false;

try {
    $orderSql = "SELECT order_id, bouquet_description
                 FROM orders
                 ORDER BY order_date DESC";

    $orders = $conn->query($orderSql);

    $inventorySql = "SELECT item_id, item_name, colour,
                            quantity_in_stock, unit
                     FROM inventory
                     ORDER BY item_name ASC, colour ASC";

    $inventory = $conn->query($inventorySql);

} catch (mysqli_sql_exception $e) {
    error_log("Material usage dropdown query failed: " . $e->getMessage());

    $queryError = true;
    $orders = null;
    $inventory = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Material Usage</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>

        <div class="main-content">

            <header class="top-header">
                <h1>Material Usage</h1>
                <p>Record the materials used to create customer orders.</p>

                <?php if (isset($_GET['recorded']) && $_GET['recorded'] == 1): ?>
                    <p class="success-message">
                        Material usage recorded and inventory updated successfully!
                    </p>
                <?php endif; ?>
            </header>

            <section class="overview">

                <?php if ($errorMessage !== ""): ?>
                    <p class="error-message">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </p>
                <?php endif; ?>

                <?php if ($queryError): ?>
                    <p class="error-message">
                        Unable to load orders and inventory right now. Please try again later.
                    </p>
                <?php endif; ?>

                <?php if (!$queryError): ?>

                <div class="form-container">

                    <h2>Record Materials Used</h2>

                        <form action="" method="POST">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
                        >

                            <div class="form-group">
                                <label for="order_id">Select Order</label>

                                <select name="order_id" id="order_id" required>
                                    <option value="">Choose an order</option>

                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <option value="<?php echo $order['order_id']; ?>">
                                            Order #<?php echo $order['order_id']; ?> -
                                            <?php echo htmlspecialchars($order['bouquet_description']); ?>
                                        </option>
                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <div class="form-group">
                                <label for="item_id">Select Material</label>

                                <select name="item_id" id="item_id" required>
                                    <option value="">Choose a material</option>

                                    <?php while ($item = $inventory->fetch_assoc()): ?>
                                        <option value="<?php echo $item['item_id']; ?>">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                            - <?php echo htmlspecialchars($item['colour'] ?? ''); ?>
                                            (<?php echo htmlspecialchars($item['quantity_in_stock']); ?>
                                            <?php echo htmlspecialchars($item['unit']); ?> available)
                                        </option>
                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <label for="quantity_used">Quantity Used</label>

                            <div class="form-group">
                                <input
                                    type="number"
                                    name="quantity_used"
                                    id="quantity_used"
                                    min="1"
                                    required
                                >
                            </div>

                            <button type="submit" name="record_usage">
                                Record Material Usage
                            </button>

                        </form>

                </div>

                <?php endif; ?>

            </section>

        </div>
    </div>
</body>
</html>