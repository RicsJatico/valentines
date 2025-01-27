<?php
function getSpotifyAccessToken($clientId, $clientSecret) {

    $url = 'https://accounts.spotify.com/api/token';
    
    $headers = [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $fields = 'grant_type=client_credentials';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
        curl_close($ch);
        return;
    }

    curl_close($ch);
    
    $result = json_decode($response, true);

    if (isset($result['access_token'])) {
        echo json_encode(['access_token' => $result['access_token']]);
    } else {
        echo json_encode(['error' => 'Failed to retrieve access token']);
    }
}

$clientId = getenv('SPOTIFY_CLIENT_ID') ?: '639c6624460c4f6691036e78e4eddbdd';
$clientSecret = getenv('SPOTIFY_CLIENT_SECRET') ?: '6603ff1c99224795b0e442177e621479';
getSpotifyAccessToken($clientId, $clientSecret);
