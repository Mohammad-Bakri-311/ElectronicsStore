<?php
session_start();
include 'navbar.php';
require 'db_connection.php';

$conn = OpenCon();

/* Block admin */
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin_dashboard.php");
    exit;
}

/* Add to cart */
if(isset($_POST['add_to_cart'])) {

    if(!isset($_SESSION['email'])) {
        header("Location: login.php");
        exit;
    }

    $email = $_SESSION['email'];

    $u = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    $urow = mysqli_fetch_assoc($u);
    $user_id = $urow['id'];

    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    if ($qty <= 0) {
        die("Invalid quantity");
    }

    $check = mysqli_query($conn,
        "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id"
    );

    if(mysqli_num_rows($check) > 0) {
        mysqli_query($conn,
            "UPDATE cart SET quantity = quantity + $qty
             WHERE user_id=$user_id AND product_id=$product_id"
        );
    } else {
        mysqli_query($conn,
            "INSERT INTO cart (user_id, product_id, quantity)
             VALUES ($user_id, $product_id, $qty)"
        );
    }

    echo "<p style='color:green;text-align:center;'>Product added to cart</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1 style="text-align:center;">Our Products</h1>

<div class="products-container">

<?php
$result = mysqli_query($conn, "SELECT * FROM products");

while($row = mysqli_fetch_assoc($result))
{
    echo '<div class="product">';
    echo '<img src="'.$row['img'].'">';
    echo '<h3>'.$row['name'].'</h3>';
    echo '<p class="price">$'.$row['price'].'</p>';

    echo '<form method="POST">';
    echo '<input type="hidden" name="product_id" value="'.$row['id'].'">';

    echo '<select name="quantity">';
    for ($i = 1; $i <= $row['stock']; $i++) {
        echo '<option value="'.$i.'">'.$i.'</option>';
    }
    echo '</select>';

    echo '<button type="submit" name="add_to_cart">Add to Cart</button>';
    echo '</form>';

    echo '</div>';
}
?>

</div>

</body>
</html>