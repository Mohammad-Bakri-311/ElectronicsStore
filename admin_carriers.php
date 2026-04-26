<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn=OpenCon();
if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; $stmt=$conn->prepare("DELETE FROM carriers WHERE id=?"); $stmt->bind_param("i",$id); $stmt->execute(); redirect('admin_carriers.php'); }
$result=$conn->query("SELECT * FROM carriers ORDER BY name");
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Carriers</title><link rel="stylesheet" href="css/style.css"></head><body><?php include 'navbar.php'; ?><div class="admin-container"><h2>Carriers List</h2><table><tr><th>ID</th><th>Name</th><th>Action</th></tr><?php while($r=$result->fetch_assoc()):?><tr><td><?=(int)$r['id']?></td><td><?=e($r['name'])?></td><td><a class="btn danger" onclick="return confirm('Delete carrier?')" href="admin_carriers.php?delete=<?=(int)$r['id']?>">Delete</a></td></tr><?php endwhile;?></table></div></body></html>
