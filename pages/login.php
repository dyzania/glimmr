<?php
session_start();

$page_title = "Login";

include '../includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/database.php';
    include '../functions/user_functions.php';
    
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    
    $user = authenticateUser($pdo, $username, $password);
    
    if ($user) {
        if ($user['is_verified']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../pages/feed.php");
            exit();
        } else {
            $error = "Please verify your email first. Check your inbox for the OTP.";
            header("Location: ../pages/verify.php");
        }
    } else {
        $error = "Invalid username or password";
    }
}

?>

<div class="container">
    <div class="auth-form">
        <h2 class="form-title">Login to Your Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; 
            unset($_SESSION['success']); ?></div><?php endif; ?>
        
        
        <form method="POST">
            <div class="mb-2">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="mb-2">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>
        
        <div class="text-center mt-2">
            <p>Don't have an account? <a href="../pages/signup.php">Sign Up</a></p>
        </div>
    </div>
</div>