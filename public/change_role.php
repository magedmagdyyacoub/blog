<?php
require '../includes/auth.php';
require '../config/db.php';
include '../includes/header.php';

// Ensure only superadmin can access
if ($_SESSION['user']['role'] !== 'superadmin') {
    die("<div class='container mt-5'><div class='alert alert-danger'>Access denied.</div></div>");
}

// Handle role change
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = $_POST['role'];
    $user_id = $_POST['user_id'];

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    $message = "<div class='alert alert-success mt-3'>Role updated successfully!</div>";
}

// Fetch users (excluding current logged-in user)
$users = $pdo->query("SELECT id, name, role FROM users WHERE id != {$_SESSION['user']['id']}")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Change User Roles</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?= $message ?>
            <form method="POST" class="p-4 bg-dark text-light rounded shadow">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Select New Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning w-100">Change Role</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
