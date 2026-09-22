-- =============================================
-- POS ร้านขายของเบ็ดเตล็ด - Database Schema
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

CREATE DATABASE IF NOT EXISTS `pos_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pos_db`;

-- =============================================
-- ตาราง admin (ผู้ใช้ระบบ/พนักงาน)
-- =============================================
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) NOT NULL,
  `admin_user` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_permission` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=พนักงานขาย, 1=ผู้จัดการ, 2=เจ้าของร้าน',
  `admin_status` tinyint(1) NOT NULL DEFAULT 1,
  `admin_phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_user`, `admin_pass`, `admin_permission`, `admin_status`) VALUES
(1, 'เจ้าของร้าน', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1),
(2, 'พนักงาน 1', 'cashier1', '827ccb0eea8a706c4c34a16891f84e7b', 0, 1);

-- =============================================
-- ตาราง category_index (หมวดหมู่สินค้า)
-- =============================================
DROP TABLE IF EXISTS `category_index`;
CREATE TABLE `category_index` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_img` varchar(255) DEFAULT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_status` tinyint(1) NOT NULL DEFAULT 1,
  `category_sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `category_index` (`category_id`, `category_name`, `category_status`, `category_sort`) VALUES
(1, 'เครื่องดื่ม', 1, 1),
(2, 'ขนมขบเคี้ยว', 1, 2),
(3, 'อาหารสำเร็จรูป', 1, 3),
(4, 'ของใช้ในบ้าน', 1, 4),
(5, 'เครื่องเขียน', 1, 5),
(6, 'ยาและเวชภัณฑ์', 1, 6),
(7, 'ของสด', 1, 7),
(8, 'บุหรี่/เครื่องดื่มแอลกอฮอล์', 1, 8),
(9, 'อื่นๆ', 1, 9);

-- =============================================
-- ตาราง product_index (สินค้า)
-- =============================================
DROP TABLE IF EXISTS `product_index`;
CREATE TABLE `product_index` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_img` varchar(255) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ราคาทุน',
  `product_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ราคาขาย',
  `product_stock` int(11) NOT NULL DEFAULT 0 COMMENT 'จำนวนคงเหลือ',
  `product_stock_min` int(11) NOT NULL DEFAULT 5 COMMENT 'จำนวนขั้นต่ำ แจ้งเตือน',
  `product_unit` varchar(50) DEFAULT 'ชิ้น' COMMENT 'หน่วยนับ',
  `product_status` tinyint(1) NOT NULL DEFAULT 1,
  `product_sort` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  INDEX `idx_barcode` (`product_barcode`),
  INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `product_index` (`product_barcode`, `product_code`, `product_name`, `product_cost`, `product_price`, `product_stock`, `product_unit`, `category_id`, `product_sort`) VALUES
('8850999220017', 'P001', 'น้ำดื่มสิงห์ 600ml', 5.00, 7.00, 100, 'ขวด', 1, 1),
('8851123212345', 'P002', 'โค้ก 325ml', 10.00, 15.00, 80, 'กระป๋อง', 1, 2),
('8850987654321', 'P003', 'เลย์ รสออริจินัล 75g', 15.00, 22.00, 50, 'ซอง', 2, 3),
('8851234567890', 'P004', 'มาม่า รสต้มยำกุ้ง', 5.00, 6.00, 200, 'ซอง', 3, 4),
('8850111222333', 'P005', 'ผงซักฟอก บรีส 500g', 30.00, 45.00, 30, 'ถุง', 4, 5),
('8850444555666', 'P006', 'ปากกา ตราม้า', 5.00, 8.00, 100, 'ด้าม', 5, 6),
('8850777888999', 'P007', 'พาราเซตามอล 10 เม็ด', 8.00, 12.00, 60, 'แผง', 6, 7),
('8850333222111', 'P008', 'นมจืด ดัชมิลล์ 180ml', 8.00, 12.00, 40, 'กล่อง', 1, 8),
('8850666777888', 'P009', 'ทิชชู่ ศรีไทย 6ม้วน', 25.00, 39.00, 35, 'แพ็ค', 4, 9),
('8850999888777', 'P010', 'ข้าวเหนียวหมูปิ้ง', 10.00, 15.00, 20, 'ไม้', 7, 10);

