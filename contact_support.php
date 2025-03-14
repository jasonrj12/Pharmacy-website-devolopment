<?php
session_start();
include 'connect.php';


// Handle form submission
if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Prepare and execute SQL insert
    $stmt = $conn->prepare("INSERT INTO `contact_messages` (`name`, `email`, `message`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Thank you, $name! Your message has been received.');</script>";
    } else {
        echo "<script>alert('Error: Could not submit your message. Please try again.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - Pills Station Pharmacy</title>
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
            text-align: center;
        }
        h1 {
            font-size: 2.8em;
            color: #333;
            margin-bottom: 20px;
        }
        .contact-info {
            margin-bottom: 30px;
        }
        .contact-info p {
            margin: 15px 0;
            font-size: 1.2em;
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s;
        }
        .contact-info a:hover {
            color: #0056b3;
        }
        .contact-info i {
            margin-right: 8px;
            font-size: 18px;
            color: #007bff;
        }
        .contact-form {
            max-width: 500px;
            margin: 0 auto;
        }
        .contact-form h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }
        .contact-form label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s;
        }
        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: #007bff;
        }
        .contact-form textarea {
            height: 150px;
            resize: vertical;
        }
        .contact-form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .contact-form button:hover {
            background-color: #218838;
        }
        .contact-form button i {
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            h1 {
                font-size: 2.2em;
            }
            .contact-info p {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact Support</h1>

        <!-- Contact Information with Icons -->
        <div class="contact-info">
            <p><a href="mailto:support@pillstation.com"><i class="fas fa-envelope"></i> support@pillstation.com</a></p>
            <p><a href="tel:0111234567"><i class="fas fa-phone"></i> 011-123-4567</a></p>
            <p><a href="contact_support.php"><i class="fas fa-headset"></i> Live Support Chat (Coming Soon)</a></p>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h2>Send Us a Message</h2>
            <form action="" method="POST">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="message">Your Message</label>
                <textarea id="message" name="message" placeholder="How can we assist you?" required></textarea>

                <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
                <button type="button" onclick="window.location.href='index.php';"><i class="fas fa-arrow-left"></i> Go Back</button>
            </form>
        </div>
    </div>

</body>
</html>