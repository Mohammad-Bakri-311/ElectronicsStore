<?php
session_start(); require 'db_connection.php'; require_once 'helpers.php'; require_admin(); $conn = OpenCon();
$msg='';
if (isset($_POST['add'])) {
    $name=trim($_POST['name']); $price=(float)$_POST['price']; $stock=(int)$_POST['stock']; $img=trim($_POST['img']);
    $stmt=$conn->prepare("INSERT INTO products (name, price, stock, img) VALUES (?, ?, ?, ?)"); $stmt->bind_param("sdis", $name,$price,$stock,$img); $stmt->execute(); $msg='Product added.';
}
if (isset($_POST['update'])) {
    $id=(int)$_POST['product_id']; $price=(float)$_POST['price']; $stock=(int)$_POST['stock'];
    $stmt=$conn->prepare("UPDATE products SET price=?, stock=? WHERE id=?"); $stmt->bind_param("dii", $price,$stock,$id); $stmt->execute(); $msg='Product updated.';
}
if (isset($_POST['delete'])) {
    $id=(int)$_POST['product_id'];
    $stmt=$conn->prepare("DELETE FROM products WHERE id=?"); $stmt->bind_param("i", $id); $stmt->execute(); $msg='Product deleted.';
}
$result=$conn->query("SELECT id,name,price,stock,img FROM products ORDER BY id DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Manage Products</title><link rel="stylesheet" href="css/style.css"></head><body><?php include 'navbar.php'; ?><div class="admin-container"><h2>Manage Products</h2><?php if($msg):?><p class="success"><?=e($msg)?></p><?php endif;?>
<form method="post" class="card"><h3>Add Product</h3><input name="name" placeholder="Product Name" required><input type="number" step="0.01" name="price" placeholder="Price" required><input type="number" name="stock" placeholder="Stock" required><input name="img" placeholder="photos/example.jpg" required><button name="add">Add Product</button></form><br>
<table><tr><th>Name</th><th>Image</th><th>Price</th><th>Stock</th><th>Actions</th></tr><?php while($r=$result->fetch_assoc()):?><tr><form method="post"><td><?=e($r['name'])?></td><td><?=e($r['img'])?></td><td><input type="number" step="0.01" name="price" value="<?=e($r['price'])?>"></td><td><input type="number" name="stock" value="<?=e($r['stock'])?>"></td><td><input type="hidden" name="product_id" value="<?=(int)$r['id']?>"><button name="update">Update</button> <button class="danger" name="delete" onclick="return confirm('Delete product?')">Delete</button></td></form></tr><?php endwhile;?></table></div></body></html>
