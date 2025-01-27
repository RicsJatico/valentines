<?php
session_start();  // Start the session at the very beginning

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "valentines";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If no session, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();  // Stop further execution of the script
}

$user_name = '';
$reference_number = '';
$partner = ''; // Add variable for partner

// Retrieve user data based on session user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, reference_number, status, partner FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $reference_number, $status, $partner);
$stmt->fetch();
$stmt->close();

// Update user status to 'online' if it's not already
if ($status !== 'online') {
    $update_status_stmt = $conn->prepare("UPDATE users SET status = 'online' WHERE id = ?");
    $update_status_stmt->bind_param("i", $user_id);
    $update_status_stmt->execute();
    $update_status_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dedicate To Your Loved Ones</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google Fonts for typography -->
    <link rel="icon" href="" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> 
    <style> 
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #E5E5E5;
            background-color: #f0f0f0;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }
        
        /* Navigation Bar */
       /* Modify navbar styling */

/* Hero Section */
.hero {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background:#ec688944;
    padding: 0 20px;
    box-sizing: border-box;
    background-attachment: fixed;
}

.hero-container {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;     /* Center vertically */
    max-width: 1200px;
    width: 100%;
    text-align: center; /* Ensure text is centered inside the container */
}

.hero-text {
    max-width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    

}



.hero-text h1 {
    overflow: hidden; /* Ensures the text is hidden as it's being typed */

            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: #ff3366;
          
    white-space: nowrap;
    letter-spacing: 2px;
   
}


.hero-text p {
    font-size: 1.5rem;
    margin-bottom: 30px;
}



/* Button Styling */
/* Bubble Button Design */
/* Bubble Button Styling */
.btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 25px;
    font-size: 1.2em;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    color: #ffffff;
    background: linear-gradient(135deg, #690dcb, #005eff); /* Gradient background */
    border-radius: 15px; /* Rounded shape */
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    transition: all 0.3s ease-in-out;
    position: relative; /* For pseudo-elements */
    overflow: hidden;
}

/* Hover Effects */
.btn:hover {
    transform: translateY(-5px); /* Slight upward movement */
    background: linear-gradient(135deg, #7800f8, #2701ff); /* Gradient background */
}

/* Adding Glow Animation */
.btn::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 150%;
    height: 150%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0));
    opacity: 0;
    border-radius: 50%;
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.btn:hover::before {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.2); /* Expand glow effect */
}

        /* About Us Section */
        /* About Us Section */
.about {
    padding: 80px 0;
    text-align: center;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('background.svg') no-repeat center center/cover;
    color: white;
    background-attachment: fixed;
}

.about h2 {
    font-size: 2.5em;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;

}

.about-content {
    display: flex; /* Use flexbox for layout */
    align-items: center; /* Vertically align content */
    justify-content: flex-start; /* Ensure content is aligned to the left */
    flex-wrap: wrap; /* Allow content to wrap on smaller screens */
}

.about-content img {
    width: 100%;
    max-width: 580px; /* Control the image size */
    border-radius: 10px;
    margin-right: 20px; /* Space between the image and text */
}

.about-text {
    color: #fff;
    max-width: 600px;
}

.about-text p {
    font-size: 1.5em;
    line-height: 2;
}

     /* Services Section */
.services {
    padding: 50px 0;

    text-align: center;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('background.svg') no-repeat center center/cover;
    color: white;
    background-attachment: fixed;
}

.social-links {
    text-align: center;
    margin: 10px 0;
}

.social-links a {
    margin: 0 5px;
    display: inline-block;
    transition: transform 0.3s, box-shadow 0.3s;
}

