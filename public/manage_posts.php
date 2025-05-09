<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];

// Ensure only admins or superadmins can manage posts
if ($user['role'] !== 'admin' && $user['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

// Fetch all posts with category and author information
$posts = $pdo->query("SELECT posts.*, categories.name AS category_name, users.name AS author 
                      FROM posts 
                      JOIN categories ON posts.category_id = categories.id 
                      JOIN users ON posts.user_id = users.id 
                      ORDER BY posts.created_at DESC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Manage Posts</h2>

    <?php if ($user['role'] === 'admin' || $user['role'] === 'superadmin'): ?>
        <div class="text-center mb-3">
            <a href="add_post.php" class="btn btn-success">Add New Post</a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered">
            <thead class="table-warning">
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars($post['category_name']) ?></td>
                        <td><?= htmlspecialchars($post['author']) ?></td>
                        <td><img src="/blog/public/uploads/<?= htmlspecialchars($post['image']) ?>" width="50">
                        </td>
                        <td>
                            <?php if ($user['role'] === 'superadmin' || $user['id'] === $post['user_id']): ?>
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
