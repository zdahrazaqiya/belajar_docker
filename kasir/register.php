<?php
include '../config/koneksi.php';

$pesan = "";
if(isset($_POST['register'])){
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $no_telp  = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT * FROM kasir WHERE email='$email'");
    if(mysqli_num_rows($cek) > 0){
        $pesan = "<div class='alert alert-danger'>Email sudah terdaftar!</div>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO kasir (nama_lengkap, no_telp, email, password) VALUES ('$nama', '$no_telp', '$email', '$password')");
        if($insert){
            $pesan = "<div class='alert alert-success'>Registrasi berhasil! <a href='login.php'>Login disini</a></div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal mendaftar!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Kasir Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 450px;">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">Registrasi Kasir</h4>
        </div>
        <div class="card-body p-4">
            <?= $pesan ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">Daftar Akun</button>
            </form>
            <div class="text-center mt-3">
                <small>Sudah punya akun? <a href="login.php">Login di sini</a></small>
            </div>
        </div>
    </div>
</div>
</body>
</html>