<?php
// Define your Spotify API credentials
$client_id = '639c6624460c4f6691036e78e4eddbdd';  // Replace with your actual client ID
$client_secret = '6603ff1c99224795b0e442177e621479';  // Replace with your actual client secret
$redirect_uri = 'http://sharinglove.free.nf/plans.php';  // Replace with your redirect URI

// Define the scopes you need
$scope = 'streaming user-read-email';  // The necessary scopes

// Create the Spotify authorization URL
$authorize_url = 'https://accounts.spotify.com/authorize?' . http_build_query([
    'response_type' => 'code',
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => $scope,
]);

// Redirect the user to the authorization page
header('Location: ' . $authorize_url);
exit();
?>
