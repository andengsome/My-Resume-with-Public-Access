<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    echo "<h2>Database Check</h2>";
    
    // Check if users table exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "✅ Users table exists<br>";
    echo "📊 Total users in database: " . $count . "<br><br>";
    
    // Check admin user
    $stmt = $pdo->prepare("SELECT uid, username, textpass FROM users WHERE username = ?");
    $stmt->execute(['andengsome']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Admin user found!<br><br>";
        echo "User ID: " . $user['uid'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Password: " . $user['textpass'] . "<br>";
    } else {
        echo "❌ Admin user NOT found in database!<br>";
        echo "<br><strong>Solution: Run this SQL in pgAdmin:</strong><br><br>";
        echo "<pre>";
        echo "INSERT INTO users (username, password, textpass) VALUES 
('admin', '" . password_hash('1234', PASSWORD_DEFAULT) . "', '1234');";
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
}
?>