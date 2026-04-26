<?php
session_start();
require 'db_connection.php';
require_once 'helpers.php';
$conn = OpenCon();
$message = "";

if (isset($_POST['submit'])) {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $pass = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($email === '' || $first === '' || $last === '' || strlen($pass) < 6) {
        $message = "Please fill all fields. Password must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $message = "Email already exists.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $isAdmin = 0;
            $blocked = 0;
            $attempts = 0;
            $insert = $conn->prepare("INSERT INTO users (password, first_name, last_name, phone, email, address, blocked_until, attempts, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("ssssssiii", $hash, $first, $last, $phone, $email, $address, $blocked, $attempts, $isAdmin);
            $insert->execute();
            $userId = $conn->insert_id;

            $hist = $conn->prepare("INSERT INTO password_history (user_id, password) VALUES (?, ?)");
            $hist->bind_param("is", $userId, $hash);
            $hist->execute();

            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $first;
            $_SESSION['is_admin'] = 0;

            send_html_mail($email, "Welcome to ElectronicsStore", "<h2>Welcome, " . e($first) . "!</h2><p>Your account was created successfully.</p>");
            redirect('home.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Sign Up</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<?php include 'navbar.php'; ?>
<div class="signup-form">
    <h2 class="center">Create Account</h2>
    <?php if ($message): ?><p class="form-message"><?= e($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Email</label><input type="email" name="email" required>
        <label>Phone</label><input type="text" name="phone" required>
        <label>First Name</label><input type="text" name="first_name" required>
        <label>Last Name</label><input type="text" name="last_name" required>
        <label>Password</label><input type="password" name="password" required minlength="6">
        <label>Address</label><input type="text" name="address" required>
        <button type="submit" name="submit">Sign Up</button>
    </form>
    <p class="center">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
