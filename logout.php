<?php

// Hapus cookie auth_token
setcookie(
    "auth_token",
    "",
    [
        'expires' => time() - 3600,
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'path' => '/',
        'samesite' => 'Lax'
    ]
);

// Hapus cookie oauth_state_token apabila masih ada
setcookie(
    "oauth_state_token",
    "",
    [
        'expires' => time() - 3600,
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'path' => '/',
        'samesite' => 'Lax'
    ]
);

// Redirect ke halaman utama atau login
header("Location: index.php");
exit;
