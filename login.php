<?php
session_start();
require 'db_connection.php';
require_once 'helpers.php';
$conn = OpenCon();
$message = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        $message = "Email or password is incorrect.";
    } else {
        $currentTime = time();
        $blockedUntil = (int)($user['blocked_until'] ?? 0);
        $attempts = (int)($user['attempts'] ?? 0);

        if ($blockedUntil > $currentTime) {
            $remaining = $blockedUntil - $currentTime;
            $message = "Account blocked. Try again after $remaining seconds.";
        } else {
            if ($blockedUntil !== 0) {
                $reset = $conn->prepare("UPDATE users SET attempts = 0, blocked_until = 0 WHERE id = ?");
                $reset->bind_param("i", $user['id']);
                $reset->execute();
                $attempts = 0;
            }

            if (password_matches($pass, $user['password'])) {
                if (password_get_info($user['password'])['algo'] === 0) {
                    $newHash = password_hash($pass, PASSWORD_DEFAULT);
                    $upgrade = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upgrade->bind_param("si", $newHash, $user['id']);
                    $upgrade->execute();
                }

                $ok = $conn->prepare("UPDATE users SET attempts = 0, blocked_until = 0 WHERE id = ?");
                $ok->bind_param("i", $user['id']);
                $ok->execute();

                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['first_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = (int)$user['is_admin'];

                redirect(is_admin() ? 'admin_dashboard.php' : 'home.php');
            } else {
                $attempts++;
                $newBlockedUntil = $attempts >= 3 ? time() + 30 : 0;
                $upd = $conn->prepare("UPDATE users SET attempts = ?, blocked_until = ? WHERE id = ?");
                $upd->bind_param("iii", $attempts, $newBlockedUntil, $user['id']);
                $upd->execute();
                $message = $attempts >= 3 ? "Too many attempts. Account blocked for 30 seconds." : "Email or password is incorrect.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Login</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<?php include 'navbar.php'; ?>
<div class="login-box">
    <h2 class="center">Login</h2>
    <?php if ($message): ?><p class="error"><?= e($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
        <a class="btn" href="signup.php">Create Account</a>
    </form>
    <p class="center"><a href="forgot_password.php">Forgot Password?</a></p>
</div>
</body>
</html>
