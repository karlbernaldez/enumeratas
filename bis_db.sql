-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 04:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blotter_reports`
--

CREATE TABLE `blotter_reports` (
  `id` int(11) UNSIGNED NOT NULL,
  `complainant_user_id` int(11) UNSIGNED NOT NULL,
  `complainant_name` varchar(150) NOT NULL,
  `complainant_email` varchar(150) DEFAULT NULL,
  `incident_type` varchar(100) NOT NULL,
  `incident_date` date DEFAULT NULL,
  `incident_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `persons_involved` text DEFAULT NULL,
  `narrative` text NOT NULL,
  `respondent_name` varchar(150) DEFAULT NULL,
  `respondent_email` varchar(150) DEFAULT NULL,
  `respondent_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','under_investigation','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `processed_by` int(11) UNSIGNED DEFAULT NULL,
  `summons_sent_at` datetime DEFAULT NULL,
  `hearing_date` date DEFAULT NULL,
  `hearing_time` time DEFAULT NULL,
  `hearing_notes` text DEFAULT NULL,
  `scheduled_by` int(11) UNSIGNED DEFAULT NULL,
  `letter_issued_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_reports`
--

INSERT INTO `blotter_reports` (`id`, `complainant_user_id`, `complainant_name`, `complainant_email`, `incident_type`, `incident_date`, `incident_time`, `location`, `persons_involved`, `narrative`, `respondent_name`, `respondent_email`, `respondent_address`, `status`, `remarks`, `processed_by`, `summons_sent_at`, `hearing_date`, `hearing_time`, `hearing_notes`, `scheduled_by`, `letter_issued_at`, `created_at`, `updated_at`) VALUES
(1, 6, 'Juan Dela Cruz', 'juandelacruz@gmail.com', 'Physical Altercation / Fight', '2026-05-05', '20:00:00', 'Colico\'s Resident', 'Mark Santiago Argarin', 'heheheheheheheheheh', 'Ma.Joy Colico', 'ma.colico@my.cspc.edu.ph', 'zone 5 bacolod, bato, camarines sur', 'under_investigation', NULL, 7, '2026-05-10 06:52:45', '2026-05-22', '13:00:00', NULL, NULL, '2026-05-10 06:52:55', '2026-05-06 15:17:19', '2026-05-10 06:52:55'),
(2, 6, 'Juan Dela Cruz', 'juandelacruz@gmail.com', 'Physical Altercation / Fight', '2026-05-04', '09:00:00', 'Colico\'s Resident', 'Mark Santiago Argarin', 'uwiekjsdsbdnsndjamsnmruowiskmdcv', 'Mariane Bantayan', 'mabantayan@my.cspc.edu.ph', 'Nabua, Camarines Sur', 'under_investigation', '', 7, '2026-05-06 15:20:12', '2026-05-27', '10:00:00', NULL, NULL, '2026-05-11 00:56:15', '2026-05-06 15:17:59', '2026-05-11 00:56:15'),
(3, 10, 'Ma.Joy Colico', 'majoycolico@gmail.com', 'Verbal Abuse / Harassment', '2026-05-04', '11:11:00', 'zone 2', 'lordan mary ann', 'kapay kuno ya', '', '', '', 'under_investigation', NULL, 7, '2026-05-10 07:04:30', '2026-06-01', '10:00:00', NULL, 7, '2026-05-11 01:02:02', '2026-05-09 03:35:17', '2026-05-11 01:02:02'),
(4, 10, 'Ma.Joy Colico', 'majoycolico@gmail.com', 'Property Damage', NULL, NULL, 'zone 2', 'lordan mary ann', 'hahahhahaa', 'Ma.Joy Colico', 'ma.colico@gmail.com', 'zone 5 bacolod, bato, camarines sur', 'under_investigation', NULL, 1, '2026-05-10 06:45:46', '2026-05-21', '11:11:00', NULL, NULL, '2026-05-10 06:45:05', '2026-05-09 07:56:12', '2026-05-10 06:45:46');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_requests`
--

