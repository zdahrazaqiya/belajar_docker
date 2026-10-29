<?php
include '../config/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_simpan'])){
    $metode = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $meja   = mysqli_real_escape_string($conn, $_POST['nomor_meja']);

    $inserted_ids = [];

    if(isset($_POST['qty']) && is_array($_POST['qty'])){
        foreach($_POST['qty'] as $id_menu => $qty){
            $qty = (int)$qty;
            if($qty > 0){
                $id_menu = (int)$id_menu;
                
                // Simpan ke database dengan id_kasir = NULL (Menandakan Pesanan Online)
                $query = "INSERT INTO pesanan (id_menu, jumlah_pesanan, nama_pelanggan, nomor_meja, metode_pembayaran, id_kasir) 
                          VALUES ('$id_menu', '$qty', '$nama', '$meja', '$metode', NULL)";
                
                if(mysqli_query($conn, $query)){
                    $inserted_ids[] = mysqli_insert_id($conn);
                }
            }
        }
    }

    if(!empty($inserted_ids)){
        unset($_SESSION['pesanan_temp']);
        $_SESSION['sukses_order_ids'] = $inserted_ids;
        header("Location: sukses.php");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan pesanan! Silakan coba lagi.'); window.location.href='index.php';</script>";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>