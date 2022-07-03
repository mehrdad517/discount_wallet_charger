-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.33 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for arvan
CREATE DATABASE IF NOT EXISTS `arvan` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `arvan`;

-- Dumping structure for table arvan.discounts
CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_count` int(11) NOT NULL DEFAULT '0',
  `usage_count` int(11) NOT NULL DEFAULT '0',
  `is_percent` tinyint(1) NOT NULL DEFAULT '0',
  `discount_value` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `type` enum('by_finance','by_order') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'by_finance',
  `expiration` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discount_code` (`discount_code`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table arvan.discounts: ~1 rows (approximately)
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
INSERT INTO `discounts` (`id`, `discount_code`, `status`, `title`, `total_count`, `usage_count`, `is_percent`, `discount_value`, `type`, `expiration`, `created_at`, `updated_at`) VALUES
	(1, 'worldcup', 1, 'فینال جام جهانی', 1000, 0, 0, '5000000', 'by_order', NULL, '2022-06-29 19:28:27', '2022-06-29 19:28:26');
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;

-- Dumping structure for table arvan.discount_usage
CREATE TABLE IF NOT EXISTS `discount_usage` (
  `discount_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`discount_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table arvan.discount_usage: ~3 rows (approximately)
/*!40000 ALTER TABLE `discount_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `discount_usage` ENABLE KEYS */;

-- Dumping structure for table arvan.finance
CREATE TABLE IF NOT EXISTS `finance` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `financeable_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` enum('credit','locked_credit') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'credit',
  `financeable_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creditor` double unsigned NOT NULL DEFAULT '0',
  `debtor` double unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `user_id_type` (`user_id`,`type`),
  KEY `optional_id` (`financeable_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table arvan.finance: ~4 rows (approximately)
/*!40000 ALTER TABLE `finance` DISABLE KEYS */;
INSERT INTO `finance` (`id`, `financeable_id`, `user_id`, `type`, `financeable_type`, `creditor`, `debtor`, `created_at`) VALUES
	('4f1d22d6-fa96-11ec-b94f-a0481c8be72c', 1, 1, 'credit', 'discount', 5000000, 0, '2022-07-03 10:36:42'),
	('759a9d17-fa96-11ec-b94f-a0481c8be72c', 1, 2, 'credit', 'discount', 5000000, 0, '2022-07-03 10:37:46'),
	('7bbcee69-fa98-11ec-b94f-a0481c8be72c', 1, 3, 'credit', 'discount', 5000000, 0, '2022-07-03 10:52:15'),
	('d40b78d9-fa9a-11ec-b94f-a0481c8be72c', 1, 3, 'credit', 'discount', 5000000, 0, '2022-07-03 11:09:03');
/*!40000 ALTER TABLE `finance` ENABLE KEYS */;

-- Dumping structure for table arvan.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `credit` double NOT NULL DEFAULT '0',
  `locked_credit` double unsigned NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=48009117 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table arvan.users: ~32 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for procedure arvan.discount_code_finance_charger
DELIMITER //
CREATE PROCEDURE `discount_code_finance_charger`(
	IN `username_arg` VARCHAR(11),
	IN `discount_code_arg` VARCHAR(20)
)
BEGIN

	-- handle transaction error
	DECLARE EXIT HANDLER FOR SQLEXCEPTION 
	BEGIN
	  GET DIAGNOSTICS CONDITION 1
     @err = MESSAGE_TEXT;
	  SELECT @err AS err;
	  ROLLBACK;
	END;

	SET @_discount_status = 0;
	SET @_total_count = 0;
	SET @_usage_count = 0;
	SET @_discount_id = 0;
	SET @_user_id = 0;
	SET @_discount_type = NULL;

 	-- start transction
	START TRANSACTION;
	
	 -- fetch discount record
	SELECT `status`, `total_count`, `usage_count`, id, `type` 
	INTO @_discount_status, @_total_count, @_usage_count, @_discount_id, @_discount_type 
	FROM discounts 
	WHERE discount_code = discount_code_arg OR id = discount_code_arg LIMIT 1;
	
	IF @_discount_type <> 'by_finance'
	THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This discount code is not defined for charging wallets';
	END IF;
	
	IF @_discount_id = 0
	THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The discount code is invalid!';
	END IF;
	
	IF @_total_count <= @_usage_count
	THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The discount capacity is full!';
	END IF;
	
	IF @_discount_status = 0
	THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The discount code has expired!';
	END IF;

	-- fetch or create user
	SELECT id INTO @_user_id FROM users WHERE mobile = username_arg OR id = username_arg;
	
	IF @_user_id = 0
	THEN
		-- or use global function for create user
		INSERT INTO users(mobile) VALUES (username_arg);
		SELECT LAST_INSERT_ID() into @_user_id;
	END IF;

	-- write in discount usage
	INSERT INTO discount_usage (user_id, discount_id) VALUES (@_user_id, @_discount_id);
	
	-- commit transaction
	COMMIT;
	
	-- return user info
	CALL user_info(@_user_id);

END//
DELIMITER ;

-- Dumping structure for procedure arvan.user_info
DELIMITER //
CREATE PROCEDURE `user_info`(
	IN `user_id_arg` BIGINT
)
BEGIN
	SELECT id, mobile, credit - locked_credit AS total_credit FROM users WHERE id = user_id_arg;
END//
DELIMITER ;

-- Dumping structure for event arvan.handle_discount_expiration
DELIMITER //
CREATE EVENT `handle_discount_expiration` ON SCHEDULE EVERY 1 MINUTE STARTS '2022-06-30 01:04:39' ON COMPLETION PRESERVE ENABLE DO BEGIN
	UPDATE discounts SET `status` = 0 WHERE `status` = 1 AND expiration IS NOT NULL AND NOW() > expiration;
END//
DELIMITER ;

-- Dumping structure for trigger arvan.discounts_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `discounts_before_update` BEFORE UPDATE ON `discounts` FOR EACH ROW BEGIN
	IF NEW.usage_count >= NEW.total_count AND NEW.`status` = 1
	THEN
		SET NEW.`status` = 0; -- expire status 
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger arvan.discount_usage_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `discount_usage_after_insert` AFTER INSERT ON `discount_usage` FOR EACH ROW BEGIN
	-- set discount default value
	SET @discount_type = '';
	SET @discount_value = 0;
	
	-- update discount usage counter
	UPDATE discounts SET usage_count = usage_count + 1 WHERE id = NEW.discount_id;
	
	-- fetch discount record and access to type and amount
	SELECT `type`, discount_value INTO @discount_type, @discount_value
	FROM discounts WHERE id = NEW.discount_id;
	
	
	-- check discount type is by finance
	IF @discount_type = 'by_finance' AND @discount_value > 0
	THEN	
	
		-- insert into finance table and increase credit
		INSERT INTO finance (user_id, creditor, financeable_id, financeable_type) 
		VALUES (NEW.user_id, @discount_value, NEW.discount_id, 'discount');
		
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger arvan.finance_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finance_after_insert` AFTER INSERT ON `finance` FOR EACH ROW BEGIN
	-- update user total credit
	UPDATE users 
	SET credit = IFNULL((SELECT SUM(creditor) - SUM(debtor) FROM finance WHERE user_id = NEW.user_id AND `type`='credit'), 0),
	locked_credit = abs(IFNULL((SELECT SUM(creditor) - SUM(debtor) FROM finance WHERE user_id = NEW.user_id AND `type`='locked_credit'), 0))
	WHERE id = NEW.user_id;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger arvan.finance_before_delete
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finance_before_delete` AFTER DELETE ON `finance` FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO= 30002, MESSAGE_TEXT= 'The financial record cannot be deleted';
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger arvan.finance_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finance_before_insert` BEFORE INSERT ON `finance` FOR EACH ROW BEGIN
	SET NEW.id = UUID();
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger arvan.finance_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `finance_before_update` BEFORE UPDATE ON `finance` FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO= 30001, MESSAGE_TEXT= 'The financial record cannot be updated';
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
