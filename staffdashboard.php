<?php
session_start();
@include 'connect.php'; // Include the database connection file

// Ensure the user is logged in as a staff
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'staff') {
    header("Location: login.php");
    exit;
}

// Fetch products
$products = [];
$query = "SELECT * FROM products";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch consultation messages and details
$messages = [];
$query = "SELECT * FROM consultations";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch prescriptions
$prescriptions = [];
$query = "SELECT * FROM prescriptions";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $prescriptions = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch orders, including customer name and product name
$orders = [];
$query = "
SELECT o.id, o.user_id, o.shipping_address, o.total_price, o.payment_method, o.order_date, 
       oi.quantity, o.status, u.name AS customer_name, p.name AS product_name
FROM orders o
INNER JOIN users u ON o.user_id = u.id
INNER JOIN order_items oi ON o.id = oi.order_id
INNER JOIN products p ON oi.product_id = p.id
";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}


// Handle replying to consultation messages
if (isset($_POST['reply'])) {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply_message'];

    // Validate that the message_id exists in the consultations table
    $query = "SELECT id FROM consultations WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Insert the reply into the database
        $query = "INSERT INTO consultation_replies (message_id, reply) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $message_id, $reply);
        if ($stmt->execute()) {
            $message = "Reply sent successfully!";
        } else {
            $message = "Error: {$stmt->error}";
        }
    } else {
        $message = "Error: Invalid message ID.";
    }
}
if (isset($_GET['delete_message'])) {
    $message_id = $_GET['delete_message'];

    // Validate that the message_id is a number
    if (is_numeric($message_id)) {
        // Delete related replies first (if foreign key constraints exist)
        $query = "DELETE FROM consultation_replies WHERE message_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();

        // Now delete the consultation message
        $query = "DELETE FROM consultations WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $message_id);
        if ($stmt->execute()) {
            $message = "Consultation message deleted successfully!";
        } else {
            $message = "Error: {$stmt->error}";
        }
    } else {
        $message = "Invalid message ID.";
    }
}


// Handle deleting prescriptions
if (isset($_GET['delete_prescription'])) {
    $prescription_id = $_GET['delete_prescription'];

    // Delete the prescription from the database
    $query = "DELETE FROM prescriptions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $prescription_id);
    $stmt->execute();
    $message = "Prescription deleted successfully!";
}

// Handle marking orders as processed
if (isset($_GET['mark_processed'])) {
    $order_id = $_GET['mark_processed'];
    
    // Update the order status to 'Packed' (or another appropriate next status)
    $query = "UPDATE orders SET status = 'Packed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $message = "Order status updated to 'Packed'!";
    
    // Redirect back to the staff dashboard
    header("Location: staffdashboard.php");
    exit;
}
// Handle marking orders as processed
if (isset($_GET['mark_processed'])) {
    $order_id = $_GET['mark_processed'];
    
    // Update the order status to 'Packed'
    $query = "UPDATE orders SET status = 'Packed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Update the tracking status to 'Packed'
    $query = "UPDATE tracking SET status = 'Packed' WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $message = "Order status updated to 'Packed'!";
    
    // Redirect back to the staff dashboard
    header("Location: staffdashboard.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update the order status in the database
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();

    $message = "Order status updated to '$order_status'!";
    // Optional redirect or feedback message display
    header("Location: staffdashboard.php");
    exit;
}


// Handle deleting orders
if (isset($_GET['delete_order'])) {
    $order_id = $_GET['delete_order'];
    
    // First, delete related order items
    $query = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Now, delete the order itself
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $message = "Order deleted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Pills Station Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-900 text-white w-64 flex-shrink-0 hidden md:block">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-8">Staff Panel</h2>
                <nav class="space-y-2">
                    <a href="#products" class="block py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-colors bg-gray-800">Products</a>
                    <a href="#consultations" class="block py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-colors">Consultations</a>
                    <a href="#prescriptions" class="block py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-colors">Prescriptions</a>
                    <a href="#orders" class="block py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-colors">Orders</a>
                    <a href="login.php" class="block py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-colors absolute bottom-6">Logout</a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-7xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
            </header>

            <?php if (isset($message)): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700">
                <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Product Management -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="products">
                <h2 class="text-xl font-semibold mb-6">Product Management</h2>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Consultation -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="consultations">
                <h2 class="text-xl font-semibold mb-6">Consultation</h2>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reply</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['phone']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['date']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['time']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($message['message']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <form action="" method="POST">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <textarea name="reply_message" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <button type="submit" name="reply" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-reply mr-2"></i>Send Reply
                                </button>
                            </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <a href="?delete_message=<?php echo $message['id']; ?>" onclick="return confirm('Are you sure?')" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash-alt mr-2"></i>Delete
                            </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No messages found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

           
                <!-- Prescriptions -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="prescriptions">
                    <h2 class="text-xl font-semibold mb-6">Prescriptions</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prescription ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($prescriptions)): ?>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['id']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['patient_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['contact_details']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['address']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><a href="<?php echo htmlspecialchars($prescription['file_path']); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-500">View</a></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="?delete_prescription=<?php echo $prescription['id']; ?>" onclick="return confirm('Are you sure?')" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No prescriptions found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="orders">
                    <h2 class="text-xl font-semibold mb-6">Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['product_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['total_price']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['order_date']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="" method="POST">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="order_status" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="Order Placed" <?php if ($order['status'] == 'Order Placed') echo 'selected'; ?>>Order Placed</option>
                                                        <option value="Order Received" <?php if ($order['status'] == 'Order Received') echo 'selected'; ?>>Order Received</option>
                                                        <option value="Packed" <?php if ($order['status'] == 'Packed') echo 'selected'; ?>>Packed</option>
                                                        <option value="Out for Delivery" <?php if ($order['status'] == 'Out for Delivery') echo 'selected'; ?>>Out for Delivery</option>
                                                        <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="?delete_order=<?php echo $order['id']; ?>" onclick="return confirm('Are you sure?');" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>