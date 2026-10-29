<?php
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'coffeeshop';
$pass = getenv('DB_PASSWORD') ?: 'coffeeshop_secret';
$db   = getenv('DB_NAME') ?: 'db_coffeeshop';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
session_start();
?>