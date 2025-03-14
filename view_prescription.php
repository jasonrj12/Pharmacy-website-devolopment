<?php
// Database connection details
$host = 'localhost';
$db = 'healthplus_pharmacy';
$user = 'root';  // Replace with your DB username if different
$password = '';  // Replace with your DB password if set

// Connect to the database
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch prescription details based on the id parameter
$prescription_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($prescription_id) {
    $query = "SELECT * FROM prescriptions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $prescription_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $prescription = $result->fetch_assoc();
    } else {
        $message = "Prescription not found.";
    }
} else {
    $message = "Invalid prescription ID.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Prescription - HealthPlus Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    /* Add your styles here */
    body {
      font-family: 'Montserrat', sans-serif;
      color: #333;
      background-color: #f3f3f3;
    }
    .prescription-details {
      background-color: #fff;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
      width: 80%;
      max-width: 700px;
      margin: 50px auto;
    }
    .prescription-details h2 {
      font-size: 32px;
      color: #007b5e;
      margin-bottom: 20px;
      text-align: center;
    }
    .detail-item {
      margin-bottom: 15px;
      font-size: 18px;
      color: #333;
    }
    .detail-item strong {
      font-weight: bold;
    }
    .prescription-image {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin-top: 20px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    footer {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 10px 0;
      font-size: 14px;
      position: relative;
      bottom: 0;
      width: 100%;
    }
  </style>
</head>
<body>


  <div class="prescription-details">
    <?php if (isset($message)): ?>
      <p style="color: red; text-align: center;"><?php echo $message; ?></p>
    <?php elseif (isset($prescription)): ?>
      <h2>Prescription Details</h2>
      <div class="detail-item">
        <strong>Prescription ID:</strong> <?php echo $prescription['id']; ?>
      </div>
      <div class="detail-item">
        <strong>Doctor's Name:</strong> <?php echo $prescription['doctor_name']; ?>
      </div>
      <div class="detail-item">
        <strong>Patient's Name:</strong> <?php echo $prescription['patient_name']; ?>
      </div>
      
      <?php if (in_array(strtolower(pathinfo($prescription['file_path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])): ?>
        <div class="detail-item">
          <strong>Prescription Image:</strong><br>
          <img src="<?php echo $prescription['file_path']; ?>" alt="Prescription Image" class="prescription-image">
        </div>
      <?php elseif (strtolower(pathinfo($prescription['file_path'], PATHINFO_EXTENSION)) == 'pdf'): ?>
        <div class="detail-item">
          <strong>Prescription Document:</strong><br>
          <a href="<?php echo $prescription['file_path']; ?>" target="_blank">View Prescription PDF</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
