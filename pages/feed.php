<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../functions/post_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post_id'])) {
        $post_id = intval($_POST['delete_post_id']);
        if (deletePost($pdo, $post_id, $_SESSION['user_id'])) {
            header("Location: ../pages/feed.php?deleted=1");
            exit();
        } else {
            header("Location: ../pages/feed.php?deleted=0");
            exit();
        }
    } else {
        $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
        $hashtags = extractHashtags($content);
        
        $media_path = null;
        if (!empty($_FILES['media']['name'])) {
            $upload_dir = __DIR__ . '/../assets/uploads/posts/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
            $file_name = 'post_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
            $media_path = '/glimmr/assets/uploads/posts/' . $file_name;

            move_uploaded_file($_FILES['media']['tmp_name'], __DIR__ . '/..' . $media_path);
        }
        
        if (!empty($content)) {
            createPost($pdo, $_SESSION['user_id'], $content, $media_path, $hashtags);
        }
        
        header("Location: ../pages/feed.php");
        exit();
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$posts = $search ? searchPosts($pdo, $search) : getAllPosts($pdo);

// Count posts for searched hashtag
$hashtag_count = 0;
if (strpos($search, '#') === 0) {
    $hashtag_count = countPostsByHashtag($pdo, substr($search, 1));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <title>Glimmr - Feed</title>
    <link rel="stylesheet" href="../assets/css/feed.css">
</head>
<body>
    <!-- Custom Header for Feed Page -->
    <header class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo-container d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo">
            </div>
            <div class="search-container">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           class="form-control search-input" 
                           name="search" 
                           placeholder="Search posts or #hashtags"
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <nav class="navbar">
                <ul class="nav-list d-flex">
                    <li class="nav-item"><a href="../pages/feed.php">
                        <img border="0" alt="Feed" src="../assets/img/feed.png" width="70" height="28" 
                        class="nav-link"></a></li>
                    <li class="nav-item"><a href="../includes/auth.php?logout=true">
                        <img border="0" alt="Logout" src="../assets/img/logout.png" width="70" height="28" 
                        class="nav-link"></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 105px;">
        <div class="row justify-content-center w-100">
            <div class="col-lg-8">
                <!-- Search Results Info -->
                <?php if ($search): ?>
                    <div class="alert alert-info mb-4">
                        <?php if (strpos($search, '#') === 0): ?>
                            Showing <?= $hashtag_count ?> posts with hashtag <strong><?= htmlspecialchars($search) ?></strong>
                        <?php else: ?>
                            Showing results for: <strong><?= htmlspecialchars($search) ?></strong>
                        <?php endif; ?>
                        <a href="../pages/feed.php" class="float-end">Clear search</a>
                    </div>
                <?php endif; ?>

                <!-- Post Creation Card -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <form id="postForm" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <textarea class="form-control post-textarea" 
                                          name="content" 
                                          rows="3" 
                                          placeholder="What's on your mind?"
                                          required></textarea>
                            </div>
                            
                            <!-- Media Preview -->
                            <div id="mediaPreview" class="mb-3 text-center position-relative"></div>
                            
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex">
                                    <!-- Media Upload -->
                                    <label class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="fas fa-image me-1"></i> Photo
                                        <input type="file" 
                                               id="media" 
                                               name="media" 
                                               accept="image/*, video/*" 
                                               class="d-none"
                                               onchange="previewMedia(this)">
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">Post</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- News Feed -->
                <?php if (empty($posts)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5>No posts found</h5>
                            <p class="text-muted">Be the first to post something!</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
<div class="card mb-4 post-card position-relative">
    <form method="POST" class="delete-post-form position-absolute top-0 end-0 m-2" onsubmit="return confirm('Are you sure you want to delete this post?');">
        <input type="hidden" name="delete_post_id" value="<?= $post['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm delete-post-button" title="Delete Post">&times;</button>
    </form>
    <div class="card-body">
                                <div class="d-flex mb-3">
                                    <img src="/../assets/img/profile-pic.png" 
                                         class="rounded-circle me-3" 
                                         width="50" 
                                         height="50" 
                                         alt="Profile picture">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($post['username']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('F j, Y \a\t g:i a', strtotime($post['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <p class="post-content mb-3"><?= formatPostContent($post['content']) ?></p>
                                
                                <?php if ($post['media_path']): ?>
                                    <?php if (strpos($post['media_path'], '.mp4') !== false): ?>
                                        <video controls class="w-100 rounded mb-3">
                                            <source src="<?= $post['media_path'] ?>" type="video/mp4">
                                        </video>
                                    <?php else: ?>
                                        <img src="<?= $post['media_path'] ?>" 
                                             class="img-fluid rounded mb-3" 
                                             alt="Post image">
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($post['hashtags'])): ?>
                                    <div class="hashtags mt-2">
                                        <?php 
                                        $tags = explode(',', $post['hashtags']);
                                        foreach ($tags as $tag):
                                            if (!empty(trim($tag))):
                                        ?>
                                            <a href="/pages/feed.php?search=%23<?= urlencode(trim($tag)) ?>" 
                                               class="hashtag me-2">
                                                #<?= htmlspecialchars(trim($tag)) ?>
                                            </a>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Media Preview Functionality
    function previewMedia(input) {
        const preview = document.getElementById('mediaPreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileType = file.type.split('/')[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (fileType === 'image') {
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             class="img-fluid rounded" 
                             style="max-height: 200px">
                        <button type="button" 
                                class="btn-close position-absolute top-0 end-0 m-2" 
                                onclick="clearMedia()"></button>
                    `;
                } else if (fileType === 'video') {
                    preview.innerHTML = `
                        <video controls class="w-100 rounded">
                            <source src="${e.target.result}" type="${file.type}">
                        </video>
                        <button type="button" 
                                class="btn-close position-absolute top-0 end-0 m-2" 
                                onclick="clearMedia()"></button>
                    `;
                }
            }
            
            reader.readAsDataURL(file);
        }
    }

    function clearMedia() {
        document.getElementById('mediaPreview').innerHTML = '';
        document.getElementById('media').value = '';
    }
    </script>
</body>
</html>