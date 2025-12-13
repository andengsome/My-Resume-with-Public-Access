<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "✅ Found $count user(s) in database";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>