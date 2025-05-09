<?php
require '../includes/auth.php';
require '../config/db.php';

session_start();
$user = $_SESSION['user'];

if (!$user) {
    die("<div class='container mt-5'><div class='alert alert-danger'>You must be logged in to access the dashboard.</div></div>");
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-light shadow">
                <div class="card-body text-center">
                    <h1 class="fw-bold">Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
                    <p class="badge bg-warning text-dark fs-5">Role: <?= htmlspecialchars($user['role']) ?></p>

                    <!-- Admin and Superadmin Controls -->
                    <?php if ($user['role'] === 'superadmin'): ?>
                        <div class="mt-4">
                            <a href="change_role.php" class="btn btn-primary mb-2">Change User Roles</a><br>
                            <a href="manage_categories.php" class="btn btn-secondary">Manage Categories</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($user['role'] === 'admin' || $user['role'] === 'superadmin'): ?>
                        <div class="mt-4">
                            <a href="manage_posts.php" class="btn btn-secondary">Manage Posts</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Show all posts if the user role is "user" -->
    <?php if ($user['role'] === 'user'): ?>
        <h2 class="fw-bold mt-5">All Posts</h2>

        <?php
        $posts_stmt = $pdo->query("SELECT posts.*, users.name AS author FROM posts 
                                   JOIN users ON posts.user_id = users.id 
                                   ORDER BY posts.created_at DESC");
        $posts = $posts_stmt->fetchAll();

        foreach ($posts as $post):
        ?>
            <div class="card post-card my-4">

                <img src="/blog/public/uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top img-fluid w-25" alt="<?= htmlspecialchars($post['title']) ?>">
                <div class="card-body">
                    <h3 class="fw-bold"><?= htmlspecialchars($post['title']) ?></h3>
                    <p class="text-muted">By <?= htmlspecialchars($post['author']) ?> | <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                </div>
            </div>

            <!-- Comments Section -->
            <h4 class="fw-bold mt-3">Comments</h4>

            <?php
            $comments_stmt = $pdo->prepare("SELECT comments.*, users.name AS commenter FROM comments 
                                            JOIN users ON comments.user_id = users.id 
                                            WHERE post_id = :post_id AND parent_id IS NULL
                                            ORDER BY comments.created_at DESC");
            $comments_stmt->execute(['post_id' => $post['id']]);
            $comments = $comments_stmt->fetchAll();

            foreach ($comments as $comment):
            ?>
                <div class="p-3 border rounded mb-2">
                    <strong><?= htmlspecialchars($comment['commenter']) ?></strong> <small class="text-muted"><?= date('F j, Y', strtotime($comment['created_at'])) ?></small>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                    <!-- Reply Form -->
                    <form method="POST" action="add_comment.php">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                        <textarea name="content" class="form-control" rows="2" required></textarea>
                        <button type="submit" class="btn btn-info btn-sm mt-2">Reply</button>
                    </form>

                    <!-- Display Replies -->
                    <?php
                    $replies_stmt = $pdo->prepare("SELECT comments.*, users.name AS commenter FROM comments 
                                                   JOIN users ON comments.user_id = users.id 
                                                   WHERE parent_id = :parent_id 
                                                   ORDER BY comments.created_at ASC");
                    $replies_stmt->execute(['parent_id' => $comment['id']]);
                    $replies = $replies_stmt->fetchAll();

                    foreach ($replies as $reply):
                    ?>
                        <div class="ms-4 p-2 border rounded">
                            <strong><?= htmlspecialchars($reply['commenter']) ?></strong> 
                            <small class="text-muted"><?= date('F j, Y', strtotime($reply['created_at'])) ?></small>
                            <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Comment Form -->
            <h4 class="fw-bold mt-4">Add a Comment</h4>
            <form method="POST" action="add_comment.php">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="content" class="form-control" rows="3" required></textarea>
                <button type="submit" class="btn btn-success mt-3">Submit Comment</button>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
