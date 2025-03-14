<?php
session_start();
@include 'connect.php';  // Include the database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Collect and sanitize form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        // Prepare SQL query to check if the user exists
        $query = "SELECT * FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("s", $email);

            // Execute the statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        // Start a session and store user info
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_role'] = $user['role'];  // Store user role in session
                        $_SESSION['user_name'] = $user['fullname']; // Store the user's name
                        $_SESSION['user_email'] = $user['email']; // Store the user's email

                        // Redirect based on role
                        if ($user['role'] == 'admin') {
                            header("Location: admindashboard.php");  // Redirect to admin dashboard
                        } elseif ($user['role'] == 'staff') {
                            header("Location: staffdashboard.php");  // Redirect to staff dashboard
                        } else {
                            header("Location: index.php");  // Redirect to index for regular users
                        }
                        exit;
                    } else {
                        $message = "Incorrect password.";
                    }
                } else {
                    $message = "No user found with this email address.";
                }
            } else {
                $message = "Error executing query: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $message = "Error preparing the SQL statement.";
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!-- HTML part -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter flex items-center justify-center min-h-screen">
<?php include 'header.php'; ?>
  <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Login</h2>
    <form class="space-y-6" action="login.php" method="POST">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="password" required placeholder="Enter your password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <button type="submit" name="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Login</button>
    </form>

    <!-- Link to signup page -->
    <p class="mt-6 text-center text-sm text-gray-600">Don't have an account? <a href="signup.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up here</a></p>

    <?php if (isset($message)): ?>
      <div class="mt-4 p-4 rounded-lg bg-red-100 text-red-700 flex justify-between items-center">
        <?php echo $message; ?>
        <i class="fas fa-times cursor-pointer" onclick="this.parentElement.style.display='none';"></i>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>