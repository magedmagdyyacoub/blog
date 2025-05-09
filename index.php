<?php
require 'config/db.php';
?>

<?php include 'includes/header.php'; ?>

<main class="container my-4">

  <?php
  // Fetch categories + post counts
  $categories = $pdo->query("
      SELECT categories.*, COUNT(posts.id) AS post_count 
      FROM categories 
      LEFT JOIN posts ON posts.category_id = categories.id 
      GROUP BY categories.id
  ")->fetchAll();
  ?>

  <!-- Categories Section -->
  <h3 class="fw-bold text-center mt-2">Categories</h3>
  <div class="row justify-content-center">
    <?php foreach ($categories as $cat): ?>
      <div class="col-md-2 col-sm-4">
        <a href="/blog/public/category.php?id=<?= $cat['id'] ?>" class="btn btn-outline-warning w-100 mb-2 small-btn d-flex justify-content-between">
          <?= htmlspecialchars($cat['name']) ?>
          <span class="badge bg-dark"><?= $cat['post_count'] ?></span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
  // Fetch recent posts with author names
  $posts = $pdo->query("SELECT posts.*, users.name AS author FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC LIMIT 6")->fetchAll();
  ?>

  <!-- Latest Posts Section -->
  <h3 class="fw-bold text-center mt-2">Latest Posts</h3>
  <div class="row">
    <?php foreach ($posts as $post): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm position-relative">
          <!-- Post Image -->
          <div class="image-wrapper position-relative">
            <img src="/blog/public/uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($post['title']) ?>">

            <!-- Author Name Overlay -->
            <div class="position-absolute text-light bg-dark bg-opacity-75 p-1 rounded" style="bottom: 10px; left: 10px; font-size: 14px;">
              <small><?= htmlspecialchars($post['author']) ?></small>
            </div>
          </div>

          <!-- Post Title & Read More Button -->
          <div class="card-body text-center">
            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
            <a href="/blog/public/post.php?id=<?= $post['id'] ?>" class="btn btn-warning btn-sm">Read More</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
