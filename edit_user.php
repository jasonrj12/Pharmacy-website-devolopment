<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PillsStation_pharmacy";

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
$error_message = '';
$success_message = '';

$sql = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $role);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $new_username, $new_email, $new_role, $user_id);

    if ($stmt->execute()) {
        $success_message = "User details updated successfully!";
    } else {
        $error_message = "Error updating user details. Please try again.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      color: #000;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #d32f2f;
      color: #fff;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
    }

    nav ul {
      list-style: none;
      display: flex;
      padding: 0;
      margin: 0;
    }

    nav ul li {
      margin: 0 15px;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-weight: 700;
    }

    .container {
      padding: 20px;
      max-width: 600px;
      margin: 0 auto;
    }

    h2 {
      color: #d32f2f;
      margin-bottom: 20px;
    }

    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    form input,
    form select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .btn {
      padding: 10px 15px;
      background-color: #d32f2f;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    .btn:hover {
      background-color: #b71c1c;
    }

    .message {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 4px;
    }

    .error {
      background-color: #f44336;
      color: #fff;
    }

    .success {
      background-color: #4caf50;
      color: #fff;
    }
  </style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="manage_reservations.php">Manage Reservations</a></li>
      <li><a href="manage_parking.php">Manage Parking</a></li>
      <li><a href="manage_users.php">Manage Users</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>
</header>

<main class="container">
  <h2>Edit User</h2>

  <?php if ($error_message): ?>
    <div class="message error"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <?php if ($success_message): ?>
    <div class="message success"><?php echo $success_message; ?></div>
  <?php endif; ?>

  <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
      <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Admin</option>
      <option value="user" <?php if ($role === 'user') echo 'selected'; ?>>User</option>
    </select>

    <button type="submit" class="btn">Update User</button>
  </form>
</main>

</body>
</html>
