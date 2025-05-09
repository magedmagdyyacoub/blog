<?php
require '../config/db.php';
$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid post.</div></div>");
}

// Fetch post details
$post_stmt = $pdo->prepare("SELECT posts.*, users.name AS author FROM posts 
                            JOIN users ON posts.user_id = users.id 
                            WHERE posts.id = :post_id");
$post_stmt->execute(['post_id' => $post_id]);
$post = $post_stmt->fetch();

if (!$post) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Post not found.</div></div>");
}

// Fetch comments
$comments_stmt = $pdo->prepare("SELECT comments.*, users.name AS commenter FROM comments 
                                JOIN users ON comments.user_id = users.id 
                                WHERE post_id = :post_id 
                                ORDER BY comments.created_at DESC");
$comments_stmt->execute(['post_id' => $post_id]);
$comments = $comments_stmt->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container my-5">
    <div class="card shadow-sm">
        <img src="/blog/public/uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($post['title']) ?>">
        <div class="card-body">
            <h2 class="fw-bold"><?= htmlspecialchars($post['title']) ?></h2>
            <p class="text-muted">By <?= htmlspecialchars($post['author']) ?> | <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
            <p class="mt-3"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </div>

    <!-- Comments Section -->
    <h3 class="fw-bold mt-5">Comments</h3>
    <?php foreach ($comments as $comment): ?>
        <div class="p-3 border rounded mb-2">
            <strong><?= htmlspecialchars($comment['commenter']) ?></strong> <small class="text-muted"><?= date('F j, Y', strtotime($comment['created_at'])) ?></small>
            <p class="mt-2"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
        </div>
    <?php endforeach; ?>

    <!-- Add Comment Form -->
    <h4 class="fw-bold mt-4">Add a Comment</h4>
    <form method="POST" action="add_comment.php">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <textarea name="content" class="form-control" rows="3" required></textarea>
        <button type="submit" class="btn btn-success mt-3">Submit Comment</button>
    </form>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
