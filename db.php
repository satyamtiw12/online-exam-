<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

try {
    $conn = new PDO(
        "pgsql:host=dpg-d6gad65m5p6s73dupe1g-a.singapore-postgres.render.com;port=5432;dbname=auth_system_0hva",
        "auth_system_0hva_user",
        "VUnue8rx0sqGsS38IDVsLnEotDndICcy"
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>