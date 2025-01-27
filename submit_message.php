<?php
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "valentines";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If no session, redirect to login page
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to send a message.";
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Ensure the message is not empty
if (!empty($message)) {
    // Prepare and bind the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE users SET message = ? WHERE id = ?");
    $stmt->bind_param("si", $message, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Message cannot be empty.";
}

$conn->close();
?>
