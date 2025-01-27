
<?php 
// Start session to manage logged-in users (if needed)
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

// Check if reference_number is in the URL
if (!isset($_GET['reference_number']) || empty($_GET['reference_number'])) {
    echo "<h2>Invalid reference link. Please check the URL.</h2>";
    exit();
}

// Sanitize the reference number
$reference_number = htmlspecialchars($_GET['reference_number']);

// Fetch user-specific content based on the reference number from the users table
$stmt = $conn->prepare("SELECT name, partner, message, picture FROM users WHERE reference_number = ?");
$stmt->bind_param("s", $reference_number);
$stmt->execute();
$result = $stmt->get_result();

// Check if a matching record was found in the users table
if ($result->num_rows > 0) {
    // Fetch the user details
    $row = $result->fetch_assoc();
    $name = htmlspecialchars($row['name']);
    $partner = htmlspecialchars($row['partner']);
    $message = htmlspecialchars($row['message']);
    $picture = htmlspecialchars($row['picture']); // Assuming the picture field contains the image filename
} else {
    echo "<h2>No content found for the provided reference number in users table.</h2>";
    exit();
}

// Fetch the song_id from the dedicated_songs table based on reference_number
$stmt_song = $conn->prepare("SELECT song_id FROM dedicated_songs WHERE reference_number = ?");
$stmt_song->bind_param("s", $reference_number);
$stmt_song->execute();
$result_song = $stmt_song->get_result();

// Check if a matching record was found in dedicated_songs table
if ($result_song->num_rows > 0) {
    // Fetch the song_id
    $song_row = $result_song->fetch_assoc();
    $song_id = htmlspecialchars($song_row['song_id']); // Assuming the song_id is a valid Spotify song ID
} else {
    echo "<h2>No song found for the provided reference number.</h2>";
    exit();
}

// Fetch the longitude and latitude from the locations table based on reference_number
$stmt_location = $conn->prepare("SELECT latitude, longitude FROM locations WHERE reference_number = ?");
$stmt_location->bind_param("s", $reference_number);
$stmt_location->execute();
$result_location = $stmt_location->get_result();

if ($result_location->num_rows > 0) {
    $location = $result_location->fetch_assoc();
    $latitude = htmlspecialchars($location['latitude']);
    $longitude = htmlspecialchars($location['longitude']);
} else {
    echo "<h2>Location data not found.</h2>";
    exit();
}
$sql = "SELECT u.reference_number, u.name, u.partner, u.picture, l.latitude, l.longitude
        FROM users u
        JOIN locations l ON u.reference_number = l.reference_number
        WHERE l.status = 'allowed'"; // Ensures only allowed users are displayed
$result = $conn->query($sql);

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = [
        'name' => htmlspecialchars($row['name']),
        'partner' => htmlspecialchars($row['partner']),
        'latitude' => (float)$row['latitude'],
        'longitude' => (float)$row['longitude'],
        'picture' => htmlspecialchars($row['picture']) // Ensure picture is included
    ];
}

$my_location = [
  'latitude' => $latitude, // Replace with your actual location
  'longitude' => $longitude // Replace with your actual location
];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Greetings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

      *
      *::after,
      *::before {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
      }

      :root {
        --dark-color: #000;
      }

      body {
    display: flex;
    flex-direction: column; /* Ensure content stacks vertically */
    align-items: center;
    justify-content: flex-start; /* Start from the top */
    min-height: 100vh;
    background-color: var(--dark-color);
    overflow-x: hidden; /* Allow vertical scrolling but prevent horizontal scroll */
    perspective: 1000px;
}

      
      .title {
        position: absolute;
        top: -30px;
        font-size: 45px;
        color: white;
        z-index: 999;
        font-family: 'Poppins', sans-serif;
      }
      h1.title {
    font-family: 'Dancing Script', cursive; /* Set the font family */
    font-size: 56px; /* Smaller font size */
    color: #ff4081; /* Pink color */
    margin-bottom: 10px; /* Adjust margin to make it closer to content */
    text-align: center; /* Center align the text */
}/* Button styling */
.reloadBtn {
    position: absolute;
    top: 500px;
    z-index: 999;
    padding: 8px 30px;
    font-size: 18px;
    font-family: 'Poppins', sans-serif;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    background-color: #ff4081; /* Pink background for Valentine's theme */
    color: white; /* Text color */
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease-in-out, border-radius 0.3s ease;
}

/* Hover effect with pulsing heart */
.reloadBtn:hover {
    background-color: #eaeaea; 
    animation: pulseHeart 1.5s infinite; /* Heart pulsing animation */
    transform: scale(1.1); /* Slight scale effect */
}

/* Pulse effect keyframes */
@keyframes pulseHeart {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2); /* Heart grows */
    }
    100% {
        transform: scale(1);
    }
}



.reloadBtn.circle {
    width: 100px; /* Size of the circle */
    height: 100px; /* Size of the circle */
    border-radius: 50%; /* Make it circular */
    padding: 0; /* Remove padding for a perfect circle */
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease; /* Smooth transition */
    position: fixed; /* Fixed position so it stays at the right side */
    right: 20px; /* 20px from the right edge of the viewport */
    bottom: 20px; /* 20px from the bottom edge */
}
.first-section, .second-section {
    min-height: 100vh; /* Each section takes up the full screen */
    width: 100%;
}

/* Hover effect with pulsing heart */
.reloadBtn:hover {
    background-color: #ff4081; 
    animation: pulseHeart 1.5s infinite; /* Heart pulsing animation */
    transform: scale(1.1); /* Slight scale effect */
}

