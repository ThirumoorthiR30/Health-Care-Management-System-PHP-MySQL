<?php
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system'.'3310');

// Fetch the details of the newly inserted doctor
if (isset($_GET['id'])) {
    $doctor_id = $mysqli->real_escape_string($_GET['id']);
    $result = $mysqli->query("SELECT * FROM doctors WHERE id = '$doctor_id'");
    $new_doctor = $result->fetch_assoc();
}

// Fetch all doctors' details
$result_all_doctors = $mysqli->query("SELECT * FROM doctors");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Doctor Details</title>
</head>
<body>
    <h1>Doctor Details</h1>
    
    <?php if (isset($new_doctor)): ?>
        <h2>New Doctor Added</h2>
        <p><strong>Name:</strong> <?php echo $new_doctor['name']; ?></p>
        <p><strong>Specialization:</strong> <?php echo $new_doctor['specialization']; ?></p>
        <p><strong>Phone:</strong> <?php echo $new_doctor['phone']; ?></p>
        <p><strong>Email:</strong> <?php echo $new_doctor['email']; ?></p>
        <p><strong>User ID:</strong> <?php echo $new_doctor['user_id']; ?></p>
    <?php endif; ?>

    <h2>All Doctors</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Specialization</th>
            <th>Phone</th>
            <th>Email</th>
            <th>User ID</th>
        </tr>
        <?php while($row = $result_all_doctors->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['specialization']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
