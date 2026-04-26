<?php
session_start();
require_once 'helpers.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Admin Dashboard</title><link rel="stylesheet" href="css/style.css"></head>
<body><?php include 'navbar.php'; ?>
<div class="admin-container">
    <h1>Admin Dashboard</h1>
    <div class="admin-actions">
        <a href="admin_products.php" class="admin-btn">Manage Products</a>
        <a href="admin_users.php" class="admin-btn">Users List</a>
        <a href="admin_deliveries.php" class="admin-btn">Manage Orders & Deliveries</a>
        <a href="admin_tickets.php" class="admin-btn">Support Tickets</a>
        <a href="admin_orders_report.php" class="admin-btn">Orders Report</a>
        <a href="admin_add_manager.php" class="admin-btn">Add Manager</a>
        <a href="admin_add_user.php" class="admin-btn">Add User</a>
        <a href="admin_add_carrier.php" class="admin-btn">Add Carrier</a>
        <a href="admin_carriers.php" class="admin-btn">Manage Carriers</a>
        <a href="admin_closed_orders.php" class="admin-btn">Closed Orders</a>
        <a href="logout.php" class="admin-btn logout">Logout</a>
    </div>
</div>
</body></html>
