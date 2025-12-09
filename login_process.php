<?php
session_start();

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "restaurant_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// check all fields
if (
    empty($_POST['username']) ||
    empty($_POST['password']) ||
    empty($_POST['role'])
) {
    echo "<script>alert('Please fill all fields'); window.location='login.html';</script>";
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];
$role = $_POST['role']; // NEW

// fetch user
$stmt = $conn->prepare("SELECT id, role, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {

    // check role match
    if ($row['role'] !== $role) {
        echo "<script>alert('Incorrect role selected'); window.location='login.html';</script>";
        exit();
    }

    // verify password
    if (password_verify($password, $row['password'])) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($role === 'admin') {
            header("Location: admin_dashboard.html");
        } else {
            header("Location: index.html");
        }
        exit();

    } else {
        echo "<script>alert('Wrong password'); window.location='login.html';</script>";
        exit();
    }

} else {
    echo "<script>alert('User not found'); window.location='login.html';</script>";
    exit();
}
?>
