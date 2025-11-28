<?php
session_save_path('/tmp');
session_start();

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Konfigurasi Google OAuth
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?: "xxxNOENVxxx");
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?: "xxxNOENVxxx");
define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8080/callback.php');

// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google OAuth App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .user-info {
            background: #f7f7f7;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 4px solid #667eea;
        }
        .user-name {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .user-email {
            color: #666;
            font-size: 0.9em;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 10px 5px;
        }
        .btn-google {
            background: #4285f4;
            color: white;
        }
        .btn-google:hover {
            background: #357ae8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(66, 133, 244, 0.3);
        }
        .btn-logout {
            background: #dc3545;
            color: white;
        }
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }
        .welcome-text {
            color: #667eea;
            font-size: 1.1em;
            margin: 20px 0;
        }
        .google-icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 OAuth App</h1>
        <p class="subtitle">Aplikasi dengan Google OAuth</p>

        <?php if ($isLoggedIn): ?>
            <div class="user-info">
                <?php if (isset($user['picture'])): ?>
                    <img src="<?= htmlspecialchars($user['picture']) ?>" alt="Avatar" class="avatar">
                <?php endif; ?>
                <div class="user-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></div>
                <div class="user-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <p class="welcome-text">Selamat datang! Anda berhasil login.</p>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        <?php else: ?>
            <p class="welcome-text">Silakan login dengan akun Google Anda</p>
            <a href="login.php" class="btn btn-google">
                <svg class="google-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Login dengan Google
            </a>
        <?php endif; ?>
    </div>
</body>
</html>