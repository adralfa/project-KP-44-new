-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 02, 2025 at 11:52 AM
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
  `judul_kp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelompok`
--

-- --------------------------------------------------------

--
-- Table structure for table `kpconnection`
--

CREATE TABLE `kpconnection` (
  `id` int NOT NULL,
  `no_kelompok` int DEFAULT NULL,
  `nim` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nik` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id_mitra` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kpconnection`
--

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
  `telp` varchar(15) DEFAULT NULL,
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

INSERT INTO `mahasiswa` (`nim`, `email`, `password`, `nama`, `jk`, `telp`, `prodi`, `angkatan`, `kelas`, `mbkm`, `jaket`, `status`, `file_upload`, `status_validasi`) VALUES
('20210810001', '20210810001@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 1', 'L', '081234567001', 'TI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210810002', '20210810002@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 2', 'L', '081234567002', 'TI', 2021, '05', 0, 'XXL', 'anggota', '', 0),
('20210810003', '20210810003@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 3', 'P', '081234567003', 'TI', 2021, '04', 0, 'L', 'anggota', '', 0),
('20210810004', '20210810004@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 4', 'L', '081234567004', 'TI', 2021, '03', 0, 'L', 'anggota', '', 0),
('20210810005', '20210810005@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 5', 'P', '081234567005', 'TI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210810006', '20210810006@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 6', 'P', '081234567006', 'TI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210810007', '20210810007@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 7', 'P', '081234567007', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810008', '20210810008@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 8', 'P', '081234567008', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810009', '20210810009@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 9', 'P', '081234567009', 'TI', 2021, '03', 1, 'XXL', 'anggota', '', 0),
('20210810010', '20210810010@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 10', 'P', '081234567010', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810011', '20210810011@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 11', 'L', '081234567011', 'TI', 2021, '02', 0, 'S', 'anggota', '', 0),
('20210810012', '20210810012@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 12', 'L', '081234567012', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810013', '20210810013@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 13', 'L', '081234567013', 'TI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210810014', '20210810014@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 14', 'L', '081234567014', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810015', '20210810015@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 15', 'P', '081234567015', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810016', '20210810016@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 16', 'L', '081234567016', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810017', '20210810017@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 17', 'P', '081234567017', 'TI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210810018', '20210810018@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 18', 'P', '081234567018', 'TI', 2021, '04', 1, 'XXL', 'anggota', '', 0),
('20210810019', '20210810019@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 19', 'L', '081234567019', 'TI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210810020', '20210810020@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 20', 'L', '081234567020', 'TI', 2021, '02', 0, 'M', 'anggota', '', 0),
('20210810021', '20210810021@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 21', 'L', '081234567021', 'TI', 2021, '03', 0, 'S', 'anggota', '', 0),
('20210810022', '20210810022@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 22', 'P', '081234567022', 'TI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210810023', '20210810023@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 23', 'P', '081234567023', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810024', '20210810024@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 24', 'L', '081234567024', 'TI', 2021, '04', 1, 'XL', 'anggota', '', 0),
('20210810025', '20210810025@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 25', 'P', '081234567025', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810026', '20210810026@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 26', 'L', '081234567026', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810027', '20210810027@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 27', 'L', '081234567027', 'TI', 2021, '01', 1, 'S', 'anggota', '', 0),
('20210810028', '20210810028@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 28', 'P', '081234567028', 'TI', 2021, '05', 0, 'XXL', 'anggota', '', 0),
('20210810029', '20210810029@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 29', 'P', '081234567029', 'TI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210810030', '20210810030@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 30', 'L', '081234567030', 'TI', 2021, '03', 0, 'L', 'anggota', '', 0),
('20210810031', '20210810031@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 31', 'P', '081234567031', 'TI', 2021, '02', 1, 'XL', 'anggota', '', 0),
('20210810032', '20210810032@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 32', 'L', '081234567032', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810033', '20210810033@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 33', 'P', '081234567033', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810034', '20210810034@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 34', 'P', '081234567034', 'TI', 2021, '04', 1, 'XXL', 'anggota', '', 0),
('20210810035', '20210810035@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 35', 'L', '081234567035', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810036', '20210810036@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 36', 'L', '081234567036', 'TI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210810037', '20210810037@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 37', 'P', '081234567037', 'TI', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20210810038', '20210810038@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 38', 'L', '081234567038', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810039', '20210810039@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 39', 'P', '081234567039', 'TI', 2021, '04', 0, 'M', 'anggota', '', 0),
('20210810040', '20210810040@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 40', 'L', '081234567040', 'TI', 2021, '03', 0, 'XL', 'anggota', '', 0),
('20210810041', '20210810041@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 41', 'P', '081234567041', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810042', '20210810042@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 42', 'L', '081234567042', 'TI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210810043', '20210810043@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 43', 'P', '081234567043', 'TI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210810044', '20210810044@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 44', 'L', '081234567044', 'TI', 2021, '04', 0, 'S', 'anggota', '', 0),
('20210810045', '20210810045@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 45', 'P', '081234567045', 'TI', 2021, '03', 1, 'XL', 'anggota', '', 0),
('20210810046', '20210810046@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 46', 'L', '081234567046', 'TI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210810047', '20210810047@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 47', 'P', '081234567047', 'TI', 2021, '01', 1, 'M', 'anggota', '', 0),
('20210810048', '20210810048@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 48', 'P', '081234567048', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810049', '20210810049@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 49', 'L', '081234567049', 'TI', 2021, '04', 0, 'S', 'anggota', '', 0),
('20210810050', '20210810050@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 50', 'P', '081234567050', 'TI', 2021, '03', 1, 'XXL', 'anggota', '', 0),
('20210810051', '20210810051@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 51', 'L', '081234567051', 'TI', 2021, '02', 0, 'M', 'anggota', '', 0),
('20210810052', '20210810052@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 52', 'L', '081234567052', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810053', '20210810053@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 53', 'P', '081234567053', 'TI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210810054', '20210810054@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 54', 'L', '081234567054', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810055', '20210810055@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 55', 'P', '081234567055', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810056', '20210810056@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 56', 'L', '081234567056', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810057', '20210810057@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 57', 'P', '081234567057', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810058', '20210810058@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 58', 'P', '081234567058', 'TI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210810059', '20210810059@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 59', 'L', '081234567059', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210810060', '20210810060@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 60', 'P', '081234567060', 'TI', 2021, '03', 1, 'S', 'anggota', '', 0),
('20210810061', '20210810061@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 61', 'L', '081234567061', 'TI', 2021, '02', 0, 'M', 'anggota', '', 0),
('20210810062', '20210810062@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 62', 'P', '081234567062', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810063', '20210810063@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 63', 'L', '081234567063', 'TI', 2021, '05', 0, 'S', 'anggota', '', 0),
('20210810064', '20210810064@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 64', 'P', '081234567064', 'TI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210810065', '20210810065@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 65', 'L', '081234567065', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810066', '20210810066@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 66', 'P', '081234567066', 'TI', 2021, '02', 1, 'XL', 'anggota', '', 0),
('20210810067', '20210810067@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 67', 'L', '081234567067', 'TI', 2021, '01', 0, 'S', 'anggota', '', 0),
('20210810068', '20210810068@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 68', 'P', '081234567068', 'TI', 2021, '05', 1, 'M', 'anggota', '', 0),
('20210810069', '20210810069@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 69', 'L', '081234567069', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210810070', '20210810070@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 70', 'P', '081234567070', 'TI', 2021, '03', 1, 'L', 'anggota', '', 0),
('20210810071', '20210810071@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 71', 'L', '081234567071', 'TI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210810072', '20210810072@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 72', 'P', '081234567072', 'TI', 2021, '01', 1, 'L', 'anggota', '', 1),
('20210810073', '20210810073@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 73', 'L', '081234567073', 'TI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210810074', '20210810074@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 74', 'P', '081234567074', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810076', '20210810076@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 76', 'P', '081234567076', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810077', '20210810077@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 77', 'L', '081234567077', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810078', '20210810078@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 78', 'P', '081234567078', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810079', '20210810079@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 79', 'L', '081234567079', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210810080', '20210810080@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 80', 'P', '081234567080', 'TI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210810081', '20210810081@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 81', 'L', '081234567081', 'TI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210810082', '20210810082@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 82', 'L', '081234567082', 'TI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210810083', '20210810083@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 83', 'P', '081234567083', 'TI', 2021, '04', 0, 'L', 'anggota', '', 0),
('20210810084', '20210810084@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 84', 'L', '081234567084', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810085', '20210810085@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 85', 'P', '081234567085', 'TI', 2021, '03', 0, 'S', 'anggota', '', 0),
('20210810086', '20210810086@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 86', 'L', '081234567086', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810087', '20210810087@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 87', 'P', '081234567087', 'TI', 2021, '01', 0, 'XL', 'anggota', '', 0),
('20210810088', '20210810088@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 88', 'L', '081234567088', 'TI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210810089', '20210810089@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 89', 'P', '081234567089', 'TI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210810090', '20210810090@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 90', 'L', '081234567090', 'TI', 2021, '03', 1, 'L', 'anggota', '', 0),
('20210810091', '20210810091@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 91', 'P', '081234567091', 'TI', 2021, '01', 0, 'S', 'anggota', '', 1),
('20210810092', '20210810092@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 92', 'L', '081234567092', 'TI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210810093', '20210810093@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 93', 'P', '081234567093', 'TI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210810094', '20210810094@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 94', 'L', '081234567094', 'TI', 2021, '04', 1, 'XXL', 'anggota', '', 0),
('20210810095', '20210810095@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 95', 'P', '081234567095', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810096', '20210810096@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 96', 'L', '081234567096', 'TI', 2021, '05', 0, 'S', 'anggota', '', 0),
('20210810097', '20210810097@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 97', 'P', '081234567097', 'TI', 2021, '01', 1, 'L', 'anggota', '', 1),
('20210810098', '20210810098@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 98', 'L', '081234567098', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210810099', '20210810099@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 99', 'P', '081234567099', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810100', '20210810100@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 100', 'L', '081234567100', 'TI', 2021, '03', 0, 'L', 'anggota', '', 0),
('20210810101', '20210810101@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 101', 'P', '081234567101', 'TI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210810102', '20210810102@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 102', 'L', '081234567102', 'TI', 2021, '02', 0, 'S', 'anggota', '', 0),
('20210810103', '20210810103@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 103', 'P', '081234567103', 'TI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210810104', '20210810104@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 104', 'L', '081234567104', 'TI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210810105', '20210810105@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 105', 'P', '081234567105', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810106', '20210810106@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 106', 'L', '081234567106', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810107', '20210810107@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 107', 'P', '081234567107', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810108', '20210810108@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 108', 'L', '081234567108', 'TI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210810109', '20210810109@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 109', 'P', '081234567109', 'TI', 2021, '04', 0, 'M', 'anggota', '', 0),
('20210810110', '20210810110@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 110', 'L', '081234567110', 'TI', 2021, '01', 1, 'S', 'anggota', '', 1),
('20210810111', '20210810111@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 111', 'P', '081234567111', 'TI', 2021, '05', 1, 'M', 'anggota', '', 0),
('20210810112', '20210810112@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 112', 'L', '081234567112', 'TI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210810113', '20210810113@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 113', 'P', '081234567113', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810114', '20210810114@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 114', 'L', '081234567114', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810115', '20210810115@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 115', 'P', '081234567115', 'TI', 2021, '01', 1, 'M', 'anggota', '', 0),
('20210810116', '20210810116@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 116', 'L', '081234567116', 'TI', 2021, '02', 0, 'XL', 'anggota', '', 0),
('20210810117', '20210810117@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 117', 'P', '081234567117', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810118', '20210810118@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 118', 'L', '081234567118', 'TI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210810119', '20210810119@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 119', 'P', '081234567119', 'TI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210810120', '20210810120@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 120', 'L', '081234567120', 'TI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210810121', '20210810121@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 121', 'P', '081234567121', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810122', '20210810122@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 122', 'L', '081234567122', 'TI', 2021, '02', 0, 'S', 'anggota', '', 0),
('20210810123', '20210810123@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 123', 'P', '081234567123', 'TI', 2021, '03', 1, 'M', 'anggota', '', 0),
('20210810124', '20210810124@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 124', 'L', '081234567124', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210810125', '20210810125@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 125', 'P', '081234567125', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810126', '20210810126@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 126', 'L', '081234567126', 'TI', 2021, '05', 0, 'S', 'anggota', '', 0),
('20210810127', '20210810127@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 127', 'P', '081234567127', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810128', '20210810128@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 128', 'L', '081234567128', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810129', '20210810129@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 129', 'P', '081234567129', 'TI', 2021, '04', 0, 'L', 'anggota', '', 0),
('20210810130', '20210810130@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 130', 'L', '081234567130', 'TI', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20210810131', '20210810131@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 131', 'P', '081234567131', 'TI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210810132', '20210810132@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 132', 'L', '081234567132', 'TI', 2021, '03', 1, 'M', 'anggota', '', 0),
('20210810133', '20210810133@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 133', 'P', '081234567133', 'TI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210810134', '20210810134@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 134', 'L', '081234567134', 'TI', 2021, '05', 0, 'S', 'anggota', '', 0),
('20210810135', '20210810135@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 135', 'P', '081234567135', 'TI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210810136', '20210810136@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 136', 'L', '081234567136', 'TI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210810137', '20210810137@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 137', 'P', '081234567137', 'TI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210810138', '20210810138@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 138', 'L', '081234567138', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810139', '20210810139@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 139', 'P', '081234567139', 'TI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210810140', '20210810140@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 140', 'L', '081234567140', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810141', '20210810141@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 141', 'P', '081234567141', 'TI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210810142', '20210810142@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 142', 'L', '081234567142', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810143', '20210810143@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 143', 'P', '081234567143', 'TI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210810144', '20210810144@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 144', 'L', '081234567144', 'TI', 2021, '05', 0, 'S', 'anggota', '', 0),
('20210810145', '20210810145@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 145', 'P', '081234567145', 'TI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210810146', '20210810146@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 146', 'L', '081234567146', 'TI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210810147', '20210810147@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 147', 'P', '081234567147', 'TI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210810148', '20210810148@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 148', 'L', '081234567148', 'TI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210810149', '20210810149@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 149', 'P', '081234567149', 'TI', 2021, '05', 0, 'XXL', 'anggota', '', 0),
('20210810150', '20210810150@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 150', 'L', '081234567150', 'TI', 2021, '01', 1, 'S', 'anggota', '', 0),
('20210810151', '20210810151@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 151', 'P', '081234567151', 'TI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210810152', '20210810152@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 152', 'L', '081234567152', 'TI', 2021, '03', 1, 'M', 'anggota', '', 0),
('20210810153', '20210810153@uniku.ac.id', 'pass12345', 'Nama Mahasiswa 153', 'P', '081234567153', 'TI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210910001', '20210910001@uniku.ac.id', 'Password20210910001', 'Mahasiswa 154', 'L', '081234567154', 'SI', 2021, '01', 1, 'M', 'anggota', '', 0),
('20210910002', '20210910002@uniku.ac.id', 'Password20210910002', 'Mahasiswa 155', 'P', '081234567155', 'SI', 2021, '01', 0, 'L', 'anggota', '', 0),
('20210910003', '20210910003@uniku.ac.id', 'Password20210910003', 'Mahasiswa 156', 'L', '081234567156', 'SI', 2021, '02', 1, 'XL', 'anggota', '', 0),
('20210910004', '20210910004@uniku.ac.id', 'Password20210910004', 'Mahasiswa 157', 'P', '081234567157', 'SI', 2021, '03', 1, 'XXL', 'anggota', '', 0),
('20210910005', '20210910005@uniku.ac.id', 'Password20210910005', 'Mahasiswa 158', 'L', '081234567158', 'SI', 2021, '03', 0, 'S', 'anggota', '', 0),
('20210910006', '20210910006@uniku.ac.id', 'Password20210910006', 'Mahasiswa 159', 'L', '081234567159', 'SI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210910007', '20210910007@uniku.ac.id', 'Password20210910007', 'Mahasiswa 160', 'P', '081234567160', 'SI', 2021, '04', 0, 'L', 'anggota', '', 0),
('20210910008', '20210910008@uniku.ac.id', 'Password20210910008', 'Mahasiswa 161', 'L', '081234567161', 'SI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210910009', '20210910009@uniku.ac.id', 'Password20210910009', 'Mahasiswa 162', 'P', '081234567162', 'SI', 2021, '05', 0, 'XXL', 'anggota', '', 0),
('20210910010', '20210910010@uniku.ac.id', 'Password20210910010', 'Mahasiswa 163', 'L', '081234567163', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910011', '20210910011@uniku.ac.id', 'Password20210910011', 'Mahasiswa 164', 'L', '081234567164', 'SI', 2021, '01', 1, 'M', 'anggota', '', 0),
('20210910012', '20210910012@uniku.ac.id', 'Password20210910012', 'Mahasiswa 165', 'P', '081234567165', 'SI', 2021, '01', 0, 'L', 'anggota', '', 0),
('20210910013', '20210910013@uniku.ac.id', 'Password20210910013', 'Mahasiswa 166', 'L', '081234567166', 'SI', 2021, '02', 1, 'XL', 'anggota', '', 0),
('20210910014', '20210910014@uniku.ac.id', 'Password20210910014', 'Mahasiswa 167', 'P', '081234567167', 'SI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210910015', '20210910015@uniku.ac.id', 'Password20210910015', 'Mahasiswa 168', 'L', '081234567168', 'SI', 2021, '03', 1, 'S', 'anggota', '', 0),
('20210910016', '20210910016@uniku.ac.id', 'Password20210910016', 'Mahasiswa 169', 'P', '081234567169', 'SI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210910017', '20210910017@uniku.ac.id', 'Password20210910017', 'Mahasiswa 170', 'L', '081234567170', 'SI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210910018', '20210910018@uniku.ac.id', 'Password20210910018', 'Mahasiswa 171', 'P', '081234567171', 'SI', 2021, '04', 0, 'XL', 'anggota', '', 0),
('20210910019', '20210910019@uniku.ac.id', 'Password20210910019', 'Mahasiswa 172', 'L', '081234567172', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910020', '20210910020@uniku.ac.id', 'Password20210910020', 'Mahasiswa 173', 'P', '081234567173', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910021', '20210910021@uniku.ac.id', 'Password20210910021', 'Mahasiswa 174', 'L', '081234567174', 'SI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210910022', '20210910022@uniku.ac.id', 'Password20210910022', 'Mahasiswa 175', 'P', '081234567175', 'SI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210910023', '20210910023@uniku.ac.id', 'Password20210910023', 'Mahasiswa 176', 'L', '081234567176', 'SI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210910024', '20210910024@uniku.ac.id', 'Password20210910024', 'Mahasiswa 177', 'P', '081234567177', 'SI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210910025', '20210910025@uniku.ac.id', 'Password20210910025', 'Mahasiswa 178', 'L', '081234567178', 'SI', 2021, '03', 1, 'S', 'anggota', '', 0),
('20210910026', '20210910026@uniku.ac.id', 'Password20210910026', 'Mahasiswa 179', 'P', '081234567179', 'SI', 2021, '02', 0, 'M', 'anggota', '', 0),
('20210910027', '20210910027@uniku.ac.id', 'Password20210910027', 'Mahasiswa 180', 'L', '081234567180', 'SI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210910028', '20210910028@uniku.ac.id', 'Password20210910028', 'Mahasiswa 181', 'P', '081234567181', 'SI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210910029', '20210910029@uniku.ac.id', 'Password20210910029', 'Mahasiswa 182', 'L', '081234567182', 'SI', 2021, '04', 1, 'XXL', 'anggota', '', 0),
('20210910030', '20210910030@uniku.ac.id', 'Password20210910030', 'Mahasiswa 183', 'L', '081234567183', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910031', '20210910031@uniku.ac.id', 'Password20210910031', 'Mahasiswa 184', 'P', '081234567184', 'SI', 2021, '03', 1, 'M', 'anggota', '', 0),
('20210910032', '20210910032@uniku.ac.id', 'Password20210910032', 'Mahasiswa 185', 'L', '081234567185', 'SI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210910033', '20210910033@uniku.ac.id', 'Password20210910033', 'Mahasiswa 186', 'P', '081234567186', 'SI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210910034', '20210910034@uniku.ac.id', 'Password20210910034', 'Mahasiswa 187', 'L', '081234567187', 'SI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210910035', '20210910035@uniku.ac.id', 'Password20210910035', 'Mahasiswa 188', 'P', '081234567188', 'SI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210910036', '20210910036@uniku.ac.id', 'Password20210910036', 'Mahasiswa 189', 'L', '081234567189', 'SI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210910037', '20210910037@uniku.ac.id', 'Password20210910037', 'Mahasiswa 190', 'P', '081234567190', 'SI', 2021, '06', 0, 'L', 'anggota', '', 0),
('20210910038', '20210910038@uniku.ac.id', 'Password20210910038', 'Mahasiswa 191', 'L', '081234567191', 'SI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210910039', '20210910039@uniku.ac.id', 'Password20210910039', 'Mahasiswa 192', 'P', '081234567192', 'SI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210910040', '20210910040@uniku.ac.id', 'Password20210910040', 'Mahasiswa 193', 'L', '081234567193', 'SI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210910041', '20210910041@uniku.ac.id', 'Password20210910041', 'Mahasiswa 194', 'P', '081234567194', 'SI', 2021, '04', 0, 'M', 'anggota', '', 0),
('20210910042', '20210910042@uniku.ac.id', 'Password20210910042', 'Mahasiswa 195', 'L', '081234567195', 'SI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210910043', '20210910043@uniku.ac.id', 'Password20210910043', 'Mahasiswa 196', 'L', '081234567196', 'SI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210910044', '20210910044@uniku.ac.id', 'Password20210910044', 'Mahasiswa 197', 'P', '081234567197', 'SI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210910045', '20210910045@uniku.ac.id', 'Password20210910045', 'Mahasiswa 198', 'L', '081234567198', 'SI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210910046', '20210910046@uniku.ac.id', 'Password20210910046', 'Mahasiswa 199', 'P', '081234567199', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910047', '20210910047@uniku.ac.id', 'Password20210910047', 'Mahasiswa 200', 'L', '081234567200', 'SI', 2021, '05', 0, 'L', 'anggota', '', 0),
('20210910048', '20210910048@uniku.ac.id', 'Password20210910048', 'Mahasiswa 201', 'P', '081234567201', 'SI', 2021, '04', 1, 'XL', 'anggota', '', 0),
('20210910049', '20210910049@uniku.ac.id', 'Password20210910049', 'Mahasiswa 202', 'L', '081234567202', 'SI', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20210910050', '20210910050@uniku.ac.id', 'Password20210910050', 'Mahasiswa 203', 'P', '081234567203', 'SI', 2021, '03', 0, 'S', 'anggota', '', 0),
('20210910051', '20210910051@uniku.ac.id', 'Password20210910051', 'Mahasiswa 204', 'L', '081234567204', 'SI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210910052', '20210910052@uniku.ac.id', 'Password20210910052', 'Mahasiswa 205', 'P', '081234567205', 'SI', 2021, '04', 0, 'L', 'anggota', '', 0),
('20210910053', '20210910053@uniku.ac.id', 'Password20210910053', 'Mahasiswa 206', 'L', '081234567206', 'SI', 2021, '06', 0, 'XL', 'anggota', '', 0),
('20210910054', '20210910054@uniku.ac.id', 'Password20210910054', 'Mahasiswa 207', 'P', '081234567207', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910055', '20210910055@uniku.ac.id', 'Password20210910055', 'Mahasiswa 208', 'L', '081234567208', 'SI', 2021, '01', 1, 'S', 'anggota', '', 0),
('20210910056', '20210910056@uniku.ac.id', 'Password20210910056', 'Mahasiswa 209', 'P', '081234567209', 'SI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210910057', '20210910057@uniku.ac.id', 'Password20210910057', 'Mahasiswa 210', 'L', '081234567210', 'SI', 2021, '02', 1, 'L', 'anggota', '', 0),
('20210910058', '20210910058@uniku.ac.id', 'Password20210910058', 'Mahasiswa 211', 'P', '081234567211', 'SI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210910059', '20210910059@uniku.ac.id', 'Password20210910059', 'Mahasiswa 212', 'L', '081234567212', 'SI', 2021, '04', 1, 'XXL', 'anggota', '', 0),
('20210910060', '20210910060@uniku.ac.id', 'Password20210910060', 'Mahasiswa 213', 'P', '081234567213', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910061', '20210910061@uniku.ac.id', 'Password20210910061', 'Mahasiswa 214', 'L', '081234567214', 'SI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210910062', '20210910062@uniku.ac.id', 'Password20210910062', 'Mahasiswa 215', 'P', '081234567215', 'SI', 2021, '03', 0, 'L', 'anggota', '', 0),
('20210910063', '20210910063@uniku.ac.id', 'Password20210910063', 'Mahasiswa 216', 'L', '081234567216', 'SI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210910064', '20210910064@uniku.ac.id', 'Password20210910064', 'Mahasiswa 217', 'P', '081234567217', 'SI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210910065', '20210910065@uniku.ac.id', 'Password20210910065', 'Mahasiswa 218', 'L', '081234567218', 'SI', 2021, '01', 1, 'S', 'anggota', '', 0),
('20210910066', '20210910066@uniku.ac.id', 'Password20210910066', 'Mahasiswa 219', 'P', '081234567219', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910067', '20210910067@uniku.ac.id', 'Password20210910067', 'Mahasiswa 220', 'L', '081234567220', 'SI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210910068', '20210910068@uniku.ac.id', 'Password20210910068', 'Mahasiswa 221', 'P', '081234567221', 'SI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210910069', '20210910069@uniku.ac.id', 'Password20210910069', 'Mahasiswa 222', 'L', '081234567222', 'SI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210910070', '20210910070@uniku.ac.id', 'Password20210910070', 'Mahasiswa 223', 'P', '081234567223', 'SI', 2021, '04', 1, 'S', 'anggota', '', 0),
('20210910071', '20210910071@uniku.ac.id', 'Password20210910071', 'Mahasiswa 224', 'L', '081234567224', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910072', '20210910072@uniku.ac.id', 'Password20210910072', 'Mahasiswa 225', 'P', '081234567225', 'SI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210910073', '20210910073@uniku.ac.id', 'Password20210910073', 'Mahasiswa 226', 'L', '081234567226', 'SI', 2021, '05', 0, 'XL', 'anggota', '', 0),
('20210910074', '20210910074@uniku.ac.id', 'Password20210910074', 'Mahasiswa 227', 'P', '081234567227', 'SI', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20210910075', '20210910075@uniku.ac.id', 'Password20210910075', 'Mahasiswa 228', 'L', '081234567228', 'SI', 2021, '03', 0, 'S', 'anggota', '', 0),
('20210910076', '20210910076@uniku.ac.id', 'Password20210910076', 'Mahasiswa 229', 'P', '081234567229', 'SI', 2021, '04', 1, 'M', 'anggota', '', 0),
('20210910077', '20210910077@uniku.ac.id', 'Password20210910077', 'Mahasiswa 230', 'L', '081234567230', 'SI', 2021, '01', 0, 'L', 'anggota', '', 0),
('20210910078', '20210910078@uniku.ac.id', 'Password20210910078', 'Mahasiswa 231', 'P', '081234567231', 'SI', 2021, '06', 0, 'XL', 'anggota', '', 0),
('20210910079', '20210910079@uniku.ac.id', 'Password20210910079', 'Mahasiswa 232', 'L', '081234567232', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910080', '20210910080@uniku.ac.id', 'Password20210910080', 'Mahasiswa 233', 'P', '081234567233', 'SI', 2021, '02', 0, 'S', 'anggota', '', 0),
('20210910081', '20210910081@uniku.ac.id', 'Password20210910081', 'Mahasiswa 234', 'L', '081234567234', 'SI', 2021, '03', 1, 'L', 'anggota', '', 0),
('20210910082', '20210910082@uniku.ac.id', 'Password20210910082', 'Mahasiswa 235', 'L', '081234567235', 'SI', 2021, '04', 0, 'XL', 'anggota', '', 0),
('20210910083', '20210910083@uniku.ac.id', 'Password20210910083', 'Mahasiswa 236', 'P', '081234567236', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910084', '20210910084@uniku.ac.id', 'Password20210910084', 'Mahasiswa 237', 'P', '081234567237', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910085', '20210910085@uniku.ac.id', 'Password20210910085', 'Mahasiswa 238', 'L', '081234567238', 'SI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210910086', '20210910086@uniku.ac.id', 'Password20210910086', 'Mahasiswa 239', 'P', '081234567239', 'SI', 2021, '02', 1, 'L', 'anggota', '', 0),
('20210910087', '20210910087@uniku.ac.id', 'Password20210910087', 'Mahasiswa 240', 'L', '081234567240', 'SI', 2021, '03', 0, 'XL', 'anggota', '', 0),
('20210910088', '20210910088@uniku.ac.id', 'Password20210910088', 'Mahasiswa 241', 'P', '081234567241', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910089', '20210910089@uniku.ac.id', 'Password20210910089', 'Mahasiswa 242', 'L', '081234567242', 'SI', 2021, '04', 0, 'S', 'anggota', '', 0),
('20210910090', '20210910090@uniku.ac.id', 'Password20210910090', 'Mahasiswa 243', 'P', '081234567243', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910091', '20210910091@uniku.ac.id', 'Password20210910091', 'Mahasiswa 244', 'L', '081234567244', 'SI', 2021, '02', 1, 'L', 'anggota', '', 0),
('20210910092', '20210910092@uniku.ac.id', 'Password20210910092', 'Mahasiswa 245', 'P', '081234567245', 'SI', 2021, '01', 0, 'XL', 'anggota', '', 0),
('20210910093', '20210910093@uniku.ac.id', 'Password20210910093', 'Mahasiswa 246', 'P', '081234567246', 'SI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210910094', '20210910094@uniku.ac.id', 'Password20210910094', 'Mahasiswa 247', 'L', '081234567247', 'SI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210910095', '20210910095@uniku.ac.id', 'Password20210910095', 'Mahasiswa 248', 'P', '081234567248', 'SI', 2021, '04', 0, 'M', 'anggota', '', 0),
('20210910096', '20210910096@uniku.ac.id', 'Password20210910096', 'Mahasiswa 249', 'L', '081234567249', 'SI', 2021, '06', 0, 'L', 'anggota', '', 0),
('20210910097', '20210910097@uniku.ac.id', 'Password20210910097', 'Mahasiswa 250', 'P', '081234567250', 'SI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20210910098', '20210910098@uniku.ac.id', 'Password20210910098', 'Mahasiswa 251', 'L', '081234567251', 'SI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210910099', '20210910099@uniku.ac.id', 'Password20210910099', 'Mahasiswa 252', 'L', '081234567252', 'SI', 2021, '03', 1, 'S', 'anggota', '', 0),
('20210910100', '20210910100@uniku.ac.id', 'Password20210910100', 'Mahasiswa 253', 'P', '081234567253', 'SI', 2021, '05', 0, 'M', 'anggota', '', 0),
('20210910101', '20210910101@uniku.ac.id', 'Password20210910101', 'Mahasiswa 254', 'L', '081234567254', 'SI', 2021, '04', 1, 'L', 'anggota', '', 0),
('20210910102', '20210910102@uniku.ac.id', 'Password20210910102', 'Mahasiswa 255', 'P', '081234567255', 'SI', 2021, '06', 0, 'XL', 'anggota', '', 0),
('20210910103', '20210910103@uniku.ac.id', 'Password20210910103', 'Mahasiswa 256', 'L', '081234567256', 'SI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210910104', '20210910104@uniku.ac.id', 'Password20210910104', 'Mahasiswa 257', 'L', '081234567257', 'SI', 2021, '02', 1, 'S', 'anggota', '', 0),
('20210910105', '20210910105@uniku.ac.id', 'Password20210910105', 'Mahasiswa 258', 'L', '081234567258', 'SI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210910106', '20210910106@uniku.ac.id', 'Password20210910106', 'Mahasiswa 259', 'P', '081234567259', 'SI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210910107', '20210910107@uniku.ac.id', 'Password20210910107', 'Mahasiswa 260', 'L', '081234567260', 'SI', 2021, '04', 0, 'XL', 'anggota', '', 0),
('20210910108', '20210910108@uniku.ac.id', 'Password20210910108', 'Mahasiswa 261', 'L', '081234567261', 'SI', 2021, '06', 0, 'XXL', 'anggota', '', 0),
('20210910109', '20210910109@uniku.ac.id', 'Password20210910109', 'Mahasiswa 262', 'P', '081234567262', 'SI', 2021, '01', 0, 'S', 'anggota', '', 0),
('20210910110', '20210910110@uniku.ac.id', 'Password20210910110', 'Mahasiswa 263', 'L', '081234567263', 'SI', 2021, '02', 1, 'M', 'anggota', '', 0),
('20210910111', '20210910111@uniku.ac.id', 'Password20210910111', 'Mahasiswa 264', 'L', '081234567264', 'SI', 2021, '03', 0, 'L', 'anggota', '', 0),
('20210910112', '20210910112@uniku.ac.id', 'Password20210910112', 'Mahasiswa 265', 'L', '081234567265', 'SI', 2021, '05', 1, 'XL', 'anggota', '', 0),
('20210910113', '20210910113@uniku.ac.id', 'Password20210910113', 'Mahasiswa 266', 'P', '081234567266', 'SI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210910114', '20210910114@uniku.ac.id', 'Password20210910114', 'Mahasiswa 267', 'L', '081234567267', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910115', '20210910115@uniku.ac.id', 'Password20210910115', 'Mahasiswa 268', 'L', '081234567268', 'SI', 2021, '01', 1, 'M', 'anggota', '', 0),
('20210910116', '20210910116@uniku.ac.id', 'Password20210910116', 'Mahasiswa 269', 'L', '081234567269', 'SI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210910117', '20210910117@uniku.ac.id', 'Password20210910117', 'Mahasiswa 270', 'L', '081234567270', 'SI', 2021, '03', 0, 'XL', 'anggota', '', 0),
('20210910118', '20210910118@uniku.ac.id', 'Password20210910118', 'Mahasiswa 271', 'P', '081234567271', 'SI', 2021, '05', 1, 'XXL', 'anggota', '', 0),
('20210910119', '20210910119@uniku.ac.id', 'Password20210910119', 'Mahasiswa 272', 'L', '081234567272', 'SI', 2021, '04', 0, 'S', 'anggota', '', 0),
('20210910120', '20210910120@uniku.ac.id', 'Password20210910120', 'Mahasiswa 273', 'L', '081234567273', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910121', '20210910121@uniku.ac.id', 'Password20210910121', 'Mahasiswa 274', 'L', '081234567274', 'SI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20210910122', '20210910122@uniku.ac.id', 'Password20210910122', 'Mahasiswa 275', 'P', '081234567275', 'SI', 2021, '02', 0, 'XL', 'anggota', '', 0),
('20210910123', '20210910123@uniku.ac.id', 'Password20210910123', 'Mahasiswa 276', 'L', '081234567276', 'SI', 2021, '03', 0, 'XXL', 'anggota', '', 0),
('20210910124', '20210910124@uniku.ac.id', 'Password20210910124', 'Mahasiswa 277', 'L', '081234567277', 'SI', 2021, '05', 1, 'S', 'anggota', '', 0),
('20210910125', '20210910125@uniku.ac.id', 'Password20210910125', 'Mahasiswa 278', 'P', '081234567278', 'SI', 2021, '04', 0, 'M', 'anggota', '', 0),
('20210910126', '20210910126@uniku.ac.id', 'Password20210910126', 'Mahasiswa 279', 'L', '081234567279', 'SI', 2021, '06', 1, 'L', 'anggota', '', 0),
('20210910127', '20210910127@uniku.ac.id', 'Password20210910127', 'Mahasiswa 280', 'L', '081234567280', 'SI', 2021, '01', 0, 'S', 'anggota', '', 0),
('20210910128', '20210910128@uniku.ac.id', 'Password20210910128', 'Mahasiswa 281', 'P', '081234567281', 'SI', 2021, '02', 0, 'XXL', 'anggota', '', 0),
('20210910129', '20210910129@uniku.ac.id', 'Password20210910129', 'Mahasiswa 282', 'L', '081234567282', 'SI', 2021, '03', 0, 'M', 'anggota', '', 0),
('20210910130', '20210910130@uniku.ac.id', 'Password20210910130', 'Mahasiswa 283', 'P', '081234567283', 'SI', 2021, '05', 1, 'L', 'anggota', '', 0),
('20210910131', '20210910131@uniku.ac.id', 'Password20210910131', 'Mahasiswa 284', 'L', '081234567284', 'SI', 2021, '04', 0, 'XXL', 'anggota', '', 0),
('20210910132', '20210910132@uniku.ac.id', 'Password20210910132', 'Mahasiswa 285', 'L', '081234567285', 'SI', 2021, '06', 0, 'S', 'anggota', '', 0),
('20210910133', '20210910133@uniku.ac.id', 'Password20210910133', 'Mahasiswa 286', 'L', '081234567286', 'SI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20210910134', '20210910134@uniku.ac.id', 'Password20210910134', 'Mahasiswa 287', 'L', '081234567287', 'SI', 2021, '02', 0, 'L', 'anggota', '', 0),
('20210910135', '20210910135@uniku.ac.id', 'Password20210910135', 'Mahasiswa 288', 'P', '081234567288', 'SI', 2021, '03', 1, 'XL', 'anggota', '', 0),
('20210910136', '20210910136@uniku.ac.id', 'Password20210910136', 'Mahasiswa 289', 'L', '081234567289', 'SI', 2021, '05', 0, 'XXL', 'anggota', '', 0),
('20210910137', '20210910137@uniku.ac.id', 'Password20210910137', 'Mahasiswa 290', 'P', '081234567290', 'SI', 2021, '04', 0, 'S', 'anggota', '', 0),
('20210910138', '20210910138@uniku.ac.id', 'Password20210910138', 'Mahasiswa 291', 'L', '081234567291', 'SI', 2021, '06', 0, 'M', 'anggota', '', 0),
('20210910139', '20210910139@uniku.ac.id', 'Password20210910139', 'Mahasiswa 292', 'P', '081234567292', 'SI', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20210910140', '20210910140@uniku.ac.id', 'Password20210910140', 'Mahasiswa 293', 'L', '081234567293', 'SI', 2021, '02', 0, 'S', 'anggota', '', 0),
('20211810001', '20211810001@uniku.ac.id', 'Password20211810001', 'Mahasiswa DKV 01', 'L', '081234567294', 'DKV', 2021, '01', 1, 'M', 'anggota', '', 0),
('20211810002', '20211810002@uniku.ac.id', 'Password20211810002', 'Mahasiswa DKV 02', 'P', '081234567295', 'DKV', 2021, '01', 0, 'L', 'anggota', '', 0),
('20211810003', '20211810003@uniku.ac.id', 'Password20211810003', 'Mahasiswa DKV 03', 'L', '081234567296', 'DKV', 2021, '01', 1, 'S', 'anggota', '', 0),
('20211810004', '20211810004@uniku.ac.id', 'Password20211810004', 'Mahasiswa DKV 04', 'P', '081234567297', 'DKV', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20211810005', '20211810005@uniku.ac.id', 'Password20211810005', 'Mahasiswa DKV 05', 'L', '081234567298', 'DKV', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20211810006', '20211810006@uniku.ac.id', 'Password20211810006', 'Mahasiswa DKV 06', 'P', '081234567299', 'DKV', 2021, '02', 0, 'M', 'anggota', '', 0),
('20211810007', '20211810007@uniku.ac.id', 'Password20211810007', 'Mahasiswa DKV 07', 'L', '081234567300', 'DKV', 2021, '01', 1, 'L', 'anggota', '', 0),
('20211810008', '20211810008@uniku.ac.id', 'Password20211810008', 'Mahasiswa DKV 08', 'P', '081234567301', 'DKV', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20211810009', '20211810009@uniku.ac.id', 'Password20211810009', 'Mahasiswa DKV 09', 'L', '081234567302', 'DKV', 2021, '01', 1, 'S', 'anggota', '', 0),
('20211810010', '20211810010@uniku.ac.id', 'Password20211810010', 'Mahasiswa DKV 10', 'P', '081234567303', 'DKV', 2021, '02', 0, 'M', 'anggota', '', 0),
('20211810011', '20211810011@uniku.ac.id', 'Password20211810011', 'Mahasiswa DKV 11', 'L', '081234567304', 'DKV', 2021, '02', 1, 'L', 'anggota', '', 0),
('20211810012', '20211810012@uniku.ac.id', 'Password20211810012', 'Mahasiswa DKV 12', 'P', '081234567305', 'DKV', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20211810013', '20211810013@uniku.ac.id', 'Password20211810013', 'Mahasiswa DKV 13', 'L', '081234567306', 'DKV', 2021, '02', 1, 'S', 'anggota', '', 0),
('20211810014', '20211810014@uniku.ac.id', 'Password20211810014', 'Mahasiswa DKV 14', 'P', '081234567307', 'DKV', 2021, '01', 0, 'M', 'anggota', '', 0),
('20211810015', '20211810015@uniku.ac.id', 'Password20211810015', 'Mahasiswa DKV 15', 'L', '081234567308', 'DKV', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20211810016', '20211810016@uniku.ac.id', 'Password20211810016', 'Mahasiswa DKV 16', 'P', '081234567309', 'DKV', 2021, '02', 0, 'L', 'anggota', '', 0),
('20211810017', '20211810017@uniku.ac.id', 'Password20211810017', 'Mahasiswa DKV 17', 'L', '081234567310', 'DKV', 2021, '02', 1, 'S', 'anggota', '', 0),
('20211810018', '20211810018@uniku.ac.id', 'Password20211810018', 'Mahasiswa DKV 18', 'P', '081234567311', 'DKV', 2021, '01', 0, 'XL', 'anggota', '', 0),
('20211810019', '20211810019@uniku.ac.id', 'Password20211810019', 'Mahasiswa DKV 19', 'L', '081234567312', 'DKV', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20211810020', '20211810020@uniku.ac.id', 'Password20211810020', 'Mahasiswa DKV 20', 'P', '081234567313', 'DKV', 2021, '01', 0, 'M', 'anggota', '', 0),
('20211810021', '20211810021@uniku.ac.id', 'Password20211810021', 'Mahasiswa DKV 21', 'L', '081234567314', 'DKV', 2021, '01', 1, 'L', 'anggota', '', 0),
('20211810022', '20211810022@uniku.ac.id', 'Password20211810022', 'Mahasiswa DKV 22', 'P', '081234567315', 'DKV', 2021, '02', 0, 'S', 'anggota', '', 0),
('20211810023', '20211810023@uniku.ac.id', 'Password20211810023', 'Mahasiswa DKV 23', 'L', '081234567316', 'DKV', 2021, '01', 1, 'M', 'anggota', '', 0),
('20211810024', '20211810024@uniku.ac.id', 'Password20211810024', 'Mahasiswa DKV 24', 'P', '081234567317', 'DKV', 2021, '02', 0, 'XL', 'anggota', '', 0),
('20211810025', '20211810025@uniku.ac.id', 'Password20211810025', 'Mahasiswa DKV 25', 'L', '081234567318', 'DKV', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20211810026', '20211810026@uniku.ac.id', 'Password20211810026', 'Mahasiswa DKV 26', 'P', '081234567319', 'DKV', 2021, '01', 0, 'S', 'anggota', '', 0),
('20211810027', '20211810027@uniku.ac.id', 'Password20211810027', 'Mahasiswa DKV 27', 'L', '081234567320', 'DKV', 2021, '02', 1, 'M', 'anggota', '', 0),
('20211810028', '20211810028@uniku.ac.id', 'Password20211810028', 'Mahasiswa DKV 28', 'P', '081234567321', 'DKV', 2021, '02', 0, 'L', 'anggota', '', 0),
('20211810029', '20211810029@uniku.ac.id', 'Password20211810029', 'Mahasiswa DKV 29', 'L', '081234567322', 'DKV', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20211810030', '20211810030@uniku.ac.id', 'Password20211810030', 'Mahasiswa DKV 30', 'P', '081234567323', 'DKV', 2021, '02', 0, 'S', 'anggota', '', 0),
('20211810031', '20211810031@uniku.ac.id', 'Password20211810031', 'Mahasiswa DKV 31', 'L', '081234567324', 'DKV', 2021, '01', 1, 'M', 'anggota', '', 0),
('20211810032', '20211810032@uniku.ac.id', 'Password20211810032', 'Mahasiswa DKV 32', 'P', '081234567325', 'DKV', 2021, '01', 0, 'L', 'anggota', '', 0),
('20211810033', '20211810033@uniku.ac.id', 'Password20211810033', 'Mahasiswa DKV 33', 'L', '081234567326', 'DKV', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20211810034', '20211810034@uniku.ac.id', 'Password20211810034', 'Mahasiswa DKV 34', 'P', '081234567327', 'DKV', 2021, '01', 0, 'S', 'anggota', '', 0),
('20211810035', '20211810035@uniku.ac.id', 'Password20211810035', 'Mahasiswa DKV 35', 'L', '081234567328', 'DKV', 2021, '02', 1, 'M', 'anggota', '', 0),
('20211810036', '20211810036@uniku.ac.id', 'Password20211810036', 'Mahasiswa DKV 36', 'P', '081234567329', 'DKV', 2021, '01', 0, 'L', 'anggota', '', 0),
('20211810037', '20211810037@uniku.ac.id', 'Password20211810037', 'Mahasiswa DKV 37', 'L', '081234567330', 'DKV', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20211810038', '20211810038@uniku.ac.id', 'Password20211810038', 'Mahasiswa DKV 38', 'P', '081234567331', 'DKV', 2021, '02', 0, 'M', 'anggota', '', 0),
('20211810039', '20211810039@uniku.ac.id', 'Password20211810039', 'Mahasiswa DKV 39', 'L', '081234567332', 'DKV', 2021, '01', 1, 'S', 'anggota', '', 0),
('20211810040', '20211810040@uniku.ac.id', 'Password20211810040', 'Mahasiswa DKV 40', 'P', '081234567333', 'DKV', 2021, '02', 0, 'L', 'anggota', '', 0),
('20211810041', '20211810041@uniku.ac.id', 'Password20211810041', 'Mahasiswa DKV 41', 'L', '081234567334', 'DKV', 2021, '01', 1, 'XXL', 'anggota', '', 0),
('20211810042', '20211810042@uniku.ac.id', 'Password20211810042', 'Mahasiswa DKV 42', 'P', '081234567335', 'DKV', 2021, '01', 0, 'M', 'anggota', '', 0),
('20211810043', '20211810043@uniku.ac.id', 'Password20211810043', 'Mahasiswa DKV 43', 'L', '081234567336', 'DKV', 2021, '02', 1, 'L', 'anggota', '', 0),
('20211810044', '20211810044@uniku.ac.id', 'Password20211810044', 'Mahasiswa DKV 44', 'P', '081234567337', 'DKV', 2021, '01', 0, 'S', 'anggota', '', 0),
('20211810045', '20211810045@uniku.ac.id', 'Password20211810045', 'Mahasiswa DKV 45', 'L', '081234567338', 'DKV', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20211810046', '20211810046@uniku.ac.id', 'Password20211810046', 'Mahasiswa DKV 46', 'P', '081234567339', 'DKV', 2021, '02', 0, 'M', 'anggota', '', 0),
('20211810047', '20211810047@uniku.ac.id', 'Password20211810047', 'Mahasiswa DKV 47', 'L', '081234567340', 'DKV', 2021, '02', 1, 'L', 'anggota', '', 0),
('20211810048', '20211810048@uniku.ac.id', 'Password20211810048', 'Mahasiswa DKV 48', 'P', '081234567341', 'DKV', 2021, '01', 0, 'XL', 'anggota', '', 0);
INSERT INTO `mahasiswa` (`nim`, `email`, `password`, `nama`, `jk`, `telp`, `prodi`, `angkatan`, `kelas`, `mbkm`, `jaket`, `status`, `file_upload`, `status_validasi`) VALUES
('20211810049', '20211810049@uniku.ac.id', 'Password20211810049', 'Mahasiswa DKV 49', 'L', '081234567342', 'DKV', 2021, '02', 1, 'XXL', 'anggota', '', 0),
('20212110001', '20212110001@uniku.ac.id', 'Password20212110001', 'Mahasiswa TS 01', 'L', '081234567343', 'TS', 2021, '01', 0, 'M', 'anggota', '', 0),
('20212110002', '20212110002@uniku.ac.id', 'Password20212110002', 'Mahasiswa TS 02', 'P', '081234567344', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20212110003', '20212110003@uniku.ac.id', 'Password20212110003', 'Mahasiswa TS 03', 'L', '081234567345', 'TS', 2021, '01', 0, 'S', 'anggota', '', 0),
('20212110004', '20212110004@uniku.ac.id', 'Password20212110004', 'Mahasiswa TS 04', 'P', '081234567346', 'TS', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20212110005', '20212110005@uniku.ac.id', 'Password20212110005', 'Mahasiswa TS 05', 'L', '081234567347', 'TS', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20212110006', '20212110006@uniku.ac.id', 'Password20212110006', 'Mahasiswa TS 06', 'P', '081234567348', 'TS', 2021, '01', 1, 'M', 'anggota', '', 0),
('20212110007', '20212110007@uniku.ac.id', 'Password20212110007', 'Mahasiswa TS 07', 'L', '081234567349', 'TS', 2021, '01', 0, 'S', 'anggota', '', 0),
('20212110008', '20212110008@uniku.ac.id', 'Password20212110008', 'Mahasiswa TS 08', 'P', '081234567350', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20212110009', '20212110009@uniku.ac.id', 'Password20212110009', 'Mahasiswa TS 09', 'L', '081234567351', 'TS', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20212110010', '20212110010@uniku.ac.id', 'Password20212110010', 'Mahasiswa TS 10', 'P', '081234567352', 'TS', 2021, '01', 1, 'S', 'anggota', '', 0),
('20212110011', '20212110011@uniku.ac.id', 'Password20212110011', 'Mahasiswa TS 11', 'L', '081234567353', 'TS', 2021, '01', 0, 'M', 'anggota', '', 0),
('20212110012', '20212110012@uniku.ac.id', 'Password20212110012', 'Mahasiswa TS 12', 'P', '081234567354', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20212110013', '20212110013@uniku.ac.id', 'Password20212110013', 'Mahasiswa TS 13', 'L', '081234567355', 'TS', 2021, '01', 0, 'S', 'anggota', '', 0),
('20212110014', '20212110014@uniku.ac.id', 'Password20212110014', 'Mahasiswa TS 14', 'P', '081234567356', 'TS', 2021, '01', 1, 'M', 'anggota', '', 0),
('20212110015', '20212110015@uniku.ac.id', 'Password20212110015', 'Mahasiswa TS 15', 'L', '081234567357', 'TS', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20212110016', '20212110016@uniku.ac.id', 'Password20212110016', 'Mahasiswa TS 16', 'P', '081234567358', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20212110017', '20212110017@uniku.ac.id', 'Password20212110017', 'Mahasiswa TS 17', 'L', '081234567359', 'TS', 2021, '01', 0, 'M', 'anggota', '', 0),
('20212110018', '20212110018@uniku.ac.id', 'Password20212110018', 'Mahasiswa TS 18', 'P', '081234567360', 'TS', 2021, '01', 1, 'S', 'anggota', '', 0),
('20212110019', '20212110019@uniku.ac.id', 'Password20212110019', 'Mahasiswa TS 19', 'L', '081234567361', 'TS', 2021, '01', 0, 'XL', 'anggota', '', 0),
('20212110020', '20212110020@uniku.ac.id', 'Password20212110020', 'Mahasiswa TS 20', 'P', '081234567362', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20212110021', '20212110021@uniku.ac.id', 'Password20212110021', 'Mahasiswa TS 21', 'L', '081234567363', 'TS', 2021, '01', 0, 'XXL', 'anggota', '', 0),
('20212110022', '20212110022@uniku.ac.id', 'Password20212110022', 'Mahasiswa TS 22', 'P', '081234567364', 'TS', 2021, '01', 1, 'M', 'anggota', '', 0),
('20212110023', '20212110023@uniku.ac.id', 'Password20212110023', 'Mahasiswa TS 23', 'L', '081234567365', 'TS', 2021, '01', 0, 'S', 'anggota', '', 0),
('20212110024', '20212110024@uniku.ac.id', 'Password20212110024', 'Mahasiswa TS 24', 'P', '081234567366', 'TS', 2021, '01', 1, 'L', 'anggota', '', 0),
('20221510001', '20221510001@uniku.ac.id', 'Password20221510001', 'Mahasiswa MI 01', 'P', '081234567367', 'MI', 2021, '01', 0, 'M', 'anggota', '', 0),
('20221510002', '20221510002@uniku.ac.id', 'Password20221510002', 'Mahasiswa MI 02', 'L', '081234567368', 'MI', 2021, '01', 1, 'L', 'anggota', '', 0),
('20221510003', '20221510003@uniku.ac.id', 'Password20221510003', 'Mahasiswa MI 03', 'P', '081234567369', 'MI', 2021, '01', 0, 'S', 'anggota', '', 0),
('20221510004', '20221510004@uniku.ac.id', 'Password20221510004', 'Mahasiswa MI 04', 'L', '081234567370', 'MI', 2021, '01', 1, 'XL', 'anggota', '', 0),
('20221510005', '20221510005@uniku.ac.id', 'Password20221510005', 'Mahasiswa MI 05', 'P', '081234567371', 'MI', 2021, '01', 0, 'XXL', 'anggota', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` int NOT NULL,
  `nama_mitra` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mitra`
--

-- --------------------------------------------------------

--
-- Table structure for table `surat`
--

CREATE TABLE `surat` (
  `id` int NOT NULL,
  `no_surat` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `no_kelompok` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `surat`
--

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
  ADD KEY `nim` (`nim`),
  ADD KEY `nik` (`nik`),
  ADD KEY `id_mitra` (`id_mitra`),
  ADD KEY `no_kelompok` (`no_kelompok`) USING BTREE;

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
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `no_kelompok` (`no_kelompok`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id_mitra` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `surat`
--
ALTER TABLE `surat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kpconnection`
--
ALTER TABLE `kpconnection`
  ADD CONSTRAINT `kpconnection_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kpconnection_ibfk_2` FOREIGN KEY (`no_kelompok`) REFERENCES `kelompok` (`no_kelompok`),
  ADD CONSTRAINT `kpconnection_ibfk_3` FOREIGN KEY (`nik`) REFERENCES `dosen` (`nik`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kpconnection_ibfk_4` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id_mitra`) ON UPDATE CASCADE;

--
-- Constraints for table `surat`
--
ALTER TABLE `surat`
  ADD CONSTRAINT `surat_ibfk_1` FOREIGN KEY (`no_kelompok`) REFERENCES `kelompok` (`no_kelompok`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
