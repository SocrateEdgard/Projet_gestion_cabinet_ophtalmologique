-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 06, 2026 at 07:57 PM
-- Server version: 5.7.40
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_kolwezi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `NumMatrAg` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NomAg` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PostNomAg` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NumCatAg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NomSiègAg` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Eq` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AdresseAg` text COLLATE utf8mb4_unicode_ci,
  `Cf` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NomConj` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PostNomConj` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NbreEnf` int(11) DEFAULT '0',
  `CodeEmpl` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NumMatrAg`),
  KEY `fk_agent_employeur` (`CodeEmpl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `fk_agent_employeur` FOREIGN KEY (`CodeEmpl`) REFERENCES `employeurs` (`code_empl`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
