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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $signin_name = $_POST['signin-name'];
    $signin_password = $_POST['signin-password'];

    $stmt = $conn->prepare("SELECT id, name, password, reference_number FROM users WHERE name = ?");
    $stmt->bind_param("s", $signin_name);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_name, $db_password, $db_reference_number);
        $stmt->fetch();
        
        if (password_verify($signin_password, $db_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_name;
            $_SESSION['reference_number'] = $db_reference_number;

            // Update user's status to 'online'
            $update_status_stmt = $conn->prepare("UPDATE users SET status = 'online' WHERE id = ?");
            $update_status_stmt->bind_param("i", $user_id);
            $update_status_stmt->execute();
            $update_status_stmt->close();

            echo "<script>
                alert('Login Successful!');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "User not found.";
    }
    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
      body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background-color: #ffecf0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow: hidden;
    position: relative;
}

.container {
    text-align: center;
    background-color: #fff;
    background-image: url('bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(228, 98, 98, 0.986);
    border: 5px dotted #ff99cc;
    position: relative;
    overflow: hidden;
    max-width: 400px;
    width: 100%;
    z-index: 1;
    height: 380px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

h2 {
    font-family: 'Dancing Script', cursive;
    font-size: 2rem;
    color: #ff6699;
    margin-bottom: 20px;
}

h1{
    font-family: 'Dancing Script', cursive;
    font-size: 2rem;
    color: #ff6699;
    margin-bottom: 20px;
}

.button {
    padding: 10px 20px;
    font-size: 1rem;
    color: white;
    background: linear-gradient(to bottom right, #ff99cc, #ff6699);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    outline: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px; /* Added margin to separate the button from the text */
}

.button:hover {
    background: linear-gradient(to bottom right, #ff6699, #ff3366);
}

form {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 350px;
    width: 100%;
    text-align: left;
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #ff6699;
}

input {
    width: calc(100% - 20px);
    padding: 7px;
    margin-bottom: 15px;
    border: 2px solid #ff99cc;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

input:focus {
    border-color: #ff6699;
    outline: none;
}

p {
    margin-top: 15px;
    color: #ff6699;
}

a {
    color: #ff3366;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

.heart {
    width: 20px;
    height: 20px;
    background-color: #ff6699;
    position: absolute;
    border-radius: 50% 50% 0 0;
    transform: rotate(-45deg);
    animation: float 5s infinite;
}

.heart::before,
.heart::after {
    content: '';
    width: 20px;
    height: 20px;
    background-color: #ff6699;
    position: absolute;
    border-radius: 50%;
}

.heart::before {
    top: -10px;
    left: 0;
}

.heart::after {
    top: 0;
    left: -10px;
}

@keyframes float {
    0% {
        transform: translateY(0) rotate(-45deg);
    }
    50% {
        transform: translateY(-20px) rotate(-45deg);
    }
    100% {
        transform: translateY(0) rotate(-45deg);
    }
}
@media (max-width: 480 px) {

    .container {
    text-align: center;
    background-color: #fff;
    background-image: url('bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(228, 98, 98, 0.986);
    border: 5px dotted #ff99cc;
    position: relative;
    overflow: hidden;
    max-width: 400px;
    width: 100%;
    z-index: 1;
    height: 380px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

h1, h2 {
    font-family: 'Dancing Script', cursive;
    font-size: 2rem;
    color: #ff6699;
    margin-bottom: 20px;

}

.button {
    padding: 10px 20px;
    font-size: 1rem;
    color: white;
    background: linear-gradient(to bottom right, #ff99cc, #ff6699);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    outline: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    text-align: center;
}

.button:hover {
    background: linear-gradient(to bottom right, #ff6699, #ff3366);
}
form {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 350px;
    width: 100%;
    text-align: left;
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #ff6699;
}

input {
    width: calc(100% - 20px);
    padding: 7px;
    margin-bottom: 15px;
    border: 2px solid #ff99cc;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

input:focus {
    border-color: #ff6699;
    outline: none;
}

p {
    margin-top: 15px;
    color: #ff6699;
}

a {
    color: #ff3366;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

.heart {
    width: 20px;
    height: 20px;
    background-color: #ff6699;
    position: absolute;
    border-radius: 50% 50% 0 0;
    transform: rotate(-45deg);
    animation: float 5s infinite;
}

.heart::before,
.heart::after {
    content: '';
    width: 20px;
    height: 20px;
    background-color: #ff6699;
    position: absolute;
    border-radius: 50%;
}

.heart::before {
    top: -10px;
    left: 0;
}

.heart::after {
    top: 0;
    left: -10px;
}

@keyframes float {
    0% {
        transform: translateY(0) rotate(-45deg);
    }
    50% {
        transform: translateY(-20px) rotate(-45deg);
    }
    100% {
        transform: translateY(0) rotate(-45deg);
    }
}
}
    </style>
</head>
<body>
    <div class="background-hearts">
        <!-- Heart animations -->
    </div>
    <div class="container form-container signin show">
        <form method="POST" action="sign-in.php">
            <h2>Sign In</h2>
            
            <!-- Display Error Message -->
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?= $error_message ?></p>
            <?php endif; ?>

            <label for="signin-name">Name</label>
            <input type="text" id="signin-name" name="signin-name" required>
            <label for="signin-password">Password</label>
            <input type="password" id="signin-password" name="signin-password" required>
            <button class="button" type="submit">Sign In</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
    <div class="background-hearts">
        <div class="heart" style="left: 20%; animation-delay: 0s;"></div>
        <div class="heart" style="left: 40%; animation-delay: 2s;"></div>
        <div class="heart" style="left: 60%; animation-delay: 4s;"></div>
        <div class="heart" style="left: 80%; animation-delay: 6s;"></div>
    </div>
   
</body>
</html>