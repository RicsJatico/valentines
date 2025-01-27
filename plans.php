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

// If no session, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_name = '';
$reference_number = '';
$partner = '';

// Retrieve user data based on session user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, reference_number, status, partner FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $reference_number, $status, $partner);
$stmt->fetch();
$stmt->close();

// Update user status to 'online'
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
     <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <title>Valentine's Plan</title>
    <style>
          html, body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: #ffe6e6;
    height: 100%; /* Ensures the full height of the page */
}

body {
    display: flex;
    flex-direction: column; /* Stack elements vertically */
    justify-content: flex-start; /* Align content at the top */
    overflow-y: auto; /* Allow vertical scrolling */
    min-height: 100%; /* Ensure the body stretches to fill the full height */
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
        .message-container {
    width: 60%;
    margin: 0 auto;
    position: relative;
    border: 2px dotted #ff3366; /* Pink dotted border */
    padding: 0; /* Remove internal padding from the container */
    border-radius: 10px;
}

textarea {
    width: 100%;
    height: 150px;
    border-radius: 10px;
    padding: 15px;
    font-size: 16px;
    resize: none;
    border: none; /* Remove default textarea border */
    box-sizing: border-box; /* Include padding in the element's total width/height */
}
        .send-button {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #ff3366;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }


.send-button i {
    color: white;
    font-size: 18px;
}

.send-button:hover {
    transform: scale(1.1);
    background-color: #ff6699;
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
            top: 50%;
            left: 50%;
            animation: float 5s infinite ease-in-out;
            opacity: 0.5;
            transform: rotate(45deg);
            clip-path: polygon(50% 0%, 61% 16%, 100% 16%, 100% 37%, 50% 100%, 0 37%, 0% 16%, 39% 16%);
        }
        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(45deg);
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
        .background-hearts {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
      
        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(45deg);
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

    #dedicate {
    height: auto; /* Adjust height for content */
    color: #ff3366;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 20px;
}

#dedicate h2 {
    font-size: 2rem;
    color: #ff3366;
    margin-bottom: 20px;
}

#songSearch {
    padding: 10px;
    font-size: 1rem;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ff3366;
}

#searchSongBtn {
    padding: 10px 20px;
    background-color: #ff3366;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#searchSongBtn:hover {
    background-color: #ff6699;
}

#songResults {
    list-style-type: none;
    padding: 0;
    margin-top: 20px;
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
}

.song-item {
    display: flex;
    align-items: center;
    margin: 10px 0;
    cursor: pointer;  /* Makes the entire song item clickable */
}

.song-item:hover {
    background-color: #f5f5f5;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.song-image {
    width: 50px;
    height: 50px;
    margin-right: 10px;
    border-radius: 5px;
}

.song-info .song-title {
    font-weight: bold;
}

.song-info .song-artist {
    font-size: 0.9em;
    color: #555;
}


#dedicateBtn {
    padding: 10px 20px;
    background-color: #ff3366;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    margin-top: 20px;
    display: none;
}

#dedicateBtn:hover {
    background-color: #ff6699;
}

#upload-picture {
    text-align: center;
    padding: 30px;
    background: #ffe6f0;
    border-radius: 10px;
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.upload-container {
    position: relative; /* Ensure the pseudo-element is positioned correctly */
    background: url('bg.jpg') no-repeat center center;
    background-size: cover; /* Make sure the image covers the entire container */
    z-index: 1; /* Ensure the container is above the pseudo-element */
    padding: 30px; /* Add padding for content inside the container */
    border: 2px dashed #d6336c;
    width:705px;
}

/* Add an overlay for transparency */
.upload-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.46); /* Semi-transparent white overlay */
    z-index: -1; /* Place the overlay behind the content */
    border-radius: inherit; /* Match any border-radius on .upload-container */
}

.upload-container h2 {
    font-family: 'Dancing Script', cursive;
            font-size: 2.3rem;
            color: #ff3366;
    margin-bottom: 10px;
    opacity: 3;
}

.upload-container p {
    color: #8c1b4d;
    margin-bottom: 20px;
    font-size: 1.1em;
}

.upload-box {
    border: 2px dashed #d6336c;
    border-radius: 10px;
    padding: 20px;
    width: 250px;
    margin: 0 auto;
    text-align: center;
    background: #fff;
    transition: 0.3s ease;
}

.upload-box:hover {
    background: #ffd9e0;
    border-color: #c40052;
    cursor: pointer;
}

.upload-icon {
    font-size: 3em;
    color: #d6336c;
}

