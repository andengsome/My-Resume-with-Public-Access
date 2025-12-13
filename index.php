<?php
try {
    $dsn = "pgsql:host=localhost;port=5432;dbname=resume;";
    $username = "postgres";
    $password = "psql08";

    $pdo = new PDO($dsn, $username, $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Connected to the PostgreSQL database successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>