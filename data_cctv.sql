-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Feb 2026 pada 02.42
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cctv`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_cctv`
--

CREATE TABLE `data_cctv` (
  `id` int(36) NOT NULL,
  `nama_kebun` varchar(255) NOT NULL,
  `ip_cctv` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_cctv`
--

INSERT INTO `data_cctv` (`id`, `nama_kebun`, `ip_cctv`) VALUES
(1, 'Kertamanah', 'http://localhost:8889/nvr1/'),
(2, 'Malabar', 'http://localhost:8889/malabar1/'),
(3, 'Sedep', 'http://localhost:8889/sedep1/'),
(4, 'Kertamanah', 'http://localhost:8889/nvr2/'),
(5, 'Kertamanah', 'http://localhost:8889/nvr4/'),
(6, 'Malabar', 'http://localhost:8889/malabar2/'),
(16, 'Sedep', 'http://localhost:8889/sedep2/'),
(17, 'head_office', 'http://localhost:8889/nvrho01/'),
(18, 'head_office', 'http://localhost:8889/nvrho02/'),
(19, 'head_office', 'http://localhost:8889/nvrho03/'),
(20, 'head_office', 'http://localhost:8889/nvrho04/'),
(21, 'head_office', 'http://localhost:8889/nvrho05/'),
(22, 'head_office', 'http://localhost:8889/nvrho06/'),
(23, 'head_office', 'http://localhost:8889/nvrho07/'),
(24, 'head_office', 'http://localhost:8889/nvrho08/'),
(25, 'head_office', 'http://localhost:8889/nvrho09/'),
(26, 'head_office', 'http://localhost:8889/nvrho10/'),
(27, 'head_office', 'http://localhost:8889/nvrho11/'),
(28, 'head_office', 'http://localhost:8889/nvrho12/'),
(29, 'head_office', 'http://localhost:8889/nvrho13/'),
(30, 'head_office', 'http://localhost:8889/nvrho14/'),
(31, 'head_office', 'http://localhost:8889/nvrho15/'),
(32, 'head_office', 'http://localhost:8889/nvrho16/'),
(33, 'head_office', 'http://localhost:8889/nvrho17/'),
(34, 'head_office', 'http://localhost:8889/nvrho18/'),
(35, 'head_office', 'http://localhost:8889/nvrho19/'),
(36, 'head_office', 'http://localhost:8889/nvrho20/'),
(37, 'head_office', 'http://localhost:8889/nvrho21/'),
(38, 'head_office', 'http://localhost:8889/nvrho22/'),
(39, 'head_office', 'http://localhost:8889/nvrho23/'),
(40, 'head_office', 'http://localhost:8889/nvrho24/'),
(41, 'head_office', 'http://localhost:8889/nvrho25/'),
(42, 'head_office', 'http://localhost:8889/nvrho26/'),
(43, 'head_office', 'http://localhost:8889/nvrho27/'),
(44, 'head_office', 'http://localhost:8889/nvrho28/'),
(45, 'head_office', 'http://localhost:8889/nvrho29/'),
(46, 'head_office', 'http://localhost:8889/nvrho30/'),
(47, 'head_office', 'http://localhost:8889/nvrho31/'),
(48, 'Kertamanah', 'http://localhost:8889/nvr3/'),
(49, 'Malabar', 'http://localhost:8889/malabar3/'),
(50, 'Malabar', 'http://localhost:8889/malabar4/'),
(51, 'Sedep', 'http://localhost:8889/sedep3/'),
(52, 'Sedep', 'http://localhost:8889/sedep4/');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_cctv`
--
ALTER TABLE `data_cctv`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_cctv`
--
ALTER TABLE `data_cctv`
  MODIFY `id` int(36) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