-- =============================================
-- ตาราง sales_index (รายการขาย/ใบเสร็จ)
-- =============================================
DROP TABLE IF EXISTS `sales_index`;
CREATE TABLE `sales_index` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_code` varchar(20) NOT NULL COMMENT 'เลขที่ใบเสร็จ เช่น POS690001',
  `sale_date` date NOT NULL,
  `sale_time` time NOT NULL,
  `sale_subtotal` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมก่อนส่วนลด',
  `sale_discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ส่วนลด',
  `sale_total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมสุทธิ',
  `sale_received` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'เงินที่รับ',
  `sale_change` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'เงินทอน',
  `sale_payment_method` enum('cash','transfer','credit') NOT NULL DEFAULT 'cash' COMMENT 'วิธีชำระเงิน',
  `sale_note` text DEFAULT NULL,
  `sale_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=สำเร็จ, 0=ยกเลิก',
  `admin_id` int(11) NOT NULL COMMENT 'พนักงานที่ทำรายการ',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sale_id`),
  UNIQUE KEY `uk_sale_code` (`sale_code`),
  INDEX `idx_sale_date` (`sale_date`),
  INDEX `idx_admin` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- ตาราง sales_detail (รายละเอียดการขาย)
-- =============================================
DROP TABLE IF EXISTS `sales_detail`;
CREATE TABLE `sales_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'เก็บชื่อ ณ ตอนขาย',
  `product_price` decimal(10,2) NOT NULL COMMENT 'ราคา ณ ตอนขาย',
  `product_cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ต้นทุน ณ ตอนขาย',
  `qty` int(11) NOT NULL DEFAULT 1,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ส่วนลดต่อรายการ',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมรายการ',
  PRIMARY KEY (`detail_id`),
  INDEX `idx_sale` (`sale_id`),
  INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- ตาราง stock_history (ประวัติรับ-จ่ายสต๊อก)
-- =============================================
DROP TABLE IF EXISTS `stock_history`;
CREATE TABLE `stock_history` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `stock_type` enum('in','out','adjust') NOT NULL COMMENT 'in=รับเข้า, out=ขายออก, adjust=ปรับปรุง',
  `stock_qty` int(11) NOT NULL COMMENT 'จำนวน (บวก=เพิ่ม, ลบ=ลด)',
  `stock_before` int(11) NOT NULL DEFAULT 0 COMMENT 'จำนวนก่อนเปลี่ยน',
  `stock_after` int(11) NOT NULL DEFAULT 0 COMMENT 'จำนวนหลังเปลี่ยน',
  `stock_note` varchar(255) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL COMMENT 'อ้างอิงใบเสร็จ (ถ้าเป็นการขาย)',
  `admin_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`),
  INDEX `idx_product` (`product_id`),
  INDEX `idx_type` (`stock_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- ตาราง information_index (ข้อมูลร้าน)
-- =============================================
DROP TABLE IF EXISTS `information_index`;
CREATE TABLE `information_index` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `info_name` varchar(255) NOT NULL DEFAULT 'ร้านขายของเบ็ดเตล็ด',
  `info_address` text DEFAULT NULL,
  `info_phone` varchar(50) DEFAULT NULL,
  `info_email` varchar(255) DEFAULT NULL,
  `info_line` varchar(255) DEFAULT NULL,
  `info_tax_id` varchar(20) DEFAULT NULL COMMENT 'เลขประจำตัวผู้เสียภาษี',
  `info_receipt_footer` text DEFAULT NULL COMMENT 'ข้อความท้ายใบเสร็จ',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `information_index` VALUES
(1, 'ร้านขายของเบ็ดเตล็ด', '123 ถ.ตัวอย่าง ต.ตัวอย่าง อ.เมือง จ.กรุงเทพฯ 10100', '02-123-4567', 'shop@example.com', '@shopline', '1234567890123', 'ขอบคุณที่ใช้บริการ');

-- =============================================
-- ตาราง daily_summary (สรุปยอดขายรายวัน)
-- =============================================
DROP TABLE IF EXISTS `daily_summary`;
CREATE TABLE `daily_summary` (
  `summary_id` int(11) NOT NULL AUTO_INCREMENT,
  `summary_date` date NOT NULL,
  `total_sales` int(11) NOT NULL DEFAULT 0 COMMENT 'จำนวนบิล',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดขายรวม',
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'ต้นทุนรวม',
  `total_profit` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'กำไรรวม',
  `total_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transfer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`summary_id`),
  UNIQUE KEY `uk_date` (`summary_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
