<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn=OpenCon();
$id=(int)($_GET['id']??0); if($id>0){$stmt=$conn->prepare("DELETE FROM products WHERE id=?"); $stmt->bind_param("i",$id); $stmt->execute();}
redirect('admin_products.php');
?>
