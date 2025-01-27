<?php
require 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference_number = $_POST['reference_number'] ?? '';
    $status = $_POST['status'] ?? '';

    if (empty($reference_number) || empty($status)) {
        echo "Invalid request.";
        exit;
    }

    // Update the status in the database
    $stmt = $conn->prepare("UPDATE locations SET status = ? WHERE reference_number = ?");
    $stmt->bind_param("ss", $status, $reference_number);

    if ($stmt->execute()) {
        echo "Status updated to '$status'.";
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
    $conn->close();
}
?>