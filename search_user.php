<?php
require_once 'db_connect.php';

if (isset($_GET['user_name'])) {
    $username = trim($_GET['user_name']);
    $stmt = $conn->prepare('SELECT user_name FROM users WHERE user_name = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo 'found';
    } else {
        echo 'not found';
    }
    $stmt->close();
}
?>