<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['partner']) || empty($data['partner'])) {
    echo json_encode(['success' => false, 'error' => 'Partner name is missing']);
    exit;
}

$partner = $data['partner'];

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


$sql = "INSERT INTO users (partner) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $partner);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
