<?php

session_start();

include __DIR__ . '/../includes/feed_header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../functions/post_functions.php';

// handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = intval($_POST['delete_post_id']);
    if (deletePost($pdo, $post_id, $_SESSION['user_id'])) {
        $_SESSION['success'] = 'Post deleted successfully.';
    } else {
        $_SESSION['error'] = 'Failed to delete post.';
    }
    header("Location: ../pages/feed.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_post_id'])) { 

        //HANDLE POST CREATION
        $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
        $hashtags = extractHashtags($content);
        
        $media_path = null;
        if (!empty($_FILES['media']['name'])) {
            $upload_dir = __DIR__ . 'assets/uploads/posts/';

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





    <!-- Content Container -->
    <script src="../assets/js/script.js"></script>

    <div class="h-100 w-100 d-flex flex-column align-items-center" style="margin-top: 95px;">

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

        <div class="row justify-content-center w-100">

                <div class="col-lg-6 post-container">

                <!-- Search Bar -->
                <form id="searchForm" method="GET" action="../pages/feed.php" class="mb-3 d-flex">
                    <input type="text" id="searchInput" name="search" class="form-control me-2" placeholder="Search hashtags or content" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!--Search Results Info -->
                <?php if ($search): ?>
                    <div class="alert alert-info mb-2">
                        <?php if (strpos($search, '#') === 0): ?>
                            Showing <?= $hashtag_count ?> posts with hashtag <strong><?= htmlspecialchars($search) ?></strong>
                        <?php else: ?>
                            Showing results for: <strong><?= htmlspecialchars($search) ?></strong>
                        <?php endif; ?>
                        <a href="../pages/feed.php" class="float-end">Clear search</a>
                    </div>
                <?php endif; ?>

                <!--Post Creation Card-->
                <div class="card mb-2 shadow-sm" id="postCreationCard">
                    <div class="card-body">
                        <form id="postForm" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <textarea class="form-control post-textarea" id="textarea_content" 
                                          name="content" 
                                          rows="3" 
                                          placeholder="What's on your mind?"
                                          required></textarea>
                            </div>
                            
                            <!--Media Preview -->
                            <div id="mediaPreview" class="mb-3 text-center position-relative"></div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex">

                                    <!--Media Upload -->
                                    <label class="btn btn-sm btn-outline-secondary me-2" style="color: white;">
                                        <i class="fas fa-image me-1"></i> Photo / Video
                                        <input type="file" 
                                               id="media" 
                                               name="media" 
                                               accept="image/*, video/*" 
                                               class="d-none"
                                               onchange="previewMedia(this)">
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4" style="background-color: #a89900; color: black;">Post</button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- NEWS FEED -->

                <!-- if posts is empty -->
                <?php if (empty($posts)): ?>
                    <div class="card" id="noPostsCard">
                        <div class="card-body text-center py-5" style="color: #a89900; background-color: #252728;">
                            <i class="fas fa-newspaper fa-3x mb-2"></i>
                            <h5>No posts found</h5>
                            <p>Be the first to post something!</p>
                        </div>
                    </div>
                <?php else: ?>

                <!-- Posts -->
                <?php 
                
                foreach ($posts as $post): ?>
                <div class="card mb-2 post-card position-relative" id="card">

                    <!-- Delete Post -->
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="position-absolute top-0 end-0 m-4">
                        <input type="hidden" name="delete_post_id" value="<?= htmlspecialchars($post['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Post">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>

                    <!-- Profile Pic, Username, Time Posted -->
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="<?= htmlspecialchars($post['profile_pic'] ?? '/assets/img/profile-pic.png') ?>" 
                                class="rounded-circle me-1" 
                                width="50" 
                                height="50" 
                                alt="Profile picture">
                                        <div class="d-flex flex-column justify-content-center ms-2">
                                            <h6 class="mb-0"><?= htmlspecialchars($post['username']) ?></h6>
                                            <small class="text" style="color:rgb(118, 118, 116);">
                                                <?= date('F j, Y \a\t g:i a', strtotime($post['created_at'])) ?>
                                            </small>
                                        </div>
                        </div>
                            
                            <!-- Post Content -->
                            <p class="post-content mb-3"> <?= formatPostContent($post['content']) ?></p>
                            
                            <?php if ($post['media_path']): ?>
                                <?php if (strpos($post['media_path'], '.mp4') !== false): ?>
                                    <video controls class="w-100 rounded mb-3">
                                    <source src="/glimmr/<?= $post['media_path'] ?>" type="video/mp4">
                                    </video>

                                <?php else: ?>
                                    <div class="w-100 d-flex justify-content-center" id="postContent">
                                        <img src="/glimmr/<?= $post['media_path'] ?>" 
                                         class="img-fluid rounded mb-3" id="postContent"
                                         alt="Post image">

                                    </div>
                                <?php endif; ?>
                            <?php endif; ?> 
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <script>
        //Handle hashtag clicks to populate search bar and submit form
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a.hashtag').forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.preventDefault();
                    var hashtag = this.textContent.trim();
                    var searchInput = document.getElementById('searchInput');
                    var searchForm = document.getElementById('searchForm');
                    if (searchInput && searchForm) {
                        searchInput.value = hashtag;
                        searchForm.submit();
                    }
                });
            });
        });
    </script>
