<?php
session_start();
require '../config/database.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$page_title = "Admin Dashboard";
include '../includes/admin_header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <a href="admin_logout.php" class="btn btn-danger">Logout</a>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                    $count = $stmt->fetchColumn();
                    ?>
                    <h2 class="card-text"><?= $count ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Posts</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
                    $count = $stmt->fetchColumn();
                    ?>
                    <h2 class="card-text"><?= $count ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Admins</h5>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = TRUE");
                    $count = $stmt->fetchColumn();
                    ?>
                    <h2 class="card-text"><?= $count ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Recent Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");
                        while ($user = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="admin_edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="admin_delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>