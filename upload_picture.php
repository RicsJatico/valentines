<?php
session_start(); // Start the session to access user data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) { // Ensure the user is logged in
        $userId = $_SESSION['user_id']; // Get the logged-in user's ID

        if (isset($_FILES['uploadedPicture']) && $_FILES['uploadedPicture']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            $fileTmpPath = $_FILES['uploadedPicture']['tmp_name'];
            $fileName = basename($_FILES['uploadedPicture']['name']);
            $fileSize = $_FILES['uploadedPicture']['size'];
            $fileType = $_FILES['uploadedPicture']['type'];

            // Validate file type and size
            if (!in_array($fileType, $allowedMimeTypes)) {
                die('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
            }

            if ($fileSize > $maxFileSize) {
                die('File size exceeds the maximum limit of 2MB.');
            }

            // Create uploads directory if not exists
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique name for the uploaded file
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('valentine_', true) . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFileName;

            // Move the uploaded file to the "uploads" directory
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Connect to the database
                $dbHost = 'localhost';
                $dbUser = 'root';
                $dbPass = '';
                $dbName = 'valentines';


                $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

                if ($conn->connect_error) {
                    die('Database connection failed: ' . $conn->connect_error);
                }

                // Update the user's picture in the database
                $stmt = $conn->prepare("UPDATE users SET picture = ? WHERE id = ?");
                $stmt->bind_param('si', $uploadPath, $userId);

                if ($stmt->execute()) {
                    echo 'File uploaded and associated with the user successfully.';
                } else {
                    echo 'Database update failed: ' . $conn->error;
                }

                $stmt->close();
                $conn->close();
            } else {
                echo 'Failed to move the uploaded file.';
            }
        } else {
            echo 'No file uploaded or an error occurred.';
        }
    } else {
        echo 'You must be logged in to upload a picture.';
    }
}
?>
