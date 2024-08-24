-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2024 at 06:58 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `electronicdocument`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(15) NOT NULL,
  `uname` varchar(30) DEFAULT NULL,
  `upass` varchar(30) DEFAULT NULL,
  `uencrypt` varchar(100) DEFAULT NULL,
  `ulname` varchar(30) DEFAULT NULL,
  `uoname` varchar(30) DEFAULT NULL,
  `uphone` varchar(15) DEFAULT NULL,
  `ustatus` varchar(30) DEFAULT NULL,
  `ulevel` varchar(30) DEFAULT NULL,
  `udept` varchar(30) DEFAULT NULL,
  `uemail` varchar(50) DEFAULT NULL,
  `udate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `uname`, `upass`, `uencrypt`, `ulname`, `uoname`, `uphone`, `ustatus`, `ulevel`, `udept`, `uemail`, `udate`) VALUES
(1, 'Admin', 'Admin', NULL, 'Userdemo', 'Userdemo', '1234567890', NULL, '400', 'Computer Science', 'jaffy011@gmail.com', '2024-08-10 22:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `allowdocumenttypes`
--

CREATE TABLE `allowdocumenttypes` (
  `id` int(8) NOT NULL,
  `extension` varchar(5) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `id` int(15) NOT NULL,
  `dname` varchar(30) DEFAULT NULL,
  `dtitle` varchar(80) DEFAULT NULL,
  `downer` int(15) DEFAULT NULL,
  `dsize1` varchar(15) DEFAULT NULL,
  `dsize2` varchar(10) DEFAULT NULL,
  `dtype` varchar(30) DEFAULT NULL,
  `daccess` varchar(30) DEFAULT NULL,
  `ddate` date DEFAULT NULL,
  `dtime` time DEFAULT NULL,
  `ddept` varchar(30) DEFAULT NULL,
  `ddept2` varchar(30) DEFAULT NULL,
  `dstatus` varchar(30) DEFAULT NULL,
  `dencrypt` varchar(60) DEFAULT NULL,
  `dpath` varchar(150) DEFAULT NULL,
  `deditdate` date DEFAULT NULL,
  `dedittime` time DEFAULT NULL,
  `dcomment` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `dname`, `dtitle`, `downer`, `dsize1`, `dsize2`, `dtype`, `daccess`, `ddate`, `dtime`, `ddept`, `ddept2`, `dstatus`, `dencrypt`, `dpath`, `deditdate`, `dedittime`, `dcomment`) VALUES
(1, 'memo', 'Windows_11_Pro_Produkt_Key_Box_2023.jpg', 1, '73283', '73283', 'jpg', 'Department', '2024-08-11', '01:32:11', 'Computer Science', 'Unknown', NULL, 'df21a917820a5fe8a2f202e2712979da', 'document_uploads/house.jpg', '2024-08-11', '01:32:11', 'test document'),
(7, 'Document', 'Messi in national jersey', 2, '78522', '78522', 'jpg', 'Department', '2024-08-11', '05:33:35', 'Computer Science', NULL, NULL, '06bfa6288de86ebf67e352606c1f0dab', 'document_uploads/Messi Argentine, footballer in national jersey transparent png.jpg', '2024-08-11', '05:33:35', 'Messi Argentine, footballer in national jersey transparent png.jpg'),
(10, 'Document', 'Use class diagram', 2, '98498', '98498', 'png', 'Public', '2024-08-14', '03:00:20', NULL, NULL, NULL, 'd7fc2db0aec5808875ff82c74e283291', 'document_uploads/5-Figure2-1.png', '2024-08-14', '03:00:20', 'Use class diagram'),
(12, 'Flyer', 'Bam', 2, '300301', '300301', 'jpg', 'Public', '2024-08-14', '03:05:25', NULL, NULL, NULL, '9cf3c0918f31c8b75007134e23de73f6', 'document_uploads/BAM.jpg', '2024-08-14', '03:05:25', ''),
(14, 'CHAPTER 2', 'PROJECT', 1, '18999', '18999', 'docx', 'Department', '2024-08-14', '16:21:48', 'Statistics', NULL, NULL, '05068c13550400abe83f405d24d1700a', 'document_uploads/CHAPTER TWO.docx', '2024-08-14', '16:21:48', 'Attached here is my chapter 2');

-- --------------------------------------------------------

--
-- Table structure for table `newusers`
--

CREATE TABLE `newusers` (
  `id` int(15) NOT NULL,
  `otp` varchar(30) DEFAULT NULL,
  `used` varchar(30) DEFAULT NULL,
  `ndept` varchar(30) DEFAULT NULL,
  `nname` varchar(30) DEFAULT NULL,
  `nlevel` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recycleddocuments`
