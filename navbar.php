<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { ?>

    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="admin_products.php">Manage Products</a>
    <a href="admin_users.php">Users</a>
    <a href="admin_deliveries.php">Deliveries</a>
    <a href="admin_tickets.php">Tickets</a>
    <a href="admin_orders_report.php">Reports</a>
    <a href="logout.php">Logout</a>

<?php } else { ?>

    <a href="home.php">Home</a>
    <a href="products.php">Products</a>
    <a href="contact.php">Contact Us</a>

    <?php if (isset($_SESSION['username'])) { ?>
        <a href="my_orders.php">My Orders</a>
        <a href="cart.php">Cart</a>

        <div class="dropdown">
            <a href="#">Account Settings ▾</a>
            <div class="dropdown-content">
                <a href="update_password.php?email=<?= $_SESSION['email'] ?>">Update Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    <?php } else { ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Signup</a>
    <?php } ?>

<?php } ?>
</nav>

</body>
</html>