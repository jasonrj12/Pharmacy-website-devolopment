<?php
session_start();
include 'connect.php'; // Include the database connection file

// Check if the order_id is passed via the URL
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: index.php'); // Redirect if no order_id is provided
    exit;
}

// Fetch the order details
$order_query = "SELECT o.*, u.fullname as user_name FROM `orders` o 
                JOIN `users` u ON o.user_id = u.id WHERE o.id = ?";
$order_stmt = $conn->prepare($order_query);

// Check if prepare() succeeded
if ($order_stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Check if the query retrieved results
if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
} else {
    header('Location: index.php'); // Redirect if the order is not found
    exit;
}

// Fetch the order items
$order_items_query = "SELECT oi.*, p.name as product_name, p.price as product_price FROM `order_items` oi
                      JOIN `products` p ON oi.product_id = p.id WHERE oi.order_id = ?";
$order_items_stmt = $conn->prepare($order_items_query);
$order_items_stmt->bind_param("i", $order_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();

$order_items = [];
while ($item = $order_items_result->fetch_assoc()) {
    $order_items[] = $item;
}

// Define pharmacy details (hardcoded for now)
$pharmacy_name = "Pills Station Pharmacy";
$pharmacy_address = "123 Galle Road, Colombo 04, Sri Lanka";
$pharmacy_contact = "Phone: +94 (11) 123-4567 | Email: support@pillsstation.com";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Include html2pdf.js for generating PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="bg-gray-100 font-inter">
    <div class="max-w-4xl mx-auto p-6">
        <div id="receipt-content" class="bg-white rounded-lg shadow-md p-6">
            <!-- Pharmacy Header -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($pharmacy_name); ?></h1>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($pharmacy_address); ?></p>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($pharmacy_contact); ?></p>
                <div class="border-t border-gray-200 mt-4"></div>
            </div>

            <h2 class="text-2xl font-semibold text-gray-900 text-center mb-6">Order Confirmation</h2>

            <p class="text-lg text-gray-700">Thank you for your order, <?php echo htmlspecialchars($order['user_name']); ?>!</p>
            <p class="text-lg text-gray-700">Your order ID is: <strong>#<?php echo $order['id']; ?></strong></p>
            <p class="text-lg text-gray-700">Order Date: <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-4">Shipping Information</h3>
            <p class="text-lg text-gray-700"><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-4">Order Details</h3>
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-sm font-medium text-gray-700 border border-gray-200">Product</th>
                        <th class="p-3 text-sm font-medium text-gray-700 border border-gray-200">Price</th>
                        <th class="p-3 text-sm font-medium text-gray-700 border border-gray-200">Quantity</th>
                        <th class="p-3 text-sm font-medium text-gray-700 border border-gray-200">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-600 border border-gray-200"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="p-3 text-gray-600 border border-gray-200">$<?php echo number_format($item['product_price'], 2); ?></td>
                            <td class="p-3 text-gray-600 border border-gray-200"><?php echo $item['quantity']; ?></td>
                            <td class="p-3 text-gray-600 border border-gray-200">$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 class="text-lg font-bold text-gray-900 mt-4">Total Price: $<?php echo number_format($order['total_price'], 2); ?></h3>
            <p class="text-lg text-gray-700 mt-2"><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>

            <p class="text-lg text-gray-700 mt-4">If you have any questions about your order, feel free to contact us.</p>

            <!-- Pharmacy Footer -->
            <div class="text-center mt-6">
                <div class="border-t border-gray-200 mb-4"></div>
                <p class="text-sm text-gray-600">Thank you for choosing <?php echo htmlspecialchars($pharmacy_name); ?>!</p>
            </div>
        </div>

        <div class="flex justify-center space-x-4 mt-6">
            <a href="index.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Return to Home</a>
            <a href="status.php?order_id=<?php echo $order['id']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Track Order</a>
            <button id="download-receipt" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Download Receipt</button>
        </div>
    </div>

    <script>
        document.getElementById('download-receipt').addEventListener('click', function() {
            const element = document.getElementById('receipt-content');
            const opt = {
                margin: 1,
                filename: 'Order_Confirmation_#<?php echo $order['id']; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>