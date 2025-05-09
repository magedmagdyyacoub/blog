<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
$id = $_GET['id'] ?? null;

if (!$id) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid post ID.</div></div>");
}

// Fetch post details
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Post not found.</div></div>");
}

// Check ownership or superadmin access
if ($user['role'] !== 'superadmin' && $user['id'] !== $post['user_id']) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Access denied.</div></div>");
}

// Delete post action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: dashboard.php?message=Post deleted successfully.");
        exit();
    } catch (PDOException $e) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div></div>");
    }
}

?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Delete Post</h2>
    <div class="bg-dark text-light p-4 rounded shadow text-center">
        <p class="fs-5">Are you sure you want to delete <strong><?= htmlspecialchars($post['title']) ?></strong>?</p>
        <form method="POST">
            <button type="submit" class="btn btn-danger w-50">Yes, Delete</button>
            <a href="dashboard.php" class="btn btn-secondary w-50">Cancel</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
