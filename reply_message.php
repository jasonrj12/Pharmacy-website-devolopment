<?php
session_start();
@include 'connect.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    // Fetch the consultation message
    $query = "SELECT * FROM consultation_messages WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $reply = $_POST['reply'];
        // Update the message with the admin's reply
        $query = "UPDATE consultation_messages SET reply = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $reply, $message_id);
        $stmt->execute();
        $message = "Reply sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Consultation</title>
</head>
<body>

<h1>Reply to Consultation</h1>

<?php if (isset($message)): ?>
    <div style="color: green;"><?php echo $message; ?></div>
<?php endif; ?>

<form action="reply_message.php?id=<?php echo $message['id']; ?>" method="POST">
    <label>Message:</label><br>
    <p><?php echo $message['message']; ?></p>
    <label>Reply:</label><br>
    <textarea name="reply" required></textarea><br>
    <button type="submit">Send Reply</button>
</form>

</body>
</html>
