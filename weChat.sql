-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 20, 2016 at 09:48 AM
-- Server version: 5.7.15
-- PHP Version: 7.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weChat`
--

-- --------------------------------------------------------

--
-- Table structure for table `we_user`
--

CREATE TABLE `we_user` (
  `we_id` int(11) NOT NULL,
  `we_openid` varchar(32) NOT NULL COMMENT '用户openid',
  `we_subscribeDate` varchar(50) NOT NULL COMMENT '关注时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `we_user`
--

INSERT INTO `we_user` (`we_id`, `we_openid`, `we_subscribeDate`) VALUES
(4, 'o8SEYwhNzG-hPuEjw_kjxb9nZ1aA', '1476954512');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `we_user`
--
ALTER TABLE `we_user`
  ADD PRIMARY KEY (`we_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `we_user`
--
ALTER TABLE `we_user`
  MODIFY `we_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