--

CREATE TABLE `recycleddocuments` (
  `id` int(11) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `restored` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `share`
--

CREATE TABLE `share` (
  `id` int(30) NOT NULL,
  `ssender` int(15) DEFAULT NULL,
  `sreceiver` varchar(30) DEFAULT NULL,
  `sdoc` int(30) DEFAULT NULL,
  `sdate` date DEFAULT NULL,
  `stime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `share`
--

INSERT INTO `share` (`id`, `ssender`, `sreceiver`, `sdoc`, `sdate`, `stime`) VALUES
(1, 2, 'Userdemo', 4, '2024-08-11', '04:44:45'),
(2, 1, 'Achile@admin.com', 1, '2024-08-11', '16:51:15'),
(3, 1, 'Achile@admin.com', 9, '2024-08-12', '00:22:37'),
(4, 2, 'Achile@admin.com', 7, '2024-08-14', '02:50:54'),
(5, 2, 'Userdemo', 12, '2024-08-14', '03:38:50'),
(6, 2, 'Userdemo', 10, '2024-08-14', '03:52:48'),
(7, 2, 'Userdemo', 7, '2024-08-14', '03:55:02'),
(8, 1, 'Achile@admin.com', 14, '2024-08-14', '16:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(30) NOT NULL,
  `usid` int(15) DEFAULT NULL,
  `usaction` varchar(300) DEFAULT NULL,
  `usdate` date DEFAULT NULL,
  `ustime` time DEFAULT NULL,
  `usdept` varchar(30) DEFAULT NULL,
  `usstatus` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(15) NOT NULL,
  `uname` varchar(30) DEFAULT NULL,
  `upass` varchar(30) DEFAULT NULL,
  `uencrypt` varchar(100) DEFAULT NULL,
  `ulname` varchar(30) DEFAULT NULL,
  `uoname` varchar(30) DEFAULT NULL,
  `uphone` varchar(15) DEFAULT NULL,
  `ustatus` varchar(30) DEFAULT NULL,
  `ulevel` varchar(30) DEFAULT NULL,
  `udept` varchar(30) DEFAULT NULL,
  `uemail` varchar(50) DEFAULT NULL,
  `udate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uname`, `upass`, `uencrypt`, `ulname`, `uoname`, `uphone`, `ustatus`, `ulevel`, `udept`, `uemail`, `udate`) VALUES
(1, 'Userdemo', 'Userdemo', NULL, 'Userdemo', 'Userdemo', '1234567890', NULL, '400', 'Computer Science', 'jaffy011@gmail.com', '2024-08-10 22:26:02'),
(2, 'Achile@admin.com', 'Achile@admin.com', NULL, 'Achile Thomas', 'Achimugu', '08067984560', '', '400', 'Maths Science', 'princeachile37@gmail.com', '2024-08-11 02:42:51'),
(4, 'Admindemo', 'Admindemo', NULL, 'ACHIMUGU', 'ACHILE', '08067984560', '', '400', 'Statistics', 'mynaijakingsblog@gmail.com', '2024-08-14 02:24:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uname` (`uname`);

--
-- Indexes for table `allowdocumenttypes`
--
ALTER TABLE `allowdocumenttypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newusers`
--
ALTER TABLE `newusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recycleddocuments`
--
ALTER TABLE `recycleddocuments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `share`
--
ALTER TABLE `share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uname` (`uname`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `allowdocumenttypes`
--
ALTER TABLE `allowdocumenttypes`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `newusers`
--
ALTER TABLE `newusers`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share`
--
ALTER TABLE `share`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recycleddocuments`
--
ALTER TABLE `recycleddocuments`
  ADD CONSTRAINT `recycleddocuments_ibfk_1` FOREIGN KEY (`id`) REFERENCES `document` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