CREATE TABLE `clearance_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `household_no` char(5) DEFAULT NULL,
  `for_member` varchar(150) DEFAULT NULL COMMENT 'Full name of the family member the document is for',
  `member_relationship` varchar(50) DEFAULT NULL COMMENT 'Relationship to household head (e.g. self, spouse, child)',
  `document_type` enum('Barangay Clearance','Certificate of Residency','Certificate of Indigency') NOT NULL DEFAULT 'Barangay Clearance',
  `purpose` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','released') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL COMMENT 'Captain/secretary remarks on approval or rejection',
  `processed_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'FK → users.id (captain/secretary who processed)',
  `processed_at` datetime DEFAULT NULL,
  `est_release_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_requests`
--

INSERT INTO `clearance_requests` (`id`, `user_id`, `household_no`, `for_member`, `member_relationship`, `document_type`, `purpose`, `notes`, `status`, `remarks`, `processed_by`, `processed_at`, `est_release_date`, `created_at`, `updated_at`) VALUES
(1, 6, '65449', 'REY DELA CRUZ', 'Child', 'Certificate of Residency', 'Scholarship Application', NULL, 'approved', NULL, 1, '2026-05-10 06:43:21', '2026-05-07', '2026-05-05 08:09:27', '2026-05-10 06:43:21'),
(2, 6, '65449', 'REY DELA CRUZ', 'Child', 'Certificate of Indigency', 'Scholarship Application', NULL, 'approved', NULL, 1, '2026-05-10 06:43:18', '2026-05-07', '2026-05-05 08:10:09', '2026-05-10 06:43:18'),
(4, 6, '65449', 'JUAN DELA CRUZ', 'Household Head', 'Barangay Clearance', 'Government Transaction', NULL, 'approved', NULL, 7, '2026-05-05 09:19:47', '2026-05-07', '2026-05-05 09:00:35', '2026-05-05 09:19:47'),
(5, 10, '61084', 'MA.JOY COLICO', 'Household Head', 'Barangay Clearance', 'Employment / Job Application', NULL, 'approved', NULL, 1, '2026-05-10 06:43:51', '2026-05-12', '2026-05-09 03:21:33', '2026-05-10 06:43:51'),
(6, 10, '61084', 'MA.JOY COLICO', 'Household Head', 'Certificate of Indigency', 'Social Welfare (DSWD / 4Ps)', NULL, 'approved', NULL, 1, '2026-05-10 06:43:49', '2026-05-12', '2026-05-09 08:13:55', '2026-05-10 06:43:49'),
(7, 11, '34929', 'FELIX SANTOR', 'Household Head', 'Certificate of Residency', 'PhilHealth / SSS / GSIS', NULL, 'pending', NULL, NULL, NULL, '2026-05-12', '2026-05-10 07:43:39', '2026-05-10 07:43:39'),
(8, 11, '34929', 'ALEX SANTOR', 'Child', '', 'Employment / Job Application', NULL, 'pending', NULL, NULL, NULL, '2026-05-12', '2026-05-10 07:47:45', '2026-05-10 07:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `household_no` char(5) NOT NULL,
  `zone` varchar(50) DEFAULT NULL,
  `last_name` varchar(80) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `middle_name` varchar(80) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(150) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `civil_status` enum('Single','Married','Widowed','Separated','Annulled') NOT NULL DEFAULT 'Single',
  `nationality` varchar(60) NOT NULL DEFAULT 'Filipino',
  `religion` varchar(80) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `monthly_income` decimal(12,2) NOT NULL DEFAULT 0.00,
  `contact_number` varchar(20) DEFAULT NULL,
  `educational_attainment` varchar(80) DEFAULT NULL,
  `philhealth_no` varchar(30) DEFAULT NULL,
  `registered_voter` tinyint(1) DEFAULT 0,
  `num_families` tinyint(3) UNSIGNED DEFAULT 1,
  `address` text DEFAULT NULL,
  `years_of_residency` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `house_ownership` enum('Owned','Rented','Shared','Informal Settler') NOT NULL DEFAULT 'Owned',
  `is_4ps` tinyint(1) NOT NULL DEFAULT 0,
  `is_pwd` tinyint(1) NOT NULL DEFAULT 0,
  `is_senior_citizen` tinyint(1) NOT NULL DEFAULT 0,
  `is_solo_parent` tinyint(1) NOT NULL DEFAULT 0,
  `is_indigenous` tinyint(1) NOT NULL DEFAULT 0,
  `water_source_level` enum('I','II','III','none') DEFAULT NULL,
  `water_safety_managed` tinyint(1) DEFAULT NULL,
  `sanitation_basic` enum('with','without') DEFAULT NULL,
  `sanitation_managed` enum('with','without') DEFAULT NULL,
  `recorded_by` int(11) UNSIGNED DEFAULT NULL,
  `recorded_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`household_no`, `zone`, `last_name`, `first_name`, `middle_name`, `suffix`, `date_of_birth`, `place_of_birth`, `gender`, `civil_status`, `nationality`, `religion`, `occupation`, `monthly_income`, `contact_number`, `educational_attainment`, `philhealth_no`, `registered_voter`, `num_families`, `address`, `years_of_residency`, `house_ownership`, `is_4ps`, `is_pwd`, `is_senior_citizen`, `is_solo_parent`, `is_indigenous`, `water_source_level`, `water_safety_managed`, `sanitation_basic`, `sanitation_managed`, `recorded_by`, `recorded_date`, `created_at`, `updated_at`) VALUES
('34929', 'Zone 3', 'SANTOR ', 'FELIX', 'S', '', '1972-05-08', 'BATO', 'Male', 'Married', 'FILIPINO', 'ROMAN CATHOLIC ', 'FARMER AND FISHERMAN', 15000.00, '09876543212', 'High School Graduate', '', 0, 1, NULL, 9, 'Owned', 0, 0, 0, 0, 0, 'I', 1, 'with', 'without', 7, '2026-05-07', '2026-05-07 04:58:53', '2026-05-07 04:58:53'),
('61084', 'Zone 1', 'COLICO', 'MA.JOY', 'LAGONG', '', '2001-02-06', 'BACOLOD BATO', 'Female', 'Married', 'FILIPINO', 'ROMAN CATHOLIC', 'CONSTRUCTION WORKER', 11100.00, '', '', '', 0, 1, NULL, 3, 'Owned', 1, 1, 0, 1, 1, 'III', 0, 'without', 'with', 1, '2026-05-09', '2026-05-09 03:12:26', '2026-05-09 03:12:26'),
('65449', 'Zone 1', 'DELA CRUZ', 'JUAN', 'SANTOS', 'Sr', '1969-01-10', 'BATO', 'Male', 'Married', 'FILIPINO', 'ROMAN CATHOLIC ', 'FARMER', 5000.00, '09054952764', 'High School Graduate', '123456789102', 1, 1, '', 5, 'Owned', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 1, '2026-05-04', '2026-05-04 15:19:19', '2026-05-10 06:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

CREATE TABLE `household_members` (
  `id` int(11) UNSIGNED NOT NULL,
  `household_no` char(5) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `middle_name` varchar(80) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `monthly_income` decimal(12,2) NOT NULL DEFAULT 0.00,
  `philhealth_no` varchar(30) DEFAULT NULL,
  `educational_attainment` varchar(80) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `household_members`
--

INSERT INTO `household_members` (`id`, `household_no`, `relationship`, `last_name`, `first_name`, `middle_name`, `suffix`, `date_of_birth`, `gender`, `occupation`, `monthly_income`, `philhealth_no`, `educational_attainment`, `created_at`, `updated_at`) VALUES
(1, '65449', 'spouse', 'DELA CRUZ', 'MARIA', 'LUNA', '', '1970-01-10', NULL, 'TEARCHER', 10000.00, '111122323334', 'College Graduate', '2026-05-04 15:19:19', '2026-05-04 15:19:19'),
(2, '65449', 'child', 'DELA CRUZ', 'REY', 'LUNA', '', '2005-01-01', NULL, 'STUDENT', 0.00, '', NULL, '2026-05-04 15:19:19', '2026-05-04 15:19:19'),
(3, '65449', 'Father', 'DELA CRUZ', 'VICENTE', 'SANTOS', '', '1930-01-20', NULL, NULL, 0.00, NULL, NULL, '2026-05-04 15:19:19', '2026-05-04 15:19:19'),
(4, '34929', 'spouse', 'SANTOR ', 'ANITA', 'FETIL', '', '1979-08-08', NULL, 'HOUSEWIFE', 0.00, '', 'High School Graduate', '2026-05-07 04:58:53', '2026-05-07 04:58:53'),
(5, '34929', 'child', 'SANTOR', 'ALEX', 'FETIL', '', '1998-12-09', NULL, 'MECHANICAL ENGINEER', 50000.00, '', 'College Graduate', '2026-05-07 04:58:53', '2026-05-07 05:00:36'),
(6, '34929', 'child', 'SANTOR ', 'ANTHONY', 'FETIL', '', '2000-07-12', NULL, '', 0.00, '', 'High School Graduate', '2026-05-07 04:58:53', '2026-05-07 05:00:43'),
(7, '34929', 'child', 'SANTOR', 'ARIEL', 'FETIL', '', '2002-10-23', NULL, 'SECURITY GUARD', 15000.00, '', 'High School Level', '2026-05-07 04:58:53', '2026-05-07 04:59:57'),
(8, '34929', 'cousin', 'SANTOR', 'MARIAA', 'TANGTANG', '', '2003-01-12', NULL, 'TEACHER', 15000.00, '123456789102', 'College Graduate', '2026-05-10 06:49:50', '2026-05-10 06:49:50');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(5, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1777876072, 1),
(12, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateHouseholdsTable', 'default', 'App', 1777907106, 2),
(13, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateHouseholdMembersTable', 'default', 'App', 1777907106, 2),
(15, '2024-01-01-000005', 'App\\Database\\Migrations\\AddContactAndHouseholdToUsers', 'default', 'App', 1777908239, 3),
(16, '2024-01-01-000006', 'App\\Database\\Migrations\\AddUniqueConstraints', 'default', 'App', 1777961642, 4),
(17, '2024-01-01-000007', 'App\\Database\\Migrations\\CreateClearanceRequestsTable', 'default', 'App', 1777968103, 5),
(18, '2024-01-01-000008', 'App\\Database\\Migrations\\CreateBlotterReportsTable', 'default', 'App', 1778080630, 6),
(19, '2024-01-01-000009', 'App\\Database\\Migrations\\FixHouseholdUniqueConstraints', 'default', 'App', 1778129167, 7),
(20, '2024-01-01-000010', 'App\\Database\\Migrations\\AddGenderToHouseholdMembers', 'default', 'App', 1778130536, 8),
(21, '2024-01-01-000011', 'App\\Database\\Migrations\\CreateSkYouthTable', 'default', 'App', 1778132675, 9),
(22, '2024-01-01-000012', 'App\\Database\\Migrations\\AddHouseholdExtras', 'default', 'App', 1778302626, 10),
(23, '2024-01-01-000013', 'App\\Database\\Migrations\\AddBlotterScheduleFields', 'default', 'App', 1778309680, 11),
(24, '2024-01-01-000014', 'App\\Database\\Migrations\\CreateSchedulesTable', 'default', 'App', 1778310478, 12),
(25, '2024-01-01-000015', 'App\\Database\\Migrations\\AddAvatarToUsers', 'default', 'App', 1778311929, 13),
(26, '2024-01-01-000016', 'App\\Database\\Migrations\\AddScheduleVisibility', 'default', 'App', 1778388209, 14);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `event_type` enum('hearing','meeting','appointment','event','other') NOT NULL DEFAULT 'appointment',
  `color` varchar(20) NOT NULL DEFAULT '#1d2448',
  `location` varchar(255) DEFAULT NULL,
  `blotter_id` int(11) UNSIGNED DEFAULT NULL,
  `visibility` enum('private','shared') DEFAULT 'private',
  `shared_with` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `title`, `description`, `event_date`, `start_time`, `end_time`, `event_type`, `color`, `location`, `blotter_id`, `visibility`, `shared_with`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'meeting', '', '2026-05-11', '10:00:00', '15:00:00', 'meeting', '#1d2448', 'barangay hall', NULL, 'private', NULL, 1, '2026-05-09 08:01:38', '2026-05-09 08:01:38'),
(2, 'meeting', 'about pantomina', '2026-05-18', '08:00:00', '12:00:00', 'meeting', '#1d2448', 'malawag', NULL, 'private', NULL, 7, '2026-05-09 08:04:18', '2026-05-09 08:04:18'),
(3, 'Clean-Up Drive', '', '2026-05-10', '06:00:00', '00:00:00', 'event', '#1d2448', 'barangay hall', NULL, 'private', NULL, 1, '2026-05-10 04:03:49', '2026-05-10 04:03:49'),
(4, 'mhkuiriduy', '', '2026-05-20', '09:00:00', '00:00:00', 'appointment', '#1d2448', 'barangay hall', NULL, 'private', NULL, 1, '2026-05-10 06:40:16', '2026-05-10 06:40:16'),
(5, 'Meeting with sec', '', '2026-05-20', '09:00:00', '10:00:00', 'appointment', '#1d2448', 'barangay hall', NULL, 'private', NULL, 7, '2026-05-10 06:47:42', '2026-05-10 06:47:42');

-- --------------------------------------------------------

--
-- Table structure for table `sk_youth`
--

CREATE TABLE `sk_youth` (
  `id` int(11) UNSIGNED NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `middle_name` varchar(80) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(150) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `religion` varchar(80) DEFAULT NULL,
  `citizenship` varchar(60) NOT NULL DEFAULT 'Filipino',
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zone` varchar(50) DEFAULT NULL,
  `months_in_brgy` varchar(50) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `organizations` text DEFAULT NULL,
  `age_group` varchar(20) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `educational_background` varchar(100) DEFAULT NULL,
  `school_type` enum('Public School','Private School') DEFAULT NULL,
  `school_detail` varchar(200) DEFAULT NULL,
  `governance` varchar(50) DEFAULT NULL,
  `health_concerns` text DEFAULT NULL,
  `social_inclusion` text DEFAULT NULL,
  `economic_status` varchar(50) DEFAULT NULL,
  `monthly_income` varchar(80) DEFAULT NULL,
  `advocacy` text DEFAULT NULL,
  `volunteer` text DEFAULT NULL,
  `issue_1` varchar(200) DEFAULT NULL,
  `issue_2` varchar(200) DEFAULT NULL,
  `issue_3` varchar(200) DEFAULT NULL,
  `suggestions` text DEFAULT NULL,
  `recorded_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('captain','secretary','treasurer','sk','resident') NOT NULL DEFAULT 'resident',
  `status` enum('unverified','pending','active','rejected') NOT NULL DEFAULT 'unverified',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `verify_token` varchar(64) DEFAULT NULL,
  `verify_token_expires` datetime DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `contact_number` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `household_no` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password`, `role`, `status`, `created_at`, `updated_at`, `verify_token`, `verify_token_expires`, `email_verified`, `contact_number`, `avatar`, `household_no`) VALUES
