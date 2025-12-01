<?php
require __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$secret = $_ENV['JWT_SECRET'];

// Ambil state token
if (!isset($_COOKIE['oauth_state_token'])) {
    die("Invalid session token");
}

$jwt = $_COOKIE['oauth_state_token'];

try {
    $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
} catch (Exception $e) {
    die("Invalid or expired state");
}

$storedState = $decoded->state;

// Validasi state Google
if (!isset($_GET['state']) || $_GET['state'] !== $storedState) {
    die('Invalid OAuth state');
}

// Mendapatkan Access Token
$tokenResponse = file_get_contents('https://oauth2.googleapis.com/token?' . http_build_query([
    'code' => $_GET['code'],
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
    'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
    'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'],
    'grant_type' => 'authorization_code'
]));

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    die("Failed to get token");
}

// Ambil informasi user Google
$userInfoRaw = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=$accessToken");
$userInfo = json_decode($userInfoRaw, true);

// Buat JWT untuk login user
$userPayload = [
    'sub' => $userInfo['id'],
    'email' => $userInfo['email'],
    'name' => $userInfo['name'],
    'picture' => $userInfo['picture'],
    'exp' => time() + 86400 // expires 1 hari
];

$userJwt = JWT::encode($userPayload, $secret, 'HS256');

// Simpan ke cookie HttpOnly
setcookie("auth_token", $userJwt, [
    'expires' => time() + 86400,
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'path' => '/',
    'samesite' => 'Lax'
]);

// Hapus oauth_state
setcookie("oauth_state_token", "", time() - 3600, "/");

// Redirect
header("Location: index.php");
exit;