.social-links img {
    width: 40px;
    height: 40px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.social-links a:hover img {
    transform: scale(1.2); /* Scale up to 120% */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Add a shadow effect */
}

.services h2 {
    font-size: 2.5em;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;
}

.services-grid {
    display: flex;
    justify-content: center;
    gap: 20px;
   
    flex-wrap: wrap;
}

.service-item {
    background: linear-gradient(135deg, #fa03e974, #4a10d2a2); /* Same gradient as hero section */
    padding: 50px 30px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.service-item i {
    font-size: 2em;
    color: #007BFF;
    margin-bottom: 20px;
}

.service-item h3 {
    font-size: 1.2em;
    margin-bottom: 15px;
}

.service-item p {
    font-size: 1em;
    line-height: 1.6;
    color: #ffffff;
}


        /* Contact Us Section */
        .contact {
            padding: 80px 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('background.svg') no-repeat center center/cover;
    color: white;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
            background-attachment: fixed;
            text-align: center;
        }

        .contact h2 {
            font-size: 2.5em;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;
        }

        .contact-form {
            color: #ffffff;
            max-width: 600px;
            margin: auto;
            text-align: left;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007BFF;
            outline: none;
        }

        .contact-form .btn {
            width: 100%;
            padding: 15px;
            font-size: 1.1em;
            background-color: linear-gradient(135deg, #fa03e9, #4a10d2);
        }

      

        /* Scroll Down Arrow Styling */
/* Scroll Down Arrow Styling */
.scroll-arrow {
    position: fixed;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
}

.scroll-arrow img {
    width: 60px;
    height: 60px;
    transition: transform 0.3s ease;
}

.scroll-arrow a {
    display: inline-block;
}

.scroll-arrow:hover img {
    transform: translateY(-5px);
}

/* Hide Scroll Arrow in other sections */
.no-scroll-arrow #scroll-down {
    display: none;
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}


/* Section Styling */
.team {
    padding: 80px 0;
    text-align: center;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('background.svg') no-repeat center center/cover;
    background-attachment: fixed;
    color: white;
}

.team h2 {
    font-size: 2.5em;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;
}

/* Flip Card Container */
.flip-card-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
    width: 100%;
    padding: 20px;
}
.flip-card-back img {
    width:100%;
    height: 240px;
}
/* Flip Card */
.flip-card {
    width: 230px;
    height: 300px;
    perspective: 1000px;
}

.flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.6s;
}

.flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}
.flip-card-back h3{ 
    font-size:1em;
}
.flip-card-back p{ 
    font-size:0.8em;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;
}
.flip-card-front,
.flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.flip-card-front {
    background: url('Officialpicture.png') no-repeat center center;
    background-size: cover;
    background-color: rgba(0, 0, 0, 0.8); /* Fallback background */
    box-shadow: 0 0 20px rgba(167, 13, 126, 0.7);
}

.flip-card-back {
    transform: rotateY(180deg);
    box-shadow: 0 0 20px rgba(167, 13, 126, 0.7);
}


.hero-image {
    max-width: 700px; /* Ensure the image is responsive */
    height: auto; /* Maintain aspect ratio */
    margin: 20px 0; /* Add spacing */
    border-radius: 10px; /* Optional: Add rounded corners */
    box-shadow: 0 8px 15px rgba(95, 92, 92, 0); /* Pinkish shadow for emphasis */
    background-size: fill;
    background-position: center;
}

.projects {
    padding: 80px 0;
    text-align: center;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('background.svg') no-repeat center center/cover;
    background-attachment: fixed;
    color: white;
}

.projects h2 {
    font-size: 2.5em;
    margin-bottom: 40px;
    background: linear-gradient(135deg, #fa03e9, #4a10d2); /* Same gradient as hero section */
    -webkit-background-clip: text;
    color: transparent; /* Ensure text is transparent to show the gradient */
    text-shadow: #ffffff;
}


/* Initial state for the text */
.section-text {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 1s ease, transform 1s ease;
}

/* When the section comes into view */
.section-text.visible {
    opacity: 1;
    transform: translateY(0);
}


.hero {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}

.hero-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}





.hero-text {
    position: absolute;
    top: 45%;
    left: 55%;
    transform: translate(-50%, -50%);
    z-index: 1;
    color: white;
    text-align: center;
}

