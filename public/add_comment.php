<?php
require '../config/db.php';

session_start();

if (!isset($_SESSION['user'])) {
    die("<div class='container mt-5'><div class='alert alert-danger'>You must be logged in to add a comment.</div></div>");
}

$user = $_SESSION['user'];
$post_id = $_POST['post_id'] ?? null;
$parent_id = $_POST['parent_id'] ?? null;
$content = trim($_POST['content']);

if (!$post_id || empty($content)) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid comment. Please provide valid content.</div></div>");
}

try {
    $comment_stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_id) 
                                   VALUES (:post_id, :user_id, :content, :parent_id)");
    $comment_stmt->execute([
        'post_id' => $post_id,
        'user_id' => $user['id'],
        'content' => $content,
        'parent_id' => $parent_id
    ]);

    header("Location: post.php?id=$post_id");
    exit();
} catch (PDOException $e) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Error adding comment: " . htmlspecialchars($e->getMessage()) . "</div></div>");
}
?>
