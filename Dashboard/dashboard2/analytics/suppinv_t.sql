/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100427 (10.4.27-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : myxfin

 Target Server Type    : MySQL
 Target Server Version : 100427 (10.4.27-MariaDB)
 File Encoding         : 65001

 Date: 25/03/2024 10:25:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for suppinv_t
-- ----------------------------
DROP TABLE IF EXISTS `suppinv_t`;
CREATE TABLE `suppinv_t`  (
  `compcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cidentity` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ctranno` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nident` int NOT NULL,
  `creference` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nrefidentity` int NOT NULL,
  `crefPO` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nrefidentity_po` int NULL DEFAULT NULL,
  `citemno` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nqty` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `nqtyorig` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `nqtyreturned` decimal(18, 4) NULL DEFAULT 0.0000,
  `cunit` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nprice` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `namount` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `nbaseamount` decimal(18, 4) NULL DEFAULT NULL,
  `ncost` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `nfactor` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `cmainunit` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cacctcode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cvatcode` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nrate` decimal(18, 4) NULL DEFAULT 0.0000,
  `nnetvat` decimal(18, 4) NULL DEFAULT 0.0000,
  `nlessvat` decimal(18, 4) NULL DEFAULT 0.0000,
  `cewtcode` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `newtrate` int NULL DEFAULT 0,
  PRIMARY KEY (`compcode`, `cidentity`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of suppinv_t
-- ----------------------------
INSERT INTO `suppinv_t` VALUES ('001', 'IT001', 'INV001', 1, 'REF001', 1, NULL, NULL, 'ITEM0002', 10.0000, 10.0000, NULL, 'PCS', 100.0000, 1000.0000, NULL, 900.0000, 1.0000, 'PCS', 'ACC001', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `suppinv_t` VALUES ('001', 'IT002', 'INV002', 1, 'REF003', 2, NULL, NULL, 'ITEM0003', 20.0000, 20.0000, NULL, 'PCS', 50.0000, 1000.0000, NULL, 800.0000, 1.0000, 'PCS', 'ACC002', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `suppinv_t` VALUES ('001', 'IT003', 'INV003', 1, 'REF005', 3, NULL, NULL, 'H0000001', 5.0000, 5.0000, NULL, 'LIC', 200.0000, 1000.0000, NULL, 800.0000, 1.0000, 'LIC', 'ACC003', NULL, NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
