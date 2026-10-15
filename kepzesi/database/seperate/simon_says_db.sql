-- --------------------------------------------------------
-- Hoszt:                        127.0.0.1
-- Szerver verzió:               8.4.3 - MySQL Community Server - GPL
-- Szerver OS:                   Win64
-- HeidiSQL Verzió:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Adatbázis struktúra mentése a simon_says.
CREATE DATABASE IF NOT EXISTS `simon_says` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `simon_says`;

-- Struktúra mentése tábla simon_says. leaderboard
CREATE TABLE IF NOT EXISTS `leaderboard` (
  `id` int NOT NULL AUTO_INCREMENT,
  `score` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Tábla adatainak mentése simon_says.leaderboard: ~2 rows (hozzávetőleg)
INSERT INTO `leaderboard` (`id`, `score`, `username`) VALUES
	(1, 0, 'Saci'),
	(2, 0, 'vIKI'),
	(3, 0, 'Miki');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
