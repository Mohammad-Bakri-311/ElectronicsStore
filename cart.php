<?php
session_start();
require 'db_connection.php';
include 'navbar.php';

$conn = OpenCon();

/* Block admin */
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin_dashboard.php");
    exit;
}

/* Check login */
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

$resUser = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
$userRow = mysqli_fetch_assoc($resUser);
$user_id = $userRow['id'];

$sql = "
SELECT cart.product_id,
       cart.quantity,
       products.name,
       products.price,
       products.img
FROM cart
JOIN products ON products.id = cart.product_id
WHERE cart.user_id = $user_id
";

$result = mysqli_query($conn, $sql);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1 style="text-align:center;">Your Cart</h1>

<div class="products-container">

<?php
while($row = mysqli_fetch_assoc($result)) {

    $itemTotal = $row['quantity'] * $row['price'];
    $total += $itemTotal;

    echo "<div class='product'>";
    echo "<img src='{$row['img']}'>";
    echo "<h3>{$row['name']}</h3>";
    echo "<p>Price: {$row['price']}</p>";
    echo "<p>Quantity: {$row['quantity']}</p>";
    echo "<p>Total: $itemTotal</p>";

    echo "
    <form method='post' action='remove_from_cart.php'>
        <input type='hidden' name='product_id' value='{$row['product_id']}'>
        <button type='submit'>Remove</button>
    </form>
    ";

    echo "</div>";
}
?>

</div>

<h2 style="text-align:center;">Total: $<?php echo $total; ?></h2>

<?php if ($total > 0) { ?>
    <div style="text-align:center;">
        <a href="order_summary.php" class="pay-btn">Checkout</a>
    </div>
<?php } ?>

</body>
</html>