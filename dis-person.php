<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch all discharged persons from the database
$result = $mysqli->query("SELECT * FROM discharged_person");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharged Persons - Healthcare Management System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        header {
            text-align: center;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
        }
        body, html {
            height: 50%;
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
            border-collapse: collapse;
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
    padding: 1px 0; 
    background-color: #343a40;
    color: #fff;
    position: fixed;
    width: 100%;
    bottom: 0;
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
            <li><a href="viewpat.php">View Patients</a></li>
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
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Discharged Time</th>
                    <th>Insurance Provider</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['date_of_birth']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td><?php echo $row['insurance_provider']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Healthcare Management System</p>
    </footer>
</body>
</html>
