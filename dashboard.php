<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle search query
$search = "";
$doctorDetails = [];
$doctorAppointments = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $mysqli->real_escape_string($_POST['search']);
    
    // Search for doctors
    $doctorQuery = "SELECT * FROM doctors WHERE name LIKE '%$search%'";
    $doctorResult = $mysqli->query($doctorQuery);
    $doctorDetails = $doctorResult->fetch_all(MYSQLI_ASSOC);
    
    // Fetch appointments and patient names for each doctor
    foreach ($doctorDetails as $doctor) {
        $doctorId = $doctor['id']; // Assuming 'id' is the primary key of doctors table
        $appointmentQuery = "SELECT * FROM appointments WHERE doctor_id = $doctorId";
        $appointmentResult = $mysqli->query($appointmentQuery);
        $appointments = $appointmentResult->fetch_all(MYSQLI_ASSOC);

        $patients = [];
        foreach ($appointments as $appointment) {
            $patientId = $appointment['patient_id']; // Assuming 'patient_id' is the foreign key in appointments table
            $patientQuery = "SELECT name FROM patients WHERE id = $patientId";
            $patientResult = $mysqli->query($patientQuery);
            if ($patientRow = $patientResult->fetch_assoc()) {
                $patients[] = $patientRow['name'];
            }
        }

        $doctorAppointments[$doctorId] = $patients;
    }
}

// Fetch statistics
$patientCountResult = $mysqli->query("SELECT COUNT(*) AS count FROM patients");
$patientCount = $patientCountResult->fetch_assoc()['count'];

$appointmentCountResult = $mysqli->query("SELECT COUNT(*) AS count FROM appointments");
$appointmentCount = $appointmentCountResult->fetch_assoc()['count'];

$doctorCountResult = $mysqli->query("SELECT COUNT(*) AS count FROM doctors");
$doctorCount = $doctorCountResult->fetch_assoc()['count'];

$dischargedPersonCountResult = $mysqli->query("SELECT COUNT(*) AS count FROM discharged_person");
$dischargedPersonCount = $dischargedPersonCountResult->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Healthcare Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .full-height {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .content-section {
            padding: 10px;
            background-color: #f9f9f9;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            margin-bottom: 20px;
        }

        .card-body {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .navbar-nav .nav-link {
            color: white !important;
        }

        .counter {
            font-size: 2em;
            font-weight: bold;
            color: #28a745;
        }
    </style>
    <script>
        function validateSearch() {
            var searchInput = document.getElementById('search').value;
            if (searchInput.trim() === "") {
                var alertDiv = document.getElementById('searchAlert');
                alertDiv.style.display = 'block';
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerText = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const patientCounter = document.getElementById('patientCounter');
            const appointmentCounter = document.getElementById('appointmentCounter');
            const doctorCounter = document.getElementById('doctorCounter');
            const dischargedPersonCounter = document.getElementById('dischargedPersonCounter');

            animateCounter(patientCounter, 0, <?php echo $patientCount; ?>, 2000);
            animateCounter(appointmentCounter, 0, <?php echo $appointmentCount; ?>, 2000);
            animateCounter(doctorCounter, 0, <?php echo $doctorCount; ?>, 2000);
            animateCounter(dischargedPersonCounter, 0, <?php echo $dischargedPersonCount; ?>, 2000);
        });
    </script>
</head>
<body>
    <div class="full-height">
        <header class="bg-success text-white text-center py-4">
            <h1>Healthcare Management System</h1>
            <p>Welcome!</p>
        </header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Home</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="patients.php">Patients</a></li>
                        <li class="nav-item"><a class="nav-link" href="viewpat.php">View Patients</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="viewapp.php">View Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="insert_doctor.php">Add Doctor</a></li>
                        <li class="nav-item"><a class="nav-link" href="viewdoc.php">View Doctor</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container my-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Statistics</h2>
                    <div class="content-section">
                        Number of Patients: <span id="patientCounter" class="counter"></span>
                    </div>
                    <div class="content-section">
                        Number of Appointments: <span id="appointmentCounter" class="counter"></span>
                    </div>
                    <div class="content-section">
                        Number of Doctors: <span id="doctorCounter" class="counter"></span>
                    </div>
                    <div class="content-section">
                        Number of Discharged Persons: <span id="dischargedPersonCounter" class="counter"></span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Financial Management</h2>
                    <div class="content-section">
                        Financial management in a healthcare management system involves various aspects that ensure smooth and efficient handling of financial operations within a healthcare organization. Here are the key components:
                    </div>
                    <h3 class="mt-4">Billing and Invoicing</h3>
                    <div class="content-section">
                        Manages billing for medical services, generating invoices, and handling payments. This module ensures accurate and timely billing, reducing the chances of errors and improving cash flow.
                    </div>
                    <h3 class="mt-4">Insurance Claims</h3>
                    <div class="content-section">
                        Facilitates the processing of insurance claims and reimbursements. It helps in tracking the status of claims, managing denials, and ensuring that the organization gets reimbursed promptly.
                    </div>
                    <h3 class="mt-4">Financial Reporting</h3>
                    <div class="content-section">
                        Provides financial reports and analytics to monitor revenue, expenses, and profitability. These reports help in making informed decisions and maintaining the financial health of the organization.
                    </div>
                    <h4 class="mt-4">Key Features:</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Automated Billing Processes</li>
                        <li class="list-group-item">Comprehensive Insurance Claims Management</li>
                        <li class="list-group-item">Detailed Financial Reports</li>
                        <li class="list-group-item">Revenue Cycle Management</li>
                        <li class="list-group-item">Expense Tracking</li>
                    </ul>
                    <blockquote class="blockquote mt-4">
                        <p class="mb-0">"Efficient financial management is crucial for the sustainability and growth of any healthcare organization."</p>
                    </blockquote>
                </div>
            </div>
        </main>
        <footer class="bg-dark text-white text-center py-3">
            <p>&copy; 2024 Healthcare Management System</p>
        </footer>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
