<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultation - Pills Station Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    * {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: 'Montserrat', sans-serif;
  color: #333;
  background-color: #f0f2f5;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
.consultation-form {
  background-color: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  margin: 50px auto;
  transition: transform 0.3s;
}
.consultation-form:hover {
  transform: translateY(-5px);
}
.consultation-form h3 {
  font-size: 32px;
  color: #222;
  margin-bottom: 20px;
  text-align: center;
}
.consultation-form label {
  font-size: 14px;
  color: #555;
  display: block;
  margin-top: 15px;
}
.consultation-form input,
.consultation-form select {
  width: 100%;
  padding: 14px;
  margin-top: 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  color: #333;
  background-color: #f9f9f9;
  transition: border-color 0.3s;
}
.consultation-form input:focus,
.consultation-form select:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}
.consultation-form button {
  width: 100%;
  padding: 14px;
  border-radius: 6px;
  background-color: #007bff;
  color: white;
  font-size: 18px;
  border: none;
  cursor: pointer;
  margin-top: 20px;
  transition: background-color 0.3s, box-shadow 0.3s;
}
.consultation-form button:hover {
  background-color: #0056b3;
  box-shadow: 0 8px 15px rgba(0, 123, 255, 0.3);
}
footer {
  background-color: #222;
  color: #ccc;
  text-align: center;
  padding: 15px;
  font-size: 14px;
  width: 100%;
  margin-top: auto;
}
footer p {
  margin: 5px 0;
  color: #aaa;
}
@media (max-width: 768px) {
  .consultation-form {
    width: 90%;
    padding: 20px;
  }
  .consultation-form h3 {
    font-size: 28px;
  }
}

  </style>
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <section class="consultation-section">
    <h2>Consultation Services</h2>
    <p>Our pharmacists are here to offer personalized health advice and support. Book a consultation to discuss your medication, wellness, or any health concerns.</p>
  </section>

  <section class="consultation-form">
    <h3>Book Your Consultation</h3>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Database connection details
      $host = 'localhost';
      $db = 'PillsStation_pharmacy';
      $user = 'root';  // Adjust with your DB username
      $password = '';  // Adjust with your DB password

      // Create connection
      $conn = new mysqli($host, $user, $password, $db);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      // Get form data
      $name = $conn->real_escape_string($_POST['name']);
      $email = $conn->real_escape_string($_POST['email']);
      $phone = $conn->real_escape_string($_POST['phone']);
      $date = $conn->real_escape_string($_POST['date']);
      $time = $conn->real_escape_string($_POST['time']);

      // Insert data into the consultations table (without the message field)
      $sql = "INSERT INTO consultations (name, email, phone, date, time)
              VALUES ('$name', '$email', '$phone', '$date', '$time')";

      if ($conn->query($sql) === TRUE) {
        echo "<p style='text-align:center;color:green;'>Your consultation request has been submitted successfully!</p>";
      } else {
        echo "<p style='text-align:center;color:red;'>Error: " . $conn->error . "</p>";
      }

      // Close connection
      $conn->close();
    }
    ?>

    <form action="consultation.php" method="POST">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>
      
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="date">Preferred Date</label>
      <input type="date" id="date" name="date" required>

      <label for="time">Preferred Time</label>
      <select id="time" name="time" required>
        <option value="9am-10am">9:00 AM - 10:00 AM</option>
        <option value="10am-11am">10:00 AM - 11:00 AM</option>
        <option value="11am-12pm">11:00 AM - 12:00 PM</option>
        <option value="2pm-3pm">2:00 PM - 3:00 PM</option>
        <option value="3pm-4pm">3:00 PM - 4:00 PM</option>
        <option value="4pm-5pm">4:00 PM - 5:00 PM</option>
      </select>

      <button type="submit">Submit</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
