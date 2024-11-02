<?php
include 'auth.php';
include "dbConnect.php";

// Get form data
$name = $_POST["name"];
$age = $_POST["age"];
$address = $_POST["address"];
$disease = $_POST["disease"];
$gender = $_POST["gender"];
$contact = $_POST["contact"];

// Prepare SQL query
$sql = "INSERT INTO patient (name, age, address, disease, gender, contact) VALUES ('$name', '$age', '$address', '$disease', '$gender', '$contact')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    // Get the last inserted patient ID
    $last_id = mysqli_insert_id($conn);

    // Redirect to the patient details page using the last inserted ID
    header("Location: patientDetails.php?id=" . $last_id);
    exit(); // Make sure to exit after a header redirect
} else {
    echo "Error saving record: " . mysqli_error($conn); // Provide error details
}
?>
