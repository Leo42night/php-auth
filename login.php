<?php
require __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$secret = $_ENV['JWT_SECRET'];

// Generate random state
$state = bin2hex(random_bytes(16));

// Payload JWT state
$payload = [
    'state' => $state,
    'exp' => time() + 300
];

// Encode JWT
$jwt = JWT::encode($payload, $secret, 'HS256');

// Simpan cookie HttpOnly
setcookie("oauth_state_token", $jwt, [
    'expires' => time() + 300,
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'path' => '/',
    'samesite' => 'Lax'
]);

// Redirect ke Google OAuth
$googleAuthUrl = "https://accounts.google.com/o/oauth2/auth?" . http_build_query([
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
    'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'],
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state
]);

header("Location: $googleAuthUrl");
exit;
