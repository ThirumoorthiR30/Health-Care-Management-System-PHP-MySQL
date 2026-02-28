<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'healthcare_system','3310');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Prepare the DELETE statement
    $stmt = $mysqli->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the view patients page with a success message
        $_SESSION['message'] = "Patient record has been deleted successfully.";
        $_SESSION['msg_type'] = "success";
        header("Location: viewpat.php");
    } else {
        // Redirect back with an error message
        $_SESSION['message'] = "Failed to delete the patient record.";
        $_SESSION['msg_type'] = "danger";
        header("Location: viewpat.php");
    }

    // Close the statement
    $stmt->close();
} else {
    // Redirect back with an error message if ID is not set
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['msg_type'] = "danger";
    header("Location: viewpat.php");
}

// Close the connection
$mysqli->close();
?>
