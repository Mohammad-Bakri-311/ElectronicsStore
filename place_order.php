<?php
session_start();
require 'db_connection.php';
require_once 'helpers.php';
require_login();
$conn = OpenCon();

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin_dashboard.php");
    exit;
}

if (!isset($_POST['confirm'])) redirect('cart.php');
$userId = current_user_id();
$orderType = $_POST['order_type'] === 'pickup' ? 'pickup' : 'delivery';
$city = trim($_POST['city'] ?? '');
$street = trim($_POST['street'] ?? '');
$house = trim($_POST['house_number'] ?? '');
$branch = trim($_POST['pickup_branch'] ?? '');
$timeRange = trim($_POST['pickup_time_range'] ?? '');

if ($orderType === 'delivery' && ($city === '' || $street === '' || $house === '')) die('Delivery address is required.');
if ($orderType === 'pickup' && ($branch === '' || $timeRange === '')) die('Pickup details are required.');

$conn->begin_transaction();
try {
    $cartStmt = $conn->prepare("SELECT c.product_id, c.quantity, p.price, p.stock, p.name FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ? FOR UPDATE");
    $cartStmt->bind_param("i", $userId);
    $cartStmt->execute();
    $items = $cartStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (!$items) throw new Exception('Cart is empty.');

    $total = 0;
    foreach ($items as $item) {
        if ((int)$item['quantity'] > (int)$item['stock']) throw new Exception('Not enough stock for ' . $item['name']);
        $total += (int)$item['quantity'] * (float)$item['price'];
    }

    if ($orderType === 'pickup') {
        $status = 'processing';
        $ins = $conn->prepare("INSERT INTO orders (user_id, total_price, order_type, order_status, pickup_branch, pickup_time_range) VALUES (?, ?, 'pickup', ?, ?, ?)");
        $ins->bind_param("idsss", $userId, $total, $status, $branch, $timeRange);
    } else {
        $status = 'processing';
        $ins = $conn->prepare("INSERT INTO orders (user_id, total_price, order_type, order_status) VALUES (?, ?, 'delivery', ?)");
        $ins->bind_param("ids", $userId, $total, $status);
    }
    $ins->execute();
    $orderId = $conn->insert_id;

    foreach ($items as $item) {
        $productId = (int)$item['product_id'];
        $qty = (int)$item['quantity'];
        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $itemStmt->bind_param("iii", $orderId, $productId, $qty);
        $itemStmt->execute();
        $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stockStmt->bind_param("ii", $qty, $productId);
        $stockStmt->execute();
    }

    if ($orderType === 'delivery') {
        $delStatus = 'processing';
        $del = $conn->prepare("INSERT INTO deliveries (order_id, city, street, house_number, status) VALUES (?, ?, ?, ?, ?)");
        $del->bind_param("issss", $orderId, $city, $street, $house, $delStatus);
        $del->execute();
    }

    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear->bind_param("i", $userId);
    $clear->execute();
    $conn->commit();

    send_html_mail($_SESSION['email'], 'Order Confirmation', "<h2>Order Confirmed</h2><p>Order #$orderId</p><p>Total: $$total</p>");
} catch (Exception $e) {
    $conn->rollback();
    die(e($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Order Complete</title><link rel="stylesheet" href="css/style.css"></head>
<body><?php include 'navbar.php'; ?>
<div class="container"><div class="card center"><h2>Order placed successfully!</h2><p>Order #: <?= (int)$orderId ?></p><p>Order Type: <?= e(strtoupper($orderType)) ?></p><p>Total: $<?= number_format($total, 2) ?></p><a class="btn" href="home.php">Back to Home</a></div></div>
</body></html>
