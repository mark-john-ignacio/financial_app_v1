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

 Date: 25/03/2024 10:24:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for suppinv
-- ----------------------------
DROP TABLE IF EXISTS `suppinv`;
CREATE TABLE `suppinv`  (
  `compcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ctranno` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ddate` datetime NOT NULL,
  `dreceived` date NOT NULL,
  `cpreparedby` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ccode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ccustacctcode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cremarks` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `crefsi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ngross` decimal(18, 4) NOT NULL DEFAULT 0.0000,
  `nbasegross` decimal(18, 4) NULL DEFAULT NULL,
  `lapproved` tinyint(1) NOT NULL DEFAULT 0,
  `lvoid` tinyint(1) NOT NULL DEFAULT 0,
  `lcancelled` tinyint(1) NOT NULL DEFAULT 0,
  `lprintposted` tinyint(1) NOT NULL DEFAULT 0,
  `lamtcancel` tinyint(1) NOT NULL DEFAULT 0,
  `lamtpost` tinyint(1) NOT NULL DEFAULT 0,
  `ccurrencycode` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ccurrencydesc` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nexchangerate` decimal(18, 4) NULL DEFAULT NULL,
  `crefrr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nvat` decimal(18, 4) NULL DEFAULT NULL,
  `nnet` decimal(18, 4) NULL DEFAULT 0.0000,
  `ndue` decimal(18, 4) NULL DEFAULT NULL,
  `npaidamount` decimal(18, 4) NULL DEFAULT NULL,
  PRIMARY KEY (`compcode`, `ctranno`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of suppinv
-- ----------------------------
INSERT INTO `suppinv` VALUES ('001', 'INV001', '2024-03-20 08:00:00', '2024-03-20', 'John Doe', 'PLDT', 'ACC001', 'Sample Remarks', 'REF001', 1000.0000, NULL, 1, 0, 0, 0, 0, 0, 'PHP', 'Philippine Peso', NULL, 'REF002', 120.0000, 1120.0000, 1120.0000, 0.0000);
INSERT INTO `suppinv` VALUES ('001', 'INV002', '2024-03-21 09:00:00', '2024-03-21', 'Jane Smith', 'SUP001', 'ACC002', 'Another Remarks', 'REF003', 1500.0000, NULL, 1, 0, 0, 0, 0, 0, 'PHP', 'Philippine Peso', NULL, 'REF004', 180.0000, 1680.0000, 1680.0000, 0.0000);
INSERT INTO `suppinv` VALUES ('001', 'INV003', '2024-03-22 10:00:00', '2024-03-22', 'Alice Johnson', 'SUP002', 'ACC003', 'Additional Remarks', 'REF005', 2000.0000, NULL, 1, 0, 0, 0, 0, 0, 'PHP', 'Philippine Peso', NULL, 'REF006', 240.0000, 2240.0000, 2240.0000, 0.0000);

SET FOREIGN_KEY_CHECKS = 1;
