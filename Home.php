<?php
// Database connection settings
$host = 'localhost'; // Your database host
$dbname = 'valentines'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

// Create a PDO instance to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error, display a message
    die("Could not connect to the database: " . $e->getMessage());
}

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$query = "SELECT name FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Centered Containers</title>
  <style> 
    /* Reset styles */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Comic Sans MS', cursive, sans-serif;
      background-color: #ffe6f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .center-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }

    .content-box {
      background-color: #ffb3d9;
      border: 2px solid #ff4d94;
      border-radius: 20px;
      box-shadow: 0 8px 15px rgba(255, 0, 102, 0.3);
      padding: 20px;
      width: 320px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .content-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(255, 0, 102, 0.5);
    }

    .content-box h2 {
      margin: 0;
      font-size: 1.6rem;
      color: #ff3366;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
    }

    .content-box .icon {
      margin-top: 10px;
      font-size: 2.5rem;
      color: #ffffff;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .button {
      padding: 5px 15px;
      font-size: 1.2rem;
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
    .button::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: width 0.3s ease, height 0.3s ease;
    }
    .button:hover::before {
      width: 300%;
      height: 300%;
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

    /* Position for the logout button */
    .logout-container {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .logout-container a {
      text-decoration: none;
    }

    @media (max-width: 480px)
    {
      
    .content-box {
      background-color: #ffb3d9;
      border: 2px solid #ff4d94;
      border-radius: 20px;
      box-shadow: 0 8px 15px rgba(255, 0, 102, 0.3);
      padding: 30px;
      width: 320px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    }
  </style>
</head>
<body>
  <div class="logout-container">
    <span>Welcome, <?php echo $username; ?></span>
    <a href="logout.php"><button class="button">Logout</button></a>
  </div>

  <div class="center-container">
    <!-- First Container -->
    <div class="content-box" onclick="window.location.href='user.php'">
      <h2>Dedicate Message</h2>
      <div class="icon">💌</div>
    </div>

    <!-- Second Container -->
    <div class="content-box">
      <h2>Explore Global Chat</h2>
      <div class="icon">🔍</div>
    </div>



    <!-- Third Container -->
    <div class="content-box">
      <h2>More Updates Coming Soon</h2>
      <div class="icon">⏳</div>
    </div>
  </div>
</body>
</html>
