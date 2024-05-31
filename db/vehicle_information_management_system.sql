-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 22, 2024 at 05:56 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vehicle_information_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `fuel`
--

CREATE TABLE `fuel` (
  `fuelID` int(11) NOT NULL,
  `litres` varchar(5) NOT NULL,
  `cost` varchar(20) NOT NULL,
  `purchased_on` date NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `image` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel`
--

INSERT INTO `fuel` (`fuelID`, `litres`, `cost`, `purchased_on`, `vehicleID`, `image`, `created_at`) VALUES
(3, '80', '260000', '2024-03-18', 4, '1711718275Screenshot 2023-12-02 132058.png', '2024-03-29 16:17:55'),
(4, '10', '32500', '2024-03-26', 1, '1711718373Screenshot 2023-12-02 132058.png', '2024-03-29 16:19:33'),
(5, '130', '416000', '2024-04-06', 6, '1712412347Screenshot 2023-12-02 132058.png', '2024-04-06 17:05:47'),
(6, '100', '325000', '2024-04-06', 4, '1712430111Screenshot 2023-12-02 132058.png', '2024-04-06 22:01:51'),
(7, '100', '32100', '2024-04-18', 5, '1713434840Command Prompt - netsh 10_24_2023 10_45_20.png', '2024-04-18 13:07:20');

-- --------------------------------------------------------

--
-- Table structure for table `insurance`
--

CREATE TABLE `insurance` (
  `insuranceID` int(11) NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `insure` varchar(70) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `type` varchar(15) NOT NULL,
  `amount` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `insurance`
--

INSERT INTO `insurance` (`insuranceID`, `vehicleID`, `insure`, `start`, `end`, `type`, `amount`, `created_at`, `updated_at`) VALUES
(1, 2, 'Jubilee Insurance', '2024-04-14', '2025-04-13', 'Third-Party', '700000', '2024-04-14 14:01:09', '2024-04-18 08:52:39'),
(2, 3, 'Milembe', '2024-04-18', '2025-04-17', 'Third-Party', '1000000', '2024-04-18 08:55:06', NULL),
(3, 5, 'Reliance Insurance', '2024-04-18', '2025-04-17', 'Comprehensive', '5000000', '2024-04-18 13:08:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `mainID` int(11) NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `last_maintenance` date NOT NULL,
  `next_maintenance` date NOT NULL,
  `actions` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`mainID`, `vehicleID`, `last_maintenance`, `next_maintenance`, `actions`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-04-14', '2024-04-29', 'Front left tyre changed,Back right tyre changed', '2024-04-14 13:47:54', '2024-04-18 09:31:13'),
(2, 2, '2024-04-14', '2024-04-20', 'Front right tyre changed,Back right tyre changed', '2024-04-14 13:50:13', '2024-04-18 09:30:42'),
(3, 4, '2024-04-18', '2024-05-18', 'Front left tyre changed,Front right tyre changed,Back left tyre changed,Back right tyre changed', '2024-04-18 09:44:59', NULL),
(4, 5, '2024-04-18', '2024-05-18', 'Bumper changed,Front right tyre changed,Back right tyre changed', '2024-04-18 13:06:06', '2024-04-22 00:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `notidate`
--

CREATE TABLE `notidate` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(15) NOT NULL,
  `reg_no` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notidate`
--

INSERT INTO `notidate` (`id`, `date`, `type`, `reg_no`) VALUES
(1, '2024-04-14', 'maintenance', 'T888 EEE'),
(2, '2024-04-14', 'maintenance', 'T672 EDZ'),
(3, '2024-04-14', 'insurance', 'T672 EDZ'),
(4, '2024-04-14', 'service', 'T672 EDZ'),
(5, '2024-04-15', 'insurance', 'T672 EDZ'),
(6, '2024-04-15', 'maintenance', 'T888 EEE'),
(7, '2024-04-15', 'maintenance', 'T672 EDZ'),
(8, '2024-04-15', 'service', 'T672 EDZ'),
(9, '2024-04-15', 'service', 'T888 EEE'),
(10, '2024-04-16', 'insurance', 'T672 EDZ'),
(11, '2024-04-16', 'maintenance', 'T888 EEE'),
(13, '2024-04-16', 'service', 'T888 EEE'),
(14, '2024-04-16', 'service', 'T672 EDZ'),
(17, '2024-04-16', 'maintenance', 'T672 EDZ'),
(18, '2024-04-17', 'insurance', 'T672 EDZ'),
(19, '2024-04-17', 'maintenance', 'T888 EEE'),
(20, '2024-04-17', 'maintenance', 'T672 EDZ'),
(21, '2024-04-17', 'service', 'T888 EEE'),
(22, '2024-04-17', 'service', 'T672 EDZ'),
(23, '2024-04-18', 'maintenance', 'T888 EEE'),
(24, '2024-04-18', 'maintenance', 'T672 EDZ'),
(25, '2024-04-18', 'service', 'T888 EEE'),
(26, '2024-04-19', 'maintenance', 'T672 EDZ'),
(27, '2024-04-19', 'service', 'T888 EEE'),
(28, '2024-04-20', 'maintenance', 'T672 EDZ'),
(29, '2024-04-20', 'service', 'T888 EEE'),
(30, '2024-04-21', 'service', 'T888 EEE'),
(31, '2024-04-22', 'maintenance', 'T888 EEE'),
(32, '2024-04-22', 'service', 'T888 EEE');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notID` int(11) NOT NULL,
  `notification` varchar(300) NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `flag` int(11) NOT NULL DEFAULT 0,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notID`, `notification`, `vehicleID`, `type`, `flag`, `created_at`) VALUES
