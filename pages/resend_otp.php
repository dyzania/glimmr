<?php

session_start();

$page_title = "Resend OTP";

include '../includes/header.php';

if (!isset($_SESSION['email'])) {
    header("Location: /pages/signup.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/database.php';
    include '../config/email.php';
    include '../functions/user_functions.php';
    
    $email = $_SESSION['email'];
    $otp = rand(100000, 999999);
    
    //update OTP in database
    $stmt = $pdo->prepare("UPDATE users SET otp = ?, created_at = CURRENT_TIMESTAMP WHERE email = ?");
    if ($stmt->execute([$otp, $email])) {
        if (sendVerificationEmail($email, $otp)) {
            $_SESSION['otp'] = $otp;
            $_SESSION['success'] = "New verification code sent!";
            header("Location: ../pages/verify.php");
            exit();
        } else {
            $error = "Failed to send new verification code.";
        }
    } else {
        $error = "Failed to generate new verification code.";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="auth-form">
        <h2 class="form-title">Resend Verification Code</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <p>We'll send a new 6-digit code to <?php echo $_SESSION['email']; ?></p>
        
        <form method="POST">
            <button type="submit" class="btn btn-primary w-100">Resend Code</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="../pages/signup.php" class="text-muted">Need to change email?</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>