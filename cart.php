<?php
session_start();
include 'connect.php'; // Include the database connection file

$user_id = $_SESSION['user_id'] ?? null; // Assuming user is logged in and user_id is stored in session
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $delete_stmt = $conn->prepare("DELETE FROM `cart` WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $delete_id, $user_id);
    $delete_stmt->execute();
    header('Location: cart.php'); // Refresh the page
    exit;
}

// Fetch cart items
$query = $conn->prepare("SELECT c.*, p.name, p.price, p.image_url FROM `cart` c JOIN `products` p ON c.product_id = p.id WHERE c.user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$cart_items = $query->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    
    // Update the quantity in the cart
    $update_cart = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
    $update_cart->bind_param("ii", $quantity, $cart_id);
    $update_cart->execute();
    $message = "Cart updated!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">
    <div class="container max-w-4xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <a href="products.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Your Cart</h1>
            <div class="w-24"></div> <!-- Spacer to balance the layout -->
        </div>

        <?php if (isset($message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php foreach ($cart_items as $item): ?>
                <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="w-24 h-24 object-cover rounded-md">
                    <div class="flex-1 ml-6">
                        <h3 class="text-xl font-semibold text-gray-900"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-600">Price: $<?php echo $item['price']; ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <form action="" method="POST" class="flex items-center space-x-4">
                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                            <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" name="update_cart" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Update Cart</button>
                        </form>
                        <a href="cart.php?action=delete&id=<?php echo $item['id']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="checkout.php" class="block text-center mt-6 text-indigo-600 hover:text-indigo-500 font-medium">Proceed to Checkout</a>
    </div>
</body>
</html>