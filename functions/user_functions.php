<?php
function usernameAvailable($pdo, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() == 0;
}

function emailAvailable($pdo, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() == 0;
}

function registerUser($pdo, $first_name, $last_name, $username, $email, $password, $otp) {
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password, otp) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$first_name, $last_name, $username, $email, $password, $otp]);
}

function authenticateUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function verifyOTP($pdo, $email, $otp) {
    // Fetch OTP from database by email
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