.reloadBtn.circle:before {
    margin-right: 0; /* Remove space when in circle mode */
    font-size: 40px; /* Increase the heart size */
}
      
      .night {
        position: fixed;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
        width: 100%;
        height: 100%;
        filter: blur(0.1vmin);
        background-image: radial-gradient(
            ellipse at top,
            transparent 0%,
            var(--dark-color)
          ),
          radial-gradient(
            ellipse at bottom,
            var(--dark-color),
            rgba(145, 233, 255, 0.2)
          ),
          repeating-linear-gradient(
            220deg,
            black 0px,
            black 19px,
            transparent 19px,
            transparent 22px
          ),
          repeating-linear-gradient(
            189deg,
            black 0px,
            black 19px,
            transparent 19px,
            transparent 22px
          ),
          repeating-linear-gradient(
            148deg,
            black 0px,
            black 19px,
            transparent 19px,
            transparent 22px
          ),
          linear-gradient(90deg, #00fffa,rgb(229, 255, 0));
      }

      .flowers {
        position: relative;
        transform: scale(0.9);
      }

      .flower {
        position: absolute;
        bottom: 10vmin;
        transform-origin: bottom center;
        z-index: 10;
        --fl-speed: 0.8s;
      }
      .flower--1 {
        animation: moving-flower-1 4s linear infinite;
      }
      .flower--1 .flower__line {
        height: 70vmin;
        animation-delay: 0.3s;
      }
      .flower--1 .flower__line__leaf--1 {
        animation: blooming-leaf-right var(--fl-speed) 1.6s backwards;
      }
      .flower--1 .flower__line__leaf--2 {
        animation: blooming-leaf-right var(--fl-speed) 1.4s backwards;
      }
      .flower--1 .flower__line__leaf--3 {
        animation: blooming-leaf-left var(--fl-speed) 1.2s backwards;
      }
      .flower--1 .flower__line__leaf--4 {
        animation: blooming-leaf-left var(--fl-speed) 1s backwards;
      }
      .flower--1 .flower__line__leaf--5 {
        animation: blooming-leaf-right var(--fl-speed) 1.8s backwards;
      }
      .flower--1 .flower__line__leaf--6 {
        animation: blooming-leaf-left var(--fl-speed) 2s backwards;
      }
      .flower--2 {
        left: 50%;
        transform: rotate(20deg);
        animation: moving-flower-2 4s linear infinite;
      }
      .flower--2 .flower__line {
        height: 60vmin;
        animation-delay: 0.6s;
      }
      .flower--2 .flower__line__leaf--1 {
        animation: blooming-leaf-right var(--fl-speed) 1.9s backwards;
      }
      .flower--2 .flower__line__leaf--2 {
        animation: blooming-leaf-right var(--fl-speed) 1.7s backwards;
      }
      .flower--2 .flower__line__leaf--3 {
        animation: blooming-leaf-left var(--fl-speed) 1.5s backwards;
      }
      .flower--2 .flower__line__leaf--4 {
        animation: blooming-leaf-left var(--fl-speed) 1.3s backwards;
      }
      .flower--3 {
        left: 50%;
        transform: rotate(-15deg);
        animation: moving-flower-3 4s linear infinite;
      }
      .flower--3 .flower__line {
        animation-delay: 0.9s;
      }
      .flower--3 .flower__line__leaf--1 {
        animation: blooming-leaf-right var(--fl-speed) 2.5s backwards;
      }
      .flower--3 .flower__line__leaf--2 {
        animation: blooming-leaf-right var(--fl-speed) 2.3s backwards;
      }
      .flower--3 .flower__line__leaf--3 {
        animation: blooming-leaf-left var(--fl-speed) 2.1s backwards;
      }
      .flower--3 .flower__line__leaf--4 {
        animation: blooming-leaf-left var(--fl-speed) 1.9s backwards;
      }
      .flower__leafs {
        position: relative;
        animation: blooming-flower 2s backwards;
      }
      .flower__leafs--1 {
        animation-delay: 1.1s;
      }
      .flower__leafs--2 {
        animation-delay: 1.4s;
      }
      .flower__leafs--3 {
        animation-delay: 1.7s;
      }
      .flower__leafs::after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        transform: translate(-50%, -100%);
        width: 8vmin;
        height: 8vmin;
        background-color:rgb(241, 4, 4);
        filter: blur(10vmin);
      }
      .flower__leaf {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 8vmin;
        height: 11vmin;
        border-radius: 51% 49% 47% 53%/44% 45% 55% 69%;
        background-color:rgb(241, 4, 4);
        background-image: linear-gradient(to top,rgb(255, 51, 0),rgb(255, 0, 0));
        transform-origin: bottom center;
        opacity: 0.9;
        box-shadow: inset 0 0 2vmin rgba(255, 255, 255, 0.5);
      }
      .flower__leaf--1 {
        transform: translate(-10%, 1%) rotateY(40deg) rotateX(-50deg);
      }
      .flower__leaf--2 {
        transform: translate(-50%, -4%) rotateX(40deg);
      }
      .flower__leaf--3 {
        transform: translate(-90%, 0%) rotateY(45deg) rotateX(50deg);
      }
      .flower__leaf--4 {
        width: 8vmin;
        height: 8vmin;
        transform-origin: bottom left;
        border-radius: 4vmin 10vmin 4vmin 4vmin;
        transform: translate(0%, 18%) rotateX(70deg) rotate(-43deg);
        background-image: linear-gradient(to top,rgb(255, 51, 0),rgb(255, 0, 0));
        z-index: 1;
        opacity: 0.8;
      }
      .flower__white-circle {
        position: absolute;
        left: -3.5vmin;
        top: -3vmin;
        width: 9vmin;
        height: 4vmin;
        border-radius: 50%;
        background-color: #fff;
      }
      .flower__white-circle::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 45%;
        transform: translate(-50%, -50%);
        width: 60%;
        height: 60%;
        border-radius: inherit;
        background-image: repeating-linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            67.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            112.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            112.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            22.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            22.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            157.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            67.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          repeating-linear-gradient(
            67.5deg,
            rgba(0, 0, 0, 0.03) 0px,
            rgba(0, 0, 0, 0.03) 1px,
            transparent 1px,
            transparent 12px
          ),
          linear-gradient(90deg, #ffeb12, #ffce00);
      }
      .flower__line {
        height: 55vmin;
        width: 1.5vmin;
        background-image: linear-gradient(
            to left,
            rgba(0, 0, 0, 0.2),
            transparent,
            rgba(255, 255, 255, 0.2)
          ),
          linear-gradient(to top, transparent 10%, #14757a, #39c6d6);
        box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.5);
        animation: grow-flower-tree 4s backwards;
      }
      .flower__line__leaf {
        --w: 7vmin;
        --h: calc(var(--w) + 2vmin);
        position: absolute;
        top: 20%;
        left: 90%;
        width: var(--w);
        height: var(--h);
        border-top-right-radius: var(--h);
        border-bottom-left-radius: var(--h);
        background-image: linear-gradient(
          to top,
          rgba(20, 117, 122, 0.4),
rgb(255, 0, 0)
        );
      }
      .flower__line__leaf--1 {
        transform: rotate(70deg) rotateY(30deg);
      }
      .flower__line__leaf--2 {
        top: 45%;
        transform: rotate(70deg) rotateY(30deg);
      }
      .flower__line__leaf--3,
      .flower__line__leaf--4,
      .flower__line__leaf--6 {
        border-top-right-radius: 0;
        border-bottom-left-radius: 0;
        border-top-left-radius: var(--h);
        border-bottom-right-radius: var(--h);
        left: -460%;
        top: 12%;
        transform: rotate(-70deg) rotateY(30deg);
      }
      .flower__line__leaf--4 {
        top: 40%;
      }
      .flower__line__leaf--5 {
        top: 0;
        transform-origin: left;
        transform: rotate(70deg) rotateY(30deg) scale(0.6);
      }
      .flower__line__leaf--6 {
        top: -2%;
        left: -450%;
        transform-origin: right;
        transform: rotate(-70deg) rotateY(30deg) scale(0.6);
      }
      .flower__light {
        position: absolute;
        bottom: 0vmin;
        width: 1vmin;
        height: 1vmin;
        background-color: #fffb00;
        border-radius: 50%;
        filter: blur(0.2vmin);
        animation: light-ans 4s linear infinite backwards;
      }
      .flower__light:nth-child(odd) {
        background-color:rgb(241, 4, 4);
      }
      .flower__light--1 {
        left: -2vmin;
        animation-delay: 1s;
      }
      .flower__light--2 {
        left: 3vmin;
        animation-delay: 0.5s;
      }
      .flower__light--3 {
        left: -6vmin;
        animation-delay: 0.3s;
      }
      .flower__light--4 {
        left: 6vmin;
        animation-delay: 0.9s;
      }
      .flower__light--5 {
        left: -1vmin;
        animation-delay: 1.5s;
      }
      .flower__light--6 {
        left: -4vmin;
        animation-delay: 3s;
      }
      .flower__light--7 {
        left: 3vmin;
        animation-delay: 2s;
      }
      .flower__light--8 {
        left: -6vmin;
        animation-delay: 3.5s;
      }
      .flower__grass {
        --c:rgb(241, 4, 4);
        --line-w: 1.5vmin;
        position: absolute;
        bottom: 12vmin;
        left: -7vmin;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        z-index: 20;
        transform-origin: bottom center;
        transform: rotate(-48deg) rotateY(40deg);
      }
      .flower__grass--1 {
        animation: moving-grass 2s linear infinite;
      }
      .flower__grass--2 {
        left: 2vmin;
        bottom: 10vmin;
        transform: scale(0.5) rotate(75deg) rotateX(10deg) rotateY(-200deg);
        opacity: 0.8;
        z-index: 0;
        animation: moving-grass--2 1.5s linear infinite;
      }
      .flower__grass--top {
        width: 7vmin;
        height: 10vmin;
        border-top-right-radius: 100%;
        border-right: var(--line-w) solid var(--c);
        transform-origin: bottom center;
        transform: rotate(-2deg);
      }
      .flower__grass--bottom {
        margin-top: -2px;
        width: var(--line-w);
        height: 25vmin;
        background-image: linear-gradient(to top, transparent, var(--c));
      }
      .flower__grass__leaf {
        --size: 10vmin;
        position: absolute;
        width: calc(var(--size) * 2.1);
        height: var(--size);
        border-top-left-radius: var(--size);
        border-top-right-radius: var(--size);
        background-image: linear-gradient(
          to top,
          transparent,
          transparent 30%,
          var(--c)
        );
        z-index: 100;
      }
      .flower__grass__leaf--1 {
        top: -6%;
        left: 30%;
        --size: 6vmin;
        transform: rotate(-20deg);
        animation: growing-grass-ans--1 2s 2.6s backwards;
      }
      @keyframes growing-grass-ans--1 {
        0% {
          transform-origin: bottom left;
          transform: rotate(-20deg) scale(0);
        }
      }
      .flower__grass__leaf--2 {
        top: -5%;
        left: -110%;
        --size: 6vmin;
        transform: rotate(10deg);
        animation: growing-grass-ans--2 2s 2.4s linear backwards;
      }
      @keyframes growing-grass-ans--2 {
        0% {
          transform-origin: bottom right;
          transform: rotate(10deg) scale(0);
        }
      }
      .flower__grass__leaf--3 {
        top: 5%;
        left: 60%;
        --size: 8vmin;
        transform: rotate(-18deg) rotateX(-20deg);
        animation: growing-grass-ans--3 2s 2.2s linear backwards;
      }
      @keyframes growing-grass-ans--3 {
        0% {
          transform-origin: bottom left;
          transform: rotate(-18deg) rotateX(-20deg) scale(0);
        }
      }
      .flower__grass__leaf--4 {
        top: 6%;
        left: -135%;
        --size: 8vmin;
        transform: rotate(2deg);
        animation: growing-grass-ans--4 2s 2s linear backwards;
      }
      @keyframes growing-grass-ans--4 {
        0% {
          transform-origin: bottom right;
          transform: rotate(2deg) scale(0);
        }
      }
      .flower__grass__leaf--5 {
        top: 20%;
        left: 60%;
        --size: 10vmin;
        transform: rotate(-24deg) rotateX(-20deg);
        animation: growing-grass-ans--5 2s 1.8s linear backwards;
      }
      @keyframes growing-grass-ans--5 {
        0% {
          transform-origin: bottom left;
          transform: rotate(-24deg) rotateX(-20deg) scale(0);
        }
      }
      .flower__grass__leaf--6 {
        top: 22%;
        left: -180%;
        --size: 10vmin;
        transform: rotate(10deg);
        animation: growing-grass-ans--6 2s 1.6s linear backwards;
      }
      @keyframes growing-grass-ans--6 {
        0% {
          transform-origin: bottom right;
          transform: rotate(10deg) scale(0);
        }
      }
      .flower__grass__leaf--7 {
        top: 39%;
        left: 70%;
        --size: 10vmin;
        transform: rotate(-10deg);
        animation: growing-grass-ans--7 2s 1.4s linear backwards;
      }
      @keyframes growing-grass-ans--7 {
        0% {
          transform-origin: bottom left;
          transform: rotate(-10deg) scale(0);
        }
      }
      .flower__grass__leaf--8 {
        top: 40%;
        left: -215%;
        --size: 11vmin;
        transform: rotate(10deg);
        animation: growing-grass-ans--8 2s 1.2s linear backwards;
      }
      @keyframes growing-grass-ans--8 {
        0% {
          transform-origin: bottom right;
          transform: rotate(10deg) scale(0);
        }
      }
      .flower__grass__overlay {
        position: absolute;
        top: -10%;
        right: 0%;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        filter: blur(1.5vmin);
        z-index: 100;
      }
      .flower__g-long {
        --w: 2vmin;
        --h: 6vmin;
        --c:rgb(241, 4, 4);
        position: absolute;
        bottom: 10vmin;
        left: -3vmin;
        transform-origin: bottom center;
        transform: rotate(-30deg) rotateY(-20deg);
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        animation: flower-g-long-ans 3s linear infinite;
      }
      @keyframes flower-g-long-ans {
        0%,
        100% {
          transform: rotate(-30deg) rotateY(-20deg);
        }
        50% {
          transform: rotate(-32deg) rotateY(-20deg);
        }
      }
      .flower__g-long__top {
        top: calc(var(--h) * -1);
        width: calc(var(--w) + 1vmin);
        height: var(--h);
        border-top-right-radius: 100%;
        border-right: 0.7vmin solid var(--c);
        transform: translate(-0.7vmin, 1vmin);
      }
      .flower__g-long__bottom {
        width: var(--w);
        height: 50vmin;
        transform-origin: bottom center;
        background-image: linear-gradient(to top, transparent 30%, var(--c));
        box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.5);
        clip-path: polygon(35% 0, 65% 1%, 100% 100%, 0% 100%);
      }
      .flower__g-right {
        position: absolute;
        bottom: 6vmin;
        left: -2vmin;
        transform-origin: bottom left;
        transform: rotate(20deg);
      }
      .flower__g-right .leaf {
        width: 30vmin;
        height: 50vmin;
        border-top-left-radius: 100%;
        border-left: 2vmin solidrgb(7, 151, 26);
        background-image: linear-gradient(
          to bottom,
          transparent,
          var(--dark-color) 60%
        );
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 30%,
rgb(255, 0, 0) 60%
        );
      }
      .flower__g-right--1 {
        animation: flower-g-right-ans 2.5s linear infinite;
      }
      .flower__g-right--2 {
        left: 5vmin;
        transform: rotateY(-180deg);
        animation: flower-g-right-ans--2 3s linear infinite;
      }
      .flower__g-right--2 .leaf {
        height: 75vmin;
        filter: blur(0.3vmin);
        opacity: 0.8;
      }
      @keyframes flower-g-right-ans {
        0%,
        100% {
          transform: rotate(20deg);
        }
        50% {
          transform: rotate(24deg) rotateX(-20deg);
        }
      }
      @keyframes flower-g-right-ans--2 {
        0%,
        100% {
          transform: rotateY(-180deg) rotate(0deg) rotateX(-20deg);
        }
        50% {
          transform: rotateY(-180deg) rotate(6deg) rotateX(-20deg);
        }
      }
      .flower__g-front {
        position: absolute;
        bottom: 6vmin;
        left: 2.5vmin;
        z-index: 100;
        transform-origin: bottom center;
        transform: rotate(-28deg) rotateY(30deg) scale(1.04);
        animation: flower__g-front-ans 2s linear infinite;
      }
      @keyframes flower__g-front-ans {
        0%,
        100% {
          transform: rotate(-28deg) rotateY(30deg) scale(1.04);
        }
        50% {
          transform: rotate(-35deg) rotateY(40deg) scale(1.04);
        }
      }
      .flower__g-front__line {
        width: 0.3vmin;
        height: 20vmin;
        background-image: linear-gradient(
          to top,
          transparent,
          rgb(241, 4, 4),
          transparent 100%
        );
        position: relative;
      }
      .flower__g-front__leaf-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        transform-origin: bottom left;
        transform: rotate(10deg);
      }
      .flower__g-front__leaf-wrapper:nth-child(even) {
        left: 0vmin;
        transform: rotateY(-180deg) rotate(5deg);
        animation: flower__g-front__leaf-left-ans 1s ease-in backwards;
      }
      .flower__g-front__leaf-wrapper:nth-child(odd) {
        animation: flower__g-front__leaf-ans 1s ease-in backwards;
      }
      .flower__g-front__leaf-wrapper--1 {
        top: -8vmin;
        transform: scale(0.7);
        animation: flower__g-front__leaf-ans 1s 5.5s ease-in backwards !important;
      }
      .flower__g-front__leaf-wrapper--2 {
        top: -8vmin;
        transform: rotateY(-180deg) scale(0.7) !important;
        animation: flower__g-front__leaf-left-ans-2 1s 4.6s ease-in backwards !important;
      }
      .flower__g-front__leaf-wrapper--3 {
        top: -3vmin;
        animation: flower__g-front__leaf-ans 1s 4.6s ease-in backwards;
      }
      .flower__g-front__leaf-wrapper--4 {
        top: -3vmin;
        transform: rotateY(-180deg) scale(0.9) !important;
        animation: flower__g-front__leaf-left-ans-2 1s 4.6s ease-in backwards !important;
      }
      @keyframes flower__g-front__leaf-left-ans-2 {
        0% {
          transform: rotateY(-180deg) scale(0);
        }
      }
      .flower__g-front__leaf-wrapper--5,
      .flower__g-front__leaf-wrapper--6 {
        top: 2vmin;
      }
      .flower__g-front__leaf-wrapper--7,
      .flower__g-front__leaf-wrapper--8 {
        top: 6.5vmin;
      }
      .flower__g-front__leaf-wrapper--2 {
        animation-delay: 5.2s !important;
      }
      .flower__g-front__leaf-wrapper--3 {
        animation-delay: 4.9s !important;
      }
      .flower__g-front__leaf-wrapper--5 {
        animation-delay: 4.3s !important;
      }
      .flower__g-front__leaf-wrapper--6 {
        animation-delay: 4.1s !important;
      }
      .flower__g-front__leaf-wrapper--7 {
        animation-delay: 3.8s !important;
      }
      .flower__g-front__leaf-wrapper--8 {
        animation-delay: 3.5s !important;
      }
      @keyframes flower__g-front__leaf-ans {
        0% {
          transform: rotate(10deg) scale(0);
        }
      }
      @keyframes flower__g-front__leaf-left-ans {
        0% {
          transform: rotateY(-180deg) rotate(5deg) scale(0);
        }
      }
      .flower__g-front__leaf {
        width: 10vmin;
        height: 10vmin;
        border-radius: 100% 0% 0% 100%/100% 100% 0% 0%;
        box-shadow: inset 0 2px 1vmin rgba(44, 238, 252, 0.2);
        background-image: linear-gradient(
            to bottom left,
            transparent,
            var(--dark-color)
          ),
          linear-gradient(
            to bottom right,
rgb(255, 0, 0) 50%,
            transparent 50%,
            transparent
          );
        -webkit-mask-image: linear-gradient(
          to bottom right,
rgb(255, 0, 0) 50%,
          transparent 50%,
          transparent
        );
        mask-image: linear-gradient(
          to bottom right,
rgb(255, 0, 0) 50%,
          transparent 50%,
          transparent
        );
      }
      .flower__g-fr {
        position: absolute;
        bottom: -4vmin;
        left: vmin;
        transform-origin: bottom left;
        z-index: 10;
        animation: flower__g-fr-ans 2s linear infinite;
      }
      @keyframes flower__g-fr-ans {
        0%,
        100% {
          transform: rotate(2deg);
        }
        50% {
          transform: rotate(4deg);
        }
      }
      .flower__g-fr .leaf {
        width: 30vmin;
        height: 50vmin;
        border-top-left-radius: 100%;
        border-left: 2vmin solid rgb(241, 4, 4);;
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 25%,
rgb(255, 0, 0) 50%
        );
        position: relative;
        z-index: 1;
      }
      .flower__g-fr__leaf {
        position: absolute;
        top: 0;
        left: 0;
        width: 10vmin;
        height: 10vmin;
        border-radius: 100% 0% 0% 100%/100% 100% 0% 0%;
        box-shadow: inset 0 2px 1vmin rgba(44, 238, 252, 0.2);
        background-image: linear-gradient(
            to bottom left,
            transparent,
            var(--dark-color) 98%
          ),
          linear-gradient(
            to bottom right,
rgb(255, 0, 0) 45%,
            transparent 50%,
            transparent
          );
        -webkit-mask-image: linear-gradient(
          135deg,
rgb(255, 51, 0) 40%,
          transparent 50%,
          transparent
        );
      }
      .flower__g-fr__leaf--1 {
        left: 20vmin;
        transform: rotate(45deg);
        animation: flower__g-fr-leaft-ans-1 0.5s 5.2s linear backwards;
      }
      @keyframes flower__g-fr-leaft-ans-1 {
        0% {
          transform-origin: left;
          transform: rotate(45deg) scale(0);
        }
      }
      .flower__g-fr__leaf--2 {
        left: 12vmin;
        top: -7vmin;
        transform: rotate(25deg) rotateY(-180deg);
        animation: flower__g-fr-leaft-ans-6 0.5s 5s linear backwards;
      }
      .flower__g-fr__leaf--3 {
        left: 15vmin;
        top: 6vmin;
        transform: rotate(55deg);
        animation: flower__g-fr-leaft-ans-5 0.5s 4.8s linear backwards;
      }
      .flower__g-fr__leaf--4 {
        left: 6vmin;
        top: -2vmin;
        transform: rotate(25deg) rotateY(-180deg);
        animation: flower__g-fr-leaft-ans-6 0.5s 4.6s linear backwards;
      }
      .flower__g-fr__leaf--5 {
        left: 10vmin;
        top: 14vmin;
        transform: rotate(55deg);
        animation: flower__g-fr-leaft-ans-5 0.5s 4.4s linear backwards;
      }
      @keyframes flower__g-fr-leaft-ans-5 {
        0% {
          transform-origin: left;
          transform: rotate(55deg) scale(0);
        }
      }
      .flower__g-fr__leaf--6 {
        left: 0vmin;
        top: 6vmin;
        transform: rotate(25deg) rotateY(-180deg);
        animation: flower__g-fr-leaft-ans-6 0.5s 4.2s linear backwards;
      }
      @keyframes flower__g-fr-leaft-ans-6 {
        0% {
          transform-origin: right;
          transform: rotate(25deg) rotateY(-180deg) scale(0);
        }
      }
      .flower__g-fr__leaf--7 {
        left: 5vmin;
        top: 22vmin;
        transform: rotate(45deg);
        animation: flower__g-fr-leaft-ans-7 0.5s 4s linear backwards;
      }
      @keyframes flower__g-fr-leaft-ans-7 {
        0% {
          transform-origin: left;
          transform: rotate(45deg) scale(0);
        }
      }
      .flower__g-fr__leaf--8 {
        left: -4vmin;
        top: 15vmin;
        transform: rotate(15deg) rotateY(-180deg);
        animation: flower__g-fr-leaft-ans-8 0.5s 3.8s linear backwards;
      }
      @keyframes flower__g-fr-leaft-ans-8 {
        0% {
          transform-origin: right;
          transform: rotate(15deg) rotateY(-180deg) scale(0);
        }
      }

      .long-g {
        position: absolute;
        bottom: 25vmin;
        left: -42vmin;
        transform-origin: bottom left;
      }
      .long-g--1 {
        bottom: 0vmin;
        transform: scale(0.8) rotate(-5deg);
      }
      .long-g--1 .leaf {
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 40%,
rgb(255, 0, 0) 80%
        ) !important;
      }
      .long-g--1 .leaf--1 {
        --w: 5vmin;
        --h: 60vmin;
        left: -2vmin;
        transform: rotate(3deg) rotateY(-180deg);
      }
      .long-g--2,
      .long-g--3 {
        bottom: -3vmin;
        left: -35vmin;
        transform-origin: center;
        transform: scale(0.6) rotateX(60deg);
      }
      .long-g--2 .leaf,
      .long-g--3 .leaf {
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 50%,
rgb(238, 255, 0) 80%
        ) !important;
      }
      .long-g--2 .leaf--1,
      .long-g--3 .leaf--1 {
        left: -1vmin;
        transform: rotateY(-180deg);
      }
      .long-g--3 {
        left: -17vmin;
        bottom: 0vmin;
      }
      .long-g--3 .leaf {
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 40%,
rgb(255, 0, 0) 80%
        ) !important;
      }
      .long-g--4 {
        left: 25vmin;
        bottom: -3vmin;
        transform-origin: center;
        transform: scale(0.6) rotateX(60deg);
      }
      .long-g--4 .leaf {
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 50%,
rgb(251, 255, 0) 80%
        ) !important;
      }
      .long-g--5 {
        left: 42vmin;
        bottom: 0vmin;
        transform: scale(0.8) rotate(2deg);
      }
      .long-g--6 {
        left: 0vmin;
        bottom: -20vmin;
        z-index: 100;
        filter: blur(0.3vmin);
        transform: scale(0.8) rotate(2deg);
      }
      .long-g--7 {
        left: 35vmin;
        bottom: 20vmin;
        z-index: -1;
        filter: blur(0.3vmin);
        transform: scale(0.6) rotate(2deg);
        opacity: 0.7;
      }
      .long-g .leaf {
        --w: 15vmin;
        --h: 40vmin;
        --c:rgb(129, 0, 118);
        position: absolute;
        bottom: 0;
        width: var(--w);
        height: var(--h);
        border-top-left-radius: 100%;
        border-left: 2vmin solid var(--c);
        -webkit-mask-image: linear-gradient(
          to top,
          transparent 20%,
          var(--dark-color)
        );
        transform-origin: bottom center;
      }
      .long-g .leaf--0 {
        left: 2vmin;
        animation: leaf-ans-1 4s linear infinite;
      }
      .long-g .leaf--1 {
        --w: 5vmin;
        --h: 60vmin;
        animation: leaf-ans-1 4s linear infinite;
      }
      .long-g .leaf--2 {
        --w: 10vmin;
        --h: 40vmin;
        left: -0.5vmin;
        bottom: 5vmin;
        transform-origin: bottom left;
        transform: rotateY(-180deg);
        animation: leaf-ans-2 3s linear infinite;
      }
      .long-g .leaf--3 {
        --w: 5vmin;
        --h: 30vmin;
        left: -1vmin;
        bottom: 3.2vmin;
        transform-origin: bottom left;
        transform: rotate(-10deg) rotateY(-180deg);
        animation: leaf-ans-3 3s linear infinite;
      }

      @keyframes leaf-ans-1 {
        0%,
        100% {
          transform: rotate(-5deg) scale(1);
        }
        50% {
          transform: rotate(5deg) scale(1.1);
        }
      }
      @keyframes leaf-ans-2 {
        0%,
        100% {
          transform: rotateY(-180deg) rotate(5deg);
        }
        50% {
          transform: rotateY(-180deg) rotate(0deg) scale(1.1);
        }
      }
      @keyframes leaf-ans-3 {
        0%,
        100% {
          transform: rotate(-10deg) rotateY(-180deg);
        }
        50% {
          transform: rotate(-20deg) rotateY(-180deg);
        }
      }
      .grow-ans {
        animation: grow-ans 2s var(--d) backwards;
      }

      @keyframes grow-ans {
        0% {
          transform: scale(0);
          opacity: 0;
        }
      }
      @keyframes light-ans {
        0% {
          opacity: 0;
          transform: translateY(0vmin);
        }
        25% {
          opacity: 1;
          transform: translateY(-5vmin) translateX(-2vmin);
        }
        50% {
          opacity: 1;
          transform: translateY(-15vmin) translateX(2vmin);
          filter: blur(0.2vmin);
        }
        75% {
          transform: translateY(-20vmin) translateX(-2vmin);
          filter: blur(0.2vmin);
        }
        100% {
          transform: translateY(-30vmin);
          opacity: 0;
          filter: blur(1vmin);
        }
      }
      @keyframes moving-flower-1 {
        0%,
        100% {
          transform: rotate(2deg);
        }
        50% {
          transform: rotate(-2deg);
        }
      }
      @keyframes moving-flower-2 {
        0%,
        100% {
          transform: rotate(18deg);
        }
        50% {
          transform: rotate(14deg);
        }
      }
      @keyframes moving-flower-3 {
        0%,
        100% {
          transform: rotate(-18deg);
        }
        50% {
          transform: rotate(-20deg) rotateY(-10deg);
        }
      }
      @keyframes blooming-leaf-right {
        0% {
          transform-origin: left;
          transform: rotate(70deg) rotateY(30deg) scale(0);
        }
      }
      @keyframes blooming-leaf-left {
        0% {
          transform-origin: right;
          transform: rotate(-70deg) rotateY(30deg) scale(0);
        }
      }
      @keyframes grow-flower-tree {
        0% {
          height: 0;
          border-radius: 1vmin;
        }
      }
      @keyframes blooming-flower {
        0% {
          transform: scale(0);
        }
      }
      @keyframes moving-grass {
        0%,
        100% {
          transform: rotate(-48deg) rotateY(40deg);
        }
        50% {
          transform: rotate(-50deg) rotateY(40deg);
        }
      }
      @keyframes moving-grass--2 {
        0%,
        100% {
          transform: scale(0.5) rotate(75deg) rotateX(10deg) rotateY(-200deg);
        }
        50% {
          transform: scale(0.5) rotate(79deg) rotateX(10deg) rotateY(-200deg);
        }
      }
      .growing-grass {
        animation: growing-grass-ans 1s 2s backwards;
      }

      @keyframes growing-grass-ans {
        0% {
          transform: scale(0);
        }
      }
      .not-loaded * {
        animation-play-state: paused !important;
      }
      /* Popup Styles */
    .popup-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      width: 400px;
      background-color: #ffe6e6;
      border: 2px solid #ff4d4d;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
      animation: none;
      z-index: 1000;
    }

    @keyframes pop-in {
      from {
        transform: translate(-50%, -50%) scale(0);
      }
      to {
        transform: translate(-50%, -50%) scale(1);
      }
    }

    @keyframes pop-out {
      from {
        transform: translate(-50%, -50%) scale(1);
      }
      to {
        transform: translate(-50%, -50%) scale(0);
      }
    }

    .popup-header {
      font-size: 1.8rem;
      font-weight: bold;
      color: #ff1a75;
      margin-bottom: 10px;
    }

    .popup-image {
      width: 100px;
      height: 100px;
      margin: 10px auto;
      background-color: #fff;
      border-radius: 50%;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .popup-image img {
      width: 80%;
      height: auto;
    }

    .popup-buttons {
      display: flex;
      justify-content: space-around;
      margin-top: 20px;
    }

    .popup-buttons button {
      padding: 10px 20px;
      font-size: 1rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .yes-button {
      background-color: #ff4d4d;
      color: #fff;
    }

    .no-button {
      background-color: #fff;
      color: #ff4d4d;
      border: 2px solid #ff4d4d;
    }

    .popup-buttons button:hover {
      transform: scale(1.1);
    }

    .hidden {
      display: none;
    }
.first-section {
  min-height: 100vh;
  display: flex;
  align-items: flex-end; /* Ensures the content aligns to the bottom */
  justify-content: center; /* Center content horizontally */
  background-color: var(--dark-color);
  perspective: 1000px;
  margin-top: 20px; /* Adjust this value to move the section lower */
}

/* Section Styling */
.second-section {
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    text-align: center;
    padding: 80px 20px;
}

/* Section Container */
.section-container {
    display: flex;
    justify-content: center;  /* Align to center */
    align-items: flex-start;
    gap: 20px;
    color: #fff;
    margin-top: 50px;
    padding: 20px;
    position: relative;
    max-width: 100%; /* Full width */
    width: 100%;
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
    margin-left: -10px;  /* Move entire section to the right */
}

/* Content Wrapper - Flexbox layout to align items horizontally */
.content-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

/* Box container for Spotify iframe and Image box */
.box-container {
    display: flex;
    flex-direction: column; /* Stack Spotify iframe and image vertically */
    gap: 20px; /* Increased space between Spotify and image */
    width: 100%;
    max-width: 600px; /* Limit width of box container */
}

/* Spotify Box - Center alignment of the iframe */
/* Spotify Box - Center alignment of the iframe */
.box.spotify-box {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 570px;; /* Ensure it takes full width of its container */
    height: 120px;
    transition: transform 0.3s ease; /* Add transition for bubble effect */
    color: #fff;
    margin-left:15px;
    margin-top: 50px;
    z-index: 3; /* Move Spotify box above other elements */
    position: relative; /* Ensure z-index is respected */
}

/* Spotify iframe styling */
#spotify-player {
    width: 100%;  /* Adjust width to be responsive */
    height: 230px;  /* Adjust height */
    border-radius: 10px;
    margin-top: 30px; /* Remove extra margin */
    margin-left: 10px;
}

/* Image Box Styling */
/* Image Box */
.box.image-box {
    background-color: #fff;
    padding: 5px;
    border-radius: 10px;
    background-color:rgb(255, 255, 255);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
    margin-top: -190px; /* Adjust as necessary */
    max-width: 1000px; /* Limit width of image box */
    height: 550px; /* Control height */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Bubble effect */
    position: relative; /* Ensure it respects z-index */
    z-index: 2; /* Higher z-index so it stays on top */
}



.box.message-box {
    background-color: #ffe6f0; /* Soft pink background */
    background-image: url('border.jpg'); /* Replace with your image path */
    background-size: cover; /* Ensure the image covers the entire box */
    background-size:250%;
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Ensure the image doesn't repeat */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: left;
    width: 450px;
    height: 520px;
    max-width: 450px; /* Adjust width for message box */
    border: 2px solid rgb(255, 255, 255); /* Pink border */
    transition: transform 0.3s ease; /* Add transition for bubble effect */
    position: absolute;
    top: 3%;
    right: 5%;
    z-index:4;
}


/* Styling for the image inside the image box */
/* Image inside the image box */
.box.image-box img {
    width: 550px;
    height: 330px;
    margin-top: 150px;
    border-radius: 8px;
    border: 1px rgb(0, 0, 0); /* Light pink dotted border */
}

/* Styling for overall section visibility */
.second-section.show {
    opacity: 1;
    transform: translateY(0);
}
/* Media Container */
.media-container {
    display: flex;
    flex-direction: row;
    align-items: center;
    border-radius: 10px;
    margin-bottom:30px;
    gap: 0px;
    width: 100%;
    max-width: 1200px; /* Control width of the container */
    margin: 0 auto; /* Center align container */
    padding: 20px;
    background-color: rgb(12, 12, 12);
    position: relative; /* Ensure it respects z-index */
    z-index: 1; /* Lower z-index to stay below the image box */
}

/* Style for the container holding the name and partner */
.name-partner-container {
    background-color:rgba(10, 10, 10, 0); /* Soft pink background */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
    top:77%;
    z-index:3;
    position:absolute;

    max-width: 500px;
    margin: 20px auto; /* Center the container */
}

/* Style for the header displaying the name */
h2 {
  font-family: 'Dancing Script', cursive; /* Set the font family */
    /* Smaller font size */
    color: #ff4081; /* Pink color for the name */
    font-size: 24px;
}

/* Style for the subheading displaying the partner */
h3 {
  font-family: 'Dancing Script', cursive; /* Set the font family */
    /* Smaller font size */
    color: #ff4081;  /* Pink color for the partner */
    font-size: 20px;
    margin-top: 10px;
}


@keyframes typing {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

@keyframes blink {
    50% {
        border-color: transparent;
    }
}

/* Apply the typing effect to the message */
.box.message-box {
    word-wrap: break-word; /* Break long words to the next line */
    overflow-wrap: break-word; /* Ensure words wrap properly */
    white-space: normal; /* Prevent forcing text into a single line */
}

.box.message-box p {
    line-height: 1.2; /* Adjust line spacing */
    margin: 5px 0; /* Reduce extra spacing */
    word-wrap: break-word;
}


@keyframes blinkCursor {
    50% { opacity: 0; }
}

.typing-cursor {
    font-size: 12px; /* Make cursor smaller */
    font-weight: bold;
    color: #ff66b2; /* Change color if needed */
    animation: blinkCursor 0.6s infinite;
}
.third-section {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    text-align: center;
    padding: 80px 20px;
    min-height: 100vh;
    width: 100%;
}
.third-section h2 {
        font-size: 3rem; /* Adjust the size of the h2 */
    }

    .third-section p {
      margin-bottom: 20px;
        font-size: 0.9rem; /* Make the p text bigger */
        color: white; /* Set the color to white */
    }
.maps-container {
    max-width: 900px;
    margin: 0 auto;
}

.map-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
}

#map {
    width: 100%;
    max-width: 800px;
    height: 400px;
    border-radius: 15px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
}

