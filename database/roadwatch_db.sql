-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2026 at 02:24 PM
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
-- Database: `roadwatch_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `name`) VALUES
(1, 'Bacayao Norte'),
(2, 'Bacayao Sur'),
(3, 'Barangay I (T. Bugallon)'),
(4, 'Barangay II (Nueva)'),
(5, 'Barangay IV (Zamora)'),
(6, 'Bolosan'),
(7, 'Bonuan Binloc'),
(8, 'Bonuan Boquig'),
(9, 'Bonuan Gueset'),
(10, 'Calmay'),
(11, 'Carael'),
(12, 'Caranglaan'),
(13, 'Herrero'),
(14, 'Lasip Chico'),
(15, 'Lasip Grande'),
(16, 'Lomboy'),
(17, 'Lucao'),
(18, 'Malued'),
(19, 'Mamalingling'),
(20, 'Mangin'),
(21, 'Mayombo'),
(22, 'Pantal'),
(23, 'Poblacion Oeste'),
(24, 'Pogo Chico'),
(25, 'Pogo Grande'),
(26, 'Pugaro Suit'),
(27, 'Salapingao'),
(28, 'Salisay'),
(29, 'Tambac'),
(30, 'Tapuac'),
(31, 'Tebeng');

-- --------------------------------------------------------

--
-- Table structure for table `hazard_types`
--

CREATE TABLE `hazard_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hazard_types`
--

INSERT INTO `hazard_types` (`id`, `name`) VALUES
(7, 'Accident'),
(5, 'Construction'),
(2, 'Flooding'),
(6, 'Landslide'),
(8, 'Other'),
(1, 'Pothole'),
(4, 'Road Damage'),
(3, 'Road Obstruction');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `hazard_type_id` int(11) NOT NULL,
  `road_name` varchar(150) NOT NULL,
  `landmark` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','verified','rejected','expired') NOT NULL DEFAULT 'pending',
  `passability` enum('passable','partially_passable','not_passable') DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `barangay_id`, `hazard_type_id`, `road_name`, `landmark`, `description`, `status`, `passability`, `rejection_reason`, `reviewed_by`, `submitted_at`, `verified_at`) VALUES
(1, 2, 1, 1, 'Test Road', 'Near Test Landmark', 'This is a test road hazard report.', 'verified', 'passable', NULL, 3, '2026-09-24 00:09:03', '2026-09-24 00:15:39'),
(2, 2, 1, 1, 'Second Test Road', 'Near Second Test Landmark', 'This report will be used to test rejection.', 'rejected', NULL, 'Insufficient information provided.', 3, '2026-09-24 00:23:49', NULL),
(3, 2, 1, 7, 'Arellano Street', 'Near Public Market', 'There is a road hazard causing difficulty for vehicles.', 'verified', 'passable', NULL, 3, '2026-09-24 00:49:06', '2026-09-24 03:48:08'),
(4, 2, 2, 6, 'Second Road Test', 'Near City Hall', 'This report will be used to test the admin rejection feature.', 'rejected', NULL, 'Insufficient information provided.', 3, '2026-09-24 03:52:13', NULL),
(5, 2, 6, 4, 'Photo Test Road', 'Near Test Area', 'Testing the road hazard photo upload.', 'verified', 'passable', NULL, 3, '2026-09-24 04:08:27', '2026-09-24 04:30:59'),
(6, 8, 22, 1, 'User Two Test Road', 'Near Test Area', 'Test report from the newly created user.\r\nPhoto: Choose any road/test image', 'verified', 'passable', NULL, 3, '2026-09-24 16:19:20', '2026-09-24 16:22:38'),
(7, 8, 21, 2, 'mayombo', 'victory terminal', 'the water is wet hahhhahahah', 'rejected', NULL, 'fake news, photo is from google', 3, '2026-09-24 16:26:12', NULL),
(8, 8, 24, 1, 'Photo Validation Test', 'Test Area', 'Testing normal photo upload.\r\nPhoto: Any JPG/PNG/WebP under 5 MB', 'verified', 'passable', NULL, 3, '2026-09-24 17:37:20', '2026-09-24 17:56:53'),
(9, 8, 26, 1, 'Reject Security Test', 'ilog', 'Testing updated reject function.', 'rejected', NULL, 'reject!!!', 3, '2026-09-24 17:59:54', NULL),
(10, 8, 31, 2, 'CSRF Reject Test', 'bdo', 'Testing CSRF protected rejection.', 'rejected', NULL, 'CSRF rejection test successful.', 3, '2026-09-24 18:17:46', NULL),
(11, 8, 23, 3, 'CSRF Verify Test', 'asdf', 'Testing CSRF protected verification.', 'verified', 'passable', NULL, 3, '2026-09-24 18:26:07', '2026-09-24 18:27:57'),
(12, 8, 30, 5, 'Final Upload Test', 'Test Area', 'Testing valid photo after validation update.', 'verified', 'passable', NULL, 3, '2026-09-24 19:36:24', '2026-09-24 20:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `report_photos`
--

CREATE TABLE `report_photos` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_photos`
--

