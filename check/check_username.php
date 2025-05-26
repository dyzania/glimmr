<?php
header('Content-Type: application/json');
require '../config/database.php';
require '../functions/user_functions.php';

//check if username is available
$username = $_GET['username'] ?? '';
echo json_encode(['available' => usernameAvailable($pdo, $username)]);
?>