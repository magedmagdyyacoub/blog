<?php
session_start();
require '../config/db.php';
require '../vendor/autoload.php';

$client = new Google\Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/blog/public/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_service = new Google\Service\Oauth2($client);
    $google_user = $google_service->userinfo->get();

    // Check if user exists in DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$google_user->email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Register new Google user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, 'user')");
        $stmt->execute([$google_user->name, $google_user->email]);

        $user = [
            'id' => $pdo->lastInsertId(),
            'name' => $google_user->name,
            'email' => $google_user->email,
            'role' => 'user'
        ];
    }

    $_SESSION['user'] = $user;
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
