<?php
// register.php

session_start();
require_once 'config.php';

$message = '';
$message_type = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($name) || empty($password)) {
        $message = 'All fields are required!';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match!';
        $message_type = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters!';
        $message_type = 'error';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Check if username exists
            $stmt = $pdo->prepare("SELECT uid FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = 'Username already exists!';
                $message_type = 'error';
            } else {
                // Insert new user
                $hashed_password = hashPassword($password);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, textpass) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $password]);

                $user_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO personal_info (user_id, name, email) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $name, $email]);
                
                $message = 'Registration successful! Please login.';
                $message_type = 'success';
                
                // Redirect after 2 seconds
                header("refresh:2;url=login.php");
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
    <title>Register - Portfolio System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #9676e7ff 0%, #654fc6ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 750px;
        }
        .register-header { text-align: center; margin-bottom: 30px; }
        .register-header h1 { color: #654fc6ff; font-size: 2.2rem; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
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
            border-color: #4f4fc6ff;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(182, 85, 218, 0.1);
        }
        .register-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg,   #9676e7ff 0%, #654fc6ff 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(182, 85, 218, 0.3);
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
        .login-link a {
            color: #654fc6ff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>📝 Register</h1>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required
                       placeholder="Enter your full name"
                       value="<?php echo $_POST['name'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required
                       placeholder="Enter your username"
                       value="<?php echo $_POST['username'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required
                       placeholder="Enter your email"
                       value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required
                       placeholder="Enter your password"
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       placeholder="Confirm your password">
            </div>

            <button type="submit" class="register-btn">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>