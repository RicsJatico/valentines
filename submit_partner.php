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

// Get the user_id from session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $partner = trim($_POST['partner']);

    // Insert or update partner field for the user
    $stmt = $conn->prepare("UPDATE users SET partner = ? WHERE id = ?");
    $stmt->bind_param("si", $partner, $user_id);

    if ($stmt->execute()) {
        echo "Partner updated successfully.";
        // Redirect to the song section of user.php
        header("Location: plans.php");
        exit();
    } else {
        echo "Error updating partner: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "User not logged in.";
}

$conn->close();
?>