.upload-box p {
    color: #8c1b4d;
    font-size: 1em; 
}
.submit-container {
    text-align: right; /* Aligns content to the right */
    margin-top: 20px;
    padding-right: 20px; /* Optional: Adds spacing from the right edge */
}

.submit-button {
    background-color: #ff3366;
    color: #fff;
    font-size: 1em;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s ease;
}

.submit-button:hover {
    background: #c40052;
}

#preview-container {
    text-align: center;
    margin-top: 20px;
}

#previewImage {
    border: 2px solid #d6336c;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 500px; /* Set the maximum width */
    max-height: 450px; /* Set the maximum height */
    margin-top: 20px;
    object-fit: cover; /* Ensures the image scales properly within the dimensions */
    margin-left: 50px;
}

.dedicate-container { 
   
    position: relative;
    padding: 50px 103px;
    background: url('bg.jpg') no-repeat center center;
    background-size: cover; /* Make sure the image covers the entire container */
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    overflow: hidden;
    margin-bottom: 30px 
}

.dedicate-box {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: rgba(255, 255, 255, 0.78); /* Valentine's red */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.dedication-box h3 { 
    font-size: 1.5em;
}

h2 {
    font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: #ff3366;
    margin-bottom: 20px;
}

label, #songSearch {
    font-size: 1.2em;
    color: #333;
    margin-top: 10px;
}

#songSearch {
    width: 80%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
}

button {
    background: #d6336c;
    color: #fff;
    padding: 10px 20px;
    font-size: 1.1em;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #c40052;
}

.submit-container {
    margin-top: -10px;

 
}

.dedicate-box h4 {
    font-family: 'Dancing Script', cursive;
    font-size: 3em;
            color: #ff3366;
    margin-bottom: 20px;
}

        @media (max-width : 480px) {
            h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: #ff3366;
            margin-bottom: 20px;
        }
        #previewImage {
    border: 2px solid #d6336c;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 500px; /* Set the maximum width */
    max-height: 450px; /* Set the maximum height */
    margin-top: 20px;
    object-fit: cover; /* Ensures the image scales properly within the dimensions */
    margin-left:-7px
}
.upload-container h2 {
    font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: #ff3366;
    margin-bottom: 10px;
}
#dedicateBtn {
    padding: -50px 5px;
    background-color: #ff3366;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    margin-top: 20px;
    display: none;
}
.upload-container {
    position: relative; /* Ensure the pseudo-element is positioned correctly */
    background: url('bg.jpg') no-repeat center center;
    background-size: cover; /* Make sure the image covers the entire container */
    z-index: 1; /* Ensure the container is above the pseudo-element */
    padding: 30px; /* Add padding for content inside the container */
    border: 2px dashed #d6336c;
    width:300px;
}
.upload-box {
    border: 2px dashed #d6336c;
    border-radius: 10px;
    padding: 20px;
    width: 200px;
    margin: 0 auto;
    text-align: center;
    background: #fff;
    transition: 0.3s ease;
}
.upload-container p {
    color: #8c1b4d;
    margin-bottom: 20px;
    font-size: 0.6em;
}

        .button {
            padding: 10px 30px;
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
            top: 50%;
            left: 50%;
            animation: float 5s infinite ease-in-out;
            opacity: 0.5;
            transform: rotate(45deg);
            clip-path: polygon(50% 0%, 61% 16%, 100% 16%, 100% 37%, 50% 100%, 0 37%, 0% 16%, 39% 16%);
        }
        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(45deg);
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
        .hero {
            position: relative;
            z-index: 1;
            text-align: center;
            padding-top: 0%;
        }
        .hero-container {
            display: inline-block;
            color: #d6336c;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: url('bg.jpg') no-repeat center center;
            background-size: cover; /* Make sure the image covers the entire container */
        }
        .message-container {
            margin-bottom: 20px;
            
          
        }
        textarea {
            width: 100%;
            height: 200px;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
        }

        .message-container {
    width: 100%;
    height:200px;
    margin: 0 auto;
    position: relative;
    border: 2px dotted #ff3366; /* Pink dotted border */
    padding: 0; /* Remove internal padding from the container */
    border-radius: 10px;
}

textarea {
    width: 100%;
    height: 200px;
    border-radius: 10px;
    padding: 15px;
    font-size: 16px;
    resize: none;
    border: none; /* Remove default textarea border */
    box-sizing: border-box; /* Include padding in the element's total width/height */
}
        .send-button {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #ff3366;
            border: none;
            border-radius: 50%;
            width: 15px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }


.send-button i {
    color: white;
    font-size: 17px;
}

