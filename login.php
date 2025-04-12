<?php
session_start();

// Check if already logged in, redirect to index
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit;
}

// Initialize error message variable
$error_message = "";

// Check if there's an error message from client_login.php
if(isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Flipoo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2E37A4;
            --secondary-color: #F0F4FF;
            --accent-color: #4f57d1;
            --text-color: #2B2D42;
            --light-text: #A0A3B1;
            --danger-color: #FF5E57;
            --success-color: #00C896;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        
        .login-container {
            max-width: 900px;
            width: 100%;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            display: flex;
            min-height: 500px;
        }
        
        .login-image {
            flex: 1;
            background-image: url('https://images.unsplash.com/photo-1584982751601-97dcc096659c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1472&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 2rem;
            color: white;
        }
        
        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(46, 55, 164, 0.8), rgba(0, 0, 0, 0.2));
        }
        
        .image-content {
            position: relative;
            z-index: 1;
        }
        
        .image-content h2 {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }
        
        .image-content p {
            font-weight: 300;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .login-form {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
        }
        
        .form-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .brand-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .brand-logo i {
            font-size: 1.8rem;
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            height: 50px;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.25rem;
            background-color: #f8fafc;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 55, 164, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }
        
        .input-icon {
            position: absolute;
            top: 16px;
            left: 15px;
            color: var(--light-text);
        }
        
        .input-with-icon {
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            top: 16px;
            right: 15px;
            color: var(--light-text);
            cursor: pointer;
        }
        
        .btn-login {
            height: 50px;
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            border: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-danger {
            background-color: rgba(255, 94, 87, 0.1);
            color: var(--danger-color);
        }
        
        .forgot-password {
            display: block;
            text-align: right;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .forgot-password:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-footer {
            margin-top: auto;
            text-align: center;
            font-size: 0.875rem;
            color: var(--light-text);
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 100%;
                border-radius: 0;
                height: 100%;
            }
            
            .login-image {
                height: 200px;
                padding: 1.5rem;
            }
            
            .login-form {
                padding: 1.5rem;
            }
            
            .image-content h2 {
                font-size: 1.5rem;
            }
            
            .image-content p {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-image">
                <div class="image-content">
                    <h2>Welcome to Flipoo</h2>
                    <p>Streamline your medical practice management with our comprehensive patient record system.</p>
                </div>
            </div>
            <div class="login-form">
                <div class="form-header">
                    <div class="brand-logo">
                        <i class="fas fa-heartbeat"></i>
                        <span>Flipoo</span>
                    </div>
                </div>
                
                <h1>Sign in to your account</h1>
                
                <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <form action="client_login.php" method="POST">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control input-with-icon" id="username" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control input-with-icon" id="password" name="password" placeholder="Password" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>
                </form>
                
                <div class="form-footer">
                    <p>&copy; <?php echo date('Y'); ?> Flipoo. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
