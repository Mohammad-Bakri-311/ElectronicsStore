<?php
// Common helper functions used by the whole project.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: " . $path);
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Access Denied');
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_user_by_email($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function password_matches($plain, $storedHashOrPlain) {
    if (password_get_info($storedHashOrPlain)['algo'] !== 0) {
        return password_verify($plain, $storedHashOrPlain);
    }
    return hash_equals($storedHashOrPlain, $plain);
}

function send_html_mail($to, $subject, $html) {
    $headers  = "From: admin@electronicsstore.local\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    return mail($to, $subject, $html, $headers);
}
?>
