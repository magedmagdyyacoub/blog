<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'] ?? null;
$message = '';

if (!$id) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid category ID.</div></div>");
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
$stmt->execute(['id' => $id]);
$category = $stmt->fetch();

if (!$category) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Category not found.</div></div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        try {
            $stmt->execute(['name' => $name, 'id' => $id]);
            $message = "<div class='alert alert-success mt-3'>Category updated successfully.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger mt-3'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning mt-3'>Category name cannot be empty.</div>";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center fw-bold">Edit Category</h2>

            <?= $message ?>

            <form method="POST" class="bg-dark text-light p-4 rounded shadow">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
                </div>

                <button type="submit" class="btn btn-warning w-100">Update Category</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
