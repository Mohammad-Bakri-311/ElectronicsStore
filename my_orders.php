<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$conn = OpenCon();

$email = $_SESSION['email'];

$userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userStmt->bind_param("s", $email);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows == 0) {
    header("Location: login.php");
    exit;
}

$user = $userResult->fetch_assoc();
$user_id = $user['id'];

$orderStmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.total_price,
        o.order_type,
        o.order_status,
        o.created_at
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orders = $orderStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-card wide-card">
    <h2>My Orders</h2>

    <?php if ($orders->num_rows == 0) { ?>
        <p class="empty-message">You do not have any orders yet.</p>
        <div class="center">
            <a href="products.php" class="primary-btn">Browse Products</a>
        </div>
    <?php } else { ?>

        <table>
            <tr>
                <th>Order #</th>
                <th>Total</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
                <th>Details</th>
            </tr>

            <?php while ($row = $orders->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td>$<?= number_format($row['total_price'], 2) ?></td>
                <td><?= strtoupper($row['order_type']) ?></td>
                <td>
                    <span class="status-badge">
                        <?= htmlspecialchars($row['order_status']) ?>
                    </span>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="order_details.php?order_id=<?= $row['order_id'] ?>" class="table-link">
                        View
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>

    <?php } ?>
</div>

</body>
</html>