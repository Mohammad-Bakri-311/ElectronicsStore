<?php
session_start();
require 'db_connection.php';
$conn = OpenCon();

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin_dashboard.php");
    exit;
}

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$userRes = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($userRes);
$user_id = $user['id'];

$cartRes = mysqli_query($conn, "
    SELECT c.product_id, c.quantity, p.name, p.price
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id=$user_id
");

if (mysqli_num_rows($cartRes) == 0) {
    header("Location: cart.php");
    exit;
}

$total = 0;
while ($row = mysqli_fetch_assoc($cartRes)) {
    $total += $row['quantity'] * $row['price'];
}
mysqli_data_seek($cartRes, 0);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Summary</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="checkout-container">
    <h2>Order Summary</h2>

    <form method="post" action="place_order.php">

        <table class="checkout-table">
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($cartRes)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>$<?= number_format($row['price'], 2) ?></td>
                <td>$<?= number_format($row['quantity'] * $row['price'], 2) ?></td>
            </tr>
            <?php } ?>

            <tr>
                <td colspan="3"><b>Total</b></td>
                <td><b>$<?= number_format($total, 2) ?></b></td>
            </tr>
        </table>

        <div class="checkout-section">
            <h3>Shipping Method</h3>

            <label class="radio-box">
                <input type="radio" name="order_type" value="delivery" checked onclick="toggleType()">
                Delivery
            </label>

            <label class="radio-box">
                <input type="radio" name="order_type" value="pickup" onclick="toggleType()">
                Self Pickup
            </label>
        </div>

        <div id="delivery_section" class="checkout-section">
            <h3>Delivery Address</h3>

            <label>City</label>
            <input type="text" name="city">

            <label>Street</label>
            <input type="text" name="street">

            <label>House Number</label>
            <input type="text" name="house_number">
        </div>

        <div id="pickup_section" class="checkout-section" style="display:none;">
            <h3>Pickup Details</h3>

            <label>Pickup Branch</label>
            <select name="pickup_branch">
                <option value="Tel Aviv">Tel Aviv</option>
                <option value="Haifa">Haifa</option>
                <option value="Jerusalem">Jerusalem</option>
            </select>

            <label>Pickup Time Range</label>
            <select name="pickup_time_range">
                <option value="09:00 - 12:00">09:00 - 12:00</option>
                <option value="12:00 - 15:00">12:00 - 15:00</option>
                <option value="15:00 - 18:00">15:00 - 18:00</option>
            </select>
        </div>

        <p class="demo-note">Demo project note: no real credit-card information is collected.</p>

        <button type="submit" name="confirm" class="checkout-btn">Confirm Order</button>
    </form>
</div>

<script>
function toggleType() {
    const type = document.querySelector('input[name="order_type"]:checked').value;

    document.getElementById('delivery_section').style.display =
        type === 'delivery' ? 'block' : 'none';

    document.getElementById('pickup_section').style.display =
        type === 'pickup' ? 'block' : 'none';
}
</script>

</body>
</html>