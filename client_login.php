<?php
session_start();
include "dbConnect.php"; // Ensure this file contains your DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get login credentials from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement to fetch user data by username
    $sql = "SELECT * FROM client_login WHERE username = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();


    // Check if a user with the given username was found
    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
        // Verify the password using bcrypt
        if (password_verify($password, $client['password'])) {
            // Set session variables upon successful login
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['username'] = $client['username'];
            $_SESSION['name'] = $client['name'];
            $_SESSION['loggedin'] = true;
            $_SESSION['subdomain'] = $client['subdomain'];

            // Redirect to the dashboard or desired landing page
            header("Location: index.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Username not found!";
    }

    // Close the statement and connection
    $stmt->close();
}
?>
