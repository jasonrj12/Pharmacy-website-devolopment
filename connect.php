<?php
$servername = "localhost"; // Change this if your database is on a different server
$username = "root"; // MySQL username
$password = ""; // MySQL password (usually empty for localhost)
$dbname = "PillsStation_pharmacy"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
