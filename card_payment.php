<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['checkout_data'])) {
    header('Location: checkout.php');
    exit;
}

$checkout_data = $_SESSION['checkout_data'];

if (isset($_POST['submit_card'])) {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // Basic validation (add more as needed)
    if (empty($card_number) || empty($expiry_date) || empty($cvv)) {
        $error_message = "Please fill out all card details.";
    } else {
        // Simulate payment gateway integration (replace with actual API call)
        $payment_successful = true; // This would be the result of the payment gateway API call
        $transaction_id = "txn_" . uniqid(); // Example transaction ID from payment gateway
        $masked_card_number = "**** **** **** " . substr($card_number, -4); // Masked card number

        if ($payment_successful) {
            // Insert order into `orders` table
            $order_query = "INSERT INTO `orders` (user_id, customer_name, shipping_address, total_price, payment_method, order_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $order_stmt = $conn->prepare($order_query);
            $payment_method = 'card_payment';
            $order_stmt->bind_param("issds", $user_id, $checkout_data['customer_name'], $checkout_data['shipping_address'], $checkout_data['total_price'], $payment_method);
            $order_stmt->execute();

            $order_id = $conn->insert_id;

            // Insert order items into `order_items` table
            foreach ($checkout_data['cart_items'] as $item) {
                $order_item_query = "INSERT INTO `order_items` (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $order_item_stmt = $conn->prepare($order_item_query);
                $order_item_stmt->bind_param("iiii", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $order_item_stmt->execute();
            }

            // Insert payment metadata into `payments` table
            $payment_query = "INSERT INTO `payments` (order_id, user_id, transaction_id, payment_method, masked_card_number, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, 'completed', NOW())";
            $payment_stmt = $conn->prepare($payment_query);
            $payment_stmt->bind_param("iisss", $order_id, $user_id, $transaction_id, $payment_method, $masked_card_number);
            $payment_stmt->execute();

            // Clear cart
            $clear_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $clear_cart->bind_param("i", $user_id);
            $clear_cart->execute();

            unset($_SESSION['checkout_data']); // Clear session data
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit;
        } else {
            $error_message = "Payment failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">
    <div class="max-w-md mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Card Payment</h1>
            <a href="checkout.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Back</a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Card Preview Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Credit Card Preview</h2>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold">Credit Card</span>
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M21 4H3a2 2 0 00-2 2v12a2 2 0 002 2h18a2 2 0 002-2V6a2 2 0 00-2-2zM1 10h22M7 14h4m2 0h4"/></svg>
                </div>
                <div class="text-xl font-mono tracking-widest mb-4" id="card-number-preview">•••• •••• •••• ••••</div>
                <div class="flex justify-between">
                    <div>
                        <span class="text-xs uppercase">Expires</span>
                        <div class="text-sm font-mono" id="expiry-date-preview">MM/YY</div>
                    </div>
                    <div>
                        <span class="text-xs uppercase">CVV</span>
                        <div class="text-sm font-mono" id="cvv-preview">•••</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Form Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Enter Card Details</h2>
            </div>
            <form action="card_payment.php" method="POST" class="space-y-4" id="card-form">
                <div>
                    <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" id="card_number" name="card_number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base font-mono" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCardNumber(this)">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base font-mono" placeholder="MM/YY" maxlength="5" oninput="formatExpiryDate(this)">
                    </div>
                    <div>
                        <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                        <input type="text" id="cvv" name="cvv" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base font-mono" placeholder="123" maxlength="3" oninput="updateCVV(this)">
                    </div>
                </div>
                <button type="submit" name="submit_card" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Pay Now</button>
            </form>
        </div>
    </div>

    <script>
        // Format card number with spaces every 4 digits
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, ''); // Remove non-digits
            value = value.slice(0, 16); // Limit to 16 digits
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            input.value = formatted;

            // Update card preview
            document.getElementById('card-number-preview').textContent = formatted || '•••• •••• •••• ••••';
        }

        // Format expiry date as MM/YY
        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, ''); // Remove non-digits
            value = value.slice(0, 4); // Limit to 4 digits
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            input.value = value;

            // Update expiry preview
            document.getElementById('expiry-date-preview').textContent = value || 'MM/YY';
        }

        // Update CVV preview
        function updateCVV(input) {
            let value = input.value.replace(/\D/g, ''); // Remove non-digits
            value = value.slice(0, 3); // Limit to 3 digits
            input.value = value;

            // Update CVV preview
            document.getElementById('cvv-preview').textContent = value || '•••';
        }
    </script>
</body>
</html>