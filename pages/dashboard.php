<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Check if user is admin (you'll need to add an 'is_admin' column to users table)
$is_admin = false; // Set this based on your authentication logic

$page_title = "Dashboard";
include __DIR__ . '/../includes/header.php';

// Get statistics data
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$stats['total_users'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_posts FROM posts");
$stats['total_posts'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as active_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['active_users'] = $stmt->fetchColumn();

// Get posts per day data for chart
$posts_per_day = [];
$stmt = $pdo->query("
    SELECT DATE(created_at) as day, COUNT(*) as count 
    FROM posts 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY day 
    ORDER BY day
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $posts_per_day[] = $row;
}

// Get popular hashtags
$popular_hashtags = [];
$stmt = $pdo->query("
    SELECT hashtags 
    FROM posts 
    WHERE hashtags IS NOT NULL
");
$all_hashtags = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $tags = explode(',', $row['hashtags']);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $all_hashtags[] = $tag;
        }
    }
}
$tag_counts = array_count_values($all_hashtags);
arsort($tag_counts);
$popular_hashtags = array_slice($tag_counts, 0, 10, true);

// Get user registration growth
$user_growth = [];
$stmt = $pdo->query("
    SELECT DATE(created_at) as day, COUNT(*) as count 
    FROM users 
    GROUP BY day 
    ORDER BY day
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_growth[] = $row;
}
?>

<div class="container mt-4 d-flex flex-column">
    <h1 class="mb-4">Glimmr Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="mb-0"><?= number_format($stats['total_users']) ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Posts</h5>
                            <h2 class="mb-0"><?= number_format($stats['total_posts']) ?></h2>
                        </div>
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Active Users (30d)</h5>
                            <h2 class="mb-0"><?= number_format($stats['active_users']) ?></h2>
                        </div>
                        <i class="fas fa-user-clock fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Posts Per Day (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="postsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>User Registration Growth</h5>
                </div>
                <div class="card-body">
                    <canvas id="usersChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Data -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Top 10 Hashtags</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Hashtag</th>
                                    <th>Usage Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular_hashtags as $tag => $count): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($tag) ?></td>
                                    <td><?= $count ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("
                        SELECT p.content, p.created_at, u.username 
                        FROM posts p
                        JOIN users u ON p.user_id = u.id
                        ORDER BY p.created_at DESC
                        LIMIT 5
                    ");
                    while ($post = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($post['username']) ?></strong>
                            <small class="text-muted"><?= date('M j, g:i a', strtotime($post['created_at'])) ?></small>
                        </div>
                        <p class="mb-0"><?= substr(htmlspecialchars($post['content']), 0, 100) ?>...</p>
                    </div>
                    <hr>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Posts Per Day Chart
const postsCtx = document.getElementById('postsChart').getContext('2d');
const postsChart = new Chart(postsCtx, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . date('M j', strtotime($item['day'])) . "'"; }, $posts_per_day)) ?>],
        datasets: [{
            label: 'Posts',
            data: [<?= implode(',', array_column($posts_per_day, 'count')) ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// User Growth Chart
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersChart = new Chart(usersCtx, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . date('M j', strtotime($item['day'])) . "'"; }, $user_growth)) ?>],
        datasets: [{
            label: 'User Registrations',
            data: [<?= implode(',', array_column($user_growth, 'count')) ?>],
            fill: true,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>