/* Styling for Yes/No buttons */
.location-share-buttons button {
        padding: 14px 24px;
        border-radius: 50px;
        font-size: 18px;
        font-weight: bold;
        border: 2px solid #FF4081;
        background-color: #E91E63;
        color: white;
        cursor: pointer;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .location-share-buttons button:hover {
        background-color: #F50057;
        transform: scale(1.1); /* Slight zoom-in effect on hover */
    }

    /* Specific styles for Yes and No buttons */
    .btn-yes {
        margin-right: 10px; /* Space between buttons */
    }

    .btn-no {
        background-color: #9E9E9E; /* A neutral grey color for the "No" button */
        border-color: #616161; /* Darker grey border for contrast */
    }

    .btn-no:hover {
        background-color: #757575; /* Slightly darker grey when hovered */
    }

.location-share-buttons button:hover {
    opacity: 0.8;
}
.location-share-buttons p
{
font-size: 1.2em;
}
/* Responsiveness for smaller screens */
@media (max-width: 768px) {
    .section-container {
        flex-direction: column;
        align-items: center;
    }

    .box-container {
        max-width: 100%;
    }

    #spotify-player {
        width: 100%; /* Ensure Spotify iframe scales down */
    }
    
    .box.message-box {
        max-width: 90%; /* Ensure message box doesn't overflow */
    }
}

  
@media (max-width: 480px) {
  .box.message-box p {
    font-size: 12px; /* Reduce text size */
    line-height: 1.2; /* Adjust spacing */
    margin: 5px 0; /* Reduce extra spacing */
    word-wrap: break-word;
    overflow-wrap: break-word;
}

  .second-section {
    min-height: 180vh; /* Each section takes up the full screen */
    width: 100%;
}
    /* Section Container */
    .section-container {
      height:350px;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-left: -10px; /* Shift left */
        margin-top: 30px;
        padding: 10px;
    }

    /* Content Wrapper */
    .content-wrapper {
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transform: translateX(0px); /* Move slightly left */
    }

    /* Box Container */
    .box-container {
        max-width: 100%;
        width: 100%;
        gap: 10px;
        transform: translateX(-10px);
    }

    /* Spotify Box */
    .box.spotify-box {
        width: 100%;
        height: 40px;
        margin-bottom: 30px;
        margin-left: -2px; /* Shift left */
        margin-top: -80px;
    }

    /* Spotify iframe */
    #spotify-player {
        width: 100%;
        height: 150px;
        margin-left: 10px; /* Shift left */
        margin-top: -100px;
    }

    .box.image-box {
    background-color: #fff;
    padding: 5px;
    border-radius: 10px;
    background-color:rgb(255, 255, 255);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
    margin-top: -190px; /* Adjust as necessary */
    max-width: 1000px; /* Limit width of image box */
    height: 400px; /* Control height */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Bubble effect */
    position: relative; /* Ensure it respects z-index */
    z-index: 2; /* Higher z-index so it stays on top */
}
    /* Image inside Image Box */
    .box.image-box img {
        width: 300px;;
        height: 260px;
        margin-top: 110px;
    }

    .box.message-box {
      background-color: #ffe6f0; /* Soft pink background */
    background-image: url('border.jpg'); /* Replace with your image path */
    background-size: cover; /* Ensure the image covers the entire box */
    background-size:350%;
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Ensure the image doesn't repeat */
    width: 100%; /* Slightly smaller width */
    height: auto;
    max-width: 270px; /* Adjusted max-width */
    position: absolute;
    margin-top:250px; /* Move slightly up */
    left: 7%; /* Adjust horizontal positioning */
    text-align: center;
    padding: 12px; /* Slightly reduce padding */
    transform: translateX(-5px); /* Fine-tune horizontal alignment */
}


    /* Media Container */
    .media-container {
        flex-direction: column;
        gap: 10px;
        padding: 10px;
        max-width: 320px;
        height: 100px;
        transform: translateX(-10px);
        position:absolute;
        top:20%;
        left:8%;
    }

    /* Name and Partner Container */
    .name-partner-container {
        background-color: rgba(73, 70, 70, 0);
        padding: -10px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 100%;
        max-width: 400px;
        margin: 20px auto;
        position: absolute;
        top:58%;
        transform: translateX(-10px);
    }

    /* Header Styling */
    h2 {
        font-family: 'Dancing Script', cursive;
        color: #ff4081;
        font-size: 22px;
        transform: translateX(-5px);
    }

    h3 {
        font-family: 'Dancing Script', cursive;
        color: #ff4081;
        font-size: 18px;
        margin-top: 8px;
        transform: translateX(-5px);
    }

    /* Typing Animation */
    .box.message-box p {
        line-height: 1.2;
        margin: 5px 0;
        word-wrap: break-word;
        transform: translateX(-5px);
    }

    /* Blinking Cursor */
    @keyframes blinkCursor {
        50% { opacity: 0; }
    }

    .typing-cursor {
        font-size: 10px;
        font-weight: bold;
        color: #ff66b2;
        animation: blinkCursor 0.6s infinite;
        transform: translateX(-5px);
    }
}
/* Styling for Yes/No buttons */
.location-share-buttons button {
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: bold;
        border: 2px solid #FF4081;
        background-color: #E91E63;
        color: white;
        cursor: pointer;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .location-share-buttons button:hover {
        background-color: #F50057;
        transform: scale(1.1); /* Slight zoom-in effect on hover */
    }

    /* Specific styles for Yes and No buttons */
    .btn-yes {
        margin-right: 10px; /* Space between buttons */
    }

    .btn-no {
        background-color: #9E9E9E; /* A neutral grey color for the "No" button */
        border-color: #616161; /* Darker grey border for contrast */
    }

    .btn-no:hover {
        background-color: #757575; /* Slightly darker grey when hovered */
    }

.location-share-buttons button:hover {
    opacity: 0.8;
}
.location-share-buttons p
{
font-size: 0.9em;
}

        .popup-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      width: 270px;
      background-color: #ffe6e6;
      border: 2px solid #ff4d4d;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
      animation: none;
      z-index: 1000;
    }
        .first-section {
  min-height: 100vh;
  display: flex;
  align-items: flex-end; /* Ensures the content aligns to the bottom */
  justify-content: center; /* Center content horizontally */
  background-color: var(--dark-color);
  perspective: 1000px;
  margin-top: 5px; /* Adjust this value to move the section lower */
  margin-bottom:70px;
  top:50%;
}
h1.title {
    font-family: 'Dancing Script', cursive; /* Set the font family */
    font-size: 50px; /* Smaller font size */
    color: #ff4081; /* Pink color */
    margin-bottom: 10px; /* Adjust margin to make it closer to content */
    text-align: center; /* Center align the text */
}
/* You can also modify other styles, such as the container, if needed */
/* Button styling */
.reloadBtn {
    position: absolute;
    top:90%;
    bottom:20%;
    z-index: 999;
    font-size: 24px;
    font-family: 'Poppins', sans-serif;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background-color: #ff4081; /* Pink background for Valentine's theme */
    color: white; /* Text color */
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease-in-out;
}

