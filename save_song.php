<?php
// Start the session to access user data
session_start();

// Check if the user is logged in (e.g., check if user ID is set in the session)
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Database connection (replace with your actual database details)
    $host = 'localhost';
    $db = 'valentines';
    $user = 'root';
    $pass = '';
   
    try {
        // PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL query to fetch the reference_number based on the logged-in user ID
        $stmt = $pdo->prepare("SELECT reference_number FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId);
        
        // Execute the query
        $stmt->execute();
        
        // Check if the user exists and fetch the reference number
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $referenceNumber = $row['reference_number'];
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Now we can proceed with saving the song and reference_number

        // Get the song ID from the frontend data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['songId'])) {
            $songId = $data['songId'];

            // Prepare the SQL query to insert the song ID and reference number into the database
            $stmt = $pdo->prepare("INSERT INTO dedicated_songs (song_id, reference_number) VALUES (:songId, :referenceNumber)");
            $stmt->bindParam(':songId', $songId);
            $stmt->bindParam(':referenceNumber', $referenceNumber);

            // Execute the query
            $stmt->execute();

            // Send a response back to the frontend
            echo json_encode(['status' => 'success', 'message' => 'Song dedicated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No song ID provided']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error saving song: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
?>
