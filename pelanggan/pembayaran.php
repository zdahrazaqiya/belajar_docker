<?php 
include '../config/koneksi.php'; 

// Jika form dikirim dari index.php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qty'])){
    $ada_pesanan = false;
    foreach($_POST['qty'] as $id_menu => $qty){
        if((int)$qty > 0){
            $ada_pesanan = true;
            break;
        }
    }

    if($ada_pesanan){
        $_SESSION['pesanan_temp'] = $_POST;
    } else {
        echo "<script>alert('Anda belum menentukan jumlah pesanan! Isi minimal 1 item.'); window.location.href='index.php';</script>";
        exit;
    }
}

// Validasi jika session tidak ada
if(!isset($_SESSION['pesanan_temp']) || empty($_SESSION['pesanan_temp'])) {
    echo "<script>alert('Belum ada pesanan! Silakan pilih menu terlebih dahulu.'); window.location.href='index.php';</script>";
    exit;
}

$pesanan = $_SESSION['pesanan_temp'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Review & Pembayaran Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Review & Pembayaran</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <p class="mb-1"><strong>Nama Pemesan:</strong> <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></p>
                <p class="mb-0"><strong>Nomor Meja:</strong> <?= htmlspecialchars($pesanan['nomor_meja']) ?></p>
            </div>
            <hr>

            <form action="proses_bayar.php" method="POST">
                <!-- Kirim ulang nama & nomor meja secara hidden -->
                <input type="hidden" name="nama_pelanggan" value="<?= htmlspecialchars($pesanan['nama_pelanggan']) ?>">
                <input type="hidden" name="nomor_meja" value="<?= htmlspecialchars($pesanan['nomor_meja']) ?>">

                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th style="width: 100px;">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach($pesanan['qty'] as $id_menu => $qty){
                            $qty = (int)$qty;
                            if($qty > 0){
                                $id_menu = (int)$id_menu;
                                $query = mysqli_query($conn, "SELECT * FROM menu WHERE id_menu='$id_menu'");
                                $menu = mysqli_fetch_assoc($query);
                                if($menu) {
                                    $subtotal = $menu['harga_menu'] * $qty;
                                    $total += $subtotal;
                                    echo "<tr>
                                            <td>{$menu['nama_menu']}</td>
                                            <td>Rp ".number_format($menu['harga_menu'], 0, ',', '.')."</td>
                                            <td>$qty</td>
                                            <td class='text-end'>Rp ".number_format($subtotal, 0, ',', '.')."</td>
                                          </tr>";
                                    
                                    // Hidden input untuk qty menu yang dibeli
                                    echo "<input type='hidden' name='qty[$id_menu]' value='$qty'>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center my-4 p-3 bg-light rounded">
                    <h5 class="mb-0">Total Pembayaran:</h5>
                    <h3 class="mb-0 text-primary fw-bold">Rp <?= number_format($total, 0, ',', '.') ?></h3>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Metode Pembayaran E-Wallet</label>
                    <select name="metode_pembayaran" class="form-select" required>
                        <option value="" disabled selected>-- Pilih E-Wallet --</option>
                        <option value="DANA">DANA</option>
                        <option value="GoPay">GoPay</option>
                        <option value="ShopeePay">ShopeePay</option>
                    </select>
                </div>

                <button type="submit" name="proses_simpan" class="btn btn-success btn-lg w-100">Bayar Pesanan</button>
                <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Ubah Pesanan</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>