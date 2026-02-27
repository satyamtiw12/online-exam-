<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

try {

    $conn = new PDO(
        "pgsql:host=" . getenv('DB_HOST') .
        ";port=" . getenv('DB_PORT') .
        ";dbname=" . getenv('DB_NAME'),
        getenv('DB_USER'),
        getenv('DB_PASS')
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected Successfully 🚀";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
