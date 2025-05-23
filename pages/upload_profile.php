<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $upload_dir = __DIR__ . '/../assets/uploads/profile_pics/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $file_name = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
    $target_path = $upload_dir . $file_name;

    // Validate image
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_ext), $valid_extensions)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed";
        header("Location: ../pages/profile.php");
        exit();
    }

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
        $profile_pic_path = '../assets/uploads/profile_pics/' . $file_name;
        
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->execute([$profile_pic_path, $user_id]);
        
        $_SESSION['success'] = "Profile picture updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to upload profile picture";
    }
    
    header("Location: ../pages/profile.php");
    exit();
}