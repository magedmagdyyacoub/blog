<?php
require '../includes/auth.php';
require '../config/db.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Manage Categories</h2>
    
    <div class="text-center mb-3">
        <a href="add_category.php" class="btn btn-success">Add New Category</a>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered">
            <thead class="table-warning">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['id']) ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <a href="edit_category.php?id=<?= htmlspecialchars($cat['id']) ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_category.php?id=<?= htmlspecialchars($cat['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
