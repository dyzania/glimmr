<?php 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/database.php';
    include '../functions/user_functions.php';
    
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $username = htmlspecialchars($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    
    if (!usernameAvailable($pdo, $username)) $errors[] = "Username already taken";
    if (!emailAvailable($pdo, $email)) $errors[] = "Email already registered";
    
    //in the registration success block:
    if (empty($errors)) {
        $otp = rand(100000, 999999);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if (registerUser($pdo, $first_name, $last_name, $username, $email, $hashed_password, $otp)) {
            require '../config/email.php';
            
            if (sendVerificationEmail($email, $otp)) {
                $_SESSION['email'] = $email;
                $_SESSION['otp'] = $otp;
                header("Location: ../pages/verify.php");
                exit();
            } else {
                $errors[] = "Failed to send verification email. Please try again.";
            }
            
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

$page_title = "Sign Up";
include '../includes/header.php'; 
?>

<div class="signup-container">
    <div class="auth-form">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; 
        unset($_SESSION['success']); ?></div><?php endif; ?>

        <div class="auth_logo">
            <img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo">
        </div>

        <h2 class="form-title">Create Your Account</h2>
        
        <form id="signupForm" method="POST">
            <div class="row mb-4">
                <div class="col-md-6">
                    
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                </div>
                <div class="col-md-6">
                    
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                </div>
            </div>
            
            <div class="mb-4">
                
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <div class="invalid-feedback" id="username-feedback"></div>
            </div>
            
            <div class="mb-4">
                
                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                <div class="invalid-feedback" id="email-feedback"></div>
            </div>
            
            <div class="mb-4">
                
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <div class="form-text"> * Password must be at least 8 characters long</div>
            </div>
            
            <div class="mb-4">
                
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password"required>
                <div class="invalid-feedback" id="password-feedback"></div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>
        
        <div class="text-center mt-3">
            <p>Already have an account? <a href="../pages/login.php">Log In</a></p>
        </div>
    </div>
</div>


