<?php
include '../config/koneksi.php';

if(!isset($_SESSION['id_kasir'])){
    header("Location: login.php");
    exit;
}

// Tambah Menu
if(isset($_POST['tambah'])){
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_menu']);
    $harga = (float)$_POST['harga_menu'];
    
    $filename = "";
    if(!empty($_FILES['foto_menu']['name'])){
        $dir = "../assets/img/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['foto_menu']['name']);
        move_uploaded_file($_FILES['foto_menu']['tmp_name'], $dir . $filename);
    }

    mysqli_query($conn, "INSERT INTO menu (nama_menu, harga_menu, foto_menu) VALUES ('$nama', '$harga', '$filename')");
    header("Location: menu.php");
    exit;
}

// Hapus Menu
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    
    // 1. Hapus riwayat transaksi di tabel pesanan yang menggunakan menu ini
    mysqli_query($conn, "DELETE FROM pesanan WHERE id_menu='$id'");

    // 2. Hapus file foto fisik jika ada
    $q = mysqli_query($conn, "SELECT foto_menu FROM menu WHERE id_menu='$id'");
    $f = mysqli_fetch_assoc($q);
    if(!empty($f['foto_menu']) && file_exists("../assets/img/" . $f['foto_menu'])){
        unlink("../assets/img/" . $f['foto_menu']);
    }

    // 3. Hapus data dari tabel menu
    mysqli_query($conn, "DELETE FROM menu WHERE id_menu='$id'");
    header("Location: menu.php");
    exit;
}

// Edit Menu
if(isset($_POST['edit'])){
    $id    = (int)$_POST['id_menu'];
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_menu']);
    $harga = (float)$_POST['harga_menu'];

    if(!empty($_FILES['foto_menu']['name'])){
        $dir = "../assets/img/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['foto_menu']['name']);
        move_uploaded_file($_FILES['foto_menu']['tmp_name'], $dir . $filename);
        mysqli_query($conn, "UPDATE menu SET nama_menu='$nama', harga_menu='$harga', foto_menu='$filename' WHERE id_menu='$id'");
    } else {
        mysqli_query($conn, "UPDATE menu SET nama_menu='$nama', harga_menu='$harga' WHERE id_menu='$id'");
    }
    header("Location: menu.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Kasir Coffeeshop Ara-Ara</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link active" href="menu.php">Kelola Menu</a>
            <a class="nav-link" href="riwayat.php">Riwayat Transaksi</a>
            <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Menu Baru</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Menu</label>
                            <input type="text" name="nama_menu" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Menu (Rp)</label>
                            <input type="number" name="harga_menu" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Menu</label>
                            <input type="file" name="foto_menu" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-success w-100">Simpan Menu</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Daftar Menu Coffeeshop</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM menu ORDER BY id_menu DESC");
                            while($row = mysqli_fetch_assoc($query)):
                                // Cek ketersediaan file foto fisik
                                $path_foto = "../assets/img/" . $row['foto_menu'];
                                if(!empty($row['foto_menu']) && file_exists($path_foto)){
                                    $foto = $path_foto;
                                } else {
                                    $foto = "https://via.placeholder.com/80?text=No+Img";
                                }
                            ?>
                            <tr>
                                <td><img src="<?= $foto ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border"></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama_menu']) ?></td>
                                <td>Rp <?= number_format($row['harga_menu'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_menu'] ?>">Edit</button>
                                    <a href="menu.php?hapus=<?= $row['id_menu'] ?>" onclick="return confirm('Hapus menu ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $row['id_menu'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Menu</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Menu</label>
                                                    <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($row['nama_menu']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Harga Menu</label>
                                                    <input type="number" name="harga_menu" class="form-control" value="<?= $row['harga_menu'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Foto Menu (Upload jika ingin mengganti)</label>
                                                    <input type="file" name="foto_menu" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>