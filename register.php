<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $role = $mysqli->real_escape_string($_POST['role']);

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists
    $result = $mysqli->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        echo "Username already taken";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
        if ($mysqli->query($sql) === TRUE) {
            echo "Registration successful";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}
?>
