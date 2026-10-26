<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "museum_ganesya";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal. Pastikan MySQL aktif dan database museum_ganesya tersedia.");
}

mysqli_set_charset($conn, "utf8mb4");
?>
