<?php
session_start();
require 'db_connection.php';
require_once 'helpers.php';
require_login();
$conn = OpenCon();
$userId = current_user_id();
$productId = (int)($_POST['product_id'] ?? 0);

if ($productId > 0) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
}
redirect('cart.php');
?>
