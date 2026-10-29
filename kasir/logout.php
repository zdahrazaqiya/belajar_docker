<?php
include '../config/koneksi.php';
session_destroy();
header("Location: login.php");
exit;
?>