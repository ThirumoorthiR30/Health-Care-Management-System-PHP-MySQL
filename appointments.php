<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $mysqli->real_escape_string($_POST['patient_id']);
    $doctor_id = $mysqli->real_escape_string($_POST['doctor_id']);
    $appointment_date = $mysqli->real_escape_string($_POST['appointment_date']);
    $appointment_time = $mysqli->real_escape_string($_POST['appointment_time']);
    $notes = $mysqli->real_escape_string($_POST['notes']);

    // Check if the doctor is already booked on the selected date and time
    $check_sql = "SELECT * FROM appointments WHERE doctor_id = '$doctor_id' AND appointment_date = '$appointment_date' AND appointment_time = '$appointment_time'";
    $result = $mysqli->query($check_sql);
    $error_message = "";

    if ($result->num_rows > 0) {
        $error_message = "The selected doctor is already booked on this date and time.";
    } else {
        // Insert the new appointment into the database
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes) VALUES ('$patient_id', '$doctor_id', '$appointment_date', '$appointment_time', '$notes')";
        if ($mysqli->query($sql) === TRUE) {
            $new_appointment_id = $mysqli->insert_id;
            // Redirect to viewapp.php with the appointment ID as a parameter
            header("Location: viewapp.php?id=$new_appointment_id");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

// Fetch all appointments from the database
if (isset($_GET['doctor_id'])) {
    // If doctor_id is provided in the URL, fetch appointments for that doctor only
    $doctor_id = $mysqli->real_escape_string($_GET['doctor_id']);
    $result = $mysqli->query("SELECT appointments.*, patients.name AS patient_name, doctors.name AS doctor_name 
                              FROM appointments 
                              JOIN patients ON appointments.patient_id = patients.id
                              JOIN doctors ON appointments.doctor_id = doctors.id
                              WHERE doctors.id = '$doctor_id'");
} else {
    // Fetch all appointments
    $result = $mysqli->query("SELECT appointments.*, patients.name AS patient_name, doctors.name AS doctor_name 
                              FROM appointments 
                              JOIN patients ON appointments.patient_id = patients.id
                              JOIN doctors ON appointments.doctor_id = doctors.id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Healthcare Management System</title>
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
        <h1>Appointments</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="viewapp.php">View Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <h2>Add New Appointment</h2>
        <form action="appointments.php" method="POST">
            <label for="patient_id">Patient:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">select</option>
                <?php
                $patients = $mysqli->query("SELECT id, name FROM patients");
                while($patient = $patients->fetch_assoc()):
                ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="doctor_id">Doctor:</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">select</option>
                <?php
                // Fetch the specific doctor based on doctor_id from the URL parameter
                if (isset($_GET['doctor_id'])) {
                    $doctor_id = $_GET['doctor_id'];
                    $doctor_query = $mysqli->query("SELECT id, name FROM doctors WHERE id = $doctor_id");
                    if ($doctor = $doctor_query->fetch_assoc()):
                ?>
                <option value="<?php echo $doctor['id']; ?>" selected><?php echo $doctor['name']; ?></option>
                <?php endif;
                } else {
                    // Fetch all doctors
                    $doctors = $mysqli->query("SELECT id, name FROM doctors");
                    while($doctor = $doctors->fetch_assoc()):
                ?>
                <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['name']; ?></option>
                <?php endwhile;
                }
                ?>
            </select>

            <label for="appointment_date">Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>

            <label for="appointment_time">Appointment Time:</label>
            <input type="time" id="appointment_time" name="appointment_time" required>
             
            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes"></textarea>
            <input type="reset" style="background-color:lightgray">
            <button type="submit">Add Appointment</button>
        </form>
        <?php
        // Display error message if any
        if (!empty($error_message)) {
            echo "<div class='center-message' id='error-message'>$error_message</div>";
        }
        ?>
        
    </main>
    <script>
        document.addEventListener('click', function() {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    </script>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
