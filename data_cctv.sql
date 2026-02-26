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
