<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set timezone to Chennai, India
date_default_timezone_set('Asia/Kolkata');

// Fetch the highest existing custom patient ID
function getNewPatientId($mysqli) {
    $query = "SELECT id FROM patients ORDER BY id DESC LIMIT 1";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id'];
        $number = (int) substr($lastId, 4) + 1;
        return "9536" . str_pad($number, 4, '0', STR_PAD_LEFT);
    } else {
        return "95362201";
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $age = $mysqli->real_escape_string($_POST['age']);
    $gender = $mysqli->real_escape_string($_POST['gender']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $dob = $mysqli->real_escape_string($_POST['dob']);
    $insurance_provider = $mysqli->real_escape_string($_POST['insurance_provider']);

    // Capture the current time
    $current_time = date('H:i:s'); // Format: HH:MM:SS

    $error_message = "";

    // Check if the phone number is exactly 10 digits
    if (!preg_match('/^\d{10}$/', $phone)) {
        $error_message .= "Phone number must be exactly 10 digits.<br>";
    }

    // Check if the phone already exists
    $check_phone_sql = "SELECT * FROM patients WHERE phone='$phone'";
    $check_phone_result = $mysqli->query($check_phone_sql);
    if ($check_phone_result->num_rows > 0) {
        $error_message .= "A patient with the same phone number already exists.<br>";
    }

    // Check if the email already exists
    $check_email_sql = "SELECT * FROM patients WHERE email='$email'";
    $check_email_result = $mysqli->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $error_message .= "A patient with the same email already exists.<br>";
    }

    // If there are no errors, insert the new patient
    if (empty($error_message)) {
        $new_patient_id = getNewPatientId($mysqli);
        $sql = "INSERT INTO patients (id, name, age, gender, address, phone, email, date_of_birth, insurance_provider, time) 
                VALUES ('$new_patient_id', '$name', '$age', '$gender', '$address', '$phone', '$email', '$dob', '$insurance_provider', '$current_time')";
        if ($mysqli->query($sql) === TRUE) {
            // Redirect to patient_details.php with the patient ID as a parameter
            header("Location: patient_det.php?id=$new_patient_id");
            exit; // Stop further execution
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

// Fetch all patients from the database
$result = $mysqli->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - Healthcare Management System</title>
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
            color: #721c24;
            text-align: center;
            z-index: 1000;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header>
        <h1>Patients</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="viewpat.php">View Patients</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <h2>Add New Patient</h2>
        <form action="patients.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="gender">Gender:</label>
            <input type="text" id="gender" name="gender" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required pattern="\d{10}" title="Phone number must be exactly 10 digits">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="insurance_provider">Insurance Provider:</label>
            <input type="text" id="insurance_provider" name="insurance_provider">
            <input type="reset" style="background-color:lightgray">
            <button type="submit">Add Patient</button>
        </form>

        <?php
        // Display error message if any
        if (!empty($error_message)) {
            echo "<div class='center-message' id='error-message'>$error_message</div>";
        }
        ?>
    </main>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
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
