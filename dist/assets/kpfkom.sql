-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 30, 2024 at 03:34 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kpfkom`
--
CREATE DATABASE IF NOT EXISTS `kpfkom` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `kpfkom`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `nama` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Admin Kerja Praktek', 'adminkp@uniku.ac.id', 'fkomjuara', 'admin'),
(2, 'Staff Umum', 'staffumum@uniku.ac.id', 'fkomjuara', 'staff_umum'),
(3, 'Staff Keuangan', 'staffkeu@uniku.ac.id', 'fkomjuara', 'staff_keu');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `nik` varchar(15) NOT NULL,
  `nama_dosen` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`nik`, `nama_dosen`) VALUES
('12345678901', 'Dr. John Doe'),
('12345678902', 'Prof. Jane Smith'),
('12345678903', 'Dr. Alan Turing'),
('12345678904', 'Prof. Mary Johnson'),
('12345678905', 'Dr. Richard Feynman');

-- --------------------------------------------------------

--
-- Table structure for table `kelompok`
--

CREATE TABLE `kelompok` (
  `no_kelompok` int NOT NULL,
  `judul_kp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpconnection`
--

CREATE TABLE `kpconnection` (
  `id` int NOT NULL,
  `no_kelompok` int DEFAULT NULL,
  `nim` varchar(15) DEFAULT NULL,
  `nik` varchar(15) DEFAULT NULL,
  `id_mitra` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jk` varchar(5) NOT NULL,
  `prodi` varchar(5) NOT NULL,
  `angkatan` int NOT NULL,
  `kelas` varchar(5) NOT NULL,
  `mbkm` int NOT NULL,
  `jaket` varchar(5) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `file_upload` varchar(50) DEFAULT NULL,
  `status_validasi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `email`, `password`, `nama`, `jk`, `prodi`, `angkatan`, `kelas`, `mbkm`, `jaket`, `status`, `file_upload`, `status_validasi`) VALUES
('20210810001', '20210810001@uniku.ac.id', 'password01', 'Mahasiswa 01', 'L', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810002', '20210810002@uniku.ac.id', 'password02', 'Mahasiswa 02', 'P', 'TI', 2021, '02', 1, 'L', 'anggota', '', 0),
('20210810003', '20210810003@uniku.ac.id', 'password03', 'Mahasiswa 03', 'L', 'TI', 2021, '03', 0, 'XL', 'anggota', '', 0),
('20210810004', '20210810004@uniku.ac.id', 'password04', 'Mahasiswa 04', 'P', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810005', '20210810005@uniku.ac.id', 'password05', 'Mahasiswa 05', 'L', 'TI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210810006', '20210810006@uniku.ac.id', 'password06', 'Mahasiswa 06', 'P', 'TI', 2021, '03', 1, 'L', 'anggota', '', 0),
('20210810007', '20210810007@uniku.ac.id', 'password07', 'Mahasiswa 07', 'L', 'TI', 2021, '01', 0, 'XL', 'anggota', '', 0),
('20210810008', '20210810008@uniku.ac.id', 'password08', 'Mahasiswa 08', 'P', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810009', '20210810009@uniku.ac.id', 'password09', 'Mahasiswa 09', 'L', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810010', '20210810010@uniku.ac.id', 'password10', 'Mahasiswa 10', 'P', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810011', '20210810011@uniku.ac.id', 'password11', 'Mahasiswa 11', 'L', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810012', '20210810012@uniku.ac.id', 'password12', 'Mahasiswa 12', 'P', 'TI', 2021, '04', 1, 'XL', 'anggota', '', 0),
('20210810013', '20210810013@uniku.ac.id', 'password13', 'Mahasiswa 13', 'L', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810014', '20210810014@uniku.ac.id', 'password14', 'Mahasiswa 14', 'P', 'TI', 2021, '02', 1, 'L', 'anggota', '', 0),
('20210810015', '20210810015@uniku.ac.id', 'password15', 'Mahasiswa 15', 'L', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810016', '20210810016@uniku.ac.id', 'password16', 'Mahasiswa 16', 'P', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810017', '20210810017@uniku.ac.id', 'password17', 'Mahasiswa 17', 'L', 'TI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210810018', '20210810018@uniku.ac.id', 'password18', 'Mahasiswa 18', 'P', 'TI', 2021, '05', 1, 'M', 'anggota', '', 0),
('20210810019', '20210810019@uniku.ac.id', 'password19', 'Mahasiswa 19', 'L', 'TI', 2021, '01', 0, 'L', 'anggota', '', 0),
('20210810020', '20210810020@uniku.ac.id', 'password20', 'Mahasiswa 20', 'P', 'TI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210810081', '20210810081@uniku.ac.id', '$2y$10$emHOfqdfVSSLcHf9EPNr8OTdOS4qO2IdCYDziWreAkFcd.xCMAO82', 'Adra Zulfi Alfauzi', 'L', 'TI', 2021, '01', 1, 'XL', 'anggota', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` int NOT NULL,
  `nama_mitra` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `kelompok`
--
ALTER TABLE `kelompok`
  ADD PRIMARY KEY (`no_kelompok`);

--
-- Indexes for table `kpconnection`
--
ALTER TABLE `kpconnection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_kelompok` (`no_kelompok`,`nim`,`nik`,`id_mitra`),
  ADD KEY `id_mitra` (`id_mitra`),
  ADD KEY `nim` (`nim`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kpconnection`
--
ALTER TABLE `kpconnection`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id_mitra` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kpconnection`
--
ALTER TABLE `kpconnection`
  ADD CONSTRAINT `kpconnection_ibfk_2` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id_mitra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kpconnection_ibfk_3` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kpconnection_ibfk_4` FOREIGN KEY (`nik`) REFERENCES `dosen` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kpconnection_ibfk_5` FOREIGN KEY (`no_kelompok`) REFERENCES `kelompok` (`no_kelompok`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
