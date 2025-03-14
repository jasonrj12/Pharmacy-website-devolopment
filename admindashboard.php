<?php
session_start();
@include 'connect.php'; 

// Ensure the user is logged in as an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Set the upload directory
$uploadDir = "uploads/";

// Initialize message variable
$message = "";

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Add Product
    if ($action == 'add_product' && isset($_POST['submit_product'])) {
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $image_url = '';

        // Handle file upload if a file is uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image = $_FILES['product_image'];
            $imageName = time() . "_" . basename($image['name']); // Unique name for the image
            $imagePath = $uploadDir . $imageName;
            
            // Move the uploaded file to the server directory
            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                $image_url = $imagePath;
            } else {
                $message = "Error uploading image.";
            }
        }

        // Insert product information into the database using prepared statements
        $query = "INSERT INTO products (name, price, image_url) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sds", $product_name, $price, $image_url);
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error adding product: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }

        // Redirect to prevent form resubmission
        header("Location: admindashboard.php?message=" . urlencode($message));
        exit;
    }

    // Add Staff
    if ($action == 'add_staff' && isset($_POST['submit_staff'])) {
        $staff_name = $_POST['staff_name'];
        $staff_email = $_POST['staff_email'];
        $staff_password = password_hash($_POST['staff_password'], PASSWORD_DEFAULT); // Encrypt password

        // Insert staff information into the database using prepared statements
        $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'staff')";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sss", $staff_name, $staff_email, $staff_password);
            if ($stmt->execute()) {
                $message = "Staff member added successfully!";
            } else {
                $message = "Error adding staff: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }

        // Redirect to prevent form resubmission
        header("Location: admindashboard.php?message=" . urlencode($message));
        exit;
    }

    // Delete User
    if ($action == 'delete_user' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM users WHERE id = ? AND role = 'user'";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "User deleted successfully!";
            } else {
                $message = "Error deleting user: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
        header("Location: admindashboard.php?message=" . urlencode($message));
        exit;
    }

    // Delete Staff
    if ($action == 'delete_staff' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM users WHERE id = ? AND role = 'staff'";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Staff member deleted successfully!";
            } else {
                $message = "Error deleting staff: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
        header("Location: admindashboard.php?message=" . urlencode($message));
        exit;
    }
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Delete Product
    if ($action == 'delete_product' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM products WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Product deleted successfully!";
            } else {
                $message = "Error deleting product: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
        header("Location: admindashboard.php?message=" . urlencode($message));
        exit;
    }
}

// Fetch users, staff, products, and contact messages
$users = [];
$staff = [];
$products = [];
$contact_messages = []; // New array for contact messages

// Fetch Users
$user_query = "SELECT * FROM users WHERE role = 'user'";
if ($result = $conn->query($user_query)) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    $message = "Error fetching users: " . $conn->error;
}

// Fetch Staff
$staff_query = "SELECT * FROM users WHERE role = 'staff'";
if ($result = $conn->query($staff_query)) {
    $staff = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    $message = "Error fetching staff: " . $conn->error;
}

// Fetch Products
$product_query = "SELECT * FROM products";
if ($result = $conn->query($product_query)) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    $message = "Error fetching products: " . $conn->error;
}

// Fetch Contact Messages
$contact_query = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
if ($result = $conn->query($contact_query)) {
    $contact_messages = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} else {
    $message = "Error fetching contact messages: " . $conn->error;
}

