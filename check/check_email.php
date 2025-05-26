<?php
header('Content-Type: application/json');
require '../config/database.php';
require '../functions/user_functions.php';

//check if email is available
$email = $_GET['email'] ?? '';
echo json_encode(['available' => emailAvailable($pdo, $email)]);
?>