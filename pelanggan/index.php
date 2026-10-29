<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Menu Coffeeshop Ara-Ara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">Coffeeshop Ara-Ara Menu</h1>
        <p class="text-muted">Pilih menu favoritmu dan pesan langsung dari meja!</p>
    </div>

    <form action="pembayaran.php" method="POST">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Informasi Pemesan</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pemesan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" placeholder="Contoh: Budi" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Meja</label>
                        <input type="text" name="nomor_meja" class="form-control" placeholder="Contoh: 05" required>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3">Daftar Menu</h4>
        <div class="row">
            <?php
            $query = mysqli_query($conn, "SELECT * FROM menu");
            if(mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $foto = !empty($row['foto_menu']) ? "../assets/img/".$row['foto_menu'] : "https://via.placeholder.com/300x200?text=Coffee";
            ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $foto ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= $row['nama_menu'] ?>">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold"><?= $row['nama_menu'] ?></h5>
                            <p class="card-text text-primary fw-semibold">Rp <?= number_format($row['harga_menu'], 0, ',', '.') ?></p>
                        </div>
                        <div class="mt-3">
                            <label class="form-label small text-muted">Jumlah Beli</label>
                            <input type="number" name="qty[<?= $row['id_menu'] ?>]" class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'><div class='alert alert-info'>Belum ada menu yang tersedia di database. Tambahkan menu lewat dashboard kasir terlebih dahulu.</div></div>";
            } 
            ?>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 shadow">Pesan Sekarang</button>
    </form>
</div>
</body>
</html>