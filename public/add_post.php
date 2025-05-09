<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'admin') {
    die("<div class='container mt-5'><div class='alert alert-danger'>Access denied.</div></div>");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category'];
    $content = trim($_POST['content']);
    $image = $_FILES['image']['name'];
    $target = __DIR__ . "/../public/uploads/" . basename($image);


    if (!empty($title) && !empty($category_id) && !empty($content) && move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, category_id, image, content, user_id) VALUES (:title, :category_id, :image, :content, :user_id)");
        try {
            $stmt->execute([
                'title' => $title,
                'category_id' => $category_id,
                'image' => $image,
                'content' => $content,
                'user_id' => $user['id']
            ]);
            $message = "<div class='alert alert-success'>Post added successfully.</div>";
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
    <h2 class="fw-bold text-center">Add New Post</h2>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data" class="bg-dark text-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="title" class="form-label">Post Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Post Content</label>
            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">Add Post</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
