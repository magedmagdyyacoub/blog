<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: manage_categories.php");
exit();
