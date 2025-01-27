
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
    <title>Set a Course</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-search/dist/leaflet-search.min.css">
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
        
        h2 {
            font-family: 'Dancing Script', cursive;
            font-size: 3em;
            color: #ff3366;
            margin-bottom: 20px;
        }

        .maps-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-image: url('bg.jpg'); /* Replace with your desired background image */
            background-size: cover;
            background-position: center;
        }

        .maps-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #ff3366;
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
            display: block;
            width: 100%;
            text-align: center;
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
        .top-right-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
        }
        .top-right-buttons .button {
    padding: 8px 16px;
    font-size: 1rem;
    border-radius: 20px;
}

#map {
            height: 400px;
            width: 100%;
        }
        body {
            font-family: Arial, sans-serif;

            
        }
        @media (max-width : 480px) {

            .button {
            padding: 8px 20px;
            font-size: 0.9rem;
            color: white;
            background: linear-gradient(to bottom right, #ff6699, #ff3366);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            outline: none;
            margin: 5px;
            display: block;
            width: 100%;
            text-align: center;
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
           
        h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 1.5rem;
            color: #ff3366;
            margin-bottom: 20px;
        }
        #map {
    height: 500px; /* Increased height for the map */
    width: 100%;
    border-radius: 8px;
}

.maps-container {
    width: 100%;
    max-width: 400px;
    height: 700px; /* Increased height for the container */
    margin: -70px auto 0 auto; /* Adjusted margin-top to move it slightly up */
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-image: url('bg.jpg'); /* Replace with your desired background image */
    background-size: cover;
    background-position: center;
}

.maps-container h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    text-align: center;
    color: #ff3366;
}


        .button {
            padding: 15px 23px;
            font-size: 1.4rem;
            color: white;
            background: linear-gradient(to bottom right, #ff6699, #ff3366);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            outline: none;
            margin: 5px;
            margin-top:15px;
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
        .top-right-buttons {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
}

.top-right-buttons .button {
    padding: 8px 16px;
    font-size: 1rem;
    border-radius: 20px;
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
    <div class="maps-container">
    <h2> Set a location to date with <?php echo htmlspecialchars($partner); ?> ♥️ </h2>
    <div id="map"></div>
    <form id="locationForm">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <button type="submit" class="button">Save Location</button>
    </form>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-search/dist/leaflet-search.min.js"></script>
<script>
  // Replace 'YOUR_FOURSQUARE_API_KEY' with your actual API Key
  const foursquareApiKey = 'fsq3rXA1Qodx+y9DKTvnSVr3PxvkluKEK1WvZogDOYzYVF4=';

// Initialize the map centered on Metro Manila
var map = L.map('map').setView([14.5995, 120.9842], 13);  // Metro Manila coordinates

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);

var marker;

// Function to fetch recommendations from Foursquare
function fetchRecommendations(lat, lng) {
    const url = `https://api.foursquare.com/v3/places/nearby?ll=${lat},${lng}&categories=13065&radius=1000`;

    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': foursquareApiKey,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Foursquare Data:', data);
        if (data.results && data.results.length > 0) {
            addRecommendationsToMap(data.results);
        } else {
            alert('No recommendations found nearby.');
        }
    })
    .catch(error => console.error('Error fetching recommendations:', error));
}

// Add fetched recommendations to the map
function addRecommendationsToMap(places) {
    places.forEach(place => {
        // Create a custom red marker
        var redIcon = new L.Icon({
            iconUrl: 'redpin.png', // Red marker icon
            iconSize: [20, 40],
            iconAnchor: [14, 21],
            popupAnchor: [0, -41] // Adjust the popup bubble position
        });

        // Add the marker with the custom icon
        const venueMarker = L.marker([place.geocodes.main.latitude, place.geocodes.main.longitude], { icon: redIcon })
            .addTo(map)
            .bindPopup(`<b>${place.name}</b><br>${place.location.address || 'No address available'}`)
            .openPopup(); // Open popup immediately

        // Add a custom bubble at the top of the pin (restaurant name)
        venueMarker.on('mouseover', function () {
            venueMarker.setPopupContent(`<b>${place.name}</b>`);
            venueMarker.openPopup();
        });

        venueMarker.on('mouseout', function () {
            venueMarker.closePopup();
        });
    });
}

// Handle map click event to pin location and fetch recommendations
map.on('click', function(e) {
    if (marker) {
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;

    // Fetch recommendations near the selected location
    fetchRecommendations(e.latlng.lat, e.latlng.lng);
});
// Handle map click event to pin location and fetch recommendations
map.on('click', function(e) {
    // If marker exists, update its position, else add a new marker
    if (marker) {
        marker.setLatLng(e.latlng);
    } else {
        marker = L.marker(e.latlng).addTo(map);
    }
    
    // Set the latitude and longitude in the hidden form fields
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;

    // Fetch recommendations near the selected location
    fetchRecommendations(e.latlng.lat, e.latlng.lng);
});

// Handle form submission to save coordinates
document.getElementById('locationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;

// Save the coordinates via an API or server call
fetch('save_coordinates.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({ latitude: latitude, longitude: longitude }),
})
    .then(response => response.json())
    .then(data => {
        alert('Location saved successfully!');
        // Redirect to certification.php
        window.location.href = 'certification.php';
    })
    .catch((error) => {
        alert('Error saving location!');
        console.error('Error:', error);
    });

});

// Add search functionality to the map
var searchControl = new L.Control.Search({
    url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
    jsonpParam: 'json_callback',
    propertyName: 'display_name',
    propertyLoc: ['lat', 'lon'],
    marker: false,
    moveToLocation: function(latlng, title, map) {
        map.setView(latlng, 13);
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng).addTo(map);
        }
        document.getElementById('latitude').value = latlng.lat;
        document.getElementById('longitude').value = latlng.lng;

        // Fetch recommendations for the searched location
        fetchRecommendations(latlng.lat, latlng.lng);
    }
});

map.addControl(searchControl);

</script>
</body>
</html>
