<?php
include '../config/koneksi.php';

if(isset($_SESSION['id_kasir'])){
    header("Location: dashboard.php");
    exit;
}

$error = "";
if(isset($_POST['login'])){
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM kasir WHERE email='$email'");
    if(mysqli_num_rows($query) === 1){
        $data = mysqli_fetch_assoc($query);
        if(password_verify($password, $data['password']) || $password === $data['password']){
            $_SESSION['id_kasir'] = $data['id_kasir'];
            $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 400px;">
    <div class="card shadow-sm mt-5">
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">Login Kasir</h4>
        </div>
        <div class="card-body p-4">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
            </form>
            <div class="text-center mt-3">
                <small>Belum punya akun? <a href="register.php">Daftar Kasir</a></small>
            </div>
        </div>
    </div>
</div>
</body>
</html>