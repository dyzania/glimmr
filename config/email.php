<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/vendor/PHPMailer/src/Exception.php';
require '../assets/vendor/PHPMailer/src/PHPMailer.php';
require '../assets/vendor/PHPMailer/src/SMTP.php';

function sendVerificationEmail($email, $otp) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'exodusgalimba@gmail.com'; // Your email
        $mail->Password   = 'kutw gpgu xezw tkrb'; // Use app password for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('verify@glimmr.com', 'Glimmr');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Glimmr Account';
        $mail->Body    = "
            <h2>Welcome to Glimmr!</h2>
            <p>Your verification code is: <br> <strong>$otp</strong></p>
            <p>Enter this code in the app to verify your email address.</p>
            <p>The code will expire in 10 minutes.</p>
        ";
        $mail->AltBody = "Your Glimmr verification code is: $otp";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>