<?php

//function to check if username is available
function usernameAvailable($pdo, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() == 0;
}
   
//function to check if email is available
function emailAvailable($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() == 0;
}

//function to register a new user
function registerUser($pdo, $first_name, $last_name, $username, $email, $password, $otp) {
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password, otp) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$first_name, $last_name, $username, $email, $password, $otp]);
}

//function to authenticate user
function authenticateUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

//OTP Verification function
function verifyOTP($pdo, $email, $otp) {
    //fetch OTP from database by email
    $stmt = $pdo->prepare("SELECT id, otp FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && trim($user['otp']) === trim($otp)) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, otp = NULL WHERE id = ?");
        return $stmt->execute([$user['id']]);
    }
    return false;
}
?>