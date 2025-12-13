<?php
// login.php

session_start();
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = 'All fields are required!';
        $message_type = 'error';
    }
    else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && verifyPassword($password, $user['password'])) {
                $message = 'Login Successful';
                $message_type = 'success';
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['uid'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_time'] = time();
                
                header('Location: dashboard.php'); //Page after successful log in
                exit();
            } else { 
                $message = 'Invalid Username or Password';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Login Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e776daff 0%, #ac4fc6ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #954ec5ff;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b655daff;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(182, 85, 218, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e776daff 0%, #ac4fc6ff 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(182, 85, 218, 0.3);
        }

        .login-btn:active {
            transform: translateY(1px);
        }

        /* Message Styling */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            font-size: 16px;
            animation: slideIn 0.3s ease-out;
        }

        /* Success Message - Green */
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Error Message - Red */
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
        .register-link a {
            color: #954ec5ff;
            text-decoration: none;
            font-weight: 600;
        }

        /* Animation for messages */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Login Form</h1>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       placeholder="Enter your username"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password">
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>

        <div class="register-link">
            New user? <a href="register.php">Register</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
            
            const form = document.querySelector('form');
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            
            form.addEventListener('submit', function(e) {
                username.style.borderColor = '#e1e1e1';
                password.style.borderColor = '#e1e1e1';
                
                let hasError = false;
                
                if (username.value.trim() === '') {
                    username.style.borderColor = '#dc3545';
                    hasError = true;
                }
                
                if (password.value.trim() === '') {
                    password.style.borderColor = '#dc3545';
                    hasError = true;
                }
                
                if (hasError) {
                    if (username.value.trim() === '') {
                        username.focus();
                    } else {
                        password.focus();
                    }
                }
            });
            
            username.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#e1e1e1';
                }
            });
            
            password.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#e1e1e1';
                }
            });
        });
    </script>
</body>
</html>