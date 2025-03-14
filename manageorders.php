<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Orders - Admin Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap">
  <style>
    /* Similar styling as admindashboard.php */
    /* Add specific styles for managing orders */
  </style>
</head>
<body>

<header>
  <h1>Manage Orders</h1>
</header>

<div class="container">
  <aside class="sidebar">
    <ul>
      <li><a href="admindashboard.php">Dashboard Home</a></li>
      <li><a href="manageproducts.php">Manage Products</a></li>
      <li><a href="manageorders.php">Manage Orders</a></li>
      <li><a href="managecustomers.php">Manage Customers</a></li>
      <li><a href="analytics.php">View Analytics</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </aside>

  <main class="content">
    <h2>Orders List</h2>
    <!-- Add functionality to list, update, and delete orders -->
    <p>Here you can manage orders.</p>
  </main>
</div>

<footer>
  <p>&copy; 2025 Pills Station Pharmacy Admin Dashboard</p>
</footer>

</body>
</html>
