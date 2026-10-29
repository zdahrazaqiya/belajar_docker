-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2026 at 09:19 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_coffeeshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `id_kasir` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`id_kasir`, `nama_lengkap`, `no_telp`, `email`, `password`) VALUES
(1, 'Admin', '081234567890', 'admin@gmail.com', '$2y$10$2VNnbWU.L/iPOJElW9VLG.KDnTwtdpaY0fhQmLCXAGvYM5CHk0zpW'),
(2, 'Zdahra', '082176292538', 'zdahra@gmail.com', '$2y$10$T7S2Dp9eylmCk2HorDd6L.kOleHDPoxc9IZH4qGWWpbldnyYZ4JvK'),
(3, 'Aura', '084012345768', 'aura@gmail.com', '$2y$10$O9MXAJ2oav0BYwVYg1u5me.8oZeq/ELfzOJ2HsZlm28USPaIqjbf6');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga_menu` decimal(10,2) NOT NULL,
  `foto_menu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga_menu`, `foto_menu`) VALUES
(4, 'Americano', 10000.00, '1792000533_Esspreso.jpg'),
(5, 'Macha', 15000.00, '1792026065_macha.jpg'),
(6, 'Cookies Chocolate', 7000.00, '1792026195_kukis cokelat.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `id_menu` int NOT NULL,
  `jumlah_pesanan` int NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_kasir` int DEFAULT NULL,
  `status_pesanan` enum('Menunggu Konfirmasi','Selesai') DEFAULT 'Menunggu Konfirmasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_menu`, `jumlah_pesanan`, `nama_pelanggan`, `nomor_meja`, `metode_pembayaran`, `tanggal_transaksi`, `id_kasir`, `status_pesanan`) VALUES
(2, 5, 1, 'Fallah', '3', 'GoPay', '2026-10-15 08:05:48', 3, 'Selesai'),
(3, 6, 1, 'Fallah', '3', 'GoPay', '2026-10-15 08:05:48', 3, 'Selesai'),
(4, 4, 1, 'Pudul', '1', 'ShopeePay', '2026-10-15 08:17:36', 3, 'Selesai'),
(5, 5, 1, 'Pudul', '1', 'ShopeePay', '2026-10-15 08:17:36', 3, 'Selesai'),
(6, 5, 2, 'Peluh', '1', 'ShopeePay', '2026-10-15 08:28:05', 3, 'Selesai'),
(7, 6, 1, 'Peluh', '1', 'ShopeePay', '2026-10-15 08:28:05', 3, 'Selesai'),
(8, 4, 1, 'Muwi', '5', 'Cash', '2026-10-15 08:32:06', 3, 'Selesai'),
(9, 4, 2, 'fr', '8', 'DANA', '2026-10-15 10:18:55', 3, 'Selesai'),
(10, 4, 1, 'Naila', '2', 'GoPay', '2026-10-15 10:34:57', NULL, 'Menunggu Konfirmasi'),
(11, 5, 1, 'Naila', '2', 'GoPay', '2026-10-15 10:34:57', NULL, 'Menunggu Konfirmasi'),
(12, 6, 1, 'Naila', '2', 'GoPay', '2026-10-15 10:34:57', NULL, 'Menunggu Konfirmasi'),
(13, 4, 3, 'rama', '9', 'GoPay', '2026-10-15 10:35:20', NULL, 'Menunggu Konfirmasi'),
(14, 5, 2, 'Pudul', '3', 'DANA', '2026-10-15 11:25:20', NULL, 'Menunggu Konfirmasi'),
(15, 5, 1, 'arnof', '4', 'ShopeePay', '2026-10-17 12:38:23', 3, 'Selesai'),
(16, 6, 2, 'arnof', '4', 'ShopeePay', '2026-10-17 12:38:23', 3, 'Selesai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id_kasir`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `id_kasir` (`id_kasir`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id_kasir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_kasir`) REFERENCES `kasir` (`id_kasir`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
