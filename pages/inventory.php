<?php

session_start();

require_once "../includes/db_connect.php";

// Generate a CSRF token if one doesn't already exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Retrieve all inventory items
$sql = "SELECT * FROM inventory
        ORDER BY item_name ASC, colour ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belle Boutique | Inventory</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="dashboard">

        <?php include "../includes/sidebar.php"; ?>
        
        <div class="main-content">

            <header class="top-header">
                <h1>Inventory</h1>
                <p>Manage your materials and stock levels.</p>

                <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                    <p class="success-message">
                        Inventory item added successfully!
                    </p>
                <?php endif; ?>
                <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                    <p class="success-message">
                        Inventory item updated successfully!
                    </p>
                <?php endif; ?>
                <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                    <p class="success-message">
                        Inventory item deleted successfully!
                    </p>
                <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] == 0): ?>
                    <p class="error-message">
                        Inventory item could not be deleted.
                    </p>
                <?php endif; ?>
            </header>

            <section class="overview">

                <div class="section-header">
                    <h2>All Inventory Items</h2>

                    <a href="add_inventory.php" class="primary-button">
                        + Add Inventory Item
                    </a>
                </div>

                <div class="table-container">

                    <table>

                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Colour</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Low Stock Threshold</th>
                                <th>Stock Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($result && $result->num_rows > 0): ?>

                                <?php while ($item = $result->fetch_assoc()): ?>

                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>

                                        <td><?php echo htmlspecialchars($item['colour'] ?? 'N/A'); ?></td>

                                        <td><?php echo htmlspecialchars($item['quantity_in_stock']); ?></td>

                                        <td><?php echo htmlspecialchars($item['unit']); ?></td>

                                        <td><?php echo htmlspecialchars($item['low_stock_threshold']); ?></td>

                                        <td>
                                            <?php if ($item['quantity_in_stock'] <= $item['low_stock_threshold']): ?>
                                                <span class="stock-badge stock-low">Low Stock</span>
                                            <?php else: ?>
                                                <span class="stock-badge stock-in">In Stock</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="table-actions">

                                                <a
                                                    href="edit_inventory.php?id=<?php echo $item['item_id']; ?>"
                                                    class="action-button edit-button"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    action="delete_inventory.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this item?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
                                                    >
                                                    
                                                    <input
                                                        type="hidden"
                                                        name="item_id"
                                                        value="<?php echo $item['item_id']; ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        name="delete_inventory"
                                                        class="action-button delete-button"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td> 
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="7">No inventory items found.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>

            </section>

        </div>
    </div> <!-- End of dashboard -->
</body>
</html>