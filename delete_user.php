<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gallerycafe";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $success_message = "User deleted successfully!";
} else {
    $error_message = "Error deleting user. Please try again.";
}

$stmt->close();
$conn->close();

header("Location: manage_users.php?success=" . urlencode($success_message) . "&error=" . urlencode($error_message));
exit();
?>
