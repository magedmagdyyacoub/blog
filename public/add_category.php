<?php
require '../includes/auth.php';

$user = $_SESSION['user'];
if ($user['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../config/db.php';
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        try {
            $stmt->execute(['name' => $name]);
            $message = "<div class='alert alert-success'>Category added successfully.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Category name cannot be empty.</div>";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center fw-bold">Add New Category</h2>

            <?= $message ?>

            <form method="POST" class="bg-dark text-light p-4 rounded shadow">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Category Name" required>
                </div>

                <button type="submit" class="btn btn-warning w-100">Add Category</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
