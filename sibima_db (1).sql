-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jun 2026 pada 06.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sibima_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity`, `description`, `module`, `subject_type`, `subject_id`, `properties`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(858, 2, 'Data Diperbarui', 'Terdapat pembaruan data pada User ID: 2', 'User', 'App\\Models\\User', 2, '{\"before\":{\"remember_token\":\"a7jBiCFMipbLrN4c35tMWywGo1YoisvXOWICnBigbyw2n6anvAMvkK4rzxLE\"},\"after\":{\"remember_token\":\"Wos4vkgpugZm62fZte5CRjEoViNLibsY8MIUToqDg8l5PPXbOEAThEn2WC15\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-23 04:42:35', '2026-06-23 04:42:35'),
(859, NULL, 'Logout', 'User keluar dari sistem.', 'Auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-23 04:42:35', '2026-06-23 04:42:35'),
(860, 5, 'Login', 'User berhasil login ke sistem.', 'Auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-23 04:43:08', '2026-06-23 04:43:08'),
(861, NULL, 'Logout', 'User keluar dari sistem.', 'Auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-23 04:43:24', '2026-06-23 04:43:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('info','warning','important') NOT NULL DEFAULT 'info',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('sibima-fasilkom-cache-bagus@sibima.com|127.0.0.1', 'i:1;', 1781227771),
('sibima-fasilkom-cache-bagus@sibima.com|127.0.0.1:timer', 'i:1781227771;', 1781227771),
('sibima-fasilkom-cache-dosen@sibima.com|127.0.0.1', 'i:1;', 1781228961),
('sibima-fasilkom-cache-dosen@sibima.com|127.0.0.1:timer', 'i:1781228961;', 1781228961);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `letter_settings`
--

CREATE TABLE `letter_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `last_number` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `letter_settings`
--

