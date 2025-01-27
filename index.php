<?php
session_start();
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "valentines"; // Your database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_logged_in = false;
$user_name = '';
$reference_number = '';
$partner = '';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Retrieve user data
    $stmt = $conn->prepare("SELECT name, reference_number, status, partner FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name, $reference_number, $status, $partner);
    $stmt->fetch();
    $stmt->close();

    // Mark user as online
    if ($status !== 'online') {
        $update_status_stmt = $conn->prepare("UPDATE users SET status = 'online' WHERE id = ?");
        $update_status_stmt->bind_param("i", $user_id);
        $update_status_stmt->execute();
        $update_status_stmt->close();
    }

    $user_logged_in = true; // User is logged in
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine’s Plan</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #ffe6e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: #ff3366;
            margin-bottom: 20px;
        }
        .button {
            padding: 15px 30px;
            font-size: 1.5rem;
            color: white;
            background: linear-gradient(to bottom right, #ff6699, #ff3366);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            outline: none;
            margin: 5px;
        }
        .button:hover {
            animation: heart-pulse 1s infinite;
        }
        @keyframes heart-pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
		.top-right-buttons {
    position: absolute;
    top: 10px;
    right: 20px;
    display: flex;
    align-items: center; /* Align text and button vertically */
    gap: 10px; /* Space between elements */
}

.top-right-buttons span {
    font-size: 1rem;
    color: #ff3366;
    font-weight: bold;
}

.top-right-buttons .button {
    padding: 8px 16px;
    font-size: 1rem;
    border-radius: 20px;
}
    </style>
</head>
<body>
    <div class="top-right-buttons">
        <?php if ($user_logged_in): ?>
            <span>Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
            <a href="logout.php">
                <button class="button">Logout</button>
            </a>
        <?php else: ?>
            <a href="sign-in.php">
                <button class="button">Login</button>
            </a>
            <a href="signup.php">
                <button class="button">Sign Up</button>
            </a>
        <?php endif; ?>
    </div>

    <div class="container landing">
        <h1>Plan a Valentine’s with Your Loved Ones</h1>
        <a href="Home.php">
            <button class="button">Explore</button>
        </a>
    </div>

    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const referenceNumber = urlParams.get('reference_number');

            if (referenceNumber) {
                window.location.href = 'fetch.php?reference_number=' + referenceNumber;
            }
        };
    </script>
</body>
</html>
