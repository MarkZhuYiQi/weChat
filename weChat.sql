-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2016 at 12:34 PM
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
-- Table structure for table `we_admin`
--

CREATE TABLE `we_admin` (
  `id` int(11) NOT NULL,
  `admin_userName` varchar(20) NOT NULL,
  `admin_password` varchar(40) NOT NULL,
  `admin_regTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `we_admin`
--

INSERT INTO `we_admin` (`id`, `admin_userName`, `admin_password`, `admin_regTime`) VALUES
(1, 'red', 'moJTgyDPNztjEJMT3hmPzA==', '2016-10-22 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `we_menu`
--

CREATE TABLE `we_menu` (
  `id` int(11) NOT NULL,
  `menu_text` varchar(20) NOT NULL COMMENT '菜单内容',
  `menu_type` varchar(30) NOT NULL COMMENT '菜单类型',
  `menu_key` varchar(30) DEFAULT NULL COMMENT '菜单对应键',
  `menu_url` varchar(100) DEFAULT NULL COMMENT '菜单指向链接',
  `menu_media_id` text COMMENT '图文素材对应值',
  `menu_pid` int(11) NOT NULL DEFAULT '0' COMMENT '对应父ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `we_menu`
--

INSERT INTO `we_menu` (`id`, `menu_text`, `menu_type`, `menu_key`, `menu_url`, `menu_media_id`, `menu_pid`) VALUES
(1, '最新发布', 'click', 'WE_NEWEST', NULL, '', 0),
(2, '菜单', 'father', '', NULL, '', 0),
(3, '搜索', 'view', '', 'http://www.soso.com/', '', 2),
(4, '赞一下我们', 'click', 'GOOD', NULL, NULL, 2),
(5, '关于我们', 'click', 'WE_ABOUT', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `we_menu_type`
--

CREATE TABLE `we_menu_type` (
  `id` int(11) NOT NULL,
  `type_text` varchar(30) NOT NULL COMMENT '事件类型',
  `type_introduction` text NOT NULL COMMENT '类型介绍'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `we_menu_type`
--

INSERT INTO `we_menu_type` (`id`, `type_text`, `type_introduction`) VALUES
(1, 'click', '点击推事件用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；'),
(2, 'view', '跳转URL用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。'),
(3, 'scancode_push', '扫码推事件用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。'),
(4, 'scancode_waitmsg', '扫码推事件且弹出“消息接收中”提示框用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。'),
(5, 'pic_sysphoto', '弹出系统拍照发图用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。'),
(6, 'pic_photo_or_album', '弹出拍照或者相册发图用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。'),
(7, 'pic_weixin', '弹出微信相册发图器用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。'),
(8, 'location_select', '弹出地理位置选择器用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。'),
(9, 'media_id', '下发消息（除文本消息）用户点击media_id类型按钮后，微信服务器会将开发者填写的永久素材id对应的素材下发给用户，永久素材类型可以是图片、音频、视频、图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。'),
(10, 'view_limited', '跳转图文消息URL用户点击view_limited类型按钮后，微信客户端将打开开发者在按钮中填写的永久素材id对应的图文消息URL，永久素材类型只支持图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。');

-- --------------------------------------------------------

--
-- Table structure for table `we_tree`
--

CREATE TABLE `we_tree` (
  `id` int(11) NOT NULL,
  `tree_text` varchar(30) NOT NULL COMMENT '菜单内容',
  `tree_url` varchar(100) NOT NULL DEFAULT '#' COMMENT '菜单指向的路径',
  `tree_state` int(1) NOT NULL DEFAULT '1' COMMENT '是否开启菜单',
  `tree_pid` int(11) NOT NULL DEFAULT '0' COMMENT '如果是子菜单，设定父级ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `we_tree`
--

INSERT INTO `we_tree` (`id`, `tree_text`, `tree_url`, `tree_state`, `tree_pid`) VALUES
(1, '自定义菜单', '?control=m_index&action=customMenu', 1, 0),
(2, '子功能1', '#', 1, 1),
(3, '子子功能1', '#', 1, 2),
(4, '功能2', '#', 1, 0),
(5, '子功能2', '#', 1, 1);

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
-- Indexes for table `we_admin`
--
ALTER TABLE `we_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `we_menu`
--
ALTER TABLE `we_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `we_menu_type`
--
ALTER TABLE `we_menu_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `we_tree`
--
ALTER TABLE `we_tree`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `we_user`
--
ALTER TABLE `we_user`
  ADD PRIMARY KEY (`we_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `we_admin`
--
ALTER TABLE `we_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `we_menu`
--
ALTER TABLE `we_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `we_menu_type`
--
ALTER TABLE `we_menu_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `we_tree`
--
ALTER TABLE `we_tree`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `we_user`
--
ALTER TABLE `we_user`
  MODIFY `we_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