.dedicate-container { 
   
   position: relative;
   padding: 0px 0px;
   background: url('bg.jpg') no-repeat center center;
   background-size: cover; /* Make sure the image covers the entire container */
   border-radius: 10px;
   box-shadow: 0 4px 10px rgba(221, 10, 10, 0.88);
   text-align: center;
   overflow: hidden;
   margin-bottom: 30px 
   
}

.dedicate-box {
    border: 2px dotted #ff3366; /* Pink dotted border */
    max-width: 800px;
    margin: 50px 20px;
    padding: -30px -50px;
    background: rgba(255, 255, 255, 0.78); /* Valentine's red */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


.dedicate-box h2 {font-family: 'Dancing Script', cursive;
            font-size: 4em;
            color: #ff3366;
    margin-bottom: 20px;

}
.dedicate-box h4 {
    font-family: 'Dancing Script', cursive;
    font-size: 1.8em;
            color: #ff3366;
    margin-bottom: 20px;
}
/* Style for smaller font in the label */
.dedicate-box label {
    font-size: 12px; /* Adjust as needed */
    color: #555; /* Optional: lighter color for better readability */
}

/* Style for smaller placeholder text */
.dedicate-box input::placeholder {
    font-size: 12px; /* Adjust as needed */
    color: #888; /* Optional: make the placeholder slightly lighter */
}

/* Style for the input field to ensure readability */
.dedicate-box input {
    font-size: 14px; /* Keeps input text slightly larger for readability */
    padding: 8px; /* Optional: improve padding for a balanced look */
}

        }
    </style>
</head>
<body>
<div class="background-hearts">
        <div class="heart" style="left: 20%; animation-delay: 0s;"></div>
        <div class="heart" style="left: 40%; animation-delay: 2s;"></div>
        <div class="heart" style="left: 60%; animation-delay: 4s;"></div>
        <div class="heart" style="left: 80%; animation-delay: 6s;"></div>
    </div>
    <section class="hero" id="home">

    
        <div class="hero-container">
        <div class="container">
                <h1>Create a heartful message for <?php echo htmlspecialchars($partner); ?> ♥️</h1>
            </div>
            <div class="message-container">
                <textarea id="user-message" placeholder="Write your message here..."></textarea>
                <button class="send-button">
                    <i>&#10148;</i> <!-- Unicode for right arrow -->
                </button>
            </div>
        </div>
    </section>

  
    <section id="upload-picture">
    <div class="background-hearts">
        <div class="heart" style="left: 15%; animation-delay: 1s;"></div>
        <div class="heart" style="left: 35%; animation-delay: 3s;"></div>
        <div class="heart" style="left: 55%; animation-delay: 5s;"></div>
        <div class="heart" style="left: 75%; animation-delay: 7s;"></div>
    </div>
    <div class="upload-container">
        <h2>Upload a Picture for Your Valentine 💕</h2>
        <p>Make this moment special by sharing a cherished memory!</p>
        <div class="upload-box">
            <label for="uploadInput">
                <div class="upload-icon">+</div>
                <p>Click to upload</p>
            </label>
            <input type="file" id="uploadInput" accept="image/*" style="display: none;">
        </div>
        <!-- Preview Container -->
        <div id="preview-container">
            <img id="previewImage" src="" alt="Preview" style="display: none; max-width: 100%; margin-top: 20px;">
        </div>
    </div>
</section>



<section id="dedicate">
    <div class="dedicate-container">
        <div class="background-hearts">
            <div class="heart" style="left: 15%; animation-delay: 1s;"></div>
            <div class="heart" style="left: 35%; animation-delay: 3s;"></div>
            <div class="heart" style="left: 55%; animation-delay: 5s;"></div>
            <div class="heart" style="left: 75%; animation-delay: 7s;"></div>
        </div>
        
        <div class="dedicate-box">
            <h4>Dedicate a song</h4>
            <label for="songSearch">Click the image of the song:</label>
            <input type="text" id="songSearch" placeholder="Enter song or artist name">
            <button id="searchSongBtn">Search</button>
            <ul id="songResults"></ul> <!-- Song results will be displayed here -->
            <button id="dedicateBtn" style="display: none;">Dedicate Song</button>

            <!-- Submit Button -->
         
        </div>
    </div>
</section>

<div class="submit-container">
                <button id="submitBtn" class="submit-button">Submit</button>
            </div>
    <script>
    document.querySelector('.send-button').addEventListener('click', function() {
        const userMessage = document.getElementById('user-message').value;

        if (userMessage.trim() === "") {
            alert("Please write a message before submitting.");
            return;
        }

        // AJAX request to submit message to PHP script
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_message.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Message sent successfully!');
                document.getElementById('user-message').value = ''; // Clear the textarea
            } else {
                alert('Failed to send message. Please try again.');
            }
        };
        xhr.send('message=' + encodeURIComponent(userMessage));
    });

  
