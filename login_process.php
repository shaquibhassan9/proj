<?php
session_start();

$conn = new mysqli("localhost", "root", "", "restaurant_db");
if ($conn->connect_error) {
    die("DB Connection Failed");
}

// validate input
if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>alert('Fill all fields'); window.location='login.html';</script>";
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// fetch user
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found'); window.location='login.html';</script>";
    exit();
}

$user = $result->fetch_assoc();

// verify password
if (!password_verify($password, $user['password'])) {
    echo "<script>alert('Wrong password'); window.location='login.html';</script>";
    exit();
}

// login success
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

// redirect by role (FROM DATABASE)
if ($user['role'] === 'admin') {
    header("Location: admin_dashboard.html");
} else {
    header("Location: index.html");
}
exit();
