<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getAuthenticatedUser() {
    if (!isset($_COOKIE['auth_token'])) return null;

    try {
        $secret = $_ENV['JWT_SECRET'];
        return JWT::decode($_COOKIE['auth_token'], new Key($secret, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}