// Capture message from URL parameter if exists
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pills Station Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-100 via-blue-100 to-green-100 font-inter">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gradient-to-b from-purple-600 to-indigo-600 text-white w-64 flex-shrink-0 hidden md:block">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-8 text-yellow-200">Admin Panel</h2>
                <nav class="space-y-2">
                    <a href="#products" class="block py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors bg-purple-700 text-yellow-100">Products</a>
                    <a href="#staff" class="block py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors text-yellow-100">Staff</a>
                    <a href="#users" class="block py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors text-yellow-100">Users</a>
                    <a href="#contact-messages" class="block py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors text-yellow-100">Contact Messages</a>
                    <a href="admindashboard.php?action=logout" class="block py-2.5 px-4 rounded-lg hover:bg-purple-700 transition-colors text-yellow-100 absolute bottom-6">Logout</a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-7xl mx-auto">
                <header class="mb-8 flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-purple-800">Admin Dashboard</h1>
                    <div class="text-lg font-medium text-purple-900">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                    </div>
                </header>

                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'Error') === false ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Add Product -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-blue-500" id="products">
                    <h2 class="text-xl font-semibold mb-6 text-blue-700"><i class="fas fa-box-open"></i> Add New Product</h2>
                    <form action="admindashboard.php?action=add_product" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label for="product_name" class="block text-sm font-medium text-blue-600">Product Name</label>
                            <input type="text" name="product_name" id="product_name" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-blue-600">Price</label>
                            <input type="number" name="price" id="price" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label for="product_image" class="block text-sm font-medium text-blue-600">Product Image</label>
                            <input type="file" name="product_image" id="product_image" accept="image/*" required class="mt-1 block w-full text-sm text-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="submit" name="submit_product" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"><i class="fas fa-plus"></i> Add Product</button>
                    </form>
                </div>

                <!-- Add Staff -->
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-green-500" id="staff">
                    <h2 class="text-xl font-semibold mb-6 text-green-700"><i class="fas fa-user-plus"></i> Add New Staff</h2>
                    <form action="admindashboard.php?action=add_staff" method="POST" class="space-y-4">
                        <div>
                            <label for="staff_name" class="block text-sm font-medium text-green-600">Staff Name</label>
                            <input type="text" name="staff_name" id="staff_name" required class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white">
                        </div>
                        <div>
                            <label for="staff_email" class="block text-sm font-medium text-green-600">Staff Email</label>
                            <input type="email" name="staff_email" id="staff_email" required class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white">
                        </div>
                        <div>
                            <label for="staff_password" class="block text-sm font-medium text-green-600">Staff Password</label>
                            <input type="password" name="staff_password" id="staff_password" required class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white">
                        </div>
                        <button type="submit" name="submit_staff" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"><i class="fas fa-plus"></i> Add Staff</button>
                    </form>
                </div>

                <!-- Users -->
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-yellow-500" id="users">
                    <h2 class="text-xl font-semibold mb-6 text-yellow-700"><i class="fas fa-users"></i> Users</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-yellow-200">
                            <thead class="bg-yellow-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-yellow-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-yellow-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-yellow-600 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-yellow-200">
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-yellow-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-yellow-800"><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-yellow-800"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="admindashboard.php?action=delete_user&id=<?php echo $user['id']; ?>" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Staff -->
                <div class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-pink-500" id="staff">
                    <h2 class="text-xl font-semibold mb-6 text-pink-700"><i class="fas fa-user-tie"></i> Staff</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-pink-200">
                            <thead class="bg-pink-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-pink-200">
                                <?php foreach ($staff as $staff_member): ?>
                                    <tr class="hover:bg-pink-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-pink-800"><?php echo htmlspecialchars($staff_member['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-pink-800"><?php echo htmlspecialchars($staff_member['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="admindashboard.php?action=delete_staff&id=<?php echo $staff_member['id']; ?>" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"><i class="fas fa-trash-alt"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-purple-500" id="products">
                    <h2 class="text-xl font-semibold mb-6 text-purple-700"><i class="fas fa-box"></i> Products</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-purple-200">
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Product Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-600 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-purple-200">
                                <?php foreach ($products as $product): ?>
                                    <tr class="hover:bg-purple-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-purple-800"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-purple-800"><?php echo htmlspecialchars($product['price']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="h-12 w-12 object-cover rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="admindashboard.php?action=delete_product&id=<?php echo $product['id']; ?>" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Contact Messages -->
                <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg shadow-md p-6 mb-6 border-l-4 border-teal-500" id="contact-messages">
                    <h2 class="text-xl font-semibold mb-6 text-teal-700"><i class="fas fa-envelope-open-text"></i> Contact Messages</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-teal-200">
                            <thead class="bg-teal-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-teal-600 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-teal-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-teal-600 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-teal-600 uppercase tracking-wider">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-teal-200">
                                <?php foreach ($contact_messages as $contact): ?>
                                    <tr class="hover:bg-teal-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-teal-800"><?php echo htmlspecialchars($contact['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-teal-800"><?php echo htmlspecialchars($contact['email']); ?></td>
                                        <td class="px-6 py-4 text-teal-800"><?php echo htmlspecialchars($contact['message']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-teal-800"><?php echo htmlspecialchars($contact['submitted_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>