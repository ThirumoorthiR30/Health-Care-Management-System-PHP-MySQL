<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch all unique specializations
$specializations_query = $mysqli->query("SELECT DISTINCT specialization FROM doctors");

// Function to fetch doctors by specialization
function getDoctorsBySpecialization($mysqli, $specialization) {
    $query = "SELECT doctors.*, 
              (SELECT COUNT(*) FROM appointments WHERE appointments.doctor_id = doctors.id) AS appointment_count 
              FROM doctors
              WHERE specialization = '$specialization'";
    return $mysqli->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Details - Healthcare Management System</title>
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
        <h1>Doctor Details</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="insert_doctor.php">Add Doctor</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="table-container">
        <?php while ($specialization_row = $specializations_query->fetch_assoc()): ?>
            <h3><?php echo $specialization_row['specialization']; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $specialization = $specialization_row['specialization'];
                    $doctors_result = getDoctorsBySpecialization($mysqli, $specialization);
                    while ($row = $doctors_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['specialization']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <?php if($row['appointment_count'] == 0): ?>
                                <a href="appointments.php?doctor_id=<?php echo $row['id']; ?>">Make Appointment</a>
                            <?php else: ?>
                                Has Appointment
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endwhile; ?>
    </div>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
