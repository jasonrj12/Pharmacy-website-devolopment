<?php
// Database connection details
$host = 'localhost';
$user = 'root';    // Default for XAMPP
$password = '';    // Default is an empty string for XAMPP
$db = '"PillsStation_pharmacy';

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'pillsstation_pharmacy');


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_name = $conn->real_escape_string($_POST['doctor_name']);
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $contact_details = $conn->real_escape_string($_POST['contact_details']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Handle file upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
    }
    
    $target_file = $target_dir . basename($_FILES["prescription_file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    if (in_array($file_type, $allowed_types)) {
        // Move file to the target directory
        if (move_uploaded_file($_FILES["prescription_file"]["tmp_name"], $target_file)) {
            // Insert data into the database
            $sql = "INSERT INTO prescriptions (doctor_name, patient_name, contact_details, address, file_path) 
                    VALUES ('$doctor_name', '$patient_name', '$contact_details', '$address', '$target_file')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "<p class='success'>Prescription submitted successfully!</p>";
            } else {
                $message = "<p class='error'>Error: " . $conn->error . "</p>";
            }
        } else {
            $message = "<p class='error'>Sorry, there was an error uploading your file.</p>";
        }
    } else {
        $message = "<p class='error'>Invalid file type. Only PDF, JPG, JPEG, and PNG files are allowed.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Prescription - HealthPlus Pharmacy</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css">
    <style>
        /* General styles */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        header, footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        h2 {
            font-size: 1.8rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Prescription form container */
        .prescription-form {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .prescription-form .form-group {
            margin-bottom: 20px;
        }

        .prescription-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .prescription-form input[type="text"],
        .prescription-form input[type="file"],
        .prescription-form button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            margin-bottom: 12px;
        }

        .prescription-form input[type="file"] {
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .prescription-form input[type="text"]:focus,
        .prescription-form input[type="file"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
        }

        .prescription-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }

        .prescription-form button:hover {
            background-color: #45a049;
            transition: background-color 0.3s ease;
        }

        .prescription-form button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .prescription-form p {
            text-align: center;
            font-size: 14px;
            margin: 10px 0;
        }

        .success {
            text-align: center;
            color: #27ae60;
        }

        .error {
            text-align: center;
            color: #e74c3c;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .prescription-form {
                margin: 20px;
                padding: 20px;
            }

            .prescription-form input[type="text"],
            .prescription-form input[type="file"],
            .prescription-form button {
                padding: 14px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="prescription-form">
        <h2>Submit Your Prescription</h2>
        <?php echo $message; ?>
        <form action="doctor_prescription.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="doctor-name">Doctor's Name</label>
                <input type="text" id="doctor-name" name="doctor_name" required>
            </div>
            <div class="form-group">
                <label for="patient-name">Patient's Name</label>
                <input type="text" id="patient-name" name="patient_name" required>
            </div>
            <div class="form-group">
                <label for="contact-details">Patient's Contact Details</label>
                <input type="text" id="contact-details" name="contact_details" required>
            </div>
            <div class="form-group">
                <label for="address">Patient's Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="prescription-file">Upload Prescription (PDF or Image)</label>
                <input type="file" id="prescription-file" name="prescription_file" accept=".pdf, .jpg, .jpeg, .png" required>
            </div>
            <div class="form-group">
                <button type="submit">Submit Prescription</button>
            </div>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
