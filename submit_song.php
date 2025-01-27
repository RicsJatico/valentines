<?php
// Save the incoming JSON data from the frontend
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['songId'])) {
    $songId = $data['songId'];

    // Database connection (replace with your actual database details)
    $host = 'localhost';
    $db = 'valentines';
    $user = 'root';
    $pass = '';
  
    try {
        // PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL query to insert the song ID into the database
        $stmt = $pdo->prepare("INSERT INTO dedicated_songs (song_id) VALUES (:songId)");
        $stmt->bindParam(':songId', $songId);

        // Execute the query
        $stmt->execute();

        // Send a response back to the frontend
        echo json_encode(['status' => 'success', 'message' => 'Song dedicated successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error saving song: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No song ID provided']);
}
?>