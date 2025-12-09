<?php
$conn = new mysqli("localhost", "root", "", "restaurant_db");

$id = $_GET["id"];

$conn->query("UPDATE orders SET status='Completed' WHERE id=$id");

echo "DONE";
?>
