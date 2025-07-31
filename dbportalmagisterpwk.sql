-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2025 at 01:09 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbportalmagisterpwk`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('portal_magister_pwk_cache_mhs1@example.com|127.0.0.1', 'i:2;', 1752152659),
('portal_magister_pwk_cache_mhs1@example.com|127.0.0.1:timer', 'i:1752152659;', 1752152659);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_05_195935_create_references_table', 1),
(5, '2025_07_10_025206_create_tesis_table', 1),
(6, '2025_07_10_025225_create_tesis_pembimbing_table', 1),
(7, '2025_07_10_025234_create_tesis_penguji_table', 1),
(8, '2025_07_10_025249_create_tesis_logbook_table', 1),
(9, '2025_07_10_025258_create_tesis_ujian_table', 1),
(10, '2025_07_10_025309_create_tesis_dokumen_table', 1),
(11, '2025_07_10_025318_create_tesis_laporan_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_dosen`
--

CREATE TABLE `ref_dosen` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `kode_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `bidang_keahlian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_dosen`
--

INSERT INTO `ref_dosen` (`id`, `user_id`, `kode_dosen`, `nip`, `nama_dosen`, `status_dosen`, `bidang_keahlian`, `gender`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'KD001', '198501012010121001', 'Koordinator', 'aktif', 'Sistem Informasi', 'L', '2025-07-10 12:58:32', '2025-07-10 12:58:32', NULL),
(2, 2, 'AD001', '198502022010122002', 'Administrator', 'aktif', 'Manajemen Sistem', 'L', '2025-07-10 12:58:32', '2025-07-10 12:58:32', NULL),
(3, 3, 'AD002', '198503032010123003', 'Dr. Ahmad Fauzi, M.Kom', 'aktif', 'Pemrograman Web', 'L', '2025-07-10 12:58:32', '2025-07-10 12:58:32', NULL),
(4, 33, 'DSN002', 'DSN002', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:30', '2025-07-10 13:04:30', NULL),
(5, 34, 'DSN003', 'DSN003', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(6, 35, 'DSN004', 'DSN004', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(7, 36, 'DSN005', 'DSN005', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(8, 37, 'DSN006', 'DSN006', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(9, 38, 'DSN007', 'DSN007', 'fulan', 'aktif', NULL, NULL, '2025-07-10 13:04:32', '2025-07-10 13:04:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ref_mahasiswa`
--

CREATE TABLE `ref_mahasiswa` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nim` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mhs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` int DEFAULT NULL,
  `status_mhs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `sks` int DEFAULT '0',
  `ipk` int DEFAULT '0',
  `gender` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_mahasiswa`
--

