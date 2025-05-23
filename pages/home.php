<?php 
$page_title = "Home";
include '../includes/header.php'; 
?>

<section class="hero-section">
    <div class="container d-flex flex-column justify-content-center align-items-center text-center">
        <h1 class="slogan">Share Your <span>Moments</span> with the World</h1>
        <p class="lead">Connect with friends and discover new experiences</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">About Glimmr</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card feature-card">
                    <img src="/glimmr/assets/img/screenshots/feed.jpg" class="card-img-top" alt="Social Feed">
                    <div class="card-body">
                        <h5 class="card-title">Social Feed</h5>
                        <p class="card-text">Stay updated with posts from your friends and communities in real-time.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <img src="/glimmr/assets/img/screenshots/connect.jpg" class="card-img-top" alt="Connect">
                    <div class="card-body">
                        <h5 class="card-title">Connect</h5>
                        <p class="card-text">Find and connect with people who share your interests and passions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <img src="/glimmr/assets/img/screenshots/share.jpg" class="card-img-top" alt="Share">
                    <div class="card-body">
                        <h5 class="card-title">Share</h5>
                        <p class="card-text">Share your thoughts, photos, and videos with your network.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>