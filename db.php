<?php
// 🔹 Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

try {
    // 🔹 Database connection using Render environment variables
    $conn = new PDO(
       "pgsql:host=" . getenv('dpg-d6gad65m5p6s73dupe1g-a') . ";port=" . getenv('5432') . ";dbname=" . getenv('auth_system_0hva'),
        getenv('auth_system_0hva_user'),
        getenv('VUnue8rx0sqGsS38IDVsLnEotDndICcy')
    );

    // 🔹 Throw exceptions on error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // 🔹 If connection fails, stop execution and show message
    die("Database connection failed: " . $e->getMessage());
}
?>

