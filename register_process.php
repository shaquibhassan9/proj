<?php
session_start();

$conn = new mysqli("localhost", "root", "", "restaurant_db");
if ($conn->connect_error) {
    die("DB error");
}

// validate
$required = ['role','username','contact','password','confirm_password'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo "<script>alert('Fill all fields'); window.location='register.html';</script>";
        exit();
    }
}

$role = $_POST['role'];
$username = trim($_POST['username']);
$contact = trim($_POST['contact']);
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match'); window.location='register.html';</script>";
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// check existing user
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Username already exists'); window.location='register.html';</script>";
    exit();
}
$stmt->close();

// insert user
$insert = $conn->prepare(
  "INSERT INTO users (role, username, contact, password) VALUES (?, ?, ?, ?)"
);
$insert->bind_param("ssss", $role, $username, $contact, $hashed);
$insert->execute();

echo "<script>alert('Registration successful'); window.location='login.html';</script>";
exit();
