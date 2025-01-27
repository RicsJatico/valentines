<?php
session_start();
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "valentines"; // Your database name


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_name = '';
$partner = '';
$reference_number = '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, partner, reference_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $partner, $reference_number);
$stmt->fetch();
$stmt->close();

$conn->close();

$locked_url = "http://localhost/?reference_number=" . urlencode($reference_number);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Your Work</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fce6f3;
            color: #333;
            text-align: center;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 60px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: 0 auto;
            border: 2px solid #f69a9a; /* Valentine color */
        }
        .header {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #d85c8e;
        }
        .url-container {
            font-size: 18px;
            margin: 20px 0;
            color: #7a4b7d;
        }
        #locked-url {
            color: #e34b87;
            font-weight: bold;
            text-decoration: underline;
        }
        .share-button {
            background-color: #e34b87;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .share-button:hover {
            background-color: #d05a78;
        }
        .copy-button {
            background-color: #ff6b81;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .copy-button:hover {
            background-color: #f04f67;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 20px;
            display: none;
        }
        .social-icons img {
    width: 40px;  /* Set width for all icons */
    height: 40px; /* Set height to ensure uniformity */
    object-fit: cover; /* Ensure the images are not distorted */
    cursor: pointer;
    transition: transform 0.3s ease-in-out; /* For hover effect */
}

.social-icons img:hover {
    transform: scale(1.1); /* Slightly enlarge the icon on hover */
}

        .valentine-heart {
            color: #f74f98;
            font-size: 30px;
            animation: heart-beat 1.5s infinite;
        }
        @keyframes heart-beat {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
           
        }
        /* Header styling */
header {
    font-family: 'Dancing Script', cursive;
    font-size: 3rem;
    color: #ff3366; /* Valentine's themed pink */
    margin-bottom: 20px;
    text-align: center; /* Center the header text */
}

.valentine-heart {
    color: #ff3366; /* Red heart color */
    font-size: 1.2rem; /* Slightly bigger heart */
}
/* Header styling with cursive font */
.header {
    font-family: 'Dancing Script', cursive;  /* Apply cursive font */
    font-size: 3rem;  /* Adjust font size */
    color: #ff3366;  /* Valentine's themed pink color */
    margin-bottom: 20px;
    text-align: center;  /* Center the header text */
}

.container {
    text-align: center;
    margin-top: 50px;
}
.home-button {
    background-color: #d85c8e;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    margin-top: 20px;
    transition: background-color 0.3s;
}

.home-button:hover {
    background-color: #c04a7a;
}
        @media (max-width: 480px) 
        {

            .copy-button {
            background-color: #ff6b81;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 0px;
            transition: background-color 0.3s;
        }

            .social-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
            display: none;
        }
        .header {
    font-family: 'Dancing Script', cursive;  /* Apply cursive font */
    font-size: 1.5rem;  /* Adjust font size */
    color: #ff3366;  /* Valentine's themed pink color */
    margin-bottom: 20px;
    text-align: center;  /* Center the header text */
}

.container {
    text-align: center;
    margin-top: 50px;
}
        .social-icons img {
    width: 30px;  /* Set width for all icons */
    height: 30px; /* Set height to ensure uniformity */
    object-fit: cover; /* Ensure the images are not distorted */
    cursor: pointer;
    transition: transform 0.3s ease-in-out; /* For hover effect */
}
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        Great, now that you're done, share your work with <strong><?php echo htmlspecialchars($partner); ?></strong> <span class="valentine-heart">❤️</span>
    </div>

    <div class="url-container">
        Send this URL: <span id="locked-url"><?php echo htmlspecialchars($locked_url); ?></span>
    </div>

    <button class="copy-button" onclick="copyUrl()">Copy URL</button>

    <div class="social-icons" id="social-buttons">
        <img src="ig.png" alt="Instagram" onclick="shareOnPlatform('instagram')">
        <img src="tiktok.webp" alt="TikTok" onclick="shareOnPlatform('tiktok')">
        <img src="messanger1.png" alt="Messenger" onclick="shareOnPlatform('messenger')">
        <img src="fb.webp" alt="Facebook" onclick="shareOnPlatform('facebook')">
        <img src="x.webp" alt="Twitter" onclick="shareOnPlatform('twitter')">
        
    </div>

    <button class="share-button" id="share-button">Share Link</button>
    <button class="home-button" onclick="window.location.href='index.php'">Back to Home</button>
</div>

<script>
    document.getElementById("share-button").addEventListener("click", function () {
        document.getElementById("social-buttons").style.display = "flex";
    });

    function copyUrl() {
        const url = document.getElementById("locked-url").textContent;
        navigator.clipboard.writeText(url).then(function() {
            alert("URL copied to clipboard!");
        }, function(err) {
            alert("Error copying the URL: " + err);
        });
    }

    function shareOnPlatform(platform) {
        var url = document.getElementById("locked-url").textContent;
        var shareURL = '';

        switch (platform) {
            case 'facebook':
                shareURL = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;
            case 'twitter':
                shareURL = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}`;
                break;
            case 'instagram':
                shareURL = `https://www.instagram.com/share?url=${encodeURIComponent(url)}`;
                break;
            case 'tiktok':
                shareURL = `https://www.tiktok.com/share?url=${encodeURIComponent(url)}`;
                break;
            case 'messenger':
                shareURL = `https://www.messenger.com/share?url=${encodeURIComponent(url)}`;
                break;
        }

        if (shareURL) {
            window.open(shareURL, '_blank');
        }
    }

    function goHome() {
        window.location.hrdddef = "index.html";
    }


</script>

</body>
</html>
