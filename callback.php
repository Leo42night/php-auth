<?php
session_start();

// Konfigurasi
$clientId = getenv('GOOGLE_CLIENT_ID') ?: "";
$clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: "";
$redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8080/callback.php';

// Validasi state
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Error: Invalid state parameter');
}

// Validasi code
if (!isset($_GET['code'])) {
    die('Error: No authorization code received');
}

$code = $_GET['code'];

// Tukar code dengan access token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$tokenData = [
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die('Error: Failed to get access token');
}

$tokenResponse = json_decode($response, true);
$accessToken = $tokenResponse['access_token'] ?? null;

if (!$accessToken) {
    die('Error: No access token in response');
}

// Ambil informasi user dari Google
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);

$userInfoResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die('Error: Failed to get user info');
}

$userInfo = json_decode($userInfoResponse, true);

// Simpan informasi user ke session
$_SESSION['user'] = [
    'id' => $userInfo['id'] ?? '',
    'email' => $userInfo['email'] ?? '',
    'name' => $userInfo['name'] ?? '',
    'picture' => $userInfo['picture'] ?? '',
    'verified_email' => $userInfo['verified_email'] ?? false
];

// Hapus state dari session
unset($_SESSION['oauth_state']);

// Redirect ke halaman utama
header('Location: index.php');
exit;
?>