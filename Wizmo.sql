-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 09, 2025 at 06:55 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wizmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `username` varchar(300) NOT NULL,
  `businessName` varchar(300) NOT NULL,
  `category` varchar(300) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(200) NOT NULL,
  `phoneNumber` int(11) NOT NULL,
  `city` varchar(300) NOT NULL,
  `description` varchar(500) NOT NULL,
  `logo` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`username`, `businessName`, `category`, `email`, `password`, `phoneNumber`, `city`, `description`, `logo`) VALUES
('Byte', 'BytePlay', 'entertainment', 'B@gmail.com', '$2y$10$m8wL3JFGoQnG5h9f5FbU/OER4ebKOsBGx1qdo7FTkWzV0no8zHd56', 501234567, 'khobar', 'One store infinite worlds', 'uploads/photo_2_2025-04-09_21-14-27.jpg'),
('Luna', 'LunaThread', 'fashion', 'L@gmail.com', '$2y$10$44tpZEJow2qHIyiOLrWPOu6qZwOYNM7yeN6YmqGvAGoGraAYqdQhm', 501000000, 'riyadh', 'Step into LunaThread, where fashion becomes fantasy. From gothic romance and whimsical boho to vibrant, one-of-a-kind statement pieces, our curated collection speaks to souls who dare to stand out. Every stitch tells a story, every outfit is a character. Whether you’re a dreamer, a rebel, or a wild spirit — dress like your own legend', 'uploads/photo_16_2025-04-09_21-14-27.jpg'),
('tales', 'Tiny Tales', 'education', 't@gmail.com', '$2y$10$XAEFVnV1bE196S1dPxNdTOXZUrbe4FHFu1AZPn4FyWxpsS/tR4kvu', 502030400, 'jeddah', 'Tiny Tales is a magical world built for curious minds and growing hearts. Our bookstore is filled with stories that inspire courage, laughter, and imagination. From picture books to early chapter stories, each shelf invites children to explore, dream, and learn. Come find your child’s next adventure — because every great reader starts with a tiny tale', 'uploads/photo_1_2025-04-09_21-14-27.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `deal`
--

CREATE TABLE `deal` (
  `dealID` int(11) NOT NULL,
  `distributorName` varchar(300) NOT NULL,
  `productName` varchar(300) NOT NULL,
  `username` varchar(300) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `date` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `deal`
--

INSERT INTO `deal` (`dealID`, `distributorName`, `productName`, `username`, `quantity`, `date`) VALUES
(4, 'dress2impress Shop', 'Midnight Rose Enchantrees', 'Luna', '10', '2025-04-10'),
(5, 'virgin megastore', 'Minecraft', 'Byte', '40', '2025-04-16'),
(6, 'Jarir BookStore', 'اطفال الغابة', 'tales', '30', '2025-04-30'),
(7, 'Jarir BookStore', 'قلوب ملونة', 'tales', '20', '2025-04-29');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productName` varchar(250) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `image` varchar(300) NOT NULL,
  `price` varchar(50) NOT NULL,
  `username` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productName`, `quantity`, `description`, `image`, `price`, `username`) VALUES
('4th dimension of lines', '15', 'layered skirt', 'images/photo_13_2025-04-09_21-14-27.jpg', '60SAR', 'Luna'),
('Alice : madness return', '20', 'psychological horror action game', 'images/photo_8_2025-04-09_21-14-27.jpg', '37SAR', 'Byte'),
('Cnady Choas Couture', '5', 'denim-patchwork skirt with pink layers and embellishments', 'images/photo_15_2025-04-09_21-14-27.jpg', '50SAR', 'Luna'),
('Heavy Rain', '18', 'making decision game', 'images/photo_4_2025-04-09_21-14-27.jpg', '30SAR', 'Byte'),
('Midnight Rose Enchantrees', '20', 'burgundy gothic dress', 'images/photo_11_2025-04-09_21-14-27.jpg', '199SAR', 'Luna'),
('Minecraft', '60', 'mine and craft game', 'images/photo_3_2025-04-09_21-14-27.jpg', '40SAR', 'Byte'),
('Rustic Muse', '10', 'boho floral top', 'images/photo_14_2025-04-09_21-14-27.jpg', '80SAR', 'Luna'),
('Skies of Sahara', '20', 'A line dress layered', 'images/photo_12_2025-04-09_21-14-27.jpg', '299SAR', 'Luna'),
('What Remains of Edith Finch', '7', 'cozy family story game', 'images/photo_5_2025-04-09_21-14-27.jpg', '60SAR', 'Byte'),
('ابن خلدون', '60', 'عن ابن خلدون', 'images/photo_10_2025-04-09_21-14-27.jpg', '20SAR', 'tales'),
('اطفال الغابة', '60', 'قصة للاطفال 1', 'images/photo_7_2025-04-09_21-14-27.jpg', '15SAR', 'tales'),
('قلوب ملونة', '50', 'قصة لطيفة', 'images/photo_6_2025-04-09_21-14-27.jpg', '30SAR', 'tales'),
('كم نجمة في السماء؟', '40', 'قصة للاطفال 2', 'images/photo_9_2025-04-09_21-14-27.jpg', '25SAR', 'tales');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `requestID` int(11) NOT NULL,
  `state` varchar(200) NOT NULL,
  `message` varchar(300) NOT NULL,
  `username` varchar(300) NOT NULL,
  `receiverUsername` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`requestID`, `state`, `message`, `username`, `receiverUsername`) VALUES
(7, 'Pending', 'hey, i think your dresses can suit a video game character!!\r\nlet\'s collaborate <3', 'Byte', 'Luna'),
(8, 'Accepted', 'hi how are you?, what if we advertise a game similar to book story?', 'tales', 'Byte');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `deal`
--
ALTER TABLE `deal`
  ADD PRIMARY KEY (`dealID`),
  ADD KEY `productName` (`productName`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productName`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`requestID`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deal`
--
ALTER TABLE `deal`
  MODIFY `dealID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deal`
--
ALTER TABLE `deal`
  ADD CONSTRAINT `deal_ibfk_1` FOREIGN KEY (`productName`) REFERENCES `product` (`productName`),
  ADD CONSTRAINT `deal_ibfk_2` FOREIGN KEY (`username`) REFERENCES `business` (`username`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`username`) REFERENCES `business` (`username`);

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`username`) REFERENCES `business` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
