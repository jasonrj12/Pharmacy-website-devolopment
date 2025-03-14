<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order - HealthPlus Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    /* Reset Styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* General Styling */
    body {
      font-family: 'Montserrat', sans-serif;
      color: #fff;
      background-image: url('https://img.freepik.com/free-photo/various-type-colorful-pills-isolated-white-background-with-space-writing-text_23-2148129587.jpg?t=st=1730100437~exp=1730104037~hmac=4e57587e1efa5344d98b4f33b3d125f9664ea7dad7de98bd7405323b07e2a91d&w=1060'); 
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      overflow-x: hidden;
    }

    /* Main Content Styling */
    main {
      margin-top: 100px;
      text-align: center;
      margin-bottom: 150px;
    }

    .order-section {
      padding: 20px;
      background-color: rgba(0, 0, 0, 0.5);
      border-radius: 8px;
      margin: 50px auto;
      width: 80%;
    }

    .order-section h2 {
      font-size: 36px;
      margin-bottom: 20px;
    }

    .order-table {
      width: 100%;
      margin: 20px 0;
      border-collapse: collapse;
    }

    .order-table th, .order-table td {
      padding: 12px;
      border: 1px solid #fff;
      text-align: left;
    }

    .order-table th {
      background-color: rgba(0, 0, 0, 0.6);
    }

    .total-price {
      font-weight: bold;
      font-size: 24px;
      margin: 20px 0;
    }

    /* Button Styling */
    .submit-order {
      padding: 10px 20px;
      border-radius: 25px;
      background-color: #28a745;
      color: white;
      border: none;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .submit-order:hover {
      background-color: #1e7e34;
    }

    /* Footer */
    footer {
      background-color: rgba(0, 0, 0, 0.8);
      color: #fff;
      text-align: center;
      padding: 10px 0;
      font-size: 14px;
      position: relative;
      bottom: 0;
      width: 100%;
    }

    footer p {
      margin: 0;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
  <main>
    <section class="order-section">
      <h2>Your Order</h2>
      <table class="order-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Retrieve product and price from URL
          $product = htmlspecialchars($_GET['product']);
          $price = floatval($_GET['price']);
          ?>
          <tr>
            <td><?php echo $product; ?></td>
            <td>$<?php echo number_format($price, 2); ?></td>
            <td><input type="number" value="1" min="1" onchange="updateSubtotal(this, <?php echo $price; ?>)"></td>
            <td class="subtotal">$<?php echo number_format($price, 2); ?></td>
          </tr>
        </tbody>
      </table>
      <div class="total-price" id="total-price">Total: $<?php echo number_format($price, 2); ?></div>
      <button class="submit-order" onclick="submitOrder()">Submit Order</button>
    </section>
  </main>
  <?php include 'footer.php'; ?>

  <script>
    function updateSubtotal(input, price) {
      const quantity = input.value;
      const subtotalCell = input.closest('tr').querySelector('.subtotal');
      const subtotal = (price * quantity).toFixed(2);
      subtotalCell.textContent = `$${subtotal}`;
      calculateTotal();
    }

    function calculateTotal() {
      const subtotals = document.querySelectorAll('.subtotal');
      let total = 0;
      subtotals.forEach(cell => {
        const value = parseFloat(cell.textContent.replace('$', ''));
        total += value;
      });
      document.getElementById('total-price').textContent = `Total: $${total.toFixed(2)}`;
    }

    function submitOrder() {
      const product = '<?php echo urlencode($product); ?>';
      const quantity = document.querySelector('input[type="number"]').value;
      const total = document.getElementById('total-price').textContent.replace('Total: $', '');
      window.location.href = `order_success.php?product=${product}&quantity=${quantity}&total=${total}`;
    }
  </script>
</body>
</html>
