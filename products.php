<?php
session_start();
include 'connect.php'; // Include the database connection file

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header
include 'header.php';

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

// Handle adding products to cart
if (isset($_POST['add_to_cart']) && $user_id) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if product is already in the cart
    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
    $check_cart->bind_param("ii", $user_id, $product_id);
    $check_cart->execute();
    $check_cart_result = $check_cart->get_result();

    if ($check_cart_result->num_rows > 0) {
        // Update quantity if product is already in the cart
        $update_cart = $conn->prepare("UPDATE `cart` SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $update_cart->bind_param("iii", $quantity, $user_id, $product_id);
        $update_cart->execute();
    } else {
        // Insert new product into the cart
        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_cart->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_cart->execute();
    }
    $message = "Product added to cart!";
}

// Fetch products from the database
$query = "SELECT * FROM `products`";
$result = $conn->query($query);

// Check if any products exist
$products = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: rgb(96, 90, 90);
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 85%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        h1 {
            text-align: center;
            font-size: 2.8em;
            color: #333;
            margin-bottom: 20px;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .view-cart a {
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .view-cart a:hover {
            background-color: #0056b3;
        }
        .message {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            justify-items: center;
        }
        .product {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 300px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .product h3 {
            font-size: 1.6em;
            margin: 15px 0;
            color: #333;
        }
        .product p {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form input[type="number"] {
            padding: 5px;
            font-size: 1em;
            width: 50%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        form button:hover {
            background-color: #218838;
        }

        /* Search Bar Styles */
        .search-container {
            display: flex;
            align-items: center;
        }
        .search-container input[type="text"] {
            padding: 8px 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            width: 200px;
            max-width: 100%;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-container input[type="text"]:focus {
            border-color: #007bff;
        }
        .search-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
            transition: background-color 0.3s;
        }
        .search-container button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            h1 {
                font-size: 2.2em;
            }
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container {
                margin-bottom: 10px;
            }
            .search-container input[type="text"] {
                width: 100%;
            }
            .view-cart a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Available Products</h1>

        <!-- Header Actions: Search Bar and View Cart -->
        <div class="header-actions">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search products...">
                <button type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="view-cart">
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> View Cart</a>
            </div>
        </div>

        <!-- Message for adding to cart -->
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Product List -->
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                    <?php if ($user_id): ?>
                        <form action="" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" min="1" value="1" required>
                            <button type="submit" name="add_to_cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <p>Please <a href="login.php"><i class="fas fa-sign-in-alt"></i> log in</a> to add items to the cart.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Include footer if needed -->
    <?php include 'footer.php'; ?>

    <!-- JavaScript for Search Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const products = document.querySelectorAll('.product');

            searchInput.addEventListener('input', function() {
                const searchTerm = searchInput.value.trim().toLowerCase();

                products.forEach(product => {
                    const productName = product.querySelector('h3').textContent.toLowerCase();
                    if (productName.includes(searchTerm)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>