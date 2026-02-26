


<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

try {
    // Database connection using environment variables
    $conn = new PDO(
        "pgsql:host=" . getenv('dpg-d6gad65m5p6s73dupe1g-a.singapore-postgres.render.com') . ";port=" . getenv('5432') . ";dbname=" . getenv('auth_system_0hva'),
        getenv('auth_system_0hva_user'),
        getenv('VUnue8rx0sqGsS38IDVsLnEotDndICcy')
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
