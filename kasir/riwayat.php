<?php
include '../config/koneksi.php';

if(!isset($_SESSION['id_kasir'])){
    header("Location: login.php");
    exit;
}

$search = "";
$where_search = "";
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_search = " WHERE p.nama_pelanggan LIKE '%$search%' OR p.id_pesanan LIKE '%$search%' ";
}

// Query grouping agar 1 transaksi pelanggan menjadi 1 baris di tabel
$query = mysqli_query($conn, "SELECT 
                                MIN(p.id_pesanan) AS id_pesanan,
                                p.nama_pelanggan, 
                                p.nomor_meja, 
                                p.metode_pembayaran, 
                                p.tanggal_transaksi, 
                                k.nama_lengkap AS nama_kasir,
                                GROUP_CONCAT(CONCAT(m.nama_menu, ' x', p.jumlah_pesanan) SEPARATOR '<br>') AS rincian_menu,
                                SUM(p.jumlah_pesanan) AS total_qty,
                                SUM(m.harga_menu * p.jumlah_pesanan) AS total_transaksi
                              FROM pesanan p 
                              JOIN menu m ON p.id_menu = m.id_menu 
                              LEFT JOIN kasir k ON p.id_kasir = k.id_kasir 
                              $where_search
                              GROUP BY p.nama_pelanggan, p.nomor_meja, p.tanggal_transaksi, p.metode_pembayaran, k.nama_lengkap
                              ORDER BY p.tanggal_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar Kasir -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Kasir Coffeeshop Ara-Ara</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="menu.php">Kelola Menu</a>
            <a class="nav-link active" href="riwayat.php">Riwayat Transaksi</a>
            <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Form Pencarian -->
    <div class="card shadow-sm no-print mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan ID Pesanan atau Nama Pelanggan..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat Transaksi -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Riwayat Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Kasir</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)):
                    ?>
                    <tr>
                        <td>#<?= $row['id_pesanan'] ?></td>
                        <td><small><?= $row['tanggal_transaksi'] ?></small></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                        <td><?= $row['nomor_meja'] ?></td>
                        <td><?= $row['rincian_menu'] ?></td>
                        <td><?= $row['total_qty'] ?></td>
                        <td>Rp <?= number_format($row['total_transaksi'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-info text-dark"><?= $row['metode_pembayaran'] ?></span></td>
                        <td><?= $row['nama_kasir'] ? $row['nama_kasir'] : '<span class="text-muted">Online</span>' ?></td>
                        <td class="no-print">
                            <button onclick="cetakStruk('<?= $row['id_pesanan'] ?>', '<?= $row['tanggal_transaksi'] ?>', '<?= addslashes($row['nama_pelanggan']) ?>', '<?= $row['nomor_meja'] ?>', '<?= addslashes(str_replace('<br>', ', ', $row['rincian_menu'])) ?>', '<?= $row['total_qty'] ?>', '<?= $row['total_transaksi'] ?>', '<?= $row['metode_pembayaran'] ?>', '<?= addslashes($row['nama_kasir'] ?? 'Online') ?>')" class="btn btn-sm btn-outline-secondary">Cetak Struk</button>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr><td colspan="10" class="text-center py-3">Tidak ada riwayat transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Template Modal / Area Struk yang Dicetak -->
<div id="strukModal" class="print-area d-none">
    <div style="max-width: 300px; font-family: monospace; padding: 15px; border: 1px solid #000; margin: auto;">
        <h4 style="text-align: center; margin-bottom: 5px;">COFFEESHOP ARA-ARA</h4>
        <p style="text-align: center; font-size: 12px; margin-bottom: 10px;">Struk Pembelian</p>
        <hr>
        <p style="font-size: 12px; margin: 2px 0;">ID: <span id="st_id"></span></p>
        <p style="font-size: 12px; margin: 2px 0;">Tgl: <span id="st_tgl"></span></p>
        <p style="font-size: 12px; margin: 2px 0;">Pelanggan: <span id="st_nama"></span> (Meja <span id="st_meja"></span>)</p>
        <p style="font-size: 12px; margin: 2px 0;">Kasir: <span id="st_kasir"></span></p>
        <hr>
        <p style="font-size: 12px; margin: 2px 0;"><strong>Pesanan:</strong><br><span id="st_menu"></span></p>
        <hr>
        <p style="font-size: 12px; font-weight: bold; text-align: right; margin: 2px 0;">Total: Rp <span id="st_total"></span></p>
        <p style="font-size: 12px; text-align: right; margin: 2px 0;">Bayar: <span id="st_metode"></span></p>
        <hr>
        <p style="text-align: center; font-size: 11px; margin-top: 10px;">Terima Kasih Atas Kunjungan Anda!</p>
    </div>
</div>

<script>
function cetakStruk(id, tgl, nama, meja, menu, qty, total, metode, kasir) {
    document.getElementById('st_id').innerText = '#' + id;
    document.getElementById('st_tgl').innerText = tgl;
    document.getElementById('st_nama').innerText = nama;
    document.getElementById('st_meja').innerText = meja;
    document.getElementById('st_menu').innerText = menu;
    document.getElementById('st_total').innerText = parseInt(total).toLocaleString('id-ID');
    document.getElementById('st_metode').innerText = metode;
    document.getElementById('st_kasir').innerText = kasir;

    var modalArea = document.getElementById('strukModal');
    modalArea.classList.remove('d-none');
    window.print();
    modalArea.classList.add('d-none');
}
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>