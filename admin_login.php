<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Pills Station Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      color: #fff;
      background-image: url('https://www.cheapflights.com/news/wp-content/uploads/sites/136/2015/07/theme_medication-pills-pill-bottle-shutterstock-portfolio_549397639-1536x1024.jpg'); 
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      overflow-x: hidden;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: rgba(0, 0, 0, 0.7);
      padding: 10px 20px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
    }

    .header-logo {
      display: flex;
      align-items: center;
    }

    .header-logo img {
      max-width: 50px;
      margin-right: 10px;
    }

    .header-logo h1 {
      font-size: 24px;
      font-weight: 700;
      color: #fff;
    }

    main {
      margin-top: 100px;
      text-align: center;
      margin-bottom: 150px;
    }

    .login-container {
      background-color: rgba(0, 0, 0, 0.5);
      border-radius: 8px;
      padding: 40px;
      margin: 0 auto;
      max-width: 400px;
    }

    .login-container h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #28a745;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      background-color: #fff;
      color: #333;
      font-size: 16px;
    }

    .login-container button {
      width: 100%;
      padding: 10px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 25px;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .login-container button:hover {
      background-color: #1e7e34;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

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

    .footer-address,
    .footer-phone {
      font-size: 12px;
      margin-top: 5px;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-logo">
      <img src="uploads/Pills_Station.png" alt="Pills Station Logo"> 
      <h1>Pills Station Pharmacy</h1>
    </div>
  </header>
  <main>
    <div class="login-container">
      <h2>Admin Login</h2>
      <form action="admindashboard.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
    </div>
  </main>
  <footer>
  <p>&copy; 2025 Pills Station Pharmacy. All Rights Reserved.</p>
  <p>123 Galle Road, colombo 06,  | Phone: 011-123-4567</p>
  </footer>
</body>
</html>
