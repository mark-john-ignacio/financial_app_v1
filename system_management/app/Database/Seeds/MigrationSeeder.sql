/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:33063
 Source Schema         : st_myxfin_com

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 29/08/2024 14:39:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (4, '2024-07-15-022656', 'App\\Database\\Migrations\\AddIdToSOTable', 'default', 'App', 1721702270, 1);
INSERT INTO `migrations` VALUES (5, '2024-07-23-023622', 'App\\Database\\Migrations\\AddIdToSoTTable', 'default', 'App', 1721702270, 1);
INSERT INTO `migrations` VALUES (7, '2024-07-25-063158', 'App\\Database\\Migrations\\AddCreatedUpdatedDeletedToCustomersTable', 'default', 'App', 1721889322, 2);
INSERT INTO `migrations` VALUES (8, '2024-07-25-075731', 'App\\Database\\Migrations\\AddIdToCustomersTable', 'default', 'App', 1721894383, 3);
INSERT INTO `migrations` VALUES (9, '2024-07-26-011311', 'App\\Database\\Migrations\\AddCreatedUpdatedDeletedToItemsTable', 'default', 'App', 1721956483, 4);
INSERT INTO `migrations` VALUES (13, '2024-07-26-061456', 'App\\Database\\Migrations\\AddPlantsToItemTypeOnTheGroupingsTable', 'default', 'App', 1721976291, 5);
INSERT INTO `migrations` VALUES (14, '2024-07-26-063521', 'App\\Database\\Migrations\\AddPlantClassificationOnTheGroupingsTable', 'default', 'App', 1721976291, 5);
INSERT INTO `migrations` VALUES (15, '2024-07-26-080721', 'App\\Database\\Migrations\\AddCreatedUpdatedDeletedToSuppliersTable', 'default', 'App', 1721981313, 6);
INSERT INTO `migrations` VALUES (16, '2024-07-30-022840', 'App\\Database\\Migrations\\AddIdToReceiveTTable', 'default', 'App', 1724226783, 7);
INSERT INTO `migrations` VALUES (17, '2024-08-01-034032', 'App\\Database\\Migrations\\CreateWoocommerceLandingOrdersTable', 'default', 'App', 1724226783, 7);
INSERT INTO `migrations` VALUES (18, '2024-08-02-003354', 'App\\Database\\Migrations\\CreateBIRFormImageTable', 'default', 'App', 1724226783, 7);
INSERT INTO `migrations` VALUES (19, '2024-08-20-014349', 'App\\Database\\Migrations\\AddReportingPeriodTypeToCompanyTable', 'default', 'App', 1724226783, 7);
INSERT INTO `migrations` VALUES (20, '2024-08-28-060341', 'App\\Database\\Migrations\\AddIdToSalesTable', 'default', 'App', 1724825090, 8);
INSERT INTO `migrations` VALUES (21, '2024-08-29-021303', 'App\\Database\\Migrations\\Rename1601ETo0619EBIRForm', 'default', 'App', 1724913469, 9);

SET FOREIGN_KEY_CHECKS = 1;
