<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);

    $result = $mysqli->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
    } else {
        echo "Invalid username or password";
    }
}
?>