.background-hearts {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .heart {
            width: 20px;
            height: 20px;
            background: #ff3366;
            position: absolute;
            top: 50%;n
            left: 50%;
            animation: float 5s infinite ease-in-out;
            opacity: 0.5;
            transform: rotate(45deg);
            clip-path: polygon(50% 0%, 61% 16%, 100% 16%, 100% 37%, 50% 100%, 0 37%, 0% 16%, 39% 16%);
        }
        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(60deg);
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -200%) rotate(45deg);
                opacity: 0;
            }
        }

        .hero-container {
    position: relative;
    text-align: center;
}



    .hero-image {
        width: 100%;
        height: auto;
        z-index: 1; /* Ensure the image is behind the Spotify container */
    }

    .hero-container {
        position: relative;
        text-align: center;
    }

    .spotify-container {
        position: absolute;
        top: 21%;
        left: 72%;
        transform: translateX(-50%);
        z-index: 2; /* Make sure it appears above the image */
        background-color: rgba(243, 183, 198, 0.47);
        border-radius: 20px 20px 20px 80px; /* Set border radius for left bottom corner to 67px */
        padding: 11px; /* Reduced padding */
        width: 60%; /* Reduced width */
        max-width: 280px; /* Make it a little smaller */
        color: white;
    }

    #song-search {
        width: 100%;
        padding: 8px; /* Smaller padding */
        margin-bottom: 15px; /* Reduced margin */
        border-radius: 20px;
        border: none;
        background-color:rgba(255, 255, 255, 0.86);
        color: white;
        font-size: 14px; /* Smaller font size */
    }

    .spotify-preview {
        display: flex;
        align-items: center;
    }

    .song-image {
        width: 40px; /* Reduced image size */
        height: 40px;
        border-radius: 5px;
        margin-right: 10px;
    }

    .song-details .song-title {
        font-weight: bold;
        font-size: 14px; /* Adjusted font size */
    }

    .song-details .song-artist {
        font-size: 12px; /* Adjusted font size */
        color: #B3B3B3;
    }

    .image-upload-container {
        display: flex;
        justify-content: space-around;
        position: absolute;
        top: 65%;
        left: 0;
        right: 0;
        z-index: 10;
    
    }

    .upload-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 180px; /* Small size */
        height: 150px; /* Small size */
        border: 2px dashed #ccc;
        border-radius: 10px;
        background-color: rgba(243, 183, 198, 0.47);
        overflow: hidden;
        transition: background-color 0.3s ease;
        position: relative;
        margin-right: 15px; /* Add space between the upload boxes */
    }

    .upload-box:hover {
        background-color: rgba(247, 124, 155, 0.72);
    }

    .upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        cursor: pointer;
    }

    .plus-sign {
        font-size: 30px; /* Small size */
        color: #777;
        transition: color 0.3s ease;
    }

    .upload-label:hover .plus-sign {
        color: #333;
    }

    .file-input {
        display: none;
    }

    .image-preview {
        display: none;
        width: 100%;
        height: 200px;;
        object-fit: cover;
    }

    .image-preview.visible {
        display: block;
    }

    /* Message container */
.message-container {
    position: absolute;
    top: 23%; /* Adjust the distance from the top */
    left: 42%;
    transform: translateX(-50%);
    z-index: 20; /* Ensure it's on top of other elements */
    width: 80%; /* Adjust width as needed */
    max-width: 270px; /* Limit max width */
    padding: 10px;
    background-color: rgba(243, 183, 198, 0.47);
    border-radius: 20px 20px 80px 20px; /* Set border radius for left bottom corner to 67px */
}

.message-container textarea {
    width: 100%;
    height: 148px;
    padding: 10px;
    border-radius: 20px 20px 67px 20px; /* Set border radius for left bottom corner to 67px */
    border: none;
    resize: none;
    background-color: rgba(255, 255, 255, 0.47);
    color: #333;
    font-size: 16px;
}

.message-container textarea::placeholder {
    color: #aaa;
}

/* Hero image and other elements */
.hero-container {
    position: relative;
    text-align: center;
}

.hero-image {
    width: 100%;
    height: auto;
    z-index: 1; /* Ensure the image is behind the message container */
}
.submit-container {
    position: absolute;
    top: 100%; /* Adjust as needed to place it at the top layer */
    left: 50%;
    transform: translateX(-50%);
    z-index: 20; /* Ensures it stays on top of the image */
    text-align: center;
}

#submit-button {
    padding: 10px 20px;
    font-size: 16px;
    background-color:rgba(241, 11, 34, 0.6);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#submit-button:hover {
    background-color: #f23355;
}
#song-search {
    color: black;
}


