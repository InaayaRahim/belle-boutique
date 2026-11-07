<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belle Boutique | Dashboard</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">

        <aside class="sidebar">
            <div class="brand">
                <img src="assets/images/belle-boutique-logo.png" alt="Belle Boutique Logo">
                <h2>Belle Boutique</h2>
            </div>

            <nav>
                <a href="index.php" class="active">Dashboard</a>
                <a href="pages/orders.php">Orders</a>
                <a href="pages/inventory.php">Inventory</a>
                <a href="pages/customers.php">Customers</a>
            </nav>
        </aside>

        <main class="main-content">

            <header class="top-header">
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome back, Admin!</p>
                </div>
            </header>

            <section class="overview">
                <h2>Business Overview</h2>

                <div class="summary-cards">
                    <div class="summary-card">
                        <h3>Total Orders</h3>
                        <p>0</p>
                    </div>
                    <div class="summary-card">
                        <h3>Pending Orders</h3>
                        <p>0</p>
                    </div>
                    <div class="summary-card">
                        <h3>Low Stock Items</h3>
                        <p>0</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>