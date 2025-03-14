<?php
session_start();
@include 'connect.php'; 

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Collect and sanitize form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Input validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $message = "Username must be between 3 and 20 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if the email or username is already registered
        $query = "SELECT * FROM users WHERE email = ? OR username = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Email or Username is already registered.";
            } else {
                // Hash the password and insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
                
                if ($insert_stmt = $conn->prepare($insert_query)) {
                    $insert_stmt->bind_param("sss", $username, $email, $hashed_password);

                    if ($insert_stmt->execute()) {
                        $message = "Account created successfully!";
                    } else {
                        $message = "Error creating account: " . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                } else {
                    $message = "Error preparing the insert statement.";
                }
            }
            $stmt->close();
        } else {
            $message = "Error preparing the SQL statement.";
        }
    }
    $conn->close();
}
?>

<!-- HTML part -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter flex items-center justify-center min-h-screen">
  <?php include 'header.php'; ?>
  <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Create Account</h2>
    <form class="space-y-6" action="signup.php" method="POST">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" id="username" required placeholder="Enter your username" minlength="3" maxlength="20" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="password" required placeholder="Enter your password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <button type="submit" name="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Signup</button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">Already have an account? <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Log in here</a></p>

    <?php if (isset($message)): ?>
      <div class="mt-4 p-4 rounded-lg <?php echo strpos($message, 'successfully') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> flex justify-between items-center">
        <?php echo $message; ?>
        <i class="fas fa-times cursor-pointer" onclick="this.parentElement.style.display='none';"></i>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>