INSERT INTO `ref_mahasiswa` (`id`, `user_id`, `nim`, `nama_mhs`, `angkatan`, `status_mhs`, `sks`, `ipk`, `gender`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 'MHS00002', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:52', '2025-07-10 12:58:52', NULL),
(2, 5, 'MHS00897', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:52', '2025-07-10 12:58:52', NULL),
(3, 6, 'MHS01796', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(4, 7, 'MHS02695', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(5, 8, 'MHS03594', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(6, 9, 'MHS04493', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(7, 10, 'MHS05392', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(8, 11, 'MHS06291', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(9, 12, 'MHS07190', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(10, 13, 'MHS08089', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(11, 14, 'MHS08988', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(12, 15, 'MHS09887', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(13, 16, 'MHS10786', 'fulan', 2022, 'aktif', 0, 0, 'L', '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(14, 17, 'MHS11685', 'fulan', 2024, 'aktif', 0, 0, 'L', '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(15, 18, 'MHS12584', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(16, 19, 'MHS13483', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(17, 20, 'MHS14382', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(18, 21, 'MHS15281', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(19, 22, 'MHS16180', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(20, 23, 'MHS17079', 'fulan', 2024, 'aktif', 0, 0, 'P', '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(21, 24, 'MHS17978', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(22, 25, 'MHS18877', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(23, 26, 'MHS19776', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(24, 27, 'MHS20675', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(25, 28, 'MHS21574', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(26, 29, 'MHS22473', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(27, 30, 'MHS23372', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(28, 31, 'MHS24271', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:59', '2025-07-10 12:58:59', NULL),
(29, 32, 'MHS25170', 'fulan', 2025, 'aktif', 0, 0, 'P', '2025-07-10 12:58:59', '2025-07-10 12:58:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama_role`, `deskripsi`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'Administrator', '2025-07-10 12:58:31', '2025-07-10 12:58:31', NULL),
(2, 'koordinator', 'Koordinator', '2025-07-10 12:58:31', '2025-07-10 12:58:31', NULL),
(3, 'dosen', 'Dosen', '2025-07-10 12:58:31', '2025-07-10 12:58:31', NULL),
(4, 'mahasiswa', 'Mahasiswa', '2025-07-10 12:58:31', '2025-07-10 12:58:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kLyWg2Z3qq7xzXSdkA53TXnWjIDKCl8IeOyJMj7A', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZFhMWEdSU0VORjVCVEpMOW15b0VGaVNmWkdudjdzSVhnVUloQjM0eCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3VzZXIvMzEiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3VzZXIvMzEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1752207098),
('wWwiayL2RVhPdkRWr9W0GDnizDkhEP8YWuIlfSuQ', 31, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVldtc3hZblJaVlNVeVNtb0Z0blkxUjdwNVJLVkxOQnVaS2VrSXVFWCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZHJhZnQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozMTt9', 1752208016),
('XbKOfpYAxg7AJ3UTeiV99QDHoeJWzJ0qgtSd2Ly5', 31, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHRWdVY0ako4bTAzeElQaEc5aFMySDU5aHBCZEk2U0U3ZXJnSDJ5dyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MzE7fQ==', 1752206620),
('XOUTXOs7yUiLZF5MXDX6SjPzaUtTr5BkpZeYD4aA', 2, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib1ppZUdld3dvTk15SzJERHd1TjVrRnV4SjFDMG9JdXhDNk9rNjJJQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1752207900);

-- --------------------------------------------------------

--
-- Table structure for table `tesis`
--

CREATE TABLE `tesis` (
  `id` bigint UNSIGNED NOT NULL,
  `mhs_id` bigint UNSIGNED NOT NULL,
  `judul` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstrak` text COLLATE utf8mb4_unicode_ci,
  `tgl_mulai` timestamp NULL DEFAULT NULL,
  `tgl_selesai` timestamp NULL DEFAULT NULL,
  `status_pratesis` enum('pending','waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_satu` enum('pending','waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_dua` enum('pending','waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `file_tesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_dokumen`
--

CREATE TABLE `tesis_dokumen` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `file_perm_pratesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_perm_tesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_perm_tesis_dua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_turnitin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_logbook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_draft`
--

CREATE TABLE `tesis_draft` (
  `id` bigint UNSIGNED NOT NULL,
  `mhs_id` bigint UNSIGNED NOT NULL,
  `us_judul` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `us_abstrak` text COLLATE utf8mb4_unicode_ci,
  `us_dospem_satu` bigint UNSIGNED NOT NULL,
  `us_dospem_dua` bigint UNSIGNED NOT NULL,
  `ket_dospem_satu` text COLLATE utf8mb4_unicode_ci,
  `ket_dospem_dua` text COLLATE utf8mb4_unicode_ci,
  `tgl_pengajuan` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_khs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_krs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_sk_pembimbing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_tesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tesis_draft`
--

INSERT INTO `tesis_draft` (`id`, `mhs_id`, `us_judul`, `us_abstrak`, `us_dospem_satu`, `us_dospem_dua`, `ket_dospem_satu`, `ket_dospem_dua`, `tgl_pengajuan`, `status`, `file_khs`, `file_krs`, `file_sk_pembimbing`, `file_tesis`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'rejected', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-11 03:57:29', '2025-07-10 13:21:29', '2025-07-11 03:57:29'),
(3, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(4, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(5, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(6, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(7, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(8, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(9, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(10, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(11, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(12, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(13, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(14, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(15, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(16, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycle', 5, 6, 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:21:29', 'pending', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', NULL, NULL, '2025-07-10 13:21:29', '2025-07-10 13:21:29', '2025-07-10 13:21:29'),
(17, 9, 'PEMANFAATAN SERAT KELAPA SEBAGAI ALTERNATIF PENGGANTI KEMASAN BERBAHAN PLASTIK Roland Ananda', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes can eat up a lot of space. However, the fibers of coconut shell can be used to make easily recyclable packaging. On another hand, plastic usage in Indonesia has caused environmental problems due to plastic being hard to degrade, not to mention difficult to recycle because of its chemical compounds. Using coconut shell’s fibers as material for packaging can reduce the problems that arise from both plastic and coconut shell wastes, improving Indonesia’s environment and economy by using an organic, sustainable, and recyclable product. Keywords: coconut fibers, plastic, packaging, sustainable design, recycl', 4, 5, 'asdasdasd', 'Coconut plants are common in tropical regions, including Indonesia. The coconut plants can be used from the leaves down to the roots. However, not many use its fruit’s shell, which quickly becomes a problem when the shell is wasted after the fruit’s flesh and juice have been consumed, especially at markets and recreation centers. Coconut shell wastes', '2025-07-10 13:38:07', 'approved', 'file_lampiran_khs/MHS07190_file_khs.pdf', 'file_lampiran_krs/MHS07190_file_krs.pdf', 'draft/sk_pembimbing/LW5NyE675sN7re7H6QgbMHBE0Lwi9OKXJcLm7j0I.pdf', NULL, NULL, '2025-07-10 13:38:07', '2025-07-11 03:26:35'),
(18, 28, '<!-- Info Alert -->\r\n                        <Alert class=\"text-warning text-xs items-center\">\r\n                            <AlertCircle class=\"h-4 w-4\" />\r\n                            <AlertDescription class=\"text-warning text-xs\">\r\n                                Pastikan semua data yang Anda masukkan sudah benar. Setelah submit,\r\n                                draft pratesis akan menunggu persetujuan dari admin.\r\n                            </AlertDescription>\r\n                        </Alert>', '<!-- Info Alert -->\r\n                        <Alert class=\"text-warning text-xs items-center\">\r\n                            <AlertCircle class=\"h-4 w-4\" />\r\n                            <AlertDescription class=\"text-warning text-xs\">\r\n                                Pastikan semua data yang Anda masukkan sudah benar. Setelah submit,\r\n                                draft pratesis akan menunggu persetujuan dari admin.\r\n                            </AlertDescription>\r\n                        </Alert>', 5, 6, '<!-- Info Alert -->\r\n                        <Alert class=\"text-warning text-xs items-center\">\r\n                            <AlertCircle class=\"h-4 w-4\" />', '<!-- Info Alert -->\r\n                        <Alert class=\"text-warning text-xs items-center\">\r\n                            <AlertCircle class=\"h-4 w-4\" />', '2025-07-11 04:23:18', 'approved', 'file_lampiran_khs/MHS24271_file_khs.pdf', 'file_lampiran_krs/MHS24271_file_krs.pdf', 'draft/sk_pembimbing/Q1H3Ko7LCuy6z4OnEQVx6oHIjuY2NQeG2jrd0a2U.pdf', NULL, NULL, '2025-07-11 04:23:18', '2025-07-11 04:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `tesis_laporan`
--

CREATE TABLE `tesis_laporan` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `rev_pratesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rev_tesis_satu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rev_tesis_dua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_pratesis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_tesis_satu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_tesis_dua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_logbook`
--

CREATE TABLE `tesis_logbook` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `tahap` enum('pratesis','tesis_1','tesis_2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pratesis',
  `kegiatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kendala` text COLLATE utf8mb4_unicode_ci,
  `rencana` text COLLATE utf8mb4_unicode_ci,
  `diskusi` text COLLATE utf8mb4_unicode_ci,
  `file_scan_bimbingan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_pembimbing`
--

CREATE TABLE `tesis_pembimbing` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `dospem` bigint UNSIGNED NOT NULL,
  `no_pembimbing` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pratesis` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_satu` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_dua` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_penguji`
--

CREATE TABLE `tesis_penguji` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `penguji` bigint UNSIGNED NOT NULL,
  `no_penguji` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pratesis` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_satu` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `status_tesis_dua` enum('waiting','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tesis_ujian`
--

CREATE TABLE `tesis_ujian` (
  `id` bigint UNSIGNED NOT NULL,
  `tesis_id` bigint UNSIGNED NOT NULL,
  `tipe_ujian` enum('pratesis','tesis_1','tesis_2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date DEFAULT NULL,
  `tempat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status_seminar` enum('waiting','approved','rejected','revision','done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `draft_semhas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `berita_acara` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_induk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_login` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `role_id` bigint UNSIGNED NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `nomor_induk`, `email_verified_at`, `password`, `first_login`, `is_active`, `role_id`, `photo`, `phone`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Koordinator', 'koordinator@example.com', 'KD001', NULL, '$2y$12$0ua/e8TiDXEWdoDjpa5CBOQbiwWRlsvPDHGqdtB0Nf0MjkqE96hPG', 1, 1, 2, NULL, '081234567890', NULL, '2025-07-10 12:58:32', '2025-07-10 12:58:32', NULL),
(2, 'Administrator', 'admin@example.com', 'AD001', NULL, '$2y$12$7uIYtLmepctkWoD4uyvEi.0N6UWUGTokGCmlUUp8wR/G.sn5nxx/W', 1, 1, 1, NULL, '081234567891', NULL, '2025-07-10 12:58:32', '2025-07-10 12:58:45', NULL),
(3, 'Administrator 1', 'admin1@example.com', 'AD002', NULL, '$2y$12$I8eCS7BBrWqdt0gfl7cGneXVdhFcLI2BLRLfyubXC2gBK7fl4CFiG', 0, 1, 1, NULL, '081234567891', NULL, '2025-07-10 12:58:32', '2025-07-10 12:58:32', NULL),
(4, 'fulan', 'mhsful2@gmail.com', 'MHS00002', NULL, '$2y$12$6SvlbThAH0sVOS2a2ryTsem2CxTiiZUqaG0zcygTmkPZOQXa/7WOa', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:52', '2025-07-10 12:58:52', NULL),
(5, 'fulan', 'mhsful3@gmail.com', 'MHS00897', NULL, '$2y$12$hORnjxrym3zBGSXef..CaugL/hlExQtSlLtLYEBuAcJzyGSbS8SCe', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:52', '2025-07-10 12:58:52', NULL),
(6, 'fulan', 'mhsful4@gmail.com', 'MHS01796', NULL, '$2y$12$GBWiLHxszbvkBXsMhGaAV.ieOLsYBFOV4YkShuxUdvGCBKkIYHmzq', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(7, 'fulan', 'mhsful5@gmail.com', 'MHS02695', NULL, '$2y$12$kTrZdw.BMc6LycBhDJHloe8PF7p7NhGmstd93y2KWyFP3KlyIZDZS', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(8, 'fulan', 'mhsful6@gmail.com', 'MHS03594', NULL, '$2y$12$yA6M2Td7u3TRqX7jm9/wHeUDgkCwIYae1gX0C.pIMAr57OwuyRQxS', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(9, 'fulan', 'mhsful7@gmail.com', 'MHS04493', NULL, '$2y$12$bbvz1oNnc19HlVf3Jb70UOnDy58.DsqDiRXzhhHBx5qRc9OqIB6SW', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:53', '2025-07-10 12:58:53', NULL),
(10, 'fulan', 'mhsful8@gmail.com', 'MHS05392', NULL, '$2y$12$d8q0Iw7d8Zn3hV5N3ZTdmOdLqiUI0o7DBdFP2aRnuTGpMzqOh1jBe', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(11, 'fulan', 'mhsful9@gmail.com', 'MHS06291', NULL, '$2y$12$rXw0wH8WcWkwMXGj1Dusie7H3X3FLhq4mCmrWNiiyn3e32/ymzmUe', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(12, 'fulan', 'mhsful10@gmail.com', 'MHS07190', NULL, '$2y$12$m5oOXEdh2ghRl3zgBZcsLO9qTfbw5xZztE0/UIrFfEa3LIR.1loAy', 1, 1, 4, 'profile-photos/3uSjpp05w5JWUqgCdPhKInFBPpz190lbf4VxOlca.jpg', NULL, NULL, '2025-07-10 12:58:54', '2025-07-10 13:10:19', NULL),
(13, 'fulan', 'mhsful11@gmail.com', 'MHS08089', NULL, '$2y$12$E1UdG4ZNxd3QFAfo429hIOMSEV2yDV.dWs5GwoaQkq7vYIt/KJsFG', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:54', '2025-07-10 12:58:54', NULL),
(14, 'fulan', 'mhsful12@gmail.com', 'MHS08988', NULL, '$2y$12$Q6dnz4g3RL1aQC.8DEQ0EebsNelobpAbflcp1Na51QtXZMPV6MHY.', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(15, 'fulan', 'mhsful13@gmail.com', 'MHS09887', NULL, '$2y$12$uQb.E20eq/VPLqKYGAzUVelYKOF/xbQ59jTnLG6drLs7ZCsebCOre', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(16, 'fulan', 'mhsful14@gmail.com', 'MHS10786', NULL, '$2y$12$WbXqWq2lBug3Z9V8im5WRuCVgD402V2PgRsTVN.fG4NusdMyutkxe', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(17, 'fulan', 'mhsful15@gmail.com', 'MHS11685', NULL, '$2y$12$qUk5IKAJsIldM3OcBIstMuF/KjDz6Zk9SXsqaN/G788oo4j9iDo8i', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(18, 'fulan', 'mhsfup16@gmaip.com', 'MHS12584', NULL, '$2y$12$aCxeJj4q.CGqcJal9HQeGenPIQfMpSxcjxlC7BZfRyluypH.Yldu2', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:55', '2025-07-10 12:58:55', NULL),
(19, 'fulan', 'mhsfup17@gmaip.com', 'MHS13483', NULL, '$2y$12$N11SUv5XwE9RnqcZ3xDjDuEtVbNtcbrbYpdb4gdq4L5aAM3ReuJq2', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(20, 'fulan', 'mhsfup18@gmaip.com', 'MHS14382', NULL, '$2y$12$kkKRIIR8YxZYiL0FM6twCe48QshHi.p6XBIdnkEMPn7TZJZX.8tzq', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(21, 'fulan', 'mhsfup19@gmaip.com', 'MHS15281', NULL, '$2y$12$TaCNofnnv5WqZpuqeveh.eWzdIEyqztNknVneZ6RQejUqrLDrkkqS', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(22, 'fulan', 'mhsfup20@gmaip.com', 'MHS16180', NULL, '$2y$12$UivNlUKLjZNqGjJTVni0/uKMQrYPhU4rysusk607NbVsuQCwYHRKW', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:56', '2025-07-10 12:58:56', NULL),
(23, 'fulan', 'mhsfup21@gmaip.com', 'MHS17079', NULL, '$2y$12$N6BStDwqTogOofRowPLzXuTdd9Xy7oXYQVcsxjHVSyXK0KMdlPsze', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(24, 'fulan', 'mhsfup22@gmaip.com', 'MHS17978', NULL, '$2y$12$f73ZGD2BL6mFn/sbsm/MAOvTbYzk6gJP7agPtVw9fNe7C4rEGgk9i', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(25, 'fulan', 'mhsfup23@gmaip.com', 'MHS18877', NULL, '$2y$12$XBjHrSyzSGYL09IWiGrlLeyu.rPEfrUtTzpXjIhKqcdcT0zYv.pOq', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(26, 'fulan', 'mhsfup24@gmaip.com', 'MHS19776', NULL, '$2y$12$7LR1FHa3wYqE0OngkoLf0OBsyw4/Gix8dz1GRR6dSCvq/cvbvwEs2', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:57', '2025-07-10 12:58:57', NULL),
(27, 'fulan', 'mhsfup25@gmaip.com', 'MHS20675', NULL, '$2y$12$W86mu41vD8vvqD2vAWW.5OYkxAl6EaVuS5LPoVVOL84RV0nJpZP9m', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(28, 'fulan', 'mhsfup26@gmaip.com', 'MHS21574', NULL, '$2y$12$MYttEeSUxiB.wRL/qb6IEug6u7.or2sOJBY8swEfdSP9ER.6KUcNG', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(29, 'fulan', 'mhsfup27@gmaip.com', 'MHS22473', NULL, '$2y$12$ml7BEx3o8N7qD/KchOWGGu2jDuxEKHiOoemojI5MdWsdG1PDzJOue', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(30, 'fulan', 'mhsfup28@gmaip.com', 'MHS23372', NULL, '$2y$12$dQC6oRPwrA9fWr/6w9rR6.iYYllnf2ZChlXo9zBSaXIJ82yhF4Ycu', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:58', '2025-07-10 12:58:58', NULL),
(31, 'fulan', 'mhsfup29@gmaip.com', 'MHS24271', NULL, '$2y$12$hQOib33Epx3orjap8KOiROThqpcGFWGG6Tj67OcPd4cSNnWYv/aEy', 1, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:59', '2025-07-11 04:03:34', NULL),
(32, 'fulan', 'mhsfup30@gmaip.com', 'MHS25170', NULL, '$2y$12$/VdAqTBEl.zRtVeABreZP.h0eWKrTF8zYngfmop/2AqxBPShpvQEe', 0, 1, 4, NULL, NULL, NULL, '2025-07-10 12:58:59', '2025-07-10 12:58:59', NULL),
(33, 'fulan', 'dsn2@gmail.com', 'DSN002', NULL, '$2y$12$cFsU./Xg554KY5wQN2B8S.GuLFhGXWGJEDCOl/VFqAPe//4PmVYT2', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:30', '2025-07-10 13:04:30', NULL),
(34, 'fulan', 'dsn7@gmail.com', 'DSN003', NULL, '$2y$12$yFnmW5ZCL5IWloVxBsY6GOqGZdDjrsASjNJJVBjVjTzrpleKMGMn.', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(35, 'fulan', 'dsn6@gmail.com', 'DSN004', NULL, '$2y$12$F8Q/bjsHNhJs9srvikJmx.8ffxksbpDGcwJU8M1Cq.9WwVmb0PnlS', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(36, 'fulan', 'dsn5@gmail.com', 'DSN005', NULL, '$2y$12$GgrTI2BFjsgDa3JuI3q.rObLUrMVtlr3/UOncOxZzTqQ62N0XU4Ca', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(37, 'fulan', 'dsn4@gmail.com', 'DSN006', NULL, '$2y$12$IZKA3w6koh1ZzmLBhIbYGe6Xbe.kcfclY9fEucgZO4E5ZJWLzfKCu', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:31', '2025-07-10 13:04:31', NULL),
(38, 'fulan', 'dsn3@gmail.com', 'DSN007', NULL, '$2y$12$5IO1lCyN7VEWa/CmbM8wJ.pHoykBxudYxNIqtmtggNoSyiSdUVqcu', 0, 1, 3, NULL, NULL, NULL, '2025-07-10 13:04:32', '2025-07-10 13:04:32', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `ref_dosen`
--
ALTER TABLE `ref_dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref_dosen_kode_dosen_unique` (`kode_dosen`),
  ADD UNIQUE KEY `ref_dosen_nip_unique` (`nip`),
  ADD KEY `ref_dosen_user_id_foreign` (`user_id`);

--
-- Indexes for table `ref_mahasiswa`
--
ALTER TABLE `ref_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref_mahasiswa_nim_unique` (`nim`),
  ADD KEY `ref_mahasiswa_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tesis`
--
ALTER TABLE `tesis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_mhs_id_foreign` (`mhs_id`);

--
-- Indexes for table `tesis_dokumen`
--
ALTER TABLE `tesis_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_dokumen_tesis_id_foreign` (`tesis_id`);

--
-- Indexes for table `tesis_draft`
--
ALTER TABLE `tesis_draft`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_draft_mhs_id_foreign` (`mhs_id`),
  ADD KEY `tesis_draft_us_dospem_satu_foreign` (`us_dospem_satu`),
  ADD KEY `tesis_draft_us_dospem_dua_foreign` (`us_dospem_dua`);

--
-- Indexes for table `tesis_laporan`
--
ALTER TABLE `tesis_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_laporan_tesis_id_foreign` (`tesis_id`);

--
-- Indexes for table `tesis_logbook`
--
ALTER TABLE `tesis_logbook`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_logbook_tesis_id_foreign` (`tesis_id`);

--
-- Indexes for table `tesis_pembimbing`
--
ALTER TABLE `tesis_pembimbing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_pembimbing_tesis_id_foreign` (`tesis_id`),
  ADD KEY `tesis_pembimbing_dospem_foreign` (`dospem`);

--
-- Indexes for table `tesis_penguji`
--
ALTER TABLE `tesis_penguji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_penguji_tesis_id_foreign` (`tesis_id`),
  ADD KEY `tesis_penguji_penguji_foreign` (`penguji`);

--
-- Indexes for table `tesis_ujian`
--
ALTER TABLE `tesis_ujian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tesis_ujian_tesis_id_foreign` (`tesis_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nomor_induk_unique` (`nomor_induk`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ref_dosen`
--
ALTER TABLE `ref_dosen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ref_mahasiswa`
--
ALTER TABLE `ref_mahasiswa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tesis`
--
ALTER TABLE `tesis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_dokumen`
--
ALTER TABLE `tesis_dokumen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_draft`
--
ALTER TABLE `tesis_draft`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tesis_laporan`
--
ALTER TABLE `tesis_laporan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_logbook`
--
ALTER TABLE `tesis_logbook`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_pembimbing`
--
ALTER TABLE `tesis_pembimbing`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_penguji`
--
ALTER TABLE `tesis_penguji`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tesis_ujian`
--
ALTER TABLE `tesis_ujian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ref_dosen`
--
ALTER TABLE `ref_dosen`
  ADD CONSTRAINT `ref_dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ref_mahasiswa`
--
ALTER TABLE `ref_mahasiswa`
  ADD CONSTRAINT `ref_mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis`
--
ALTER TABLE `tesis`
  ADD CONSTRAINT `tesis_mhs_id_foreign` FOREIGN KEY (`mhs_id`) REFERENCES `ref_mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_dokumen`
--
ALTER TABLE `tesis_dokumen`
  ADD CONSTRAINT `tesis_dokumen_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_draft`
--
ALTER TABLE `tesis_draft`
  ADD CONSTRAINT `tesis_draft_mhs_id_foreign` FOREIGN KEY (`mhs_id`) REFERENCES `ref_mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tesis_draft_us_dospem_dua_foreign` FOREIGN KEY (`us_dospem_dua`) REFERENCES `ref_dosen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tesis_draft_us_dospem_satu_foreign` FOREIGN KEY (`us_dospem_satu`) REFERENCES `ref_dosen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_laporan`
--
ALTER TABLE `tesis_laporan`
  ADD CONSTRAINT `tesis_laporan_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_logbook`
--
ALTER TABLE `tesis_logbook`
  ADD CONSTRAINT `tesis_logbook_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_pembimbing`
--
ALTER TABLE `tesis_pembimbing`
  ADD CONSTRAINT `tesis_pembimbing_dospem_foreign` FOREIGN KEY (`dospem`) REFERENCES `ref_dosen` (`id`),
  ADD CONSTRAINT `tesis_pembimbing_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_penguji`
--
ALTER TABLE `tesis_penguji`
  ADD CONSTRAINT `tesis_penguji_penguji_foreign` FOREIGN KEY (`penguji`) REFERENCES `ref_dosen` (`id`),
  ADD CONSTRAINT `tesis_penguji_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tesis_ujian`
--
ALTER TABLE `tesis_ujian`
  ADD CONSTRAINT `tesis_ujian_tesis_id_foreign` FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
