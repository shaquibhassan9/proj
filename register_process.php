<?php
// register_process.php
session_start();

// --- 1. Database config (edit if different) ---
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";           // default XAMPP
$DB_NAME = "restaurant_db";

// --- 2. Connect safely (mysqli) ---
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    // Stop and show an error (for dev). In production log instead.
    die("DB Connection failed: " . $conn->connect_error);
}

// --- 3. Basic server-side validation ---
$required = ['role','username','Contact','password','confirm_password'];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        echo "<script>alert('Please fill all fields.'); window.location='register.html';</script>";
        exit();
    }
}

$role = $_POST['role'];
$username = trim($_POST['username']);
$contact = trim($_POST['Contact']);  // matches your form name
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match'); window.location='register.html';</script>";
    exit();
}

// Optional: extra validation
if (strlen($username) > 50 || strlen($contact) > 20) {
    echo "<script>alert('Input too long'); window.location='register.html';</script>";
    exit();
}

// --- 4. Hash password ---
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// --- 5. Check if username exists using prepared statement ---
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('Username already exists'); window.location='register.html';</script>";
    exit();
}
$stmt->close();

// --- 6. Insert new user using prepared statement ---
$insert = $conn->prepare("INSERT INTO users (role, username, contact, password) VALUES (?, ?, ?, ?)");
if (!$insert) {
    die("Prepare failed: " . $conn->error);
}
$insert->bind_param("ssss", $role, $username, $contact, $hashed_password);

if ($insert->execute()) {
    $insert->close();
    $conn->close();
    echo "<script>alert('Registration successful. Please login.'); window.location='login.html';</script>";
    exit();
} else {
    // Insert failed (show for dev). In production, log error.
    echo "Error inserting record: " . $conn->error;
    $insert->close();
    $conn->close();
    exit();
}
?>
