<?php
session_start();
@include 'connect.php';  // Include the database connection file

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        // Check if connection is established
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      color: #333;
      background-color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h2 {
      font-size: 32px;
      margin-bottom: 20px;
      color: #333;
    }

    .login-form .field {
      margin-bottom: 15px;
      position: relative;
    }

    .login-form input[type="email"],
    .login-form input[type="password"] {
      padding: 12px;
      border-radius: 5px;
      border: 1px solid #ddd;
      width: 100%;
      font-size: 16px;
    }

    .login-form label {
      position: absolute;
      top: 14px;
      left: 15px;
      font-size: 16px;
      color: #999;
      pointer-events: none;
      transition: 0.2s ease all;
    }

    .login-form input:focus ~ label,
    .login-form input:not(:placeholder-shown) ~ label {
      top: -8px;
      left: 10px;
      font-size: 12px;
      color: #333;
      background-color: #f9f9f9;
      padding: 0 4px;
    }

    .login-form button {
      padding: 12px 25px;
      border-radius: 5px;
      background-color: #28a745;
      color: white;
      border: none;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      width: 100%;
    }

    .login-form button:hover {
      background-color: #1e7e34;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .message.show {
      display: block;
      color: red;
      font-weight: bold;
      margin-top: 15px;
    }

    .message i {
      cursor: pointer;
      margin-left: 5px;
      font-size: 14px;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
  <div class="login-container">
    <h2>Login</h2>
    <form class="login-form" action="login.php" method="POST">
      <div class="field">
        <input type="email" name="email" required placeholder=" ">
        <label>Email Address</label>
      </div>
      <div class="field">
        <input type="password" name="password" required placeholder=" ">
        <label>Password</label>
      </div>
      <button type="submit" name="submit">Login</button>
    </form>

    <?php if (isset($message)): ?>
      <div class="message show">
        <?php echo $message; ?><i class="fas fa-times" onclick="this.parentElement.style.display='none';"></i>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
