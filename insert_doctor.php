<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system'.'3310');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $name = $mysqli->real_escape_string($_POST['name']);
    $specialization = $mysqli->real_escape_string($_POST['specialization']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $error_message = "";

    $check_name_sql = "SELECT * FROM doctors WHERE email='$email'";
    $check_name_result = $mysqli->query($check_name_sql);
    if($check_name_result->num_rows > 0) {
        $row = $check_name_result->fetch_assoc();
        if($row['name'] != $name)
            $error_message .= "Error: Email is already used by another doctor with a different name.<br>";
    }

    $check_name_sql = "SELECT * FROM doctors WHERE phone='$phone'";
    $check_name_result = $mysqli->query($check_name_sql);
    if($check_name_result->num_rows > 0) {
        $row = $check_name_result->fetch_assoc();
        if($row['name'] != $name)
            $error_message .= "Error: Phone number is already used by another doctor with a different name.<br>";
    }

    if (empty($error_message)) {
        // Calculate the new custom ID
        $result = $mysqli->query("SELECT MAX(id) AS max_id FROM doctors");
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'];
        $new_id = $max_id ? $max_id + 1 : 953601;

        // Insert the new doctor into the database with the custom ID
        $sql = "INSERT INTO doctors (id, name, specialization, phone, email) VALUES ('$new_id', '$name', '$specialization', '$phone', '$email')";
        if ($mysqli->query($sql) === TRUE) {
            header("Location: viewdoc.php?id=$new_id");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .center-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border: 1px solid red;
            background-color: #f8d7da;
            text-align: center;
            z-index: 1000;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    <title>Add Doctor</title>
</head>
<body>
<header>
    <h1 style="color:Green">Add Doctor</h1>
</header>
<nav>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="viewdoc.php">View Doctors</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<main>
<form action="insert_doctor.php" method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br><br>
    
    <label for="specialization">Specialization:</label>
    <input type="text" id="specialization" name="specialization"><br><br>

    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone"><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>
    <input type="reset" style="background-color:lightgray">
    <button type="submit" name="submit">Add Doctor</button>
</form>
</main>

<?php
// Display error message if any
if (!empty($error_message)) {
    echo "<div class='center-message' id='error-message'>$error_message</div>";
}
?>
<script>
document.addEventListener('click', function() {
    var errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        errorMessage.style.display = 'none';
    }
});
</script>
</body>
</html>
