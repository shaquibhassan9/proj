<?php
$conn = new mysqli("localhost", "root", "", "restaurant_db");

$data = json_decode(file_get_contents("php://input"), true);

$items = $conn->real_escape_string($data["items"]);
$total = $data["total"];
$customer = $conn->real_escape_string($data["customer"]);

$conn->query("INSERT INTO orders (items, total, customer) VALUES ('$items', '$total', '$customer')");

echo "OK";
?>
