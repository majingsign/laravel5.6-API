/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : gdan

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-09-06 10:09:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yxkj_admin
-- ----------------------------
DROP TABLE IF EXISTS `yxkj_admin`;
CREATE TABLE `yxkj_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) DEFAULT '',
  `admin_password` varchar(150) DEFAULT '',
  `depart_id` int(11) unsigned DEFAULT '0' COMMENT '角色id  部门id',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '添加管理员时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='管理员';

-- ----------------------------
-- Records of yxkj_admin
-- ----------------------------
INSERT INTO `yxkj_admin` VALUES ('1', 'admin', 'fcea920f7412b5da7be0cf42b8c93759', '4', '0');
INSERT INTO `yxkj_admin` VALUES ('2', 'qwwww', '123456', '0', '0');
INSERT INTO `yxkj_admin` VALUES ('3', 'wwwww', '123456', '0', '0');
INSERT INTO `yxkj_admin` VALUES ('8', '测试昵称', 'e10adc3949ba59abbe56e057f20f883e', '0', '0');
INSERT INTO `yxkj_admin` VALUES ('9', 'gdtt', 'e10adc3949ba59abbe56e057f20f883e', '0', '0');
INSERT INTO `yxkj_admin` VALUES ('10', '张三', 'e10adc3949ba59abbe56e057f20f883e', '6', '0');
INSERT INTO `yxkj_admin` VALUES ('11', '李四', 'e10adc3949ba59abbe56e057f20f883e', '1', '0');
INSERT INTO `yxkj_admin` VALUES ('12', '王五', 'e10adc3949ba59abbe56e057f20f883e', '6', '0');
INSERT INTO `yxkj_admin` VALUES ('13', '赵六', 'e10adc3949ba59abbe56e057f20f883e', '5', '0');

-- ----------------------------
-- Table structure for yxkj_department
-- ----------------------------
DROP TABLE IF EXISTS `yxkj_department`;
CREATE TABLE `yxkj_department` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT ' 部门名称',
  `admin_id` int(11) unsigned DEFAULT '0' COMMENT '部门负责人',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '设置部门负责人时间',
  `act_list` varchar(600) DEFAULT '' COMMENT '菜单列表',
  `desc` varchar(500) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='部门表';

-- ----------------------------
-- Records of yxkj_department
-- ----------------------------
INSERT INTO `yxkj_department` VALUES ('1', '市场部', '11', '0', '2,13,17,3,19,20,22,23,5,25,26,27,28,29,30', null);
INSERT INTO `yxkj_department` VALUES ('2', '研发部', '2', '0', '5,6', null);
INSERT INTO `yxkj_department` VALUES ('4', '超级管理员', '9', '1535680646', '*', null);
INSERT INTO `yxkj_department` VALUES ('5', '人事部', '13', '1535683275', null, '人事管理12334');
INSERT INTO `yxkj_department` VALUES ('6', '客户部', '10', '1535683287', '1,8,9,10,11,12', '客户联系');
INSERT INTO `yxkj_department` VALUES ('7', '人事部', '0', '1535695995', '', null);
INSERT INTO `yxkj_department` VALUES ('8', '计划部', '0', '1535707344', '2,5,6,7', null);
INSERT INTO `yxkj_department` VALUES ('9', '家互补', '0', '1535707389', '2,5,6,7', null);
INSERT INTO `yxkj_department` VALUES ('10', '人事部1', '0', '1535707556', '2,5,6,7', null);
INSERT INTO `yxkj_department` VALUES ('11', '人事部2', '10', '1535707614', '2,6,7', null);