// Function to fetch the Spotify token from PHP
function getSpotifyToken() {
    return fetch('generate_token.php')  // Fetch the token from your PHP script
        .then(response => response.json())  // Parse the JSON response
        .then(data => {
            if (data.access_token) {
                return data.access_token;  // Return the token
            } else {
                throw new Error('Failed to retrieve access token');
            }
        });
}

// Function to fetch the Spotify token from PHP
function getSpotifyToken() {
    return fetch('generate_token.php')  // Fetch the token from your PHP script
        .then(response => response.json())  // Parse the JSON response
        .then(data => {
            if (data.access_token) {
                return data.access_token;  // Return the token
            } else {
                throw new Error('Failed to retrieve access token');
            }
        });
}

// Event listener for the "Search" button
document.getElementById('searchSongBtn').addEventListener('click', function() {
    const query = document.getElementById('songSearch').value;

    // Fetch the token before making the Spotify API request
    getSpotifyToken().then(token => {
        fetch(`https://api.spotify.com/v1/search?q=${query}&type=track&limit=5`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,  // Use the token here
            },
        })
        .then(response => response.json())  // Parse the response
        .then(data => {
            const results = data.tracks.items;
            const resultsList = document.getElementById('songResults');
            resultsList.innerHTML = '';  // Clear any previous results

            results.forEach(song => {
                const li = document.createElement('li');
                li.classList.add('song-item');
                li.setAttribute('data-song-id', song.id);  // Store the song ID
                const songImage = song.album.images[0].url;  // Image of the song
                const songTitle = song.name;  // Song title
                const songArtist = song.artists.map(artist => artist.name).join(', ');  // Artist name

                // Create song item content
                li.innerHTML = `
                    <img src="${songImage}" alt="${songTitle}" class="song-image">
                    <div class="song-info">
                        <p class="song-title">${songTitle}</p>
                        <p class="song-artist">${songArtist}</p>
                    </div>
                `;
                resultsList.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Error fetching song data:', error);
        });
    });
});

// Event listener for when a song image is clicked
document.getElementById('songResults').addEventListener('click', function(event) {
    // Check if an image was clicked
    if (event.target.tagName === 'IMG') {
        const li = event.target.closest('li');  // Find the li that contains the clicked image
        const songId = li.getAttribute('data-song-id');  // Get the song ID from the li element

        // Send the song ID to the backend (PHP) to save it in the database
        fetch('save_song.php', {
            method: 'POST',
            body: JSON.stringify({ songId: songId }),  // Send the song ID in the request
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Song dedicated successfully!');
            } else {
                alert(' ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error dedicating song:', error);
        });
    }
});

// Event listener for when a song is selected
document.getElementById('songResults').addEventListener('click', function(event) {
    if (event.target.tagName === 'LI') {
        document.querySelectorAll('#songResults li').forEach(li => li.classList.remove('selected'));
        event.target.classList.add('selected');
    }
});

// Event listener for the "Dedicate Song" button
document.getElementById('dedicateBtn').addEventListener('click', function() {
    const selectedSong = document.querySelector('#songResults li.selected');
    if (selectedSong) {
        const songId = selectedSong.getAttribute('data-song-id');
        
        // Send the song ID to the backend (PHP) to save it in the database
        fetch('save_song.php', {
            method: 'POST',
            body: JSON.stringify({ songId: songId }),  // Send the song ID in the request
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Song dedicated successfully!');
            } else {
                alert('Failed to dedicate song: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error dedicating song:', error);
        });
    }
});

document.getElementById('submitBtn').addEventListener('click', function () {
    const fileInput = document.getElementById('uploadInput');
    const file = fileInput.files[0];

    if (!file) {
        alert('Please upload a picture before submitting.');
        return;
    }

    const formData = new FormData();
    formData.append('uploadedPicture', file);

    fetch('upload_picture.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.text())
        .then(data => {
            alert('Picture uploaded successfully!');
            console.log(data);

            // Redirect to maps.php after successful upload
            window.location.href = 'maps.php'; // This will redirect the user
        })
        .catch(error => {
            console.error('Error uploading picture:', error);
            alert('An error occurred. Please try again.');
        });
});

    const uploadInput = document.getElementById('uploadInput');
    const previewImage = document.getElementById('previewImage');

    uploadInput.addEventListener('change', function () {
        const file = uploadInput.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            previewImage.style.display = 'none';
        }
    });


</script>
</body>
</html>
