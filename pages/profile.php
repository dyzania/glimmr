<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title = "My Profile";
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-center align-items-center vh-100 w-50">
    <div class="row justify-content-center w-100">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header" style="background-color: #7A6C00; color: white;">
                    <h4 class="mb-0">My Profile</h4>
                </div>
                <div class="card-body">
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
                    
                    <div class="text-center mb-4">
                        <img src="<?= htmlspecialchars($user['profile_pic'] ?: '../assets/img/profile-pic.png') ?>" 
                             class="rounded-circle profile-picture-lg"
                             alt="Profile picture"
                             id="profilePicPreview">
                        
                        <form method="POST" action="../pages/upload_profile.php" enctype="multipart/form-data" class="mt-3">
                            <div class="d-flex justify-content-center">
                                <label class="btn btn-primary">
                                    <i class="fas fa-camera me-2"></i> Change Photo
                                    <input type="file" 
                                           name="profile_pic" 
                                           id="profilePicInput" 
                                           class="d-none"
                                           accept="image/*"
                                           onchange="previewProfilePic(this)">
                                </label>
                                <button type="submit" class="btn btn-success ms-2" id="saveProfilePicBtn" disabled>
                                    <i class="fas fa-save me-2"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="user-info d-flex flex-column align-items-start justify-content-center">
                        <h5 class="mb-3 w-100" style="text-align: center;">User Information</h5>
                        <div class="mb-2">
                            <strong>Name:</strong> 
                            <?= htmlspecialchars($user['first_name'] . ' ' . htmlspecialchars($user['last_name'])) ?>
                        </div>
                        <div class="mb-2">
                            <strong>Username:</strong> 
                            <?= htmlspecialchars($user['username']) ?>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong> 
                            <?= htmlspecialchars($user['email']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewProfilePic(input) {
    const preview = document.getElementById('profilePicPreview');
    const saveBtn = document.getElementById('saveProfilePicBtn');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            saveBtn.disabled = false;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>