/* Hover effect with pulsing heart */
.reloadBtn:hover {
    background-color: #ff4081;
    animation: pulseHeart 1.5s infinite; /* Heart pulsing animation */
    transform: scale(1.1); /* Slight scale effect */
}




.reloadBtn.circle {
    width: 50px; /* Smaller size of the circle */
    height: 50px; /* Smaller size of the circle */
    border-radius: 50%; /* Make it circular */
    padding: 0; /* Remove padding for a perfect circle */
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease; /* Smooth transition */
    position: fixed; /* Fixed position so it stays at the bottom-right */
    right: 20px; /* 20px from the right edge of the viewport */
    bottom: 20px; /* 20px from the bottom edge */
}
.reloadBtn:hover {
    background-color: #ff4081; 
    animation: pulseHeart 1.5s infinite; /* Heart pulsing animation */
    transform: scale(1.1); /* Slight scale effect */
}

.reloadBtn.circle:before {
    margin-right: 0; /* Remove space when in circle mode */
    font-size: 40px; /* Increase the heart size */
}
      
      }
    </style>
  </head>
  <section class="first-section">
   <!-- Initial Valentine Popup -->
  <div id="valentinePopup" class="popup-container">
    <div class="popup-header">
      Will you be my Valentine, <span id="partnerName"><?php echo $partner; ?></span>? 💕
    </div>
    <div class="popup-image">
      <img src="cute5.png" alt="Heart">
    </div>
    <div class="popup-buttons">
      <button class="yes-button" onclick="handlePopupResponse(true)">Yes</button>
      <button class="no-button" onclick="handlePopupResponse(false)">No</button>
    </div>
  </div>

  <!-- Secondary Popup -->
  <div id="secondaryPopup" class="popup-container hidden">
    <div class="popup-header">
      Maybe Next Time, Take Care Love 💕
    </div>
    <div class="popup-image">
      <img src="cute4.png" alt="Heart">
    </div>
    <div class="popup-buttons">
      <button class="yes-button" onclick="dismissPopup()">Okay</button>
    </div>
  </div>

  <h1 class="title">Happy Valentines, <?php echo $partner; ?>💕</h1>
  
  <div class="night"></div>
  <div class="flowers">
      <div class="flower flower--1">
        <div class="flower__leafs flower__leafs--1">
          <div class="flower__leaf flower__leaf--1"></div>
          <div class="flower__leaf flower__leaf--2"></div>
          <div class="flower__leaf flower__leaf--3"></div>
          <div class="flower__leaf flower__leaf--4"></div>
          <div class="flower__white-circle"></div>
       
          <div class="flower__light flower__light--1"></div>
          <div class="flower__light flower__light--2"></div>
          <div class="flower__light flower__light--3"></div>
          <div class="flower__light flower__light--4"></div>
          <div class="flower__light flower__light--5"></div>
          <div class="flower__light flower__light--6"></div>
          <div class="flower__light flower__light--7"></div>
          <div class="flower__light flower__light--8"></div>
        </div>
        <div class="flower__line">
          <div class="flower__line__leaf flower__line__leaf--1"></div>
          <div class="flower__line__leaf flower__line__leaf--2"></div>
          <div class="flower__line__leaf flower__line__leaf--3"></div>
          <div class="flower__line__leaf flower__line__leaf--4"></div>
          <div class="flower__line__leaf flower__line__leaf--5"></div>
          <div class="flower__line__leaf flower__line__leaf--6"></div>
        </div>
      </div>

      <div class="flower flower--2">
        <div class="flower__leafs flower__leafs--2">
          <div class="flower__leaf flower__leaf--1"></div>
          <div class="flower__leaf flower__leaf--2"></div>
          <div class="flower__leaf flower__leaf--3"></div>
          <div class="flower__leaf flower__leaf--4"></div>
          <div class="flower__white-circle"></div>

          <div class="flower__light flower__light--1"></div>
          <div class="flower__light flower__light--2"></div>
          <div class="flower__light flower__light--3"></div>
          <div class="flower__light flower__light--4"></div>
          <div class="flower__light flower__light--5"></div>
          <div class="flower__light flower__light--6"></div>
          <div class="flower__light flower__light--7"></div>
          <div class="flower__light flower__light--8"></div>
        </div>
        <div class="flower__line">
          <div class="flower__line__leaf flower__line__leaf--1"></div>
          <div class="flower__line__leaf flower__line__leaf--2"></div>
          <div class="flower__line__leaf flower__line__leaf--3"></div>
          <div class="flower__line__leaf flower__line__leaf--4"></div>
        </div>
      </div>

      <div class="flower flower--3">
        <div class="flower__leafs flower__leafs--3">
          <div class="flower__leaf flower__leaf--1"></div>
          <div class="flower__leaf flower__leaf--2"></div>
          <div class="flower__leaf flower__leaf--3"></div>
          <div class="flower__leaf flower__leaf--4"></div>
          <div class="flower__white-circle"></div>

          <div class="flower__light flower__light--1"></div>
          <div class="flower__light flower__light--2"></div>
          <div class="flower__light flower__light--3"></div>
          <div class="flower__light flower__light--4"></div>
          <div class="flower__light flower__light--5"></div>
          <div class="flower__light flower__light--6"></div>
          <div class="flower__light flower__light--7"></div>
          <div class="flower__light flower__light--8"></div>
        </div>
        <div class="flower__line">
          <div class="flower__line__leaf flower__line__leaf--1"></div>
          <div class="flower__line__leaf flower__line__leaf--2"></div>
          <div class="flower__line__leaf flower__line__leaf--3"></div>
          <div class="flower__line__leaf flower__line__leaf--4"></div>
        </div>
      </div>

      <div class="grow-ans" style="--d: 1.2s">
        <div class="flower__g-long">
          <div class="flower__g-long__top"></div>
          <div class="flower__g-long__bottom"></div>
        </div>
      </div>

      <div class="growing-grass">
        <div class="flower__grass flower__grass--1">
          <div class="flower__grass--top"></div>
          <div class="flower__grass--bottom"></div>
          <div class="flower__grass__leaf flower__grass__leaf--1"></div>
          <div class="flower__grass__leaf flower__grass__leaf--2"></div>
          <div class="flower__grass__leaf flower__grass__leaf--3"></div>
          <div class="flower__grass__leaf flower__grass__leaf--4"></div>
          <div class="flower__grass__leaf flower__grass__leaf--5"></div>
          <div class="flower__grass__leaf flower__grass__leaf--6"></div>
          <div class="flower__grass__leaf flower__grass__leaf--7"></div>
          <div class="flower__grass__leaf flower__grass__leaf--8"></div>
          <div class="flower__grass__overlay"></div>
        </div>
      </div>

      <div class="growing-grass">
        <div class="flower__grass flower__grass--2">
          <div class="flower__grass--top"></div>
          <div class="flower__grass--bottom"></div>
          <div class="flower__grass__leaf flower__grass__leaf--1"></div>
          <div class="flower__grass__leaf flower__grass__leaf--2"></div>
          <div class="flower__grass__leaf flower__grass__leaf--3"></div>
          <div class="flower__grass__leaf flower__grass__leaf--4"></div>
          <div class="flower__grass__leaf flower__grass__leaf--5"></div>
          <div class="flower__grass__leaf flower__grass__leaf--6"></div>
          <div class="flower__grass__leaf flower__grass__leaf--7"></div>
          <div class="flower__grass__leaf flower__grass__leaf--8"></div>
          <div class="flower__grass__overlay"></div>
        </div>
      </div>

      <div class="grow-ans" style="--d: 2.4s">
        <div class="flower__g-right flower__g-right--1">
          <div class="leaf"></div>
        </div>
      </div>

      <div class="grow-ans" style="--d: 2.8s">
        <div class="flower__g-right flower__g-right--2">
          <div class="leaf"></div>
        </div>
      </div>

      <div class="grow-ans" style="--d: 2.8s">
        <div class="flower__g-front">
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--1"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--2"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--3"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--4"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--5"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--6"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--7"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div
            class="flower__g-front__leaf-wrapper flower__g-front__leaf-wrapper--8"
          >
            <div class="flower__g-front__leaf"></div>
          </div>
          <div class="flower__g-front__line"></div>
        </div>
      </div>

      <div class="grow-ans" style="--d: 3.2s">
        <div class="flower__g-fr">
          <div class="leaf"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--1"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--2"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--3"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--4"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--5"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--6"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--7"></div>
          <div class="flower__g-fr__leaf flower__g-fr__leaf--8"></div>
        </div>
      </div>

      <div class="long-g long-g--0">
        <div class="grow-ans" style="--d: 3s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 2.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 3.4s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--1">
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 3.8s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 4s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--2">
        <div class="grow-ans" style="--d: 4s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 4.4s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 4.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--3">
        <div class="grow-ans" style="--d: 4s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 3s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--4">
        <div class="grow-ans" style="--d: 4s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 3s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--5">
        <div class="grow-ans" style="--d: 4s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 3s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--6">
        <div class="grow-ans" style="--d: 4.2s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 4.4s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 4.6s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 4.8s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>

      <div class="long-g long-g--7">
        <div class="grow-ans" style="--d: 3s">
          <div class="leaf leaf--0"></div>
        </div>
        <div class="grow-ans" style="--d: 3.2s">
          <div class="leaf leaf--1"></div>
        </div>
        <div class="grow-ans" style="--d: 3.5s">
          <div class="leaf leaf--2"></div>
        </div>
        <div class="grow-ans" style="--d: 3.6s">
          <div class="leaf leaf--3"></div>
        </div>
      </div>
    </div>
    </div>
        </div>
      </div>
    </div>
  </section>
  <section class="second-section">
  <div class="section-container">
    <!-- New Wrapper Container for Box Elements -->
    <div class="content-wrapper">
        <!-- New Outer Container for Image and Spotify -->
        <div class="media-container">
            <!-- Box container for image and Spotify iframe -->
            <div class="box-container">
                <!-- Spotify Iframe Box -->
                <div class="box spotify-box">
                    <?php 
                        if (!empty($song_id)) { 
                            echo '<iframe id="spotify-player" 
                                    src="https://open.spotify.com/embed/track/' . htmlspecialchars($song_id) . '" 
                                    width="500" height="120" 
                                    frameborder="0" 
                                    allowtransparency="true" 
                                    allow="encrypted-media">
                                  </iframe>';
                        } else {
                            echo '<p>No song available.</p>';
                        }
                    ?>
                </div>
                
                <!-- Image box below the Spotify iframe -->
                <div class="box image-box">
                    <?php 
                        if (!empty($picture)) {
                            echo '<img src="' . htmlspecialchars($picture) . '" alt="User Picture">';
                        } else {
                            echo '<p>No image available.</p>';
                        }
                    ?>
                </div>
                <div class="box message-box">
    <h3>Message for: <?php echo htmlspecialchars($partner); ?></h3>
    <p id="typing-text"><?php echo nl2br(htmlspecialchars($message)); ?></p>
