<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Electronics Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="home-hero">
    <div class="home-left">
        <span class="badge">Modern Electronics Shop</span>
        <h1>Electronics Store</h1>
        <p>
            Shop smart electronics, manage your cart, choose delivery or pickup,
            and track your orders easily.
        </p>

        <div class="hero-buttons">
            <a href="products.php" class="btn-primary">Browse Products</a>
            <a href="contact.php" class="btn-secondary">Contact Support</a>
        </div>
    </div>

    <div class="home-right">
        <img src="photos/phpLOGO.png" alt="Electronics Store Logo">
    </div>
</section>

<section class="features">
    <div class="feature">
        <h3>Smart Cart</h3>
        <p>Add products, choose quantities, and review your total before checkout.</p>
    </div>

    <div class="feature">
        <h3>Delivery or Pickup</h3>
        <p>Choose home delivery or self pickup with branch and time options.</p>
    </div>

    <div class="feature">
        <h3>Support Tickets</h3>
        <p>Contact admin and follow your full support message history.</p>
    </div>
</section>

</body>
</html>