@media (max-width: 480px) {
    .hero-container {
        padding: 10px;
        text-align: center;
    }

    .hero-image {
        width: 100%;
        height: auto;
        z-index: 1;
    }

    .spotify-container {
        position: absolute;
        top: 12%;
        left: 40%;
        transform: translateX(-50%);
        background-color: rgba(243, 183, 198, 0.47);
        border-radius: 20px 20px 20px 20px;
        padding: 20px;
        width: 500px;
        height:25 0px;
        color: white;
    }

    #song-search {
        width: 100%;
        padding: 6px;
        margin-bottom: 10px;
        border-radius: 20px;
        border: none;
        background-color: rgba(243, 234, 236, 0.47);
        color: white;
        font-size: 14px;
    }

    .song-image {
        width: 100px;
        height: 100px;
        border-radius: 5px;
        margin-right: 8px;
        margin-left:30px;
    }

    .song-title {
        font-size: 12px;
    }

    .song-artist {
        font-size: 10px;
    }

    .image-upload-container {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 150%;
    left: -20%;
    transform: translateY(-50%);
    z-index: 10;
    width: 150%;
}

.upload-box {
    width: 350px;
    height: 70px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    background-color: rgba(243, 183, 198, 0.47);
    overflow: hidden;
    transition: background-color 0.3s ease;
    margin-right: 30px;
    padding: 60px;
    margin-left:-26px;
    margin-top:80px;
}

.upload-box:hover {
    background-color: rgba(247, 124, 155, 0.72);
}

.upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    cursor: pointer;
}

.plus-sign {
    font-size: 30px;
    color: #777;
    transition: color 0.3s ease;
}

.upload-label:hover .plus-sign {
    color: #333;
}

.file-input {
    display: none;
}

.image-preview {
    display: none;
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.image-preview.visible {
    display: block;
}

    /* Submit Button Position */
.submit-container {
    position: fixed;
    margin-top:132px; /* Adjusted to move the button closer to the bottom */
    left: 38%;
    transform: translateX(-50%);
    z-index: 20;
    text-align: center;
    width: 100%;
    padding: 10px;
}

#submit-button {
    padding: 8px 16px;
    font-size: 14px;
    background-color: rgba(228, 177, 190, 0.72);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left:20px;
    transition: background-color 0.3s ease;
}

#submit-button:hover {
    background-color: rgba(247, 124, 155, 0.72);
}

    #submit-button:hover {
        background-color: #f23355;
    }

    .hero-text h1 {
        overflow: hidden;
        font-family: 'Dancing Script', cursive;
        font-size: 1.2rem;
        color: #ff3366;
        white-space: nowrap;
        letter-spacing: 2px;
        position: absolute;
        top: -120%;
    }

    .hero-text p {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }

    .message-container {
        position: absolute;
        top: 16%;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        width: 85%;
        max-width: 380px;
        padding: 5px;
        background-color: rgba(243, 183, 198, 0.47);
        border-radius: 10px 10px 10px 10px;
    }

    .message-container textarea {
        width: 100%;
        height: 122px;
        font-size: 14px;
        background-color: rgba(243, 234, 236, 0.47);
    }

    /* Adjusted Mobile View */
    .submit-container {
        top: 140%;
    }
    #song-search {
    color: black;
}
}

</style>

<body>
    <div class="background-hearts">
        <div class="heart" style="left: 20%; animation-delay: 0s;"></div>
        <div class="heart" style="left: 40%; animation-delay: 2s;"></div>
        <div class="heart" style="left: 60%; animation-delay: 4s;"></div>
        <div class="heart" style="left: 80%; animation-delay: 6s;"></div>
    </div>

    <section class="hero" id="home">
        <div class="hero-container">
            <!-- Message Container -->
            <div class="message-container">
                <textarea id="user-message" placeholder="Write your message here..."></textarea>
            </div>

            <div class="hero-text">
                <h1>Plan Your Valentines With <?php echo htmlspecialchars($partner) ; ?>♥️</h1>
   
                
                <div class="spotify-container">
    <input type="text" placeholder="Search for a song" id="song-search">
    <div class="spotify-preview">
        <img src="spotify-song-image.jpg" alt="Song Image" class="song-image" id="song-image">
        <div class="song-details">
            <p class="song-title" id="song-title">Song Title</p>
            <p class="song-artist">Artist Name</p>
            <!-- Audio Player Hidden Initially -->
            <audio id="audio-player" controls style="display:none;">
                <source id="audio-source" type="audio/mp3">
            </audio>
        </div>
    </div>
