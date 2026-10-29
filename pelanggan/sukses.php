<?php
include '../config/koneksi.php';

if(!isset($_SESSION['sukses_order_ids']) || empty($_SESSION['sukses_order_ids'])){
    header("Location: index.php");
    exit;
}

$ids_string = implode(',', array_map('intval', $_SESSION['sukses_order_ids']));
$query = mysqli_query($conn, "SELECT p.*, m.nama_menu, m.harga_menu FROM pesanan p JOIN menu m ON p.id_menu = m.id_menu WHERE p.id_pesanan IN ($ids_string)");

$items = [];
$total = 0;
$nama_pelanggan = "";
$nomor_meja = "";
$metode = "";

while($row = mysqli_fetch_assoc($query)){
    $items[] = $row;
    $total += ($row['harga_menu'] * $row['jumlah_pesanan']);
    $nama_pelanggan = $row['nama_pelanggan'];
    $nomor_meja = $row['nomor_meja'];
    $metode = $row['metode_pembayaran'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembelian - Coffeeshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Library untuk mengubah HTML ke Gambar/Download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 500px;">
    
    <!-- Area Kartu Bukti Pembelian yang akan di-download -->
    <div class="card shadow border-0" id="areaBukti">
        <div class="card-body p-4 text-center">
            <div class="text-success mb-3 no-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.018-1.042z"/>
                </svg>
            </div>
            <h3 class="fw-bold">Pesanan Berhasil!</h3>
            <p class="text-muted no-print">Terima kasih, pesanan Anda sedang kami proses.</p>
            <hr>

            <div class="text-start mb-3">
                <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($nama_pelanggan) ?></p>
                <p class="mb-1"><strong>Nomor Meja:</strong> <?= htmlspecialchars($nomor_meja) ?></p>
                <p class="mb-1"><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($metode) ?></p>
            </div>

            <table class="table table-bordered text-start align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th>Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): 
                        $subtotal = $item['harga_menu'] * $item['jumlah_pesanan'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                        <td class="text-center"><?= $item['jumlah_pesanan'] ?></td>
                        <td>Rp <?= number_format($item['harga_menu'], 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th class="text-end text-primary">Rp <?= number_format($total, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
            
            <p class="text-muted small mt-3 mb-0">Coffeeshop Order System</p>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-grid gap-2 mt-3 no-print">
        <button onclick="unduhBukti()" class="btn btn-outline-primary btn-lg shadow-sm">
            Unduh Bukti Pembelian
        </button>
        <a href="index.php" class="btn btn-primary btn-lg shadow-sm">Kembali ke Katalog Menu</a>
    </div>

</div>

<script>
function unduhBukti() {
    // Sembunyikan elemen no-print sejenak sebelum direkam
    var elemen = document.getElementById("areaBukti");
    
    html2canvas(elemen, {
        scale: 2 // Meningkatkan kualitas resolusi gambar
    }).then(function(canvas) {
        // Buat link download gambar
        var link = document.createElement('a');
        link.download = 'Bukti-Pembelian-<?= preg_replace('/[^a-zA-Z0-9]/', '', $nama_pelanggan) ?>.png';
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
}
</script>

</body>
</html>