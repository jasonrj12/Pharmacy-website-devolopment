<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}
include 'connect.php'; // Include database connection

// Fetch the latest user data (e.g., profile picture, fullname, email) if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT `fullname`, `email`, `profile_image` FROM `users` WHERE `id` = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Update session variables with the latest data
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_image'] = $user['profile_image'];
    }
}
?>

<header>
    <div class="header-logo">
        <img src="uploads/Pills_Station.png" alt="Pills Station Logo">
        <h1>Pills Station Pharmacy</h1>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
            <li><a href="products.php"><i class="fas fa-pills"></i> Products</a></li>
            <li><a href="consultation.php"><i class="fas fa-comments"></i> Consultation</a></li>
            <li><a href="services.php"><i class="fas fa-concierge-bell"></i> Pharmacy Services</a></li>
            <li><a href="doctor_prescription.php"><i class="fas fa-prescription"></i> Doctor Prescription</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User Profile Dropdown -->
                <li class="dropdown">
                  <a href="#" class="dropbtn">
                    <!-- Display user profile image dynamically if available -->
                    <img src="<?php echo isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) ? htmlspecialchars($_SESSION['profile_image']) . '?v=' . time() : 'icon.jpeg'; ?>" alt="User" class="user-icon">
                    <?php 
                    // Check if fullname is set, otherwise default to 'User'
                    echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'User'; 
                    ?>
                  </a>
                  <div class="dropdown-content">
                    <div class="profile-card">
                      <!-- Display user profile image -->
                      <img src="<?php echo isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) ? htmlspecialchars($_SESSION['profile_image']) . '?v=' . time() : 'profile-icon.png'; ?>" alt="User" class="profile-card-img">
                      <div class="profile-card-info">
                        <h3><?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'User'; ?></h3>
                        <p><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Not Provided'; ?></p>
                      </div>
                      <a href="status.php" class="edit-profile-btn"><i class="fas fa-box"></i> Placed Order</a>
                      <a href="edit-profile.php" class="edit-profile-btn"><i class="fas fa-user-edit"></i> Edit Profile</a>
                      <!-- Logout Option -->
                      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                  </div>
                </li>
            <?php else: ?>
                <li><a href="login.php">Account</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- JavaScript to manage dropdown functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.dropdown');
    const dropdownContent = dropdown.querySelector('.dropdown-content');

    // Toggle dropdown visibility on click
    dropdown.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownContent.classList.toggle('show');
    });

    // Close the dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!dropdown.contains(event.target)) {
            dropdownContent.classList.remove('show');
        }
    });
});
</script>

<!-- CSS Styling -->
<style>
    /* Header Styles */
   /* General Reset */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Poppins', sans-serif;
  background-color: #f4f4f9;
  color: #333;
}

/* Header Styles */
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #007ea7, #00a8e8);
  padding: 15px 20px;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: background 0.3s ease;
}

.header-logo {
  display: flex;
  align-items: center;
}

.header-logo img {
  max-width: 50px;
  margin-right: 10px;
  border-radius: 8px;
}

.header-logo h1 {
  font-size: 24px;
  font-weight: 700;
  color: #fff;
  transition: color 0.3s ease;
}

.header-logo h1:hover {
  color: #c2f0fc;
}

/* Navigation Styles */
nav ul {
  list-style: none;
  display: flex;
  gap: 25px;
}

nav ul li a {
  color: #fff;
  text-decoration: none;
  font-size: 16px;
  font-weight: 500;
  padding: 8px 15px;
  transition: color 0.3s ease, background 0.3s ease;
  border-radius: 5px;
}

nav ul li a:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: #e0f7ff;
}

/* Dropdown Styles */
.dropdown {
  position: relative;
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  top: 100%;
  background: #fff;
  min-width: 250px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  border-radius: 10px;
  overflow: hidden;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(10px);
}

.dropdown-content.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

/* Profile Card Styles */
.profile-card {
  text-align: center;
  padding: 20px;
}

.profile-card-img {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  margin-bottom: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.profile-card-info h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 5px;
}

.profile-card-info p {
  font-size: 14px;
  color: #555;
}

.edit-profile-btn,
.logout-btn {
  display: block;
  margin: 10px 0;
  padding: 10px 15px;
  background: #007ea7;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.edit-profile-btn:hover,
.logout-btn:hover {
  background: #00a8e8;
}

/* User Profile Icon */
.user-icon {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  margin-right: 10px;
  border: 2px solid #fff;
}

.dropbtn {
  display: flex;
  align-items: center;
  color: #fff;
  text-decoration: none;
  font-size: 16px;
  transition: color 0.3s ease;
}

.dropbtn:hover {
  color: #c2f0fc;
}

/* Media Queries */
@media (max-width: 768px) {
  nav ul {
    flex-direction: column;
    background: #007ea7;
    padding: 15px;
    border-radius: 10px;
    display: none;
  }
  
  nav ul.show {
    display: flex;
  }
  
  nav ul li {
    margin-bottom: 10px;
  }
}
</style>