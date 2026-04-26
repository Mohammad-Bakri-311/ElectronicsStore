<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn=OpenCon(); $message='';
if(isset($_POST['add'])){ $name=trim($_POST['name']); if($name===''){$message='Carrier name is required.';} else { $stmt=$conn->prepare("INSERT INTO carriers (name) VALUES (?)"); $stmt->bind_param("s",$name); $stmt->execute(); redirect('admin_carriers.php'); }}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Add Carrier</title><link rel="stylesheet" href="css/style.css"></head><body><?php include 'navbar.php'; ?><div class="form-box"><h2>Add Carrier</h2><?php if($message):?><p class="error"><?=e($message)?></p><?php endif;?><form method="post"><label>Carrier Name</label><input name="name" required><button name="add">Add Carrier</button></form></div></body></html>
