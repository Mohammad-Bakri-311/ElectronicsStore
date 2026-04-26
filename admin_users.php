<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn=OpenCon();
$result=$conn->query("SELECT id,first_name,last_name,email,is_admin FROM users ORDER BY id DESC");
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Users</title><link rel="stylesheet" href="css/style.css"></head><body><?php include 'navbar.php'; ?><div class="admin-container"><h2>Users List</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr><?php while($r=$result->fetch_assoc()):?><tr><td><?=(int)$r['id']?></td><td><?=e($r['first_name'].' '.$r['last_name'])?></td><td><?=e($r['email'])?></td><td><?=((int)$r['is_admin']===1?'Admin':'Customer')?></td></tr><?php endwhile;?></table></div></body></html>