</div>
             <!-- Div Container to Display Name and Partner -->
             <div class="name-partner-container">
        <h2><?php echo htmlspecialchars($name); ?> & <?php echo htmlspecialchars($partner); ?> 💕</h2>
    </div>
            </div>

        </div>
    </div>
</div>

            
        </div>
    </div>
</section>

<section class="third-section">
    <div class="maps-container">
        <h2><?php echo htmlspecialchars($name); ?> invited you to this location</h2>
       
        <div class="map-container">
            <div id="map"></div>
        </div>
        <!-- Yes/No buttons -->
       <!-- Yes/No buttons -->
       <div class="location-share-buttons">
        <p> Do you want to share your location with others ? <p>
            <button class="btn-yes" onclick="saveResponse('Allowed')">Yes</button>
            <button class="btn-no" onclick="saveResponse('Declined')">No</button>
        </div>
      
    </div>
</section>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Show initial popup on page load
    window.onload = () => {
      const popup = document.getElementById("valentinePopup");
      popup.style.animation = "pop-in 0.5s ease forwards";
    };

    // Handle Yes/No button clicks
    function handlePopupResponse(response) {
      const popup = document.getElementById("valentinePopup");

      // Add pop-out animation
      popup.style.animation = "pop-out 0.5s ease forwards";

      // Remove initial popup after animation
      setTimeout(() => {
        popup.style.display = "none";

        if (response) {
          // Trigger flower bloom
          bloomFlower();
        } else {
          // Show secondary popup
          const secondaryPopup = document.getElementById("secondaryPopup");
          secondaryPopup.classList.remove("hidden");
          secondaryPopup.style.animation = "pop-in 0.5s ease forwards";
        }
      }, 500);
    }

    // Dismiss the secondary popup
    function dismissPopup() {
      const secondaryPopup = document.getElementById("secondaryPopup");
      secondaryPopup.style.animation = "pop-out 0.5s ease forwards";

      setTimeout(() => {
        secondaryPopup.style.display = "none";
      }, 500);
    }

    // Bloom flower logic
    function bloomFlower() {
      if (document.body.classList.contains('not-loaded')) {
        document.body.classList.remove("not-loaded");
        document.querySelector('.reloadBtn').textContent = '→ ';
        // Simulate flower blooming here
        alert("Flowers are blooming! 🌸");
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
    const secondSection = document.querySelector(".second-section");

    function revealOnScroll() {
        const sectionTop = secondSection.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        if (sectionTop < windowHeight - 100) {
            secondSection.classList.add("show");
        }
    }

    window.addEventListener("scroll", revealOnScroll);
});

