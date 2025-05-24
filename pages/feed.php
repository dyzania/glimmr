<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../functions/post_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //handle post deletion
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

        //handle post creation
        $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
        $hashtags = extractHashtags($content);
        
        $media_path = null;
        if (!empty($_FILES['media']['name'])) {
            $upload_dir = __DIR__ . 'assets/uploads/posts/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            //validate file type
            $file_ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
            $file_name = 'post_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
            $media_path = 'assets/uploads/posts/' . $file_name;

            if (!move_uploaded_file($_FILES['media']['tmp_name'], __DIR__ . '/../' . $media_path)) {
                error_log('Failed to move uploaded file to ' . __DIR__ . '/../' . $media_path);
                $_SESSION['error'] = 'Failed to upload media file.';
            }
        }

        //create post only if content is not empty
        if (!empty($content)) {
            createPost($pdo, $_SESSION['user_id'], $content, $media_path, $hashtags);
        }
        
        header("Location: ../pages/feed.php");
        exit();
    }
}

//handles search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username, u.profile_pic
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE (p.content LIKE ? OR p.hashtags LIKE ?)
        ORDER BY p.created_at DESC
    ");
    $search_param = "%$search%";
    $stmt->execute([$search_param, $search_param]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("
        SELECT p.*, u.username, u.profile_pic
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//count posts for searched hashtag
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
    <script src="assets/js/script.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <!--custom header for feed page-->
    <header class="header">
        <div class="d-flex justify-content-between align-items-center w-100 p-0 m-0">
            <div class="logo-container d-flex align-items-center">
                <img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo">
            </div>

            <!--search container-->
            <div class="search-container">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           class="form-control search-input" 
                           name="search" 
                           placeholder="Search posts or #hashtags"
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-link ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!--feed nav bar-->
            <nav class="navbar">
                <ul class="nav-list d-flex">
                    <li class="nav-item">
                        <a href="../pages/feed.php" class="nav-link" style="text-decoration: none;">
                        <i class="fas fa-newspaper"></i> Feed</a></li>

                    <li class="nav-item">
                        <a href="../pages/profile.php" class="nav-link" style="text-decoration: none;">
                        <i class="fas fa-user-circle me-1"> <?= htmlspecialchars($_SESSION['username'])?> </i></a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="showLogoutConfirmation()" style="text-decoration: none;">
                        <i class="fas fa-right-from-bracket me-1"></i> Logout</a>
                    </li>    
                </ul>
            </nav>

        </div>
    </header>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="../includes/auth.php?logout=true" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="h-100 w-100 d-flex flex-column align-items-center">
        <div class="row justify-content-center w-100">
            
            <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                <div class="col-lg-6 post-container">
                <!--search results info -->
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

                <!--post creation card-->
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
                            
                            <!--media preview -->
                            <div id="mediaPreview" class="mb-3 text-center position-relative"></div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex">

                                    <!--media upload -->
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

                <!-- NEWS FEED -->

                <?php if (empty($posts)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5>No posts found</h5>
                            <p class="text-muted">Be the first to post something!</p>
                        </div>
                    </div>
                <?php else: ?>

                    <!-- posting -->
                <?php 
                // DEBUG: output media_path for each post
                foreach ($posts as $post): ?>
                <div class="card mb-4 post-card position-relative">
                            <div class="post-options position-absolute top-0 end-0 m-2">
                            <button class="btn btn-sm btn-light three-dots-btn" title="Post options">&#x22EE;</button>
                                <div class="dropdown-menu d-none shadow-sm">
                                <form method="POST" class="delete-post-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="delete_post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                </form>
                            <button class="dropdown-item report-post-btn" data-post-id="<?= $post['id'] ?>">Report</button>
                                </div>
                            </div>
            
                        <!-- profile pic, username, time posted -->
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <img src="<?= htmlspecialchars($post['profile_pic'] ?? '/assets/img/profile-pic.png') ?>" 
                                    class="rounded-circle me-1" 
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
                                
                                <!-- post content -->
                                <p class="post-content mb-3"><?= formatPostContent($post['content']) ?></p>
                                
                                <?php if ($post['media_path']): ?>
                                    <?php if (strpos($post['media_path'], '.mp4') !== false): ?>
                                        <video controls class="w-100 rounded mb-3">
                                        <source src="/glimmr/<?= $post['media_path'] ?>" type="video/mp4">
                                        </video>
                                    <?php else: ?>
                                        <img src="/glimmr/<?= $post['media_path'] ?>" 
                                             class="img-fluid rounded mb-3" 
                                             alt="Post image">
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <!-- hashtags -->
                                <?php if (!empty($post['hashtags'])): ?>
                                    <div class="hashtags mt-2">
                                        <?php 
                                        $tags = explode(',', $post['hashtags']);
                                        foreach ($tags as $tag):
                                            if (!empty(trim($tag))):
                                        ?>
                                            <a href="../pages/feed.php?search=%23<?= urlencode(trim($tag)) ?>" 
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

    //media preview
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

    //clear media preview
    function clearMedia() {
        document.getElementById('mediaPreview').innerHTML = '';
        document.getElementById('media').value = '';
    }

    //post deletion confirmation
    document.addEventListener('DOMContentLoaded', function() {
            console.log('Feed page JS loaded');
            document.querySelectorAll('.three-dots-btn').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('Three dots clicked');
                    const dropdown = this.nextElementSibling;
                    if (dropdown.classList.contains('d-none')) {
                        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('d-none'));
                        dropdown.classList.remove('d-none');
                    } else {
                        dropdown.classList.add('d-none');
                    }
                });
            });

            document.addEventListener('click', function() {
                document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('d-none'));
            });

            document.querySelectorAll('.report-post-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    alert('Report functionality is not implemented yet for post ID: ' + postId);
                    this.parentElement.classList.add('d-none');
                });
            });
        });
    </script>   
</body>
</html>
