<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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


$user_name = '';
$reference_number = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Get user data based on session user_id
    $stmt = $conn->prepare("SELECT name, reference_number, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name, $reference_number, $status);
    $stmt->fetch();
    $stmt->close();
    
    if ($status !== 'online') {
        // Update user status to 'online' if it's not already
        $update_status_stmt = $conn->prepare("UPDATE users SET status = 'online' WHERE id = ?");
        $update_status_stmt->bind_param("i", $user_id);
        $update_status_stmt->execute();
        $update_status_stmt->close();
    }
} 
else {
    // If no session, redirect to login page
    header("Location: index.html"); // Adjust this as per your login page
    exit();
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
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
            position: relative;
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 10;
            opacity: 0;
            animation: fadeIn 1s forwards;
        }

        .container h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: #ff3366;
        }


        .message {
            font-size: 1.5rem;
            color: #ff6699;
            margin-top: 10px;
        }

        .blur-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 5;
        }

       .reference-number {
            position: absolute;
            top: 10px;
         
            background-color: #ff6699;
            padding: 10px 20px;
            color: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            animation: slideToTopRight 3s ease-out forwards;
           
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 30px; /* Adjusted slightly to the left */
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logout-btn:hover {
            background-color:rgb(240, 10, 87);
        }

        .okay-btn {
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .okay-btn:hover {
             background-color:rgb(240, 10, 87);
        }
        .okay1-btn {
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .okay1-btn:hover {
             background-color:rgb(240, 10, 87);
        }
        .second-container h1 { 
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: #ff3366;
        }
        .second-container {
            display: none;
            position: fixed;
            background-color: #fff;
            background-image: url('bg.jpg');
            background-size: fill;
            background-position: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 30px;
            border: 2px dotted lightpink;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 20;
        }

        .second-container input {
            width: 80%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes slideToTopRight {
            0% {
                top: 10px;
                right: 12px;
            }
            100% {
                top: 10px;
                right: 12%;
                transform: translateX(12%);
            }
        }

        @keyframes popOutAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0) translateY(-200px);
                opacity: 0;
            }
        }

        .pop-out {
            animation: popOutAnimation 1s forwards;
        }

        @media (max-width : 480px) {
            .container {
    text-align: center;
    position: relative;
    background-color: #fff;
    padding: 10px; /* Reduced padding */
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    z-index: 10;
    opacity: 0;
    animation: fadeIn 1s forwards;
    max-width: 300px; /* Set the max-width to 300px */
    width: 100%;
   
}
        .container h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: #ff3366;
        }


        .message {
            font-size: 1.2rem;
            color: #ff6699;
            margin-top: 10px;
        }

        .blur-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 5;
        }

        .reference-number {
            position: absolute;
            top: 10px;
         
            background-color: #ff6699;
            padding: 10px 20px;
            color: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            animation: slideToTopRight 3s ease-out forwards;
            margin-right:90px;
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 30px; /* Adjusted slightly to the left */
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logout-btn:hover {
            background-color:rgb(240, 10, 87);
        }

        .okay-btn {
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .okay-btn:hover {
             background-color:rgb(240, 10, 87);
        }
        .okay1-btn {
            background-color: #ff6699;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .okay1-btn:hover {
             background-color:rgb(240, 10, 87);
        }
        .second-container h1 { 
            font-family: 'Dancing Script', cursive;
            font-size: 2.4rem;
            color: #ff3366;
            margin-top: 40px;
        }
        .second-container {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    width: 300px;
    height: 300px;
    border: 2px dotted lightpink;
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    text-align: center;
    z-index: 20;
    background-color: #fff; /* Ensure background color is set */
    overflow: hidden; /* To contain the pseudo-element within the container */
}

.second-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('bg.jpg');
    background-size: cover;
    background-position: center;
    opacity: 0.2; /* Adjust opacity as needed */
    z-index: -1; /* Place the pseudo-element behind the content */
}


        .second-container input {
            width: 80%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        }
    </style>
</head>
<body>
    <div class="blur-background" id="blurBackground"></div>

    <div class="container">
        <h1>Hello, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="message">We're here to help you plan your valentines ♥️ ️ </p>
        <button class="okay-btn" onclick="triggerPopOut()">Okay</button>
    </div>

    <div class="reference-number">
        <?php echo htmlspecialchars($reference_number); ?>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>

    <div class="second-container" id="secondContainer">
    <form id="dedicationForm" method="POST" action="submit_partner.php">
        <h1>Who's the lucky person ? </h1>
        <input type="text" name="partner" id="dedicationInput" placeholder="Enter a name" required>
        <button type="submit" class="okay1-btn">Submit</button>
        
    </form>
</div>


<script>
      function triggerPopOut() {
            // Add the pop-out animation class to the container
            document.querySelector('.container').classList.add('pop-out');
            // Hide the blur background
            document.getElementById('blurBackground').style.display = 'none';
            // Show the second container
            document.getElementById('secondContainer').style.display = 'block';
        }
    

</script>
</body>
</html>
