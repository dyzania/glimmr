<?php

session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../pages/home.php");
    exit();
}


//handle OTP verification
if (isset($_POST['verify_otp'])) {
    
    include '../config/database.php';
    include '../functions/user_functions.php';
    
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];
    
    if (verifyOTP($pdo, $email, $otp)) {
        $_SESSION['success'] = "Email verified successfully! You can now login.";
        header("Location: ../pages/login.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }

    if ($user) {
        $otp_created = new DateTime($user['created_at']);
        $now = new DateTime();
        $diff = $now->getTimestamp() - $otp_created->getTimestamp();
        
        if ($diff <= 600) { // 10 minutes in seconds
            if (verifyOTP($pdo, $email, $otp)) {
                $_SESSION['success'] = "Email verified successfully! You can now login.";
                header("Location: ../pages/login.php");
                exit();
            } else {
                $error = "Verification failed. Please try again.";
            }
        } else {
            $error = "OTP has expired. Please request a new one.";
        }
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}

?>