<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check if the patient ID is provided in the URL
if (isset($_GET['id'])) {
    $patient_id = $mysqli->real_escape_string($_GET['id']);

    // Fetch the patient details from the database
    $result = $mysqli->query("SELECT * FROM patients WHERE id = '$patient_id'");
    $patient = $result->fetch_assoc();

    if (!$patient) {
        echo "Patient not found.";
        exit;
    }
} else {
    echo "Patient ID not provided.";
    exit;
}

// Fetch all patients from the database
$all_patients = $mysqli->query("SELECT * FROM patients");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discharge'])) {
    // Handle discharge request
    $patient_id = $mysqli->real_escape_string($_POST['id']);

    // Get the current time
    $current_time = date('Y-m-d H:i:s');
    
    // Insert patient data into discharged_person table
    $stmt = $mysqli->prepare("INSERT INTO discharged_person (id, name, age, gender, address, phone, email, date_of_birth, insurance_provider, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssss", 
        $patient['id'], 
        $patient['name'], 
        $patient['age'], 
        $patient['gender'], 
        $patient['address'], 
        $patient['phone'], 
        $patient['email'], 
        $patient['date_of_birth'], 
        $patient['insurance_provider'],
        $current_time
    );
    if (!$stmt->execute()) {
        die("Insert failed: " . $stmt->error);
    }

    // Update the action column to 'discharged'
    if (!$mysqli->query("UPDATE patients SET action='discharged' WHERE id='$patient_id'")) {
        die("Update failed: " . $mysqli->error);
    }

    // Redirect to the same page to refresh the list
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $patient_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - Healthcare Management System</title>
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

        h2 {
            margin-bottom: 20px;
            color: Green;
        }

        .remove-link {
            padding: 5px 10px;
            background-color: #f44376;
            color: white;
            text-decoration: none;
            border-radius: 2px;
            cursor: pointer;
            border: none;
            margin-top: 20px;
        }

        .remove-link:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <h1>Patient Details</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="patients.php">Patients</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="table-container">
        <h2><strong>Patient Name:</strong> <?php echo htmlspecialchars($patient['name']); ?></h2>
        <br>
        <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['id']); ?></p>
        <br>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($patient['age']); ?></p>
        <br>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
        <br>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
        <br>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
        <br>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
        <br>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></p>
        <br>
        <p><strong>Insurance Provider:</strong> <?php echo htmlspecialchars($patient['insurance_provider']); ?></p>
        <br>
        <p><strong>Admitted Time:</strong> <?php echo htmlspecialchars($patient['time']); ?></p>
        <br>
        <h3>All Patients</h3>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Date of Birth</th>
                    <th>Insurance Provider</th>
                    <th>Admitted Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $all_patients->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($row['insurance_provider']); ?></td>
                    <td><?php echo htmlspecialchars($row['time']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