INSERT INTO `letter_settings` (`id`, `type`, `format`, `last_number`, `title`, `created_at`, `updated_at`) VALUES
(1, 'sk_penguji_seminar', '[51]/UNSUB/FIK/[VI]/[2026]', 57, 'SK Tim Penguji Seminar', '2026-06-11 11:26:16', '2026-06-11 12:06:54'),
(2, 'sk_penguji_sidang', '[NUMBER]/SK/UNSUB/FIK/[MONTH]/[YEAR]', 0, 'SK Tim Penguji Sidang', '2026-06-11 11:26:16', '2026-06-11 11:58:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `logbooks`
--

CREATE TABLE `logbooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `progress_notes` text NOT NULL,
  `lecturer_notes` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mentoring_sessions`
--

CREATE TABLE `mentoring_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED NOT NULL,
  `dosen_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `topic` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('offline','online') NOT NULL DEFAULT 'offline',
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `document_original_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_01_230555_create_theses_table', 1),
(5, '2026_05_01_230556_create_logbooks_table', 1),
(6, '2026_05_01_230557_create_mentoring_sessions_table', 1),
(7, '2026_05_03_012156_add_final_title_to_theses_table', 1),
(8, '2026_05_03_015045_add_type_and_location_to_mentoring_sessions_table', 1),
(9, '2026_05_03_022117_add_absent_to_mentoring_sessions_status', 1),
(10, '2026_05_03_022951_add_feedback_to_mentoring_sessions_table', 1),
(11, '2026_05_03_071000_create_messages_table', 1),
(12, '2026_05_04_120900_add_requested_pembimbing_to_theses_table', 1),
(13, '2026_05_04_132143_add_document_to_mentoring_sessions_table', 1),
(14, '2026_05_04_135230_add_acc_status_to_theses_table', 1),
(15, '2026_05_04_141255_add_dosen_id_to_mentoring_sessions_table', 1),
(16, '2026_05_04_151153_create_announcements_table', 1),
(17, '2026_05_04_152230_create_seminar_applications_table', 1),
(18, '2026_05_04_225507_add_file_formulir_to_seminar_applications', 1),
(19, '2026_05_04_225513_create_seminar_templates_table', 1),
(20, '2026_05_05_045146_add_is_active_to_users_table', 1),
(21, '2026_05_05_050719_create_notifications_table', 1),
(22, '2026_05_05_052328_create_activity_logs_table', 1),
(23, '2026_05_05_101507_create_seminar_schedules_table', 1),
(24, '2026_05_05_101508_create_seminar_schedule_details_table', 1),
(25, '2026_05_05_140418_create_thesis_defense_applications_table', 1),
(26, '2026_05_05_140458_create_thesis_defense_templates_table', 1),
(27, '2026_05_05_151647_create_thesis_defense_schedules_table', 1),
(28, '2026_05_05_151648_create_thesis_defense_schedule_details_table', 1),
(29, '2026_05_08_213937_add_avatar_to_users_table', 1),
(30, '2026_05_08_225523_create_seminar_revisions_table', 1),
(31, '2026_05_08_230618_add_student_reply_to_seminar_revisions_table', 1),
(32, '2026_05_08_231626_change_status_in_seminar_revisions_table', 1),
(33, '2026_05_08_232203_create_seminar_revision_messages_table', 1),
(34, '2026_05_10_000001_create_thesis_defense_revisions_table', 1),
(35, '2026_05_10_000002_create_thesis_defense_revision_messages_table', 1),
(36, '2026_05_11_130319_add_scores_to_thesis_defense_revisions_table', 1),
(37, '2026_05_11_171534_create_waves_table', 1),
(38, '2026_05_11_171553_add_wave_id_to_applications_table', 1),
(39, '2026_05_11_172613_add_dates_to_waves_table', 1),
(40, '2026_05_11_173110_add_wave_id_to_schedules_tables', 1),
(41, '2026_05_11_175938_add_file_reviews_to_applications_tables', 1),
(42, '2026_05_11_181857_add_meeting_link_to_schedules_tables', 1),
(43, '2026_05_11_230553_add_entry_year_to_users_table', 1),
(44, '2026_05_11_230556_add_entry_year_to_users_table', 1),
(45, '2026_05_12_084002_add_phone_number_to_users_table', 1),
(46, '2026_05_12_084604_add_signature_to_users_table', 1),
(47, '2026_05_15_091943_add_scores_to_seminar_revisions_table', 1),
(48, '2026_05_15_142911_add_polymorphic_fields_to_activity_logs_table', 1),
(49, '2026_05_15_155733_add_verification_token_to_schedule_details_tables', 1),
(50, '2026_05_15_200647_create_letter_settings_table', 1),
(51, '2026_05_15_203918_add_workload_fields_to_users_table', 1),
(52, '2026_05_16_070524_add_topic_to_theses_table', 1),
(53, '2026_05_31_020000_add_kaprodi_role_to_users_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_applications`
--

CREATE TABLE `seminar_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED NOT NULL,
  `file_acc_pembimbing` varchar(255) NOT NULL,
  `file_pembayaran` varchar(255) NOT NULL,
  `file_kartu_bimbingan` varchar(255) NOT NULL,
  `file_skripsi` varchar(255) NOT NULL,
  `file_formulir` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_reviews` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_reviews`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_revisions`
--

CREATE TABLE `seminar_revisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seminar_schedule_detail_id` bigint(20) UNSIGNED NOT NULL,
  `examiner_id` bigint(20) UNSIGNED NOT NULL,
  `revision_notes` text DEFAULT NULL,
  `revision_file` varchar(255) DEFAULT NULL,
  `student_notes` text DEFAULT NULL,
  `student_file` varchar(255) DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `score_presentation` int(11) DEFAULT NULL,
  `score_explanation` int(11) DEFAULT NULL,
  `score_writing` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_revision_messages`
--

CREATE TABLE `seminar_revision_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seminar_revision_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_schedules`
--

CREATE TABLE `seminar_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `chairman_id` bigint(20) UNSIGNED NOT NULL,
  `moderator_id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_schedule_details`
--

CREATE TABLE `seminar_schedule_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `seminar_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_name` varchar(255) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `examiner1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `examiner2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `seminar_templates`
--

CREATE TABLE `seminar_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `seminar_templates`
--

INSERT INTO `seminar_templates` (`id`, `title`, `file_path`, `original_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Formulir pendaftaran seminar', 'seminar_templates/es2unW16QRcFuR4Arv26zEaQiwFqq5e5cUDyiLL6.docx', 'FORMULIR PENDAFTARAN SEMINAR SKRIPSI-TUGAS AKHIR.docx', 1, '2026-06-09 06:26:23', '2026-06-09 06:26:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('PAJbv7tVdhCggOOKnhb7qJlhHfCDjXmT2gqpp3El', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRU5HdXp4eThueXczb3N6RnNrcUlpS2YyNzl4REZCY3VlZ2U5NUFuaSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781372291),
('v4sHUY3dpitVaxOcGeyHuSw4JIGnSdfCYqHCBrVf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFhaNVdFaDhnbk0zSEJCT2RON1Z5cWNOalRYWHRJTUtRVVZCN3AySiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9zaWJpbWEudGVzdC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1782190414);

-- --------------------------------------------------------

--
-- Struktur dari tabel `theses`
--

CREATE TABLE `theses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `pembimbing1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pembimbing2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requested_pembimbing1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requested_pembimbing2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `final_title` varchar(255) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','completed','rejected') NOT NULL DEFAULT 'pending',
  `acc_up_p1` tinyint(1) NOT NULL DEFAULT 0,
  `acc_up_p2` tinyint(1) NOT NULL DEFAULT 0,
  `acc_sidang_p1` tinyint(1) NOT NULL DEFAULT 0,
  `acc_sidang_p2` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_applications`
--

CREATE TABLE `thesis_defense_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED NOT NULL,
  `file_formulir` varchar(255) NOT NULL,
  `file_transkrip` varchar(255) NOT NULL,
  `file_acc_pembimbing` varchar(255) NOT NULL,
  `file_logbook` varchar(255) NOT NULL,
  `file_pembayaran` varchar(255) NOT NULL,
  `file_skripsi` varchar(255) NOT NULL,
  `file_ktm` varchar(255) NOT NULL,
  `file_pkkmb_univ` varchar(255) NOT NULL,
  `file_pkkmb_fak` varchar(255) NOT NULL,
  `file_makrab` varchar(255) NOT NULL,
  `file_cisco` varchar(255) NOT NULL,
  `file_workshop` varchar(255) NOT NULL,
  `file_organisasi` varchar(255) NOT NULL,
  `file_toefl` varchar(255) NOT NULL,
  `file_kewirausahaan` varchar(255) NOT NULL,
  `file_tahsin` varchar(255) NOT NULL,
  `file_komputer` varchar(255) NOT NULL,
  `file_perpus_pinjam` varchar(255) NOT NULL,
  `file_perpus_sumbang` varchar(255) NOT NULL,
  `file_ijazah` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_reviews` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_reviews`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_revisions`
--

CREATE TABLE `thesis_defense_revisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_defense_schedule_detail_id` bigint(20) UNSIGNED NOT NULL,
  `examiner_id` bigint(20) UNSIGNED NOT NULL,
  `revision_notes` text DEFAULT NULL,
  `revision_file` varchar(255) DEFAULT NULL,
  `student_notes` text DEFAULT NULL,
  `student_file` varchar(255) DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `score_presentation` int(11) DEFAULT NULL,
  `score_explanation` int(11) DEFAULT NULL,
  `score_writing` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_revision_messages`
--

CREATE TABLE `thesis_defense_revision_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thesis_defense_revision_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_schedules`
--

CREATE TABLE `thesis_defense_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `chairman_id` bigint(20) UNSIGNED NOT NULL,
  `moderator_id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_schedule_details`
--

CREATE TABLE `thesis_defense_schedule_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `thesis_defense_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `thesis_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_name` varchar(255) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `examiner1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `examiner2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `thesis_defense_templates`
--

CREATE TABLE `thesis_defense_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `thesis_defense_templates`
--

INSERT INTO `thesis_defense_templates` (`id`, `title`, `file_path`, `original_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Formulir pendaftaran sidang', 'thesis_defense_templates/sAnhHeotLWcwnrRDQeNHPpYvXKGEMeiBPbeSZl7F.docx', 'FORMULIR PENDAFTARAN SIDANG SKRIPSI-TUGAS AKHIR.docx', 1, '2026-06-09 15:50:11', '2026-06-09 15:50:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL COMMENT 'Path to signature image',
  `signature_token` varchar(255) DEFAULT NULL COMMENT 'Token for QR verification',
  `role` enum('admin','kaprodi','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `identifier` varchar(255) DEFAULT NULL COMMENT 'NPM or NIDN',
  `research_interests` text DEFAULT NULL,
  `max_quota` int(11) NOT NULL DEFAULT 8,
  `entry_year` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `signature`, `signature_token`, `role`, `is_active`, `identifier`, `research_interests`, `max_quota`, `entry_year`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin SIBIMA', 'admin@sibima.com', NULL, NULL, NULL, 'admin', 1, '0000000000', NULL, 8, NULL, NULL, '2026-05-31 02:28:43', '$2y$12$/.QyOYY8NsNe9.HjDCstheBMbZplVNmi13.iaLTNCScOC9MQBOxX6', 'mf6bHrrHMLuU6vMnVl0wGpT8BJZGIZEi1jfEQyFuPwMBjYZO2vlscQ4FtmS3', '2026-05-31 02:28:43', '2026-05-31 02:28:43'),
(2, 'Bagus Ali Akbar, S.SI., M.Kom', 'kaprodi@sibima.com', 'eyJpdiI6Impxb2xUejIwNGV4VllmOUNEclcrK1E9PSIsInZhbHVlIjoiMEhSQzJXR2VwekNCd1VVWmtEYWMvQT09IiwibWFjIjoiMjZlNzBiOWZkMjY0Y2EzYWRkZTZhNDVkYzNiYTU3OGVjNmM2NGVlNDZkZDg1NjkyNDdiNGI3MTVhNjAyZWE3MyIsInRhZyI6IiJ9', 'signatures/J43MPdXFMNr49PzoxzvpAumrh1iTzHcdnKLh58pU.enc', 'd00a6d3d-1263-43c7-950f-6137c19d0ed2', 'kaprodi', 1, '09112', NULL, 8, NULL, 'avatars/7NjxtK3YmZhEn8V6O3nxPWHJ6FqFVBMVdIUJcTCt.jpg', '2026-05-31 02:28:43', '$2y$12$/.QyOYY8NsNe9.HjDCstheBMbZplVNmi13.iaLTNCScOC9MQBOxX6', 'Wos4vkgpugZm62fZte5CRjEoViNLibsY8MIUToqDg8l5PPXbOEAThEn2WC15', '2026-05-31 02:28:43', '2026-06-11 09:11:52'),
(3, 'Tazkia Salsabila Ardan, M.Kom', 'tazkiaardan@unsub.ac.id', NULL, 'signatures/74k3hJvXnH2WgvZXpJVXfZmCEFUPSuKbiLQWSwK8.enc', 'dec5628a-15b7-497e-ba1b-e9fadd9f6d81', 'dosen', 1, '0405079402', NULL, 8, NULL, 'avatars/X1MJWU76uZ97EexSB9NDdlcqVHBv89KVGyhnqWAd.jpg', NULL, '$2y$12$L85qXBaPOQ.izWZCFl.QRuwJsv.G4di9TWLrxiuBr2tvK6McoMIJ2', 'IayNU4HnJY9CryFJUphAzU1HniuJm07Muu4V64z2826CUmIq9EoBB5jkm42V', '2026-05-31 02:28:43', '2026-06-11 09:10:04'),
(4, 'Mahasiswa Skripsi 1', 'mahasiswa1@sibima.com', NULL, NULL, NULL, 'mahasiswa', 1, '2000000000', NULL, 8, NULL, NULL, NULL, '$2y$12$/.QyOYY8NsNe9.HjDCstheBMbZplVNmi13.iaLTNCScOC9MQBOxX6', 'IET4V3iimC64eBuxnB53nOQFxkbtNCkGVFiJ5i33zutp2Zlg0cwAFmfFHpXy', '2026-05-31 02:28:43', '2026-06-07 08:38:12'),
(5, 'Mahasiswa Skripsi 2', 'mahasiswa2@sibima.com', NULL, NULL, NULL, 'mahasiswa', 1, '3000000000', NULL, 8, NULL, NULL, NULL, '$2y$12$W7iX6whVsdaox4ANwZH3FOFVeOtt5WG4vQ1uvRxD8zS4qw8dfpZhW', NULL, '2026-06-07 08:41:34', '2026-06-07 08:41:34'),
(6, 'Bagus Ali Akbar, S.SI., M.Kom', 'bagusaliakbar@unsub.ac.id', NULL, 'signatures/IWGBMV2mavPCaPr8B6Rp5YigLwrTy146gz8KNeGW.enc', '4777ddcc-b97b-4b20-af52-c84fef27dddf', 'dosen', 1, '0410019202', NULL, 8, NULL, 'avatars/fH0emKh2e3QaMyuSynskOARC4EBxWjdTWq0akm2h.jpg', NULL, '$2y$12$UzARTnCLbdy.Y7mYj3R8lOm1UNghy7.spxTSVHPvv9FNtnRDIAHnS', NULL, '2026-06-07 08:59:03', '2026-06-11 02:40:34'),
(7, 'Maya Destriani, M.Kom', 'mayadestriani@unsub.ac.id', NULL, 'signatures/yKZTH8UikrSoVGZcTcNCsxaohXUppoHwoGvvDWQx.enc', 'd17e96e0-5387-4b5e-bcbe-9e006e3c86c6', 'dosen', 1, '0402129501', NULL, 8, NULL, 'avatars/oa5oFlNxtErmOGAAXoC0Z79LWJeMJ9GGDYiL8pIS.jpg', NULL, '$2y$12$kyjBQf3lfGXqeLwpOzBTHednOAY6NNjLiouedNNp5z3ksNUh2LSVK', NULL, '2026-06-07 09:02:16', '2026-06-11 02:59:54'),
(8, 'Dr. Tepi Peirisal, S.Sos., M.Si', 'tepipeirisal@unsub.ac.id', NULL, 'signatures/90phABNS07wTm4wMbmOf1ybrb86PIvAfR7VKUlJ4.enc', '301a1c91-10e2-4ca1-a611-bba15072193c', 'dosen', 1, '0918822', NULL, 8, NULL, 'avatars/fUVSQSkxiEL7T4QmNWqixtmEo3H8G5upwm8uFvGL.jpg', NULL, '$2y$12$qIOhna/pMGfuk200GnMtA.ZeDf.BQQ1J8DCPx7M6089m33z9R3Rpe', NULL, '2026-06-09 09:57:26', '2026-06-11 09:10:33'),
(9, 'Mahasiswa Skripsi 3', 'mahasiswa3@sibima.com', NULL, NULL, NULL, 'mahasiswa', 1, '5000000000', NULL, 8, NULL, 'avatars/sD3BNwYWpocjcbgAgOZl49x61qToHsuEkNAvS8lD.jpg', NULL, '$2y$12$cDlN5gaAtf5b87GIHSv.m.OxcKVeAcZuP8bIVjtVsQOpM7AlECohm', NULL, '2026-06-10 02:17:54', '2026-06-12 03:55:12'),
(10, 'Jaja, M.Kom', 'jaja@unsub.ac.id', NULL, NULL, NULL, 'dosen', 1, '0192873', NULL, 8, NULL, NULL, NULL, '$2y$12$1ib1JTeRdCVitTE8Wsj8WefWOJ9.EZUgBBlm4Hed1N6TvXN3CWqFe', NULL, '2026-06-10 07:17:40', '2026-06-10 07:17:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `waves`
--

CREATE TABLE `waves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`);

--
-- Indeks untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `letter_settings`
--
ALTER TABLE `letter_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `letter_settings_type_unique` (`type`);

--
-- Indeks untuk tabel `logbooks`
--
ALTER TABLE `logbooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logbooks_thesis_id_foreign` (`thesis_id`);

--
-- Indeks untuk tabel `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentoring_sessions_thesis_id_foreign` (`thesis_id`),
  ADD KEY `mentoring_sessions_dosen_id_foreign` (`dosen_id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `seminar_applications`
--
ALTER TABLE `seminar_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seminar_applications_thesis_id_foreign` (`thesis_id`),
  ADD KEY `seminar_applications_wave_id_foreign` (`wave_id`);

--
-- Indeks untuk tabel `seminar_revisions`
--
ALTER TABLE `seminar_revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seminar_revisions_seminar_schedule_detail_id_foreign` (`seminar_schedule_detail_id`),
  ADD KEY `seminar_revisions_examiner_id_foreign` (`examiner_id`);

--
-- Indeks untuk tabel `seminar_revision_messages`
--
ALTER TABLE `seminar_revision_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seminar_revision_messages_seminar_revision_id_foreign` (`seminar_revision_id`),
  ADD KEY `seminar_revision_messages_sender_id_foreign` (`sender_id`);

--
-- Indeks untuk tabel `seminar_schedules`
--
ALTER TABLE `seminar_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seminar_schedules_chairman_id_foreign` (`chairman_id`),
  ADD KEY `seminar_schedules_moderator_id_foreign` (`moderator_id`),
  ADD KEY `seminar_schedules_created_by_foreign` (`created_by`),
  ADD KEY `seminar_schedules_wave_id_foreign` (`wave_id`);

--
-- Indeks untuk tabel `seminar_schedule_details`
--
ALTER TABLE `seminar_schedule_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seminar_schedule_details_verification_token_unique` (`verification_token`),
  ADD KEY `seminar_schedule_details_seminar_schedule_id_foreign` (`seminar_schedule_id`),
  ADD KEY `seminar_schedule_details_thesis_id_foreign` (`thesis_id`),
  ADD KEY `seminar_schedule_details_examiner1_id_foreign` (`examiner1_id`),
  ADD KEY `seminar_schedule_details_examiner2_id_foreign` (`examiner2_id`);

--
-- Indeks untuk tabel `seminar_templates`
--
ALTER TABLE `seminar_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `theses`
--
ALTER TABLE `theses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theses_student_id_foreign` (`student_id`),
  ADD KEY `theses_pembimbing1_id_foreign` (`pembimbing1_id`),
  ADD KEY `theses_pembimbing2_id_foreign` (`pembimbing2_id`),
  ADD KEY `theses_requested_pembimbing1_id_foreign` (`requested_pembimbing1_id`),
  ADD KEY `theses_requested_pembimbing2_id_foreign` (`requested_pembimbing2_id`);

--
-- Indeks untuk tabel `thesis_defense_applications`
--
ALTER TABLE `thesis_defense_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thesis_defense_applications_thesis_id_foreign` (`thesis_id`),
  ADD KEY `thesis_defense_applications_wave_id_foreign` (`wave_id`);

--
-- Indeks untuk tabel `thesis_defense_revisions`
--
ALTER TABLE `thesis_defense_revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `td_rev_schedule_detail_id` (`thesis_defense_schedule_detail_id`),
  ADD KEY `thesis_defense_revisions_examiner_id_foreign` (`examiner_id`);

--
-- Indeks untuk tabel `thesis_defense_revision_messages`
--
ALTER TABLE `thesis_defense_revision_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `td_rev_msg_revision_id` (`thesis_defense_revision_id`),
  ADD KEY `thesis_defense_revision_messages_sender_id_foreign` (`sender_id`);

--
-- Indeks untuk tabel `thesis_defense_schedules`
--
ALTER TABLE `thesis_defense_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thesis_defense_schedules_chairman_id_foreign` (`chairman_id`),
  ADD KEY `thesis_defense_schedules_moderator_id_foreign` (`moderator_id`),
  ADD KEY `thesis_defense_schedules_created_by_foreign` (`created_by`),
  ADD KEY `thesis_defense_schedules_wave_id_foreign` (`wave_id`);

--
-- Indeks untuk tabel `thesis_defense_schedule_details`
--
ALTER TABLE `thesis_defense_schedule_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `thesis_defense_schedule_details_verification_token_unique` (`verification_token`),
  ADD KEY `td_sch_id_fk` (`thesis_defense_schedule_id`),
  ADD KEY `thesis_defense_schedule_details_thesis_id_foreign` (`thesis_id`),
  ADD KEY `thesis_defense_schedule_details_examiner1_id_foreign` (`examiner1_id`),
  ADD KEY `thesis_defense_schedule_details_examiner2_id_foreign` (`examiner2_id`);

--
-- Indeks untuk tabel `thesis_defense_templates`
--
ALTER TABLE `thesis_defense_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `waves`
--
ALTER TABLE `waves`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=862;

--
-- AUTO_INCREMENT untuk tabel `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `letter_settings`
--
ALTER TABLE `letter_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `logbooks`
--
ALTER TABLE `logbooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `seminar_applications`
--
ALTER TABLE `seminar_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `seminar_revisions`
--
ALTER TABLE `seminar_revisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `seminar_revision_messages`
--
ALTER TABLE `seminar_revision_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `seminar_schedules`
--
ALTER TABLE `seminar_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `seminar_schedule_details`
--
ALTER TABLE `seminar_schedule_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `seminar_templates`
--
ALTER TABLE `seminar_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `theses`
--
ALTER TABLE `theses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_applications`
--
ALTER TABLE `thesis_defense_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_revisions`
--
ALTER TABLE `thesis_defense_revisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_revision_messages`
--
ALTER TABLE `thesis_defense_revision_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_schedules`
--
ALTER TABLE `thesis_defense_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_schedule_details`
--
ALTER TABLE `thesis_defense_schedule_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `thesis_defense_templates`
--
ALTER TABLE `thesis_defense_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `waves`
--
ALTER TABLE `waves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `logbooks`
--
ALTER TABLE `logbooks`
  ADD CONSTRAINT `logbooks_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mentoring_sessions`
--
ALTER TABLE `mentoring_sessions`
  ADD CONSTRAINT `mentoring_sessions_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentoring_sessions_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `seminar_applications`
--
ALTER TABLE `seminar_applications`
  ADD CONSTRAINT `seminar_applications_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_applications_wave_id_foreign` FOREIGN KEY (`wave_id`) REFERENCES `waves` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `seminar_revisions`
--
ALTER TABLE `seminar_revisions`
  ADD CONSTRAINT `seminar_revisions_examiner_id_foreign` FOREIGN KEY (`examiner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_revisions_seminar_schedule_detail_id_foreign` FOREIGN KEY (`seminar_schedule_detail_id`) REFERENCES `seminar_schedule_details` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `seminar_revision_messages`
--
ALTER TABLE `seminar_revision_messages`
  ADD CONSTRAINT `seminar_revision_messages_seminar_revision_id_foreign` FOREIGN KEY (`seminar_revision_id`) REFERENCES `seminar_revisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_revision_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `seminar_schedules`
--
ALTER TABLE `seminar_schedules`
  ADD CONSTRAINT `seminar_schedules_chairman_id_foreign` FOREIGN KEY (`chairman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedules_moderator_id_foreign` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedules_wave_id_foreign` FOREIGN KEY (`wave_id`) REFERENCES `waves` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `seminar_schedule_details`
--
ALTER TABLE `seminar_schedule_details`
  ADD CONSTRAINT `seminar_schedule_details_examiner1_id_foreign` FOREIGN KEY (`examiner1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedule_details_examiner2_id_foreign` FOREIGN KEY (`examiner2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedule_details_seminar_schedule_id_foreign` FOREIGN KEY (`seminar_schedule_id`) REFERENCES `seminar_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seminar_schedule_details_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `theses`
--
ALTER TABLE `theses`
  ADD CONSTRAINT `theses_pembimbing1_id_foreign` FOREIGN KEY (`pembimbing1_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `theses_pembimbing2_id_foreign` FOREIGN KEY (`pembimbing2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `theses_requested_pembimbing1_id_foreign` FOREIGN KEY (`requested_pembimbing1_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `theses_requested_pembimbing2_id_foreign` FOREIGN KEY (`requested_pembimbing2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `theses_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `thesis_defense_applications`
--
ALTER TABLE `thesis_defense_applications`
  ADD CONSTRAINT `thesis_defense_applications_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_applications_wave_id_foreign` FOREIGN KEY (`wave_id`) REFERENCES `waves` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `thesis_defense_revisions`
--
ALTER TABLE `thesis_defense_revisions`
  ADD CONSTRAINT `td_rev_schedule_detail_id` FOREIGN KEY (`thesis_defense_schedule_detail_id`) REFERENCES `thesis_defense_schedule_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_revisions_examiner_id_foreign` FOREIGN KEY (`examiner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `thesis_defense_revision_messages`
--
ALTER TABLE `thesis_defense_revision_messages`
  ADD CONSTRAINT `td_rev_msg_revision_id` FOREIGN KEY (`thesis_defense_revision_id`) REFERENCES `thesis_defense_revisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_revision_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `thesis_defense_schedules`
--
ALTER TABLE `thesis_defense_schedules`
  ADD CONSTRAINT `thesis_defense_schedules_chairman_id_foreign` FOREIGN KEY (`chairman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedules_moderator_id_foreign` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedules_wave_id_foreign` FOREIGN KEY (`wave_id`) REFERENCES `waves` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `thesis_defense_schedule_details`
--
ALTER TABLE `thesis_defense_schedule_details`
  ADD CONSTRAINT `td_sch_id_fk` FOREIGN KEY (`thesis_defense_schedule_id`) REFERENCES `thesis_defense_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedule_details_examiner1_id_foreign` FOREIGN KEY (`examiner1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedule_details_examiner2_id_foreign` FOREIGN KEY (`examiner2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_defense_schedule_details_thesis_id_foreign` FOREIGN KEY (`thesis_id`) REFERENCES `theses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
