<?php
// Database configuration - Use environment variables for production (Vercel/Aiven)
// Use local fallbacks for development (XAMPP)
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db   = getenv('DB_NAME') ?: "tita_perpustakaan";
$port = getenv('DB_PORT') ?: "3306";

// For Aiven, we might need SSL. Aiven usually provides a CA certificate.
// However, many simple setups work without explicit SSL if the server allows it.
// We'll stick to a standard connection first.

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

