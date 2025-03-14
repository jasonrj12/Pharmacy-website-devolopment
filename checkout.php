<?php
session_start();
include 'connect.php'; // Include the database connection file

$user_id = $_SESSION['user_id'] ?? null; // Assuming user is logged in and user_id is stored in session
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Fetch the user's name
$query = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

// Check if user exists
if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $customer_name = $user['name']; // Assign user name to variable
} else {
    $customer_name = 'Guest'; // Fallback in case user not found
    $error_message = "User not found. Please log in again.";
}

// Fetch cart items from the database
$query = "SELECT c.*, p.name, p.price, p.image_url FROM `cart` c INNER JOIN `products` p ON c.product_id = p.id WHERE c.user_id = ?";
$cart_stmt = $conn->prepare($query);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

// Initialize cart_empty
$cart_empty = false;

// Check if cart is empty
if ($cart_result->num_rows > 0) {
    $cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
} else {
    $cart_items = [];
    $error_message = "Your cart is empty.";
    $cart_empty = true;
}

// Calculate the total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// If the user submits the checkout form
if (isset($_POST['submit_order'])) {
    // Ensure form fields are not empty
    $customer_name = $_POST['customer_name'];
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    if (empty($customer_name) || empty($shipping_address) || empty($payment_method)) {
        $error_message = "Please fill out all required fields.";
    } else {
        if ($payment_method === 'card_payment') {
            // Store checkout data in session and redirect to card payment page
            $_SESSION['checkout_data'] = [
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'shipping_address' => $shipping_address,
                'total_price' => $total_price,
                'cart_items' => $cart_items
            ];
            header('Location: card_payment.php');
            exit;
        } else {
            // Process order for Cash on Delivery
            $order_query = "INSERT INTO `orders` (user_id, customer_name, shipping_address, total_price, payment_method, order_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $order_stmt = $conn->prepare($order_query);
            $order_stmt->bind_param("issds", $user_id, $customer_name, $shipping_address, $total_price, $payment_method);
            $order_stmt->execute();

            // Get the last inserted order ID
            $order_id = $conn->insert_id;

            // Insert the order items into the order_items table
            foreach ($cart_items as $item) {
                $order_item_query = "INSERT INTO `order_items` (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $order_item_stmt = $conn->prepare($order_item_query);
                $order_item_stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $order_item_stmt->execute();
            }

            // Clear the cart after successful order submission
            $clear_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $clear_cart->bind_param("i", $user_id);
            $clear_cart->execute();

            // Redirect to a confirmation or success page
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit;
        }
    }
}
// If the user submits the checkout form
if (isset($_POST['submit_order'])) {
    // Ensure form fields are not empty
    $customer_name = $_POST['customer_name'];
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    if (empty($customer_name) || empty($shipping_address) || empty($payment_method)) {
        $error_message = "Please fill out all required fields.";
    } else {
        if ($payment_method === 'card_payment') {
            // Store checkout data in session and redirect to card payment page
            $_SESSION['checkout_data'] = [
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'shipping_address' => $shipping_address,
                'total_price' => $total_price,
                'cart_items' => $cart_items
            ];
            header('Location: card_payment.php');
            exit;
        } else {
            // Process order for Cash on Delivery
            $order_query = "INSERT INTO `orders` (user_id, customer_name, shipping_address, total_price, payment_method, order_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $order_stmt = $conn->prepare($order_query);
            $order_stmt->bind_param("issds", $user_id, $customer_name, $shipping_address, $total_price, $payment_method);
            $order_stmt->execute();

            // Get the last inserted order ID
            $order_id = $conn->insert_id;

            // Insert the order items into the order_items table
            foreach ($cart_items as $item) {
                $order_item_query = "INSERT INTO `order_items` (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $order_item_stmt = $conn->prepare($order_item_query);
                $order_item_stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $order_item_stmt->execute();
            }

            // Clear the cart after successful order submission
            $clear_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $clear_cart->bind_param("i", $user_id);
            $clear_cart->execute();

            // Redirect to a confirmation or success page
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <a href="products.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Back to Products</a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!$cart_empty): ?>
            <!-- Display Cart Items -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Cart Summary</h2>
                    <div class="space-y-4">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="w-16 h-16 object-cover rounded-md">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900"><?php echo $item['name']; ?></h3>
                                    <p class="text-sm text-gray-600">Price: $<?php echo $item['price']; ?></p>
                                    <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                                    <p class="text-sm font-semibold text-gray-800">Total: $<?php echo $item['price'] * $item['quantity']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 text-right">
                        <p class="text-lg font-bold text-gray-900">Total Price: $<?php echo $total_price; ?></p>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>
                    <form action="checkout.php" method="POST" class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700">Shipping Address</label>
                            <textarea name="shipping_address" id="shipping_address" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-24"></textarea>
                        </div>
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Payment Method --</option>
                                <option value="cash_on_delivery">Cash on Delivery</option>
                                <option value="card_payment">Card Payment</option>
                            </select>
                        </div>
                        <button type="submit" name="submit_order" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Submit Order</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">Your cart is empty. Please add products to your cart before proceeding to checkout.</p>
                <a href="products.php" class="inline-flex items-center px-4 py-2 mt-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Back to Products</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>