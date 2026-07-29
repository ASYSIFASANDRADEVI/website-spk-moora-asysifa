-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 02:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_moora_native`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int(11) NOT NULL,
  `alternatif` varchar(200) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `gambar` text NOT NULL,
  `harga` varchar(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `alternatif`, `nama`, `kategori`, `gambar`, `harga`) VALUES
(2, 'A1', 'Kipas Angin Cosmos', 'Elektronik', '1750135251-kipas.jpg', '200000'),
(3, 'A2', 'Kipas Angin Maspion', 'Elektronik', '1750135259-kipas.jpg', '0'),
(4, 'A3', 'Kipas Angin Miyako', 'Elektronik', '1750135671-kipas.jpg', '0'),
(5, 'A4', 'Kipas Angin Sekai', 'Elektronik', '1750135680-kipas.jpg', '0'),
(6, 'A5', 'Kipas Angin Panasonic', 'Elektronik', '1750135697-kipas.jpg', '0'),
(7, 'A6', 'Detergen rinso', 'Mandi', '1750135733-detergen.jpg', '0'),
(8, 'A7', 'Detergen so klin', 'Mandi', '1750135745-detergen.jpg', '0'),
(9, 'A8', 'Detergen daia', 'Mandi', '1750135757-detergen.jpg', '0'),
(10, 'A9', 'Detergen attack', 'Mandi', '1750135767-detergen.jpg', '0'),
(11, 'A10', 'Detergen boom', 'Mandi', '1750135778-detergen.jpg', '0'),
(12, 'A11', 'Shampoo clear', 'Mandi', '1750135853-shampo.jpg', '0'),
(13, 'A12', 'Shampoo lifebuoy', 'Mandi', '1750135863-shampo.jpg', '0'),
(14, 'A13', 'Shampoo sunsilk', 'Mandi', '1750135874-shampo.jpg', '0'),
(15, 'A14', 'Shampoo rejoice', 'Mandi', '1750135884-shampo.jpg', '0'),
(16, 'A15', 'Shampoo pantene', 'Mandi', '1750135897-shampo.jpg', '0');

-- --------------------------------------------------------

--
-- Table structure for table `data_ulasan`
--

CREATE TABLE `data_ulasan` (
  `id` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `id_alternatif` varchar(11) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `ulasan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hasil`
--

CREATE TABLE `hasil` (
  `id_hasil` int(11) NOT NULL,
  `id_alternatif` int(11) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hasil`
--

INSERT INTO `hasil` (`id_hasil`, `id_alternatif`, `nilai`) VALUES
(1, 2, 0.148794),
(2, 3, 0.12947),
(3, 4, 0.110145),
(4, 5, 0.0211362),
(5, 6, 0.12947),
(6, 7, 0.0687912),
(7, 8, 0.117785),
(8, 9, 0.107441),
(9, 10, 0.0647476),
(10, 11, 0.107441),
(11, 12, 0.107441),
(12, 13, 0.148794),
(13, 14, 0.0701305),
(14, 15, 0.0764317),
(15, 16, 0.110145);

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `type` enum('Benefit','Cost') NOT NULL,
  `bobot` float NOT NULL,
  `ada_pilihan` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `kode_kriteria`, `nama`, `type`, `bobot`, `ada_pilihan`) VALUES
(1, 'C1', 'Harga', 'Cost', 0.3, 1),
(2, 'C2', 'Kualitas', 'Benefit', 0.5, 1),
(3, 'C3', 'Desain', 'Benefit', 0.2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_alternatif` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id_penilaian`, `id_alternatif`, `id_kriteria`, `nilai`) VALUES
(1, 2, 1, 3),
(2, 2, 2, 6),
(3, 2, 3, 11),
(4, 3, 1, 2),
(5, 3, 2, 6),
(6, 3, 3, 11),
(7, 4, 1, 1),
(8, 4, 2, 6),
(9, 4, 3, 11),
(10, 5, 1, 1),
(11, 5, 2, 9),
(12, 5, 3, 11),
(13, 6, 1, 2),
(14, 6, 2, 6),
(15, 6, 3, 11),
(16, 7, 1, 1),
(17, 7, 2, 7),
(18, 7, 3, 12),
(19, 8, 1, 2),
(20, 8, 2, 6),
(21, 8, 3, 12),
(22, 9, 1, 3),
(23, 9, 2, 7),
(24, 9, 3, 12),
(25, 10, 1, 2),
(26, 10, 2, 7),
(27, 10, 3, 14),
(28, 11, 1, 3),
(29, 11, 2, 7),
(30, 11, 3, 12),
(49, 13, 1, 3),
(50, 13, 2, 6),
(51, 13, 3, 11),
(52, 12, 1, 3),
(53, 12, 2, 7),
(54, 12, 3, 12),
(55, 14, 1, 2),
(56, 14, 2, 8),
(57, 14, 3, 11),
(58, 15, 1, 2),
(59, 15, 2, 7),
(60, 15, 3, 13),
(61, 16, 1, 1),
(62, 16, 2, 6),
(63, 16, 3, 11);

-- --------------------------------------------------------

--
-- Table structure for table `sub_kriteria`
--

CREATE TABLE `sub_kriteria` (
  `id_sub_kriteria` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sub_kriteria`
--

INSERT INTO `sub_kriteria` (`id_sub_kriteria`, `id_kriteria`, `nama`, `nilai`) VALUES
(1, 1, 'Sangat Murah [Sangat Baik]', 50),
(2, 1, 'Murah [Baik]', 40),
(3, 1, 'Cukup Murah [Cukup Baik]', 30),
(4, 1, 'Mahal [Buruk]', 20),
(5, 1, 'Sangat Mahal [Sangat Buruk]', 10),
(6, 2, 'Sangat Baik', 50),
(7, 2, 'Baik', 40),
(8, 2, 'Cukup Baik', 30),
(9, 2, 'Buruk', 20),
(10, 2, 'Sangat Buruk', 10),
(11, 3, 'Sangat Menarik [Sangat Baik]', 50),
(12, 3, 'Menarik [Baik]', 40),
(13, 3, 'Satndar [Cukup Baik]', 30),
(14, 3, 'Kurang Menarik [Buruk]', 20),
(15, 3, 'Sangat Tidak Menarik [Sangat Buruk]', 10);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(70) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `email`, `role`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Admin', 'admin@gmail.com', '1'),
(8, 'user', '12dea96fec20593566ab75692c9949596833adc9', 'User', 'user@gmail.com', '2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indexes for table `data_ulasan`
--
ALTER TABLE `data_ulasan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hasil`
--
ALTER TABLE `hasil`
  ADD PRIMARY KEY (`id_hasil`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`);

--
-- Indexes for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD PRIMARY KEY (`id_sub_kriteria`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `data_ulasan`
--
ALTER TABLE `data_ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hasil`
--
ALTER TABLE `hasil`
  MODIFY `id_hasil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id_sub_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
