<?php
session_start();
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

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $update_status_stmt = $conn->prepare("UPDATE users SET status = 'offline' WHERE id = ?");
    $update_status_stmt->bind_param("i", $user_id);
    $update_status_stmt->execute();
    $update_status_stmt->close();
}

session_destroy();
echo "<script>
        alert('Logout Successful!');
        window.location.href = 'sign-in.php';
      </script>";

$conn->close();
?>
