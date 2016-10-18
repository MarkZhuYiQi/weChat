/*
Navicat MySQL Data Transfer

Source Server         : red
Source Server Version : 50715
Source Host           : localhost:3306
Source Database       : weChat

Target Server Type    : MYSQL
Target Server Version : 50715
File Encoding         : 65001

Date: 2016-10-18 16:54:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for we_user
-- ----------------------------
DROP TABLE IF EXISTS `we_user`;
CREATE TABLE `we_user` (
  `we_id` int(11) NOT NULL AUTO_INCREMENT,
  `we_openId` varchar(30) NOT NULL COMMENT 'user''s open Id',
  PRIMARY KEY (`we_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of we_user
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;
