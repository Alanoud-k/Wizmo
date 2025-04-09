-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 09, 2025 at 11:33 AM
-- Server version: 8.0.35
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Wizmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `Business`
--

CREATE TABLE `Business` (
  `username` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `businessName` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `phoneNumber` int NOT NULL,
  `city` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Business`
--

INSERT INTO `Business` (`username`, `businessName`, `category`, `email`, `password`, `phoneNumber`, `city`, `description`, `logo`) VALUES
('Lama', 'show party', 'party supplies', 'lama@gmail.com', '$2y$10$PNd1ctvm5k21dg61b3jfsOn1/rYZ2Kk0lAtg5.l17Q9zT/T8p2I/6', 564489325, 'Khobar', 'A one-stop shop for balloons, decorations, tableware, and all your party essentials.', 'images/partyLogo.png'),
('rahaf5', 'Fashion rose', 'Fabric', 'rahaf@gmail.com', '$2y$10$jGjTPsKO4yz1j8yTC.SAE.QcWRbIP2.8Flf6FDGr0dcqJHRjwiS8a', 501234567, 'Riyadh', 'A fabric store offering a wide variety of high-quality textiles for clothing, upholstery, and crafts.', 'images/fabricLogo.png'),
('sarah22', 'tech', 'Technology and devices', 'sarah@gmail.com', '$2y$10$oRTxPg59o7Fd7sF/6j9AKe1qQIk.mKy2J3WW.r4v0YlcYJO6ERbUW', 556284635, 'Riyadh', 'A tech store offering the latest gadgets, electronics, and accessories for everyday use.', 'images/techLogo.png');

-- --------------------------------------------------------

--
-- Table structure for table `Deal`
--

CREATE TABLE `Deal` (
  `dealID` int NOT NULL,
  `distributorName` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `productName` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date` varchar(300) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Deal`
--

INSERT INTO `Deal` (`dealID`, `distributorName`, `productName`, `username`, `quantity`, `date`) VALUES
(1, 'Fashion rose', 'Black crepe fabric', 'rahaf5', '3m', '1/4/2025'),
(2, 'tech', 'Security camera', 'sarah22', '2', '20/4/2025'),
(3, 'Fashion rose', 'Red satin fabric', 'rahaf5', '2m', '31/3/2025');

-- --------------------------------------------------------

--
-- Table structure for table `Product`
--

CREATE TABLE `Product` (
  `productName` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `price` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(300) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Product`
--

INSERT INTO `Product` (`productName`, `quantity`, `description`, `image`, `price`, `username`) VALUES
('Black crepe fabric', '50m', 'Black crepe fabric is lightweight with a slightly crinkled texture, offering a soft drape ideal for elegant dresses, blouses, and formal wear.', 'images/black.png', '80SAR per meter', 'rahaf5'),
('Party tables', '50', 'Party tables are sturdy, foldable surfaces used for serving food, displaying decor, or seating guests at events and celebrations.', 'images/tables.png', '30SAR', 'sarah22'),
('Red satin fabric', '30m', 'Red satin fabric is smooth, glossy, and luxurious, perfect for elegant dresses, evening wear, and decorative accents.', 'images/red.png', '50SAR per meter', 'rahaf5'),
('Security camera', '20 cameras', 'Security cameras provide real-time surveillance and recording to help monitor and protect homes, businesses, and public spaces.', 'images/camera.png', '100SAR', 'sarah22');

-- --------------------------------------------------------

--
-- Table structure for table `Request`
--

CREATE TABLE `Request` (
  `requestID` int NOT NULL,
  `state` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `message` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `receiverUsername` varchar(300) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Request`
--

INSERT INTO `Request` (`requestID`, `state`, `message`, `username`, `receiverUsername`) VALUES
(1, 'Pending', 'We would like to collaborate with you.', 'rahaf5', 'Lama'),
(2, 'Accepted', 'Join us for an exciting partnership!', 'sarah22', 'rahaf5'),
(3, 'Declined', 'Let\'s bring healthy food to everyone.', 'Lama', 'sarah22'),
(5, 'Pending', 'Would like to discuss business with you!', 'Lama', 'rahaf5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Business`
--
ALTER TABLE `Business`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `Deal`
--
ALTER TABLE `Deal`
  ADD PRIMARY KEY (`dealID`),
  ADD KEY `productName` (`productName`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`productName`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `Request`
--
ALTER TABLE `Request`
  ADD PRIMARY KEY (`requestID`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Deal`
--
ALTER TABLE `Deal`
  MODIFY `dealID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Request`
--
ALTER TABLE `Request`
  MODIFY `requestID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Deal`
--
ALTER TABLE `Deal`
  ADD CONSTRAINT `deal_ibfk_1` FOREIGN KEY (`productName`) REFERENCES `Product` (`productName`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `deal_ibfk_2` FOREIGN KEY (`username`) REFERENCES `Business` (`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Product`
--
ALTER TABLE `Product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`username`) REFERENCES `Business` (`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Request`
--
ALTER TABLE `Request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`username`) REFERENCES `Business` (`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
