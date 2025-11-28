<?php
session_start();

// Konfigurasi Google OAuth
$clientId = getenv('GOOGLE_CLIENT_ID') ?: "690018681390-b871npco41agqt652a2vp8a2jg7u01kp.apps.googleusercontent.com";
$redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8080/callback.php';

// Generate state untuk security
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Parameter untuk Google OAuth
$params = [
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
    'access_type' => 'online',
    'prompt' => 'select_account'
];

// Redirect ke Google OAuth
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $authUrl);
exit;
?>