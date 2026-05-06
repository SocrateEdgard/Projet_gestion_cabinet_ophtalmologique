-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 06, 2026 at 08:00 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `code_cat` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_cat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`code_cat`, `nom_cat`) VALUES
('CAT01', 'Agent'),
('CAT02', 'Pensionné'),
('CAT03', 'Tiers'),
('CAT04', 'Acheteur');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

DROP TABLE IF EXISTS `consultations`;
CREATE TABLE IF NOT EXISTS `consultations` (
  `id_cons` int(11) NOT NULL AUTO_INCREMENT,
  `num_fiche` int(11) NOT NULL,
  `date_diag` datetime DEFAULT CURRENT_TIMESTAMP,
  `medecin_id` int(11) NOT NULL,
  `plainte` text,
  `Diagnostic` text,
  `traitement` text,
  `montant` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id_cons`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `employeurs`
--

DROP TABLE IF EXISTS `employeurs`;
CREATE TABLE IF NOT EXISTS `employeurs` (
  `code_empl` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_empl` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse_empl` text COLLATE utf8mb4_unicode_ci,
  `contact_nom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code_empl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `nom_role`) VALUES
(1, 'Médecin'),
(2, 'Réceptionniste');

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
