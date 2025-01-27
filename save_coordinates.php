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

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User is not logged in."]);
    exit();
}

// Get the logged-in user's id from the session
$user_id = $_SESSION['user_id'];

// Fetch the reference number of the logged-in user
$stmt = $conn->prepare("SELECT reference_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reference_number);
$stmt->fetch();
$stmt->close();

// If no reference number is found, return an error
if (!$reference_number) {
    echo json_encode(["error" => "Reference number not found."]);
    exit();
}

// Get the latitude and longitude from the POST data
$data = json_decode(file_get_contents('php://input'), true);
$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Prepare the SQL statement to check if a location already exists for the reference number
$sql_check = "SELECT * FROM locations WHERE reference_number = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $reference_number);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Location already exists, update the coordinates
    $sql_update = "UPDATE locations SET latitude = ?, longitude = ? WHERE reference_number = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("dds", $latitude, $longitude, $reference_number);

    if ($stmt_update->execute()) {
        echo json_encode(["message" => "Coordinates updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating coordinates: " . $conn->error]);
    }
} else {
    // Location does not exist, insert new coordinates
    $sql_insert = "INSERT INTO locations (latitude, longitude, reference_number) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("dds", $latitude, $longitude, $reference_number);

    if ($stmt_insert->execute()) {
        echo json_encode(["message" => "Coordinates saved successfully"]);
    } else {
        echo json_encode(["error" => "Error saving coordinates: " . $conn->error]);
    }
}

$stmt_check->close();
$conn->close();
?>
