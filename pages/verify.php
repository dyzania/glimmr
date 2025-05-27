<?php
session_start();

$page_title = "Verify Email";

//check if required session data exists
if (!isset($_SESSION['email']) || !isset($_SESSION['otp'])) {
    //redirect to signup page if email or OTP is not set
    header("Location: ../pages/signup.php");  
    exit();
}

include __DIR__ . '/../includes/header.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../functions/user_functions.php';

    $submitted_otp = trim(string: $_POST['otp']);
    $email = $_SESSION['email'];
    
if ($submitted_otp == $_SESSION['otp']) {
        //verify OTP in database
        if (verifyOTP($pdo, $email, $submitted_otp)) {
            //clear OTP from session
            unset($_SESSION['otp']);
            $success = "Verified Successfully";
            //redirect to login page after successful verification
            header("Location: ../pages/login.php");
            exit();
        } else {
            $error = "Invalid OTP. Please try again.";
        }
    } else {
        $error = "Incorrect OTP entered.";
    }
}

//display success message if exists
?>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="auth-form">
        <h2 class="form-title">Verify Your Email</h2>
        
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <div class="d-grid mt-3">
        <a href="../pages/login.php" class="btn btn-success">Go to Login</a>
    </div>
<?php endif; ?>
        
        <p>We've sent a 6-digit code to <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        
        <form method="POST">
            <div class="mb-3">
                <label for="otp" class="form-label text-align-center">Verification Code</label>
                <input type="text" class="form-control" id="otp" name="otp" required 
                       maxlength="6" pattern="\d{6}" title="6-digit code">
            </div>
            
            <button type="submit" name="verify_otp" class="btn btn-primary w-100">Verify</button>
        </form>

        <div class="text-center mt-3">
            <p>Didn't receive the code? <a href="../pages/resend_otp.php">Resend</a></p>
            <p>or <a href="../pages/signup.php">Change email address</a></p>
        </div>
    </div>
</div>