(1, 'Barangay Secretary', 'secretary@barangay.gov.ph', 'secretary_admin', '$2y$10$HYhAjMFOd19GQTMKVhR8s.j3ETX/T0CV3YIoBjnnvzjKWNH78cX2m', 'secretary', 'active', '2026-05-04 06:29:47', '2026-05-10 04:06:28', NULL, NULL, 1, NULL, 'avatar_1_1778385928.jpg', NULL),
(6, 'Juan Dela Cruz', 'juandelacruz@gmail.com', 'juandelacruz', '$2y$10$39cCM/LbvlGTbH5PLrsBYeYLIifH.H34nVpst0JzQYUGHyPtn.3HS', 'resident', 'active', '2026-05-04 15:24:06', '2026-05-04 15:24:54', NULL, NULL, 1, NULL, NULL, '65449'),
(7, 'Juan Dela Cruz', 'captainjuandelacruz@gmail.com', 'captain_juan', '$2y$10$OmXnYww9MeEaNdwnP7.n.emUknUftpteUmXHZbEMPTrPbp5fz/dt6', 'captain', 'active', '2026-05-05 05:55:50', '2026-05-11 00:58:25', NULL, NULL, 1, NULL, 'avatar_7_1778461105.jpg', NULL),
(8, 'ESTRELLA P. ELPEDES', 'etrellaelpedes@gmail.com', 'captain_admin', '$2y$10$kUrdgYbVoLpIdr9C7UPBvuiFa8ger0q2zM6JfHdTZZ5.JXoOySoyS', 'captain', 'active', '2026-05-05 06:16:42', '2026-05-05 06:16:42', NULL, NULL, 1, NULL, NULL, NULL),
(9, 'Colico, Ma Joy L. ', 'ma.joycolico@my.cspc.edu.ph', 'skcolico', '$2y$10$1CWDHSXDIQD0D5BPrHz34eh/79Bk/gtzXhrsnU5dr8CetdHex2Pq2', 'sk', 'active', '2026-05-07 05:31:29', '2026-05-10 07:09:19', NULL, NULL, 1, NULL, 'avatar_9_1778396959.jpg', NULL),
(10, 'Ma.Joy Colico', 'majoycolico@gmail.com', 'joy', '$2y$10$2ZpBx0OxDUteO4KuwKek9eUivuSm0Reft4DV0I5RUeUa01xvwLaGO', 'resident', 'active', '2026-05-09 03:18:21', '2026-05-09 03:20:17', NULL, NULL, 1, NULL, NULL, '61084'),
(11, 'FELIX SANTOR ', 'santorfelix@gmail.com', 'santor', '$2y$10$knhDirsNoxMjhbV3gyMYkuIiuDi/o/E14P2x/AFhbG2Pl/jGAYDSC', 'resident', 'active', '2026-05-09 08:23:39', '2026-05-09 08:24:41', NULL, NULL, 1, NULL, NULL, '34929');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complainant_user_id` (`complainant_user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`household_no`),
  ADD KEY `zone` (`zone`),
  ADD KEY `last_name` (`last_name`);

--
-- Indexes for table `household_members`
--
ALTER TABLE `household_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `household_no` (`household_no`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_date` (`event_date`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sk_youth`
--
ALTER TABLE `sk_youth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_name` (`last_name`),
  ADD KEY `gender` (`gender`),
  ADD KEY `date_of_birth` (`date_of_birth`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `household_members`
--
ALTER TABLE `household_members`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sk_youth`
--
ALTER TABLE `sk_youth`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blotter_reports`
--
ALTER TABLE `blotter_reports`
  ADD CONSTRAINT `blotter_reports_complainant_user_id_foreign` FOREIGN KEY (`complainant_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `clearance_requests`
--
ALTER TABLE `clearance_requests`
  ADD CONSTRAINT `clearance_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `household_members`
--
ALTER TABLE `household_members`
  ADD CONSTRAINT `household_members_household_no_foreign` FOREIGN KEY (`household_no`) REFERENCES `households` (`household_no`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
