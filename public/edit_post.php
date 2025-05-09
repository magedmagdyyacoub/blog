<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
$id = $_GET['id'] ?? null;
$message = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category'];
    $content = trim($_POST['content']);
    $new_image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($new_image);

    if (!empty($title) && !empty($category_id) && !empty($content)) {
        // Update query
        $query = "UPDATE posts SET title = :title, category_id = :category_id, content = :content";
        
        if ($new_image && move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $query .= ", image = :image";
        }
        
        $query .= " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        
        try {
            $params = ['title' => $title, 'category_id' => $category_id, 'content' => $content, 'id' => $id];
            if ($new_image) {
                $params['image'] = $new_image;
            }
            $stmt->execute($params);
            $message = "<div class='alert alert-success'>Post updated successfully.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>All fields are required.</div>";
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Edit Post</h2>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data" class="bg-dark text-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="title" class="form-label">Post Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Upload New Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control">
            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" class="img-thumbnail mt-2" width="100">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Post Content</label>
            <textarea name="content" id="content" class="form-control" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">Update Post</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
