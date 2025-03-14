<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection
include 'connect.php'; // Ensure this path is correct (relative to edit-profile.php)

// Verify database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
$query = "SELECT fullname, email, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found in database for ID: " . $user_id);
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $profile_image = $user['profile_image']; // Default to existing image

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profile_images/';
        
        // Ensure upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        if (!is_writable($upload_dir)) {
            die("Upload directory is not writable: " . $upload_dir);
        }

        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        if (in_array($file_extension, $allowed_extensions) && $_FILES['profile_image']['size'] <= $max_file_size) {
            $uploaded_file = $upload_dir . time() . '_' . basename($_FILES['profile_image']['name']);
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploaded_file)) {
                $profile_image = $uploaded_file; // Update to new image path
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type or size exceeds 2MB.";
        }
    } elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "File upload error: " . $_FILES['profile_image']['error'];
    }

    // Update user information in the database
    if (!isset($error)) {
        $update_query = "UPDATE users SET fullname = ?, email = ?, profile_image = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            die("Update prepare failed: " . $conn->error);
        }
        $stmt->bind_param('sssi', $fullname, $email, $profile_image, $user_id);
        if ($stmt->execute()) {
            $_SESSION['fullname'] = $fullname; // Update session variable
            $_SESSION['email'] = $email;       // Update session variable
            header('Location: index.php');     // Redirect after success
            exit();
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Pills Station Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">
    <header class="bg-indigo-600 text-white p-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold">Pills Station Pharmacy</h1>
        </div>
    </header>

    <main class="max-w-lg mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Edit Profile</h2>

            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="edit-profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="profile_image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Max file size: 2MB. Accepted formats: jpg, jpeg, png.</p>
                    <?php if ($user['profile_image']): ?>
                        <p class="mt-2 text-sm text-gray-600">Current image: <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="inline h-10 w-10 rounded-full"></p>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Update Profile</button>
                </div>
            </form>

            <a href="index.php" class="block text-center mt-4 text-indigo-600 hover:text-indigo-500 font-medium">Back to Profile</a>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>