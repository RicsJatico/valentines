<?php
// Get the authorization code from the query string
$code = $_GET['code'];

// Define your Spotify API credentials
$client_id = '639c6624460c4f6691036e78e4eddbdd';  // Replace with your actual client ID
$client_secret = '6603ff1c99224795b0e442177e621479';  // Replace with your actual client secret
$redirect_uri = 'http://sharinglove.free.nf/plans.php';  // Replace with your redirect URI

// Prepare the POST request to exchange the authorization code for an access token
$token_url = 'https://accounts.spotify.com/api/token';
$data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

// Make the POST request to Spotify API
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data),
    ],
];
$context = stream_context_create($options);
$response = file_get_contents($token_url, false, $context);

// Parse the response to get the access token
$response_data = json_decode($response, true);
$access_token = $response_data['access_token'];  // Store the access token

// Now you can use the access token to authenticate API calls, including Web Playback SDK

echo 'Access Token: ' . $access_token;
?>
