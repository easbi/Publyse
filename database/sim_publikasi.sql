-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2025 pada 10.51
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
-- Database: `sim_publikasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `page_number` int(11) NOT NULL,
  `type` enum('point','area') NOT NULL,
  `position` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`position`)),
  `content` text NOT NULL,
  `status` enum('open','done') NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `uploader_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `publication_id`, `original_filename`, `stored_path`, `version`, `uploader_id`, `created_at`, `updated_at`) VALUES
(2, 2, 'Komponen Pilar 6 ZI.pdf', 'documents/poXno61xhjpIA8kHQzL1WPbvbPE2xlI7s1ZsYRzs.pdf', 1, 1, '2025-06-25 23:08:03', '2025-06-25 23:08:03'),
(3, 3, 'Statistik Hortikultura Kota Padang Panjang 2024.pdf', 'documents/wTo59zZeNL3qsRoxR5SCgvlwuHJBFuexyZho6cq2.pdf', 1, 1, '2025-06-26 01:51:00', '2025-06-26 01:51:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_06_25_055624_add_nip_to_users_table', 1),
(6, '2025_06_25_055714_create_publications_table', 1),
(7, '2025_06_25_055816_create_documents_table', 1),
(8, '2025_06_25_055847_create_publication_user_table', 1),
(9, '2025_06_25_055921_create_comments_table', 1),
(10, '2025_06_26_055413_add_parent_id_to_comments_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `publications`
--

CREATE TABLE `publications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `release_date` date NOT NULL,
  `review_deadline` date NOT NULL,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `publications`
--

INSERT INTO `publications` (`id`, `name`, `release_date`, `review_deadline`, `creator_id`, `created_at`, `updated_at`) VALUES
(2, 'Publikasi Statistik Daerah Kota Padang Panjang', '2025-06-26', '2025-06-27', 1, '2025-06-25 23:08:03', '2025-06-25 23:08:03'),
(3, 'Publikasi Kota Padang Panjang Dalam Angka 2026', '2025-06-14', '2025-06-28', 1, '2025-06-26 01:51:00', '2025-06-26 01:51:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `publication_user`
--

CREATE TABLE `publication_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `assignor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `publication_user`
--

INSERT INTO `publication_user` (`id`, `publication_id`, `reviewer_id`, `assignor_id`, `created_at`, `updated_at`) VALUES
(2, 3, 2, 1, '2025-06-26 01:51:21', '2025-06-26 01:51:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `nip`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Pembuat', '111111', 'pembuat@bps.go.id', NULL, '$2y$10$Pu.06cKEm7JrwbVI3XPxTeLN1HX666dJocIlPo/FmQTENnksdGUA6', NULL, '2025-06-25 23:06:16', '2025-06-25 23:06:16'),
(2, 'Susi Pemeriksa', '222222', 'pemeriksa@bps.go.id', NULL, '$2y$10$eN4ebLU6V9yKuguanbpxmOVf2br6AlKQrbWtECLlv.mZsq7RnKAL2', NULL, '2025-06-25 23:06:16', '2025-06-25 23:06:16');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_document_id_foreign` (`document_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`);

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_publication_id_foreign` (`publication_id`),
  ADD KEY `documents_uploader_id_foreign` (`uploader_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publications_creator_id_foreign` (`creator_id`);

--
-- Indeks untuk tabel `publication_user`
--
ALTER TABLE `publication_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publication_user_publication_id_reviewer_id_unique` (`publication_id`,`reviewer_id`),
  ADD KEY `publication_user_reviewer_id_foreign` (`reviewer_id`),
  ADD KEY `publication_user_assignor_id_foreign` (`assignor_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nip_unique` (`nip`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `publications`
--
ALTER TABLE `publications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `publication_user`
--
ALTER TABLE `publication_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_uploader_id_foreign` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `publication_user`
--
ALTER TABLE `publication_user`
  ADD CONSTRAINT `publication_user_assignor_id_foreign` FOREIGN KEY (`assignor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `publication_user_publication_id_foreign` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publication_user_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
