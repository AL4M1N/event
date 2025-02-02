-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 04:42 PM
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
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(14) NOT NULL,
  `nid` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`id`, `name`, `phone`, `nid`, `created_at`, `updated_at`) VALUES
(11, 'Thor Parkk', '01711083331', '12365470000', '2025-01-23 17:52:53', NULL),
(12, 'Brooke Montgomery', '01712345678', '6523652145', '2025-01-23 18:30:37', NULL),
(13, 'Habibur Rahman', '01711082312', '3213211221231', '2025-01-26 07:37:28', NULL),
(14, 'Md. Nurnabi Hasan', '01711082323', '12365478900', '2025-01-26 08:29:51', NULL),
(15, 'Muhammad Al Muttaqi', '01711083332', '1234567890', '2025-01-26 10:48:12', NULL),
(16, 'Arif Khan', '01711083312', '12345678900', '2025-01-26 10:51:24', NULL),
(17, 'Kylan Weber', '01711081456', '4548721231453', '2025-01-26 18:50:03', NULL),
(18, 'Moana Summers', '01711081236', '63258741255', '2025-01-26 19:02:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `capacity` int(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `date`, `capacity`, `created_at`, `updated_at`) VALUES
(5, 'Global Food Festival', 'Savor the flavors of the world at our annual food festival, featuring culinary delights from over 20 countries. Enjoy live cooking demonstrations, tasting sessions, and cultural performances that celebrate the diversity of global cuisine.', '2025-01-24', 30, '2025-01-23 08:13:48', NULL),
(6, 'Tech Innovators Conference 2025', 'Join us for a groundbreaking event where the brightest minds in technology come together to discuss the latest advancements, share innovative ideas, and network with industry leaders. Featuring keynote speakers, panel discussions, and hands-on workshops..', '2025-02-08', 5, '2025-01-23 08:56:42', NULL),
(9, 'Art & Music Extravaganza', 'Experience a vibrant celebration of creativity at our Art & Music Extravaganza. Explore art exhibits, enjoy live music performances, and participate in interactive art workshops. A perfect blend of visual and auditory arts.', '2025-02-01', 30, '2025-01-23 11:24:02', NULL),
(10, 'sadsasaasas assa', 'asdsa sadasdas dsad', '2025-01-30', 4, '2025-01-29 14:34:54', '2025-01-29 14:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `event_attendees`
--

CREATE TABLE `event_attendees` (
  `event_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_attendees`
--

INSERT INTO `event_attendees` (`event_id`, `attendee_id`, `registration_date`) VALUES
(5, 11, '2025-01-26 16:47:07'),
(5, 12, '2025-01-26 16:41:24'),
(5, 13, '2025-01-26 16:46:23'),
(5, 14, '2025-01-26 16:44:19'),
(5, 15, '2025-01-26 16:52:07'),
(5, 16, '2025-01-26 16:56:39'),
(6, 11, '2025-01-24 00:01:29'),
(6, 12, '2025-01-24 00:30:37'),
(6, 13, '2025-01-26 16:35:05'),
(6, 14, '2025-01-26 16:35:25'),
(6, 15, '2025-01-26 16:48:29'),
(9, 11, '2025-01-26 12:56:59'),
(9, 12, '2025-01-26 12:56:35'),
(9, 13, '2025-01-26 16:39:02'),
(9, 14, '2025-01-26 16:39:51'),
(9, 15, '2025-01-26 16:50:19'),
(9, 16, '2025-01-26 16:51:41'),
(9, 17, '2025-01-27 00:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(4, 'Md. Nurnabi', 'nurnabi@gmail.com', '$2y$10$A/W0d2GWPtMD8eTgn1ks2OlQ0gA9.N4/AhSZMbFsmIP0.MwzQJSx2', '2025-01-22 07:17:54', NULL),
(5, 'Al Amin', 'techamin90@gmail.com', '$2y$10$5NPZzvEkomq0tANoFgDOZ.4eA7ur9y4P3efr..PXsp9Qi8.iziwPq', '2025-01-22 08:43:37', NULL),
(6, 'Another User', 'another@user.com', '$2y$10$Hart.MO2b9EMP1dhnccCSuc41jM58epgOUAUH33o/Xmi5CQeRETLG', '2025-01-22 08:58:46', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_attendees`
--
ALTER TABLE `event_attendees`
  ADD PRIMARY KEY (`event_id`,`attendee_id`),
  ADD KEY `attendee_id` (`attendee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_attendees`
--
ALTER TABLE `event_attendees`
  ADD CONSTRAINT `event_attendees_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_attendees_ibfk_2` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
