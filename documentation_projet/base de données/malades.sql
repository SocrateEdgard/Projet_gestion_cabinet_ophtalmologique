-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 06, 2026 at 07:59 PM
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
-- Table structure for table `malades`
--

DROP TABLE IF EXISTS `malades`;
CREATE TABLE IF NOT EXISTS `malades` (
  `num_fiche` int(11) NOT NULL AUTO_INCREMENT,
  `nom_mal` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postnom_mal` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe_mal` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naiss` date DEFAULT NULL,
  `adresse_mal` text COLLATE utf8mb4_unicode_ci,
  `num_matr_ag` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_cat` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_createur` int(11) DEFAULT NULL,
  `code_empl` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Inscrit',
  PRIMARY KEY (`num_fiche`),
  KEY `code_cat` (`code_cat`),
  KEY `id_createur` (`id_createur`),
  KEY `fk_employeur` (`code_empl`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