fetch('/get-song-id.php')  
    .then(response => response.json())
    .then(data => {
        if (data.song_id) {
            let spotifyPlayer = document.getElementById("spotify-player");
            spotifyPlayer.src = `https://open.spotify.com/embed/track/${data.song_id}`;
        } else {
            console.error("No song found:", data.error);
        }
    })
    .catch(error => console.error('Error fetching song ID:', error));

    document.addEventListener("DOMContentLoaded", function () {
    let messageElement = document.getElementById("typing-text");
    let text = messageElement.innerHTML.replace(/<br\s*\/?>/g, "\n"); // Replace <br> with newlines
    messageElement.innerHTML = ""; // Clear existing text
    let index = 0;

    function typeEffect() {
        if (index < text.length) {
            let char = text.charAt(index);
            messageElement.innerHTML += char === "\n" ? "<br>" : char; // Convert \n back to <br>
            index++;
            setTimeout(typeEffect, 50); // Adjust speed here
        }
    }

    typeEffect();
});
function typeEffect() {
    if (index < text.length) {
        let char = text.charAt(index);
        if (char === "\n" || messageElement.scrollWidth >= messageElement.clientWidth) {
            messageElement.innerHTML += "<br>"; // Move to next line when max width is reached
        } else {
            messageElement.innerHTML += char;
        }
        index++;
        setTimeout(typeEffect, 50); // Typing speed
    } else {
        messageElement.innerHTML += '<span class="typing-cursor">|</span>'; // Smaller blinking cursor
    }
}
document.addEventListener("DOMContentLoaded", function () {
    var myLatitude = <?php echo json_encode($my_location['latitude']); ?>;
    var myLongitude = <?php echo json_encode($my_location['longitude']); ?>;

    // Create the map
    var map = L.map('map').setView([myLatitude, myLongitude], 13);

    // Set up tile layer for the map (OpenStreetMap tiles)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add the current user's location marker with a distinct color and larger size
    var myMarker = L.marker([myLatitude, myLongitude], {
        icon: L.icon({
            iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png', // Default marker icon
            iconSize: [40, 40], // Larger size to make the current user's marker stand out
            iconAnchor: [20, 40], // Anchor the marker appropriately
            popupAnchor: [0, -40] // Adjust popup position
        })
    }).addTo(map)
    .bindPopup("This is my location, invited by <?php echo $name; ?>")
    .openPopup();

    // Add a button to zoom in to the user's location
    var zoomInButton = L.control({position: 'topright'}); // Position of the button on the map

    zoomInButton.onAdd = function(map) {
        var button = L.DomUtil.create('button', 'zoom-in-btn');
        button.innerHTML = 'Zoom to My Location'; // The button text
        button.style.backgroundColor = '#E91E63'; // A deeper pink color for contrast
        button.style.color = 'white';
        button.style.border = '2px solid #FF4081'; // Adding a border with a slightly lighter shade
        button.style.padding = '10px 19px'; // Slightly larger padding for better proportion
        button.style.borderRadius = '50px'; // Fully rounded corners for a smooth look
        button.style.fontSize = '15px'; // Slightly larger font for readability
        button.style.fontWeight = 'bold'; // Make the text stand out
        button.style.cursor = 'pointer';
        button.style.boxShadow = '0 8px 16px rgba(0, 0, 0, 0.4)'; // More prominent shadow
        button.style.transition = 'transform 0.3s ease, background-color 0.3s ease'; // Smooth transitions for background and scaling

        // Hover effect: Slight zoom-in and color change on hover
        button.onmouseover = function() {
            button.style.backgroundColor = '#F50057'; // A slightly brighter pink
            button.style.transform = 'scale(1.1)'; // Slight zoom-in effect on hover
        };
        button.onmouseout = function() {
            button.style.backgroundColor = '#E91E63'; // Reset to original color
            button.style.transform = 'scale(1)'; // Reset the zoom effect
        };

        // Click functionality: Zoom in to the user's location
        L.DomEvent.on(button, 'click', function() {
            map.setView([myLatitude, myLongitude], 16); // Zoom in to user's location
        });

        return button;
    };

    zoomInButton.addTo(map);

    // Add other users' locations with default-sized markers
    var locations = <?php echo json_encode($locations); ?>;

    if (locations.length === 0) {
        console.error("No locations found.");
        return;
    }
    locations.forEach(function(location) {
    var imagePath = location.picture ? `${location.picture}` : 'default.jpg'; // Set a default image if none is found

    console.log(location.picture); // Debugging line to check the picture field

    var popupContent = `
        <div style="text-align: center; background: linear-gradient(to right, #ff7f7f, #ffb6c1); padding: 10px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
            <strong>${location.name} & ${location.partner} 💕</strong><br>
            <img src="${imagePath}" alt="Profile Picture" style="width:100px; height:100px; border-radius:50%; box-shadow: 2px 2px 10px rgba(0,0,0,0.2); cursor: pointer;" class="zoom-image">
        </div>
    `;

    var marker = L.marker([location.latitude, location.longitude]).addTo(map);

    // Bind the popup with custom styling
    marker.bindPopup(popupContent);

    // Zoom to the location when clicking on the image
    marker.on('popupopen', function() {
        var imageElement = document.querySelector('.zoom-image');
        
        if (imageElement) {
            imageElement.addEventListener('click', function() {
                map.setView([location.latitude, location.longitude], 15); // Zoom in to the marker's location
            });
        }
    });

    // Apply additional styling only to the inner content layer
    marker.on('popupopen', function() {
        var popupContentElement = document.querySelector('.leaflet-popup-content');
        
        if (popupContentElement) {
            // Revert back to the original styling
            popupContentElement.style.background = 'linear-gradient(to right, #ff7f7f, #ffb6c1)';
            popupContentElement.style.borderRadius = '10px';
            popupContentElement.style.padding = '15px';
            popupContentElement.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        }
    });
});




    // Optional: Make sure the user's marker stays large, even when zooming out
    map.on('zoomend', function() {
        var zoomLevel = map.getZoom();

        // Update the size of the current user's marker to remain larger with a light pink-red color
        var myMarkerIcon = L.icon({
            iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png', // Default icon URL
            iconSize: [40, 40], // Keep the user's marker larger
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            shadowSize: [50, 50],
            shadowAnchor: [20, 50]
        });

        // Set the current marker icon to the larger one
        myMarker.setIcon(myMarkerIcon);

        // For other users, the markers can stay the same size or adjust size based on zoom level
        locations.forEach(function(location) {
            var iconSize = zoomLevel < 10 ? [10, 10] : [15, 15]; // Shrink markers at lower zoom levels
            var otherMarkerIcon = L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png', // Default small marker
                iconSize: iconSize
            });

            var otherMarker = L.marker([location.latitude, location.longitude]).setIcon(otherMarkerIcon).addTo(map);
            otherMarker.bindPopup(location.name + ' & ' + location.partner + ' 💕');
        });
    });
});
function saveResponse(status) {
        let referenceNumber = "<?php echo htmlspecialchars($_GET['reference_number'] ?? ''); ?>"; // Ensure reference_number is available
        
        if (!referenceNumber) {
            alert("Reference number is missing.");
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "update_location_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText); // Show success message
                location.reload(); // Reload page to reflect changes (optional)
            }
        };

        xhr.send("reference_number=" + referenceNumber + "&status=" + status);
    }
    </script>
</body>
</html>
