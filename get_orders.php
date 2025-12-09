<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "restaurant_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "DB Connection Failed"]);
    exit;
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");

$orders = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);
?>
