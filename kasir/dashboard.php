<?php 
include '../config/koneksi.php';

// Pengecekan session login kasir
if(!isset($_SESSION['id_kasir'])){
    header("Location: login.php");
    exit;
}

$id_kasir_login = $_SESSION['id_kasir'];

// 1. Proses Konfirmasi Pesanan Online (1x Klik Konfirmasi per Transaksi Pelanggan)
if(isset($_GET['konfirmasi_nama']) && isset($_GET['konfirmasi_meja'])){
    $nama_c = mysqli_real_escape_string($conn, $_GET['konfirmasi_nama']);
    $meja_c = mysqli_real_escape_string($conn, $_GET['konfirmasi_meja']);
    
    // Update seluruh item menu milik pelanggan tersebut sekaligus
    mysqli_query($conn, "UPDATE pesanan SET id_kasir='$id_kasir_login', status_pesanan='Selesai' 
                         WHERE nama_pelanggan='$nama_c' AND nomor_meja='$meja_c' AND id_kasir IS NULL");
    header("Location: dashboard.php");
    exit;
}

// 2. Proses Pemesanan Langsung oleh Kasir (Walk-In)
if(isset($_POST['pesan_langsung'])){
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $meja   = mysqli_real_escape_string($conn, $_POST['nomor_meja']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);

    if(isset($_POST['qty']) && is_array($_POST['qty'])){
        foreach($_POST['qty'] as $id_menu => $qty){
            $qty = (int)$qty;
            if($qty > 0){
                $id_menu = (int)$id_menu;
                mysqli_query($conn, "INSERT INTO pesanan (id_menu, jumlah_pesanan, nama_pelanggan, nomor_meja, metode_pembayaran, id_kasir, status_pesanan) 
                                     VALUES ('$id_menu', '$qty', '$nama', '$meja', '$metode', '$id_kasir_login', 'Selesai')");
            }
        }
    }
    echo "<script>alert('Pesanan langsung berhasil diproses!'); window.location.href='dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Coffeeshop Ara-Ara</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar Kasir -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Kasir Coffeeshop Ara-Ara</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="menu.php">Kelola Menu</a>
            <a class="nav-link" href="riwayat.php">Riwayat Transaksi</a>
            <a class="nav-link text-danger ms-3" href="logout.php">Logout (<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Kasir') ?>)</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row">
        <!-- Kolom Kiri: Input Pesanan Langsung (Walk-In) -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pesanan Langsung</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Meja</label>
                                <input type="text" name="nomor_meja" class="form-control" placeholder="Contoh: 01" required>
                            </div>
                        </div>

                        <label class="form-label fw-bold">Pilih Menu:</label>
                        <div class="table-responsive mb-3" style="max-height: 280px;">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Menu</th>
                                        <th>Harga</th>
                                        <th style="width: 100px;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $m_query = mysqli_query($conn, "SELECT * FROM menu ORDER BY nama_menu ASC");
                                    if(mysqli_num_rows($m_query) > 0){
                                        while($m = mysqli_fetch_assoc($m_query)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['nama_menu']) ?></td>
                                        <td>Rp <?= number_format($m['harga_menu'], 0, ',', '.') ?></td>
                                        <td><input type="number" name="qty[<?= $m['id_menu'] ?>]" class="form-control form-control-sm" value="0" min="0"></td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center text-muted py-2'>Belum ada menu di database.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select" required>
                                <option value="Cash">Cash</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>

                        <button type="submit" name="pesan_langsung" class="btn btn-success w-100">Proses Pesanan Langsung</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Antrean Pesanan Online (Self-Order Meja) -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Antrean Order</h5>
                    <span class="badge bg-dark">Web Pelanggan</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php
                        // Grouping pesanan per transaksi/pelanggan
                        $q_antrean = mysqli_query($conn, "SELECT 
                                                            p.nama_pelanggan, 
                                                            p.nomor_meja, 
                                                            p.metode_pembayaran, 
                                                            MAX(p.tanggal_transaksi) as tgl_transaksi,
                                                            GROUP_CONCAT(CONCAT(m.nama_menu, ' (x', p.jumlah_pesanan, ')') SEPARATOR ', ') AS rincian_menu,
                                                            SUM(m.harga_menu * p.jumlah_pesanan) AS total_bayar
                                                          FROM pesanan p 
                                                          JOIN menu m ON p.id_menu = m.id_menu 
                                                          WHERE p.id_kasir IS NULL AND p.status_pesanan = 'Menunggu Konfirmasi' 
                                                          GROUP BY p.nama_pelanggan, p.nomor_meja, p.metode_pembayaran
                                                          ORDER BY tgl_transaksi ASC");
                        
                        if(mysqli_num_rows($q_antrean) == 0){
                            echo "<li class='list-group-item text-center text-muted py-4'>Tidak ada antrean pesanan online</li>";
                        } else {
                            while($antrean = mysqli_fetch_assoc($q_antrean)){
                        ?>
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="h6 mb-0"><?= htmlspecialchars($antrean['nama_pelanggan']) ?></strong> 
                                    <span class="badge bg-secondary ms-1">Meja <?= htmlspecialchars($antrean['nomor_meja']) ?></span>
                                </div>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($antrean['metode_pembayaran']) ?></span>
                            </div>
                            <div class="small text-muted mb-2">
                                <strong>Pesanan:</strong> <?= htmlspecialchars($antrean['rincian_menu']) ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success">Total: Rp <?= number_format($antrean['total_bayar'], 0, ',', '.') ?></span>
                                <a href="dashboard.php?konfirmasi_nama=<?= urlencode($antrean['nama_pelanggan']) ?>&konfirmasi_meja=<?= urlencode($antrean['nomor_meja']) ?>" 
                                   class="btn btn-sm btn-success px-3" 
                                   onclick="return confirm('Konfirmasi pesanan ini?')">Konfirmasi</a>
                            </div>
                        </li>
                        <?php 
                            }
                        } 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>