INSERT INTO `report_photos` (`id`, `report_id`, `filename`) VALUES
(2, 5, 'road_6ab431bb92e0f6.33307346.png'),
(3, 6, 'road_6ab4dd089cf541.08780964.webp'),
(4, 7, 'road_6ab4dea4880fc0.71606213.jpg'),
(5, 8, 'road_6ab4ef509271e2.10677307.webp'),
(6, 9, 'road_6ab4f49b02bd62.44629722.webp'),
(7, 10, 'road_6ab4f8cabfd752.57046824.jpg'),
(8, 11, 'road_6ab4fabf08dde9.79065674.webp'),
(9, 12, 'road_6ab50b386dc766.94143990.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL,
  `role` enum('user','admin','super_admin') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `security_question`, `security_answer`, `role`, `is_active`, `created_by`, `created_at`) VALUES
(1, 'RoadWatch Super Admin', 'superadmin@roadwatch.local', '$2y$10$kPbUg9vR.PipfmPEvFLIrO.lyal03R/FqMUSXkl3a9lYuvYGgrWgW', 'What is the system name?', 'RoadWatch', 'super_admin', 1, NULL, '2026-09-23 23:58:52'),
(2, 'Test User', 'user@roadwatch.local', '$2y$10$9RsOn4I3obnNNevEb4.CX.qJFzX9zAYcDclTidZmPiXZTQDSritPy', 'What is the system name?', 'RoadWatch', 'user', 1, 1, '2026-09-24 00:07:25'),
(3, 'Test Admin', 'admin@roadwatch.local', '$2y$10$ubJ.zG3.uqEV70VsBTCoeuXcDk7LZXAP.V2w83MJnOAttpmCPTeDW', 'What is the system name?', 'RoadWatch', 'admin', 1, 1, '2026-09-24 00:13:50'),
(4, 'RoadWatch Test Account', 'testaccount@roadwatch.local', '$2y$10$xJaMPag8Kgqxcbd5IMnGBu3lyUtTjRC6GdfXSj3d8hkYJhd311EJO', 'What is your favorite color?', 'Blue', 'user', 1, 1, '2026-09-24 14:49:44'),
(8, 'RoadWatch User Two', 'user2@roadwatch.local', '$2y$10$8I77A9VDmZWZSt7xOYxWAeMhQ7PFGp4eYURWR1vrG1SUg5kuFGMYW', 'What is your favorite color?', 'Blue', 'user', 1, 1, '2026-09-24 16:12:25'),
(9, 'Security Test User', 'securitytest@roadwatch.local', '$2y$10$BmKuuSmmFr3ZZz0SZ/L6u.2mYOHCPtvOGkC7oH8Oc7o.X3YHTOHZy', 'What is your favorite food?', '$2y$10$BDSJS0pOmzCpUTIhTkdGUOfdqwCdfvXoVdVn1pdT0O0zu8RzjUF0y', 'user', 1, 1, '2026-09-24 17:13:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `hazard_types`
--
ALTER TABLE `hazard_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `barangay_id` (`barangay_id`),
  ADD KEY `hazard_type_id` (`hazard_type_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `report_photos`
--
ALTER TABLE `report_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `hazard_types`
--
ALTER TABLE `hazard_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `report_photos`
--
ALTER TABLE `report_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`),
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`hazard_type_id`) REFERENCES `hazard_types` (`id`),
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `report_photos`
--
ALTER TABLE `report_photos`
  ADD CONSTRAINT `report_photos_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
