/*
Navicat MySQL Data Transfer

Source Server         : API服务器+合纵力数据库
Source Server Version : 50548
Source Host           : 60.205.6.131:3306
Source Database       : phpcms_hezongli

Target Server Type    : MYSQL
Target Server Version : 50548
File Encoding         : 65001

Date: 2018-04-17 11:15:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for v9_hzl_user_im
-- ----------------------------
DROP TABLE IF EXISTS `v9_hzl_user_im`;
CREATE TABLE `v9_hzl_user_im` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` char(20) NOT NULL,
  `user_pwd` char(64) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of v9_hzl_user_im
-- ----------------------------
INSERT INTO `v9_hzl_user_im` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '0');
INSERT INTO `v9_hzl_user_im` VALUES ('2', 'hezonglicanyin', '13e88cfa84bf11c184b7ab5b3e22d6ee', '0');
INSERT INTO `v9_hzl_user_im` VALUES ('5', 'qqq', 'b2ca678b4c936f905fb82f2733f5297f', '1523868388');
INSERT INTO `v9_hzl_user_im` VALUES ('6', 'www', '4eae35f1b35977a00ebd8086c259d4c9', '1523868394');
INSERT INTO `v9_hzl_user_im` VALUES ('7', 'eee', 'd2f2297d6e829cd3493aa7de4416a18f', '1523868408');
INSERT INTO `v9_hzl_user_im` VALUES ('8', 'rrr', '44f437ced647ec3f40fa0841041871cd', '1523868416');
INSERT INTO `v9_hzl_user_im` VALUES ('9', 'ttt', '9990775155c3518a0d7917f7780b24aa', '1523868423');
INSERT INTO `v9_hzl_user_im` VALUES ('10', 'yyy', 'f0a4058fd33489695d53df156b77c724', '1523868429');
INSERT INTO `v9_hzl_user_im` VALUES ('11', 'uuu', '3fcf6748deb8c48fcbfef4a9cd6e55a0', '1523868440');
INSERT INTO `v9_hzl_user_im` VALUES ('13', 'aaa', '47bce5c74f589f4867dbd57e9ca9f808', '1523868472');
INSERT INTO `v9_hzl_user_im` VALUES ('14', 'sss', '9f6e6800cfae7749eb6c486619254b9c', '1523868479');
INSERT INTO `v9_hzl_user_im` VALUES ('15', 'ddd', '77963b7a931377ad4ab5ad6a9cd718aa', '1523868484');
INSERT INTO `v9_hzl_user_im` VALUES ('16', 'fff', '343d9040a671c45832ee5381860e2996', '1523868490');
INSERT INTO `v9_hzl_user_im` VALUES ('17', 'ggg', 'ba248c985ace94863880921d8900c53f', '1523868495');
INSERT INTO `v9_hzl_user_im` VALUES ('18', 'hhh', 'a3aca2964e72000eea4c56cb341002a4', '1523868510');
INSERT INTO `v9_hzl_user_im` VALUES ('19', 'zzz', 'f3abb86bd34cf4d52698f14c0da1dc60', '1523869684');
INSERT INTO `v9_hzl_user_im` VALUES ('20', 'xx', '9336ebf25087d91c818ee6e9ec29f8c1', '1523869700');
INSERT INTO `v9_hzl_user_im` VALUES ('21', 'xxx', 'f561aaf6ef0bf14d4208bb46a4ccb3ad', '1523869700');
