<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system'.'3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the appointment ID is provided in the URL
if (isset($_GET['id'])) {
    $appointment_id = $mysqli->real_escape_string($_GET['id']);

    // Fetch the appointment details from the database
    $result = $mysqli->query("SELECT appointments.*, patients.name AS patient_name, doctors.name AS doctor_name 
        FROM appointments 
        JOIN patients ON appointments.patient_id = patients.id
        JOIN doctors ON appointments.doctor_id = doctors.id
        WHERE appointments.id = '$appointment_id'");
    $appointment = $result->fetch_assoc();

    if (!$appointment) {
        echo "Appointment not found.";
        exit;
    }
} else {
    echo "Appointment ID not provided.";
    exit;
}

$all_appointments = $mysqli->query("SELECT * FROM appointments");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - Healthcare Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Appointment Details</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <h2>Appointment Details</h2>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
        <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
        <p><strong>Appointment Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
        <p><strong>Notes:</strong> <?php echo htmlspecialchars($appointment['notes']); ?></p>

        <h2>All Appointments</h2>
        <table>
            <tr>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Notes</th>
            </tr>
            <?php
            $sql = "SELECT appointments.appointment_date, appointments.appointment_time, appointments.notes, 
                           patients.name AS patient_name, doctors.name AS doctor_name
                    FROM appointments
                    JOIN patients ON appointments.patient_id = patients.id
                    JOIN doctors ON appointments.doctor_id = doctors.id";
            $result = $mysqli->query($sql);
            while ($row = $result->fetch_assoc()):?>
            <tr>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($row['notes']); ?></td>
            </tr><?php endwhile; ?>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