</div>

<!-- Image Upload Containers -->
<div class="image-upload-container">
    <div class="upload-box">
        <label for="file1" class="upload-label">
            <span class="plus-sign">+</span>
        </label>
        <input type="file" id="file1" class="file-input" onchange="previewImage(event, 'image1')" />
        <img id="image1" class="image-preview" />
    </div>
    <div class="upload-box">
        <label for="file2" class="upload-label">
            <span class="plus-sign">+</span>
        </label>
        <input type="file" id="file2" class="file-input" onchange="previewImage(event, 'image2')" />
        <img id="image2" class="image-preview" />
    </div>
</div>

<div class="background-container">
<img src="Landing.png" alt="Hero Image" class="hero-image">
</div>


<!-- Submit Button -->
<div class="submit-container">
    <button id="submit-button">Submit</button>
</div>

<script>
// Function to check if an element is in the viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Add the "visible" class to sections when they are in view
function checkVisibility() {
    const sections = document.querySelectorAll('.section-text');
    sections.forEach((section) => {
        if (isInViewport(section)) {
            section.classList.add('visible');
        } else {
            section.classList.remove('visible');
        }
    });
}

// Image Preview Function
function previewImage(event, previewId) {
    const file = event.target.files[0];
    const preview = document.getElementById(previewId);

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.add('visible');
        }

        reader.readAsDataURL(file);
    }
}

// Function to fetch the Spotify access token
async function getSpotifyAccessToken() {
    const response = await fetch('generate_token.php');
    const data = await response.json();
    return data.access_token;
}

// Function to search for a song on Spotify
async function searchSong() {
    const query = document.getElementById('song-search').value;
    if (!query) return;

    const token = await getSpotifyAccessToken();
    const response = await fetch(`https://api.spotify.com/v1/search?q=${query}&type=track&limit=1`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });

    const data = await response.json();
    if (data.tracks.items.length > 0) {
        const track = data.tracks.items[0];
        document.querySelector('.song-image').src = track.album.images[0].url;
        document.querySelector('.song-title').textContent = track.name;
        document.querySelector('.song-artist').textContent = track.artists.map(artist => artist.name).join(', ');

        // Set the preview URL
        const previewUrl = track.preview_url;
        if (previewUrl) {
            document.getElementById('audio-source').src = previewUrl;
            document.getElementById('audio-player').style.display = 'inline';  // Show the audio player
        } else {
            document.getElementById('audio-player').style.display = 'none'; // Hide the audio player if no preview is available
        }
    }
}

// Function to play or pause the preview song when the image is clicked
function playPreview() {
    const audioPlayer = document.getElementById('audio-player');
    const songImage = document.getElementById('song-image');
    
    if (!audioPlayer.src) {
        // Ensure audio source is set
        alert('No preview available for this song!');
        return;
    }

    if (audioPlayer.paused) {
        audioPlayer.play()
            .catch((error) => {
                console.error('Error playing the audio:', error);
                alert('There was an error playing the preview.');
            });
    } else {
        audioPlayer.pause();
    }
}

// Listen for the input event on the search field to search for a song
document.getElementById('song-search').addEventListener('input', searchSong);

// Add an event listener to the song image to trigger the play/pause action
document.getElementById('song-image').addEventListener('click', playPreview);
// Listen for file input change event
document.querySelector('.file-input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.querySelector('.image-preview');
            preview.src = e.target.result;
            preview.classList.add('visible'); // Show the preview
        }
        
        reader.readAsDataURL(file); // Read the image file
    }
});

console.log('Track Data:', track);  // Log the track data to see the available fields.
console.log('Preview URL:', previewUrl);  // Log the preview URL to see its value.

</script>
