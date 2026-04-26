<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn=OpenCon(); $message='';
if(isset($_POST['add'])){
    $first=trim($_POST['first_name']); $last=trim($_POST['last_name']); $email=trim($_POST['email']); $pass=$_POST['password']; $phone=trim($_POST['phone']); $address=trim($_POST['address']); $isAdmin=0; $hash=password_hash($pass,PASSWORD_DEFAULT); $blocked=0; $attempts=0;
    $check=$conn->prepare("SELECT id FROM users WHERE email=?"); $check->bind_param("s",$email); $check->execute();
    if($check->get_result()->num_rows>0){$message='Email already exists.';} else { $stmt=$conn->prepare("INSERT INTO users (first_name,last_name,email,password,phone,address,is_admin,blocked_until,attempts) VALUES (?,?,?,?,?,?,?,?,?)"); $stmt->bind_param("ssssssiii",$first,$last,$email,$hash,$phone,$address,$isAdmin,$blocked,$attempts); $stmt->execute(); redirect('admin_users.php'); }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Add User</title><link rel="stylesheet" href="css/style.css"></head><body><?php include 'navbar.php'; ?><div class="form-box"><h2>Add User</h2><?php if($message):?><p class="error"><?=e($message)?></p><?php endif;?><form method="post"><input name="first_name" placeholder="First Name" required><input name="last_name" placeholder="Last Name" required><input type="email" name="email" placeholder="Email" required><input type="password" name="password" placeholder="Password" required minlength="6"><input name="phone" placeholder="Phone" required><input name="address" placeholder="Address" required><button name="add">Add User</button></form></div></body></html>
