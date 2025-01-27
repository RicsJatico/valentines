<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $song = $_POST['song'];

    // Handle file uploads
    $picture = $_FILES['picture'];
    $picture1 = $_FILES['picture1'];

    $uploadDir = 'uploads/';
    $picturePath = $uploadDir . basename($picture['name']);
    $picture1Path = $uploadDir . basename($picture1['name']);

    // Save files to server
    move_uploaded_file($picture['tmp_name'], $picture);
    move_uploaded_file($picture1['tmp_name'], $picture1);

    // Save to database (example with PDO)
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=yourdbname', 'username', 'password');
        $stmt = $pdo->prepare("INSERT INTO users (message, song, picture, picture1) VALUES (?, ?, ?, ?)");
        $stmt->execute([$message, $song, $picturePath, $picture1Path]);
        echo "Submission successful!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
