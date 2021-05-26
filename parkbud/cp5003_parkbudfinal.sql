-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 26, 2021 at 10:03 AM
-- Server version: 10.3.29-MariaDB-log
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cp5003_parkbudfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `addrule`
--

CREATE TABLE `addrule` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `streetName` varchar(200) NOT NULL,
  `periodStart` date NOT NULL,
  `periodEnd` date NOT NULL,
  `parkingMeter` tinyint(1) NOT NULL,
  `sideFlag` tinyint(1) NOT NULL,
  `mondayStart` time DEFAULT NULL,
  `mondayEnd` time DEFAULT NULL,
  `tuesdayStart` time DEFAULT NULL,
  `tuesdayEnd` time DEFAULT NULL,
  `wednesdayStart` time DEFAULT NULL,
  `wednesdayEnd` time DEFAULT NULL,
  `thursdayStart` time DEFAULT NULL,
  `thursdayEnd` time DEFAULT NULL,
  `fridayStart` time DEFAULT NULL,
  `fridayEnd` time DEFAULT NULL,
  `saturdayStart` time DEFAULT NULL,
  `saturdayEnd` time DEFAULT NULL,
  `sundayStart` time DEFAULT NULL,
  `sundayEnd` time DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `createdTS` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `addrule`
--

INSERT INTO `addrule` (`id`, `userid`, `streetName`, `periodStart`, `periodEnd`, `parkingMeter`, `sideFlag`, `mondayStart`, `mondayEnd`, `tuesdayStart`, `tuesdayEnd`, `wednesdayStart`, `wednesdayEnd`, `thursdayStart`, `thursdayEnd`, `fridayStart`, `fridayEnd`, `saturdayStart`, `saturdayEnd`, `sundayStart`, `sundayEnd`, `longitude`, `latitude`, `image`, `createdTS`) VALUES
(2, 26, 'Rue Sherbrooke Ouest', '2021-05-11', '2021-05-30', 0, 0, '22:25:00', '23:25:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.60901108029151, 45.47398001970851, '36143ff490b151a8.jpg', '2021-05-20 14:14:02'),
(3, 23, 'Rue Sherbrooke Ouest', '2021-05-11', '2021-05-30', 0, 0, '22:25:00', '23:25:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.60901108029151, 45.47398001970851, '7cfbf3656ce6249d.jpg', '2021-05-20 15:49:04'),
(4, 23, 'Rue Sherbrooke Ouest', '2021-05-17', '2021-05-31', 0, 1, '17:00:00', '21:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.5933449802915, 45.4863138697085, '04d4fdd7f7720420.jpg', '2021-05-20 20:07:26'),
(5, 22, 'Avenue Morrison', '2021-05-11', '2021-05-24', 0, 1, '05:16:00', '06:16:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '08:00:00', '11:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.6517382302915, 45.5146942697085, '8656f1810da5a3b0.jpg', '2021-05-21 04:16:16'),
(7, 22, 'Av Bannantyne', '2021-05-11', '2021-05-31', 0, 1, '00:00:00', '00:00:00', '03:18:00', '05:18:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.57695758029149, 45.4604454197085, 'a37ab8c456dadfef.jpg', '2021-05-21 15:18:10'),
(9, 27, 'Rue d\'Allet', '2021-05-04', '2021-05-24', 0, 0, '21:04:00', '12:04:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.6071126802915, 45.5864665697085, '2c7e95dafe1d8dd0.jpg', '2021-05-21 19:04:11'),
(11, 27, 'Rue Roosevelt', '2021-05-13', '2021-05-25', 0, 1, '19:16:00', '21:16:00', '00:00:00', '00:00:00', '05:00:00', '07:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.8438192302915, 45.4704130197085, '92c880edd4514103.jpg', '2021-05-21 19:16:31'),
(12, 27, 'Rue Sherbrooke Ouest', '2021-05-05', '2021-05-19', 0, 1, '18:23:00', '20:23:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.6074773802915, 45.4751614697085, 'b2b39c398f5926c0.jpg', '2021-05-21 19:23:39'),
(13, 27, 'Rue Rancourt', '2021-05-05', '2021-05-26', 0, 0, '19:25:00', '21:25:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.64817343029151, 45.5714558697085, '114840a69d61a3af.jpg', '2021-05-21 19:25:54'),
(14, 22, 'Rue St-Hubert', '2021-05-06', '2021-05-30', 0, 0, '19:52:00', '20:52:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.5948585802915, 45.5301752197085, 'a72558b90cdaf605.jpg', '2021-05-23 18:52:23'),
(16, 22, 'Rue Sherbrooke Ouest', '2021-05-11', '2021-05-30', 1, 0, '22:25:00', '23:25:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.60901108029151, 45.47398001970851, '13e9dae6ba10cc49.jpg', '2021-05-23 20:25:08'),
(18, 28, 'R. Saint-Denis', '2021-05-12', '2021-05-31', 1, 1, '00:00:00', '00:00:00', '01:42:00', '03:42:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.60000458029151, 45.5297508197085, '4365c50e78ad25e9.jpg', '2021-05-26 15:42:54'),
(19, 29, 'Avenue de Kent', '2021-05-05', '2021-05-31', 1, 1, '00:00:00', '00:00:00', '15:12:00', '18:12:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', -73.62728588029151, 45.5083212197085, '77770a625b826d61.jpg', '2021-05-26 16:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `role` enum('admin','user','','') NOT NULL DEFAULT 'user',
  `firstName` varchar(200) NOT NULL,
  `lastName` varchar(200) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `street` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `province` varchar(200) NOT NULL,
  `postalCode` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `role`, `firstName`, `lastName`, `userName`, `email`, `password`, `phone`, `street`, `city`, `province`, `postalCode`) VALUES
(21, 'user', 'John', 'Smith', 'johnsmith', 'john@yahoo.com', '$2y$10$N/ZtTYqHrprFizpDMQBe2OoepyejFYLc2g2E046maQDrOKwALpOEy', '777-999-4480', '6720 Orlando Street', 'Ottawa', 'ON', 'L2E 3S8'),
(22, 'admin', 'Emma', 'Richson', 'emmarichson', 'emma@yahoo.com', '$2y$10$eA05YZc3zwvBkDqB/560Neh3Pwlu2xDpOGekZLHNwpXhcMrmToXSq', '333-666-2222', '3075 Anthony Street', 'Ottawa', 'ON', 'E3H 2A3'),
(23, 'user', 'Joe', 'Janathan', 'joejanathan', 'joe@email.com', '$2y$10$05YXrNTtKAwI0UWLiayoH.AcK1pT/uicST3Ox5uEhUoj77wBPjO3G', '666-444-8888', '3080 Avenue Park', 'Toronto', 'ON', 'L7S 6A7'),
(24, 'user', 'Adel', 'Kidman', 'adel1', 'adel@gmail.com', '$2y$10$op/4.W0VGnmJXRpiP4a5KurESkE99Iwa01Jmh2hpOlvKz9/xif.8K', '999-333-2222', '44 Ave North', 'Toronto', 'ON', 'L7S 8G2'),
(26, 'user', 'Nick', 'Xenos', 'xenos', 'nxenos@me.com', '$2y$10$YpsJf/QvGoaEci.851GP1eoAwCNInlVmbdX.MPuMJJLK9HUqLZjRS', '514-975-6456', '150 Chemin de la Pointe-Sud', 'verdun', 'QC', 'H3E 0A7'),
(27, 'user', 'Jamshid', 'Ebrahimi', 'jamshid1', 'jamshid@yahoo.com', '$2y$10$IziZVVJ4LKFNaNqsYbEQqOADHrth2pxMYnEflMebms6DqvX2mJMBC', '545-333-8822', '490 McTavish Street', 'Halifax', 'NS', 'M2S 6W2'),
(28, 'user', 'daniel', 'rock', 'daniel1', 'daniel@gmail.com', '$2y$10$i8V2uecFT3qEppz7OAgZmOUJkx3E3L4.qzDeKj78LQTXiC530FFNq', '444-333-5555', '1230 ave kent', 'Montreal', 'QC', 'H3S 2K9'),
(29, 'user', 'ali', 'alaghbandrad', 'ali1', 'ali@yahoo.com', '$2y$10$oJK6ZjnPA36LcFQ0hLHjpuXeMGePAJp2MXHNnjGCouG1ViHEmTeKC', '444-555-3333', '44 ave 1st', 'Montreal', 'QC', 'K3T 8S4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addrule`
--
ALTER TABLE `addrule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addrule`
--
ALTER TABLE `addrule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addrule`
--
ALTER TABLE `addrule`
  ADD CONSTRAINT `addrule_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
