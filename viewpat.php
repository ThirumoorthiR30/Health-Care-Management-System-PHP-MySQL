<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set timezone to Chennai, India
date_default_timezone_set('Asia/Kolkata');

// Handle discharge request
if (isset($_GET['id'])) {
    $patient_id = $mysqli->real_escape_string($_GET['id']);
    
    // Fetch the patient details
    $result = $mysqli->query("SELECT * FROM patients WHERE id='$patient_id'");
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        
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
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "No patient found with ID: $patient_id";
    }
}

// Fetch all patients who are not discharged from the database
$result = $mysqli->query("SELECT * FROM patients WHERE action IS NULL");

if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients - Healthcare Management System</title>
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
        min-height: 100vh;
        background-color: #f2f2f2;
        padding: 20px;
        overflow-x: auto; /* Allow horizontal scrolling if needed */
    }

    table {
        width: 100%;
        max-width: 1200px; /* Optional: limit the max width */
        border-collapse: collapse;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    th, td {
        padding: 12px 15px;
        border: 1px solid #dddddd;
        text-align: left;
        white-space: nowrap; /* Prevent text wrapping */
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
        padding: 10px 0;
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

    .remove-link {
        padding: 2px 2px;
        background-color: #f44376;
        color: white;
        text-decoration: none;
        border-radius: 2px;
        cursor: pointer;
    }

    .remove-link:hover {
        background-color: #d32f2f;
    }
</style>

</head>
<body>
    <header>
        <h1>Healthcare Management System</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="patients.php">Add Patient</a></li>
            <li><a href="dis-person.php">Discharged Patients</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Date of Birth</th>
                    <th>Admitted Time</th>
                    <th>Insurance Provider</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td><?php echo htmlspecialchars($row['insurance_provider']); ?></td>
                            <td>
                                <a href="?id=<?php echo htmlspecialchars($row['id']); ?>" class="remove-link" onclick="return confirm('Are you sure you want to discharge this patient?');">Discharge</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No patients found</td>
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
