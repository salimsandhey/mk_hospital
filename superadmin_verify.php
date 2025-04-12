<?php
// Start output buffering to prevent any output before headers
ob_start();
session_start();
include "dbConnect.php";

// For debugging
$debug = [];

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // For debugging
    $debug['username'] = $username;
    $debug['password'] = $password;
    
    // Simple direct query without prepared statement for debugging
    $query = "SELECT * FROM client_login WHERE username = '$username' AND role = 'superadmin'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        $debug['query_error'] = mysqli_error($conn);
    } else {
        $debug['rows_found'] = mysqli_num_rows($result);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $debug['found_user'] = $row;
            
            // Direct password comparison
            if ($password == $row['password']) {
                // Successfully authenticated
                $_SESSION['super_admin_verified'] = true;
                $_SESSION['super_admin_verify_time'] = time();
                
                // Store the subdomain if available
                if (!empty($row['subdomain'])) {
                    $_SESSION['super_admin_subdomain'] = $row['subdomain'];
                }
                
                // Determine where to redirect
                $redirect = isset($_SESSION['requested_page']) ? $_SESSION['requested_page'] : 'misDashboard.php';
                $debug['redirect_to'] = $redirect;
                
                // Complete the output buffer and clear it
                ob_end_clean();
                
                // Redirect and exit
                header("Location: $redirect");
                exit();
            } else {
                $debug['password_match'] = false;
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}

// Store the requesting page in session if it's set
if (isset($_GET['page'])) {
    $_SESSION['requested_page'] = $_GET['page'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Verification</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        .verify-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .verify-title {
            text-align: center;
            color: #343c92;
            margin-bottom: 30px;
        }
        .debug-info {
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
            max-height: 400px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="verify-container">
            <h2 class="verify-title">Super Admin Verification</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn custom-btn">Verify Access</button>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none me-3">Back to Home</a>
                    <a href="superadmin_logout.php" class="text-decoration-none text-danger">Cancel</a>
                </div>
            </form>
            
            <?php if (!empty($debug)): ?>
                <div class="debug-info">
                    <h5>Debug Information</h5>
                    <pre><?php print_r($debug); ?></pre>
                    <h5>Session</h5>
                    <pre><?php print_r($_SESSION); ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?> 