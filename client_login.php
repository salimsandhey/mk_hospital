<?php
session_start();
include "dbConnect.php"; // Ensure this file contains your DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get login credentials from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Remember me functionality
    if(isset($_POST['remember'])) {
        // Set cookies for 30 days
        setcookie("remember_username", $username, time() + (86400 * 30), "/");
    } else {
        // If not checked, clear any existing cookies
        if(isset($_COOKIE["remember_username"])) {
            setcookie("remember_username", "", time() - 3600, "/");
        }
    }

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

            // Log the successful login attempt
            $ip = $_SERVER['REMOTE_ADDR'];
            $loginTime = date('Y-m-d H:i:s');
            $logQuery = "INSERT INTO login_logs (user_id, username, ip_address, login_time, status) VALUES (?, ?, ?, ?, 'success')";
            
            // Only log if the login_logs table exists - optional
            if($conn->query("SHOW TABLES LIKE 'login_logs'")->num_rows > 0) {
                $logStmt = $conn->prepare($logQuery);
                $logStmt->bind_param("isss", $client['id'], $username, $ip, $loginTime);
                $logStmt->execute();
                $logStmt->close();
            }

            // Redirect to the dashboard or desired landing page
            header("Location: index.php");
            exit();
        } else {
            // Set error message for invalid password
            $_SESSION['login_error'] = "Invalid password. Please try again.";
            
            // Log the failed login attempt
            $ip = $_SERVER['REMOTE_ADDR'];
            $loginTime = date('Y-m-d H:i:s');
            $logQuery = "INSERT INTO login_logs (user_id, username, ip_address, login_time, status) VALUES (?, ?, ?, ?, 'failed')";
            
            // Only log if the login_logs table exists - optional
            if($conn->query("SHOW TABLES LIKE 'login_logs'")->num_rows > 0) {
                $logStmt = $conn->prepare($logQuery);
                $logStmt->bind_param("isss", $client['id'], $username, $ip, $loginTime);
                $logStmt->execute();
                $logStmt->close();
            }
            
            header("Location: login.php");
            exit();
        }
    } else {
        // Set error message for invalid username
        $_SESSION['login_error'] = "Username not found. Please check your credentials.";
        
        // Log the failed login attempt
        $ip = $_SERVER['REMOTE_ADDR'];
        $loginTime = date('Y-m-d H:i:s');
        $logQuery = "INSERT INTO login_logs (username, ip_address, login_time, status) VALUES (?, ?, ?, 'failed')";
        
        // Only log if the login_logs table exists - optional
        if($conn->query("SHOW TABLES LIKE 'login_logs'")->num_rows > 0) {
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("sss", $username, $ip, $loginTime);
            $logStmt->execute();
            $logStmt->close();
        }
        
        header("Location: login.php");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
} else {
    // If someone tries to access this file directly, redirect to login
    header("Location: login.php");
    exit();
}
?>
