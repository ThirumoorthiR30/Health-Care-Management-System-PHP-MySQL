<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch all appointments from the database, including patient and doctor names
$query = "
    SELECT 
        appointments.patient_id,
        appointments.appointment_date,
        appointments.appointment_time,
        appointments.notes,
        patients.name AS patient_name,
        doctors.name AS doctor_name
    FROM appointments
    JOIN patients ON appointments.patient_id = patients.id
    JOIN doctors ON appointments.doctor_id = doctors.id
";

$result = $mysqli->query($query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Appointments - Healthcare Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .table-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #f2f2f2;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #dddddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        nav {
            margin-bottom: 20px;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            padding: 10px;
            background-color: #333;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
        }

        h3 {
            margin-bottom: 10px;
            color: Green;
        }

    </style>
</head>
<body>
    <header>
        <h1>All Appointments</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="appointments.php">Add Appointment</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="table-container">
        <h3>Details of the Appointments</h3>
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No appointments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
