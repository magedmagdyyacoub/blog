<?php
require '../config/db.php';
$category_id = $_GET['id'] ?? null;

if (!$category_id) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid category.</div></div>");
}

// Fetch category name
$category_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
$category_stmt->execute(['id' => $category_id]);
$category = $category_stmt->fetch();

if (!$category) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Category not found.</div></div>");
}

// Fetch posts under this category
$posts = $pdo->prepare("SELECT posts.*, users.name AS author FROM posts 
                         JOIN users ON posts.user_id = users.id 
                         WHERE category_id = :category_id 
                         ORDER BY posts.created_at DESC");
$posts->execute(['category_id' => $category_id]);
$posts = $posts->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container my-5">
    <h2 class="fw-bold text-center">Category: <?= htmlspecialchars($category['name']) ?></h2>
    <div class="row">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-4">
                <div class="card shadow-sm position-relative">
                    <img src="/blog/public/uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($post['title']) ?>">
                    <div class="position-absolute text-light bg-dark bg-opacity-75 p-1 rounded" style="bottom: 10px; left: 10px;">
                        <small><?= htmlspecialchars($post['author']) ?></small>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                        <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-warning btn-sm">Read More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