(9, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 300 KM', 1, 'service', 0, '2024-04-15'),
(11, 'Maintenance of the vehicle no. T888 EEE will be within 4 days! Make sure the maintenance is done within a time', 1, 'maintenance', 0, '2024-04-16'),
(12, 'Maintenance of the vehicle no. T672 EDZ is today! Make sure everything goes as planned', 2, 'maintenance', 0, '2024-04-16'),
(13, 'Service of the vehicle no. T888 EEE is almost there, km remained before next service is 20 KM', 1, 'service', 0, '2024-04-16'),
(14, 'Service of the vehicle no. T672 EDZ is near, km remained before next service is 520 KM', 2, 'service', 0, '2024-04-16'),
(17, 'Maintenance of the vehicle no. T672 EDZ will be within 1 day! Make sure the maintenance is done within a time', 2, 'maintenance', 0, '2024-04-16'),
(18, 'Insurance of the vehicle T672 EDZ is about to end only 1 day left', 2, 'insurance', 0, '2024-04-17'),
(19, 'Maintenance of the vehicle no. T888 EEE will be within 3 days! Make sure the maintenance is done within a time', 1, 'maintenance', 0, '2024-04-17'),
(20, 'Maintenance of the vehicle no. T672 EDZ will be within 1 day! Make sure the maintenance is done within a time', 2, 'maintenance', 0, '2024-04-17'),
(21, 'Service of the vehicle no. T888 EEE is almost there, km remained before next service is 20 KM', 1, 'service', 0, '2024-04-17'),
(22, 'Service of the vehicle no. T672 EDZ is near, km remained before next service is 520 KM', 2, 'service', 0, '2024-04-17'),
(23, 'Maintenance of the vehicle no. T888 EEE will be within 2 days! Make sure the maintenance is done within a time', 1, 'maintenance', 0, '2024-04-18'),
(24, 'Maintenance of the vehicle no. T672 EDZ will be within 2 days! Make sure the maintenance is done within a time', 2, 'maintenance', 0, '2024-04-18'),
(25, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 526 KM', 1, 'service', 0, '2024-04-18'),
(26, 'Maintenance of the vehicle no. T672 EDZ will be within 1 day! Make sure the maintenance is done within a time', 2, 'maintenance', 0, '2024-04-19'),
(27, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 526 KM', 1, 'service', 0, '2024-04-19'),
(28, 'Maintenance of the vehicle no. T672 EDZ will be within 1 day! Make sure the maintenance is done within a time', 2, 'maintenance', 0, '2024-04-20'),
(29, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 526 KM', 1, 'service', 0, '2024-04-20'),
(30, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 526 KM', 1, 'service', 0, '2024-04-21'),
(31, 'Maintenance of the vehicle no. T888 EEE will be within 7 days! Make sure the maintenance is done within a time', 1, 'maintenance', 0, '2024-04-22'),
(32, 'Service of the vehicle no. T888 EEE is near, km remained before next service is 526 KM', 1, 'service', 0, '2024-04-22');

-- --------------------------------------------------------

--
-- Table structure for table `oil`
--

CREATE TABLE `oil` (
  `oilD` int(11) NOT NULL,
  `current_km` varchar(8) NOT NULL,
  `oil_type` varchar(20) NOT NULL,
  `length` int(11) NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `time` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oil`
--

INSERT INTO `oil` (`oilD`, `current_km`, `oil_type`, `length`, `vehicleID`, `time`, `created_at`) VALUES
(3, '1800', 'Synthetic Blend Oil', 5000, 2, 2, '2024-04-18 06:16:02'),
(4, '526', 'Conventional Oil', 5000, 1, 1, '2024-04-18 06:59:58'),
(5, '120', 'Synthetic Oil', 7500, 5, 3, '2024-04-18 10:04:18'),
(6, '167', 'Conventional Oil', 5000, 3, 1, '2024-04-20 16:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `routeID` int(11) NOT NULL,
  `start` varchar(20) NOT NULL,
  `to_` varchar(20) DEFAULT NULL,
  `end` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`routeID`, `start`, `to_`, `end`, `created_at`, `updated_at`) VALUES
(1, 'Dar es salaam', 'Morogoro', 'Dar es salaam', '2024-04-14 13:39:54', NULL),
(2, 'Dar es salaam', 'Moshi', 'Dar es salaam', '2024-04-14 13:40:13', NULL),
(3, 'Dar es salaam', 'Arusha', 'Dar es salaam', '2024-04-14 13:40:25', NULL),
(4, 'Morogoro', 'Arusha', 'Morogoro', '2024-04-14 13:41:01', NULL),
(5, 'Morogoro', 'Moshi', 'Morogoro', '2024-04-14 13:41:14', NULL),
(6, 'Dar es salaam', 'Turiani', 'Dar es salaam', '2024-04-14 13:41:26', '2024-04-14 13:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `tripID` int(11) NOT NULL,
  `depart_from` varchar(20) NOT NULL,
  `arrive_in` varchar(20) NOT NULL,
  `km_before` varchar(10) NOT NULL,
  `km_after` varchar(10) DEFAULT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `details` varchar(300) DEFAULT NULL,
  `driverID` int(11) NOT NULL,
  `vehicleID` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`tripID`, `depart_from`, `arrive_in`, `km_before`, `km_after`, `departure_time`, `arrival_time`, `details`, `driverID`, `vehicleID`, `created_at`, `updated_at`) VALUES
(1, 'Dar es salaam', 'Morogoro', '120', '320', '2024-04-17 23:42:32', '2024-04-17 23:43:14', 'the trip was okay', 7, 1, '2024-04-17 23:42:44', '2024-04-17 23:43:14'),
(2, 'Morogoro', 'Dar es salaam', '325', '526', '2024-04-18 09:08:37', '2024-04-18 09:10:53', 'okay', 7, 1, '2024-04-18 09:09:09', '2024-04-18 09:10:53'),
(3, 'Dar es salaam', 'Moshi', '1201', '1800', '2024-04-21 09:18:00', '2024-04-21 19:39:00', 'superb', 10, 2, '2024-04-18 09:11:43', '2024-04-21 13:21:04'),
(4, 'Dar es salaam', 'Morogoro', '526', '5000', '2024-04-18 10:00:42', '2024-04-18 10:02:49', 'trip was goods', 7, 1, '2024-04-18 10:01:04', '2024-04-22 00:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `password` varchar(120) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `usertype` varchar(20) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `reset_token_hash` varchar(200) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstname`, `middlename`, `lastname`, `email`, `gender`, `phone`, `password`, `image`, `created_at`, `updated_at`, `usertype`, `login_time`, `logout_time`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(3, 'Rajabu', 'Mohamed', 'Muhani', 'muhanimau@gmail.com', 'Male', '0623987467', '$2y$10$F1QmzTHNksGMjuGR74aKseJaRN00sOfUI0zcEmAaxeHjiDhOxw89W', '1712498303Shantel 4x4.png', '2024-03-11 12:37:37', '2024-04-07 16:58:23', 'vehicle manager', '2024-04-18 09:48:19', '2024-04-18 09:48:23', 'a651e2884c9c4bbcc62ebfc91274f2609bedf927727155c71a61a324badf52de', '2024-04-07 16:28:40'),
(7, 'Michael', 'Beatus', 'Simbaigwile', 'mbeatus13@gmail.com', 'Male', '0713645171', '$2y$10$wp.GfILSJ6CuiQ3MwblcGeSzFUuFrF./R.VT5u3fXpkcIUzVyrO3.', '1711716264Shantel 4x4.png', '2024-03-17 12:41:49', '2024-03-29 15:44:24', 'driver', '2024-04-18 09:50:20', '2024-04-18 11:50:16', NULL, NULL),
(9, 'Meshack', 'Edward', 'Ntakiliho', 'moontagux720@gmail.com', 'Male', '0622753592', '$2y$10$E70vNPHPmMEjbT41zfV/m.SxikHIiUX1.Stnk/j3Qpffj.17eQLcu', '1711716289WhatsApp Image 2023-08-11 at 16.08.14.jpg', '2024-03-21 19:17:18', '2024-03-29 15:44:49', 'driver', '2024-04-12 13:34:23', '2024-04-12 13:34:37', NULL, NULL),
(10, 'Abdulshakur', 'Hassan', 'Mbupu', 'abdulshakurmbupu@gmail.com', 'Male', '0672030650', '$2y$10$BmRUJf5x9s7LXLp9d.v3KuqJaofQS.amFmdSvO7.g7DbG5R/bHYpq', '1713352204Shantel 4x4.png', '2024-03-26 18:16:44', '2024-04-17 14:10:04', 'driver', '2024-04-18 10:17:46', '2024-04-18 10:27:15', NULL, NULL),
(11, 'Brighton', 'Brown', 'Kingamkono', 'brizziehh@gmail.com', 'Male', '0678396496', '$2y$10$2ZJ8pjAhTA16pyrEy7fUQeWWY8queZE.6s5yQ0.Rpk8aSNXp2/eM6', '1713254887Shantel 4x4.png', '2024-03-27 18:33:18', '2024-04-16 11:08:07', 'administrator', '2024-04-20 16:44:27', '2024-04-18 12:09:26', NULL, NULL),
(14, 'Kingamkono', 'Normal', 'Brown', 'kings@gmail.com', 'Male', '0712335403', '$2y$10$Po/I3nWAIm4qEI5Q.JUeX.yMhCA8/glkKLxsUsrw0aPzVWqb1Kfqq', '1713164706original-b8057929333e4ac10768cdfd16595c2b.png', '2024-04-15 10:05:06', NULL, 'driver', '2024-04-15 10:05:47', '2024-04-15 10:06:19', NULL, NULL),
(16, 'Sheyster', 'Salim', 'Mlimagufa', 'shey@gmail.com', 'Female', '0712001001', '$2y$10$RPC1CLR.RwtRG93ZSwnHhuOtUuxxZpgN1nGduw2Ytwh2AXruWZEKu', '1713352945IMG_5539.jpg', '2024-04-17 14:22:24', NULL, 'vehicle manager', '2024-04-17 23:12:08', '2024-04-17 23:29:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicleID` int(11) NOT NULL,
  `model` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `year` varchar(4) NOT NULL,
  `make` varchar(20) NOT NULL,
  `chassis_no` varchar(50) NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `engine_no` varchar(25) NOT NULL,
  `cc` varchar(4) NOT NULL,
  `transmission` varchar(10) NOT NULL,
  `fuel` varchar(10) NOT NULL,
  `current_km` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `driverID` int(11) DEFAULT NULL,
  `routeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicleID`, `model`, `type`, `year`, `make`, `chassis_no`, `registration_no`, `engine_no`, `cc`, `transmission`, `fuel`, `current_km`, `status`, `created_at`, `updated_at`, `driverID`, `routeID`) VALUES
(1, 'Andare', 'Bus', '2024', 'Scania Marcopolo', '2XBN11283843JA032', 'T888 EEE', '5CG387DA8DS', '4000', 'manual', 'diesel', '2000', 'active', '2024-04-14 13:43:20', '2024-04-22 05:23:49', 7, 1),
(2, 'AGZ', 'Bus', '2022', 'Yutong', '2XBN11283843JJSAX', 'T672 EDZ', '5CG387DA8DSX', '4000', 'manual', 'diesel', '167', 'inactive', '2024-04-14 13:44:19', '2024-04-22 00:38:40', NULL, 2),
(3, 'AGZ 360', 'Bus', '2024', 'Zongtong', '2XBN11283843JA999', 'T201 EDX', '5CG387DA8DA', '4000', 'manual', 'gasoline', '100', 'active', '2024-04-14 13:45:23', '2024-04-20 15:25:07', 9, 6),
(4, 'AGZ 222', 'Bus', '2024', 'Yutong', '2XBN1128384XXA8SS', 'T872 DDD', '5CG387DA8DZ', '4000', 'manual', 'gasoline', '1200', 'active', '2024-04-14 13:47:15', '2024-04-20 15:25:20', NULL, 3),
(5, 'AGZ 360', 'Bus', '2024', 'Yutong', '2XBN11283843JAASZ', 'T120 ZZZ', '5CG3JASA8BCD', '4000', 'manual', 'diesel', '120', 'active', '2024-04-18 13:02:35', '2024-04-20 15:15:06', 14, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fuel`
--
ALTER TABLE `fuel`
  ADD PRIMARY KEY (`fuelID`);

--
-- Indexes for table `insurance`
--
ALTER TABLE `insurance`
  ADD PRIMARY KEY (`insuranceID`),
  ADD KEY `vehicleID` (`vehicleID`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`mainID`);

--
-- Indexes for table `notidate`
--
ALTER TABLE `notidate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notID`);

--
-- Indexes for table `oil`
--
ALTER TABLE `oil`
  ADD PRIMARY KEY (`oilD`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`routeID`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`tripID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fuel`
--
ALTER TABLE `fuel`
  MODIFY `fuelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `insurance`
--
ALTER TABLE `insurance`
  MODIFY `insuranceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `mainID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notidate`
--
ALTER TABLE `notidate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `oil`
--
ALTER TABLE `oil`
  MODIFY `oilD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `routeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `tripID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
