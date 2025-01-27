<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("sql205.infinityfree.com", "if0_38126262", "XDIGaNKAAGn4", "if0_38126262_valentines");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Fetch the song ID (modify the WHERE clause based on your needs)
$result = $conn->query("SELECT song_id FROM dedicated_songs ORDER BY id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["song_id" => $row["song_id"]]);
} else {
    echo json_encode(["error" => "No song found"]);
}

$conn->close();
?>
