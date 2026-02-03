-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2011 at 09:38 AM
-- Server version: 5.0.77
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `qualitatmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `fiche`
--

CREATE TABLE IF NOT EXISTS `fiche` (
  `id_fiche` int(11) NOT NULL auto_increment,
  `nom` varchar(18) NOT NULL COMMENT 'YYYYMMDD-HH:mm-XXX',
  `date` date NOT NULL,
  `num_fiche_jour` smallint(6) NOT NULL,
  `type_fiche_action` varchar(14) NOT NULL,
  `redacteur` varchar(16) NOT NULL,
  `id_nature` varchar(64) NOT NULL,
  `noms_natures` text NOT NULL,
  `marque_appareil` varchar(64) NOT NULL,
  `site` varchar(64) NOT NULL,
  `type_appareil` varchar(64) NOT NULL,
  `materiel_logiciel` varchar(64) NOT NULL,
  `numero_serie` varchar(32) NOT NULL,
  `faits` text NOT NULL,
  `causes` text NOT NULL,
  `consequences` text NOT NULL,
  `actions_court_terme` text NOT NULL,
  `incidence_qualite` varchar(24) NOT NULL,
  `commentaire_indice_qualite` text NOT NULL,
  `action_sur_produit` varchar(3) NOT NULL,
  `commentaire_action_sur_produit` text NOT NULL,
  `besoin_actions` text NOT NULL,
  `type_action_CPA` varchar(16) NOT NULL,
  `realisateur` varchar(16) NOT NULL,
  `delai` varchar(16) NOT NULL,
  `realisation` varchar(16) NOT NULL,
  `justificatifs` text NOT NULL,
  `efficacite` text NOT NULL,
  `cloture` varchar(16) NOT NULL,
  `visa_responsable` varchar(16) NOT NULL,
  `visa_direction` varchar(16) NOT NULL,
  UNIQUE KEY `nom` (`nom`),
  KEY `id_fiche` (`id_fiche`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `fiche`
--

INSERT INTO `fiche` (`id_fiche`, `nom`, `date`, `num_fiche_jour`, `type_fiche_action`, `redacteur`, `id_nature`, `noms_natures`, `marque_appareil`, `site`, `type_appareil`, `materiel_logiciel`, `numero_serie`, `faits`, `causes`, `consequences`, `actions_court_terme`, `incidence_qualite`, `commentaire_indice_qualite`, `action_sur_produit`, `commentaire_action_sur_produit`, `besoin_actions`, `type_action_CPA`, `realisateur`, `delai`, `realisation`, `justificatifs`, `efficacite`, `cloture`, `visa_responsable`, `visa_direction`) VALUES
(1, '20111115-15:23-1', '2011-11-15', 1, 'Amelioration', 'Guillaume', '11-', 'Logiciel<br>', 'marque1', 'Bureau', 'Type1', 'Materiel 1', 'toto0101a', 'Voici les faits', 'Voici les causes', 'Voici les consÃ©quences', 'Voici les actions menÃ©es Ã  court terme', 'Aucune non conformite', 'Voici le commentaire de non conformitÃ©', 'oui', 'Voici le commentaire d action sur le produit', 'Voici les besoins dactions:', 'Amelioration', 'RÃ©alisateur', '12/04/2012', 'Realisation?', '', 'Voici la vÃ©rification de l''efficacitÃ© des actions menÃ©es.', '15/11/11', 'GB', 'test'),
(2, '20111115-15:49-2', '2011-11-15', 2, 'Amelioration', 'Guillaume', '11-', 'Logiciel<br>', 'marque1', 'Bureau', 'Type1', 'Materiel 1', 'toto0101a', 'Voici les faits', 'Voici les causes', 'Voici les consÃ©quences', 'Voici les actions menÃ©es Ã  court terme', 'Aucune non conformite', 'Voici le commentaire de non conformitÃ©', 'oui', 'Voici le commentaire d''action sur le produit', 'Voici les besoins dactions:', 'Amelioration', 'RÃ©alisateur', '12/04/2012', 'Realisation?', '', 'Voici la vÃ©rification de l''efficacitÃ© des actions menÃ©es.', '15/11/11', 'GB', 'test'),
(3, '20111115-15:50-3', '2011-11-15', 3, 'Amelioration', 'Guillaume', '11-', 'Logiciel<br>', 'marque1', 'Bureau', 'Type1', 'Materiel 1', 'toto0101a', 'Voici les faits''', 'Voici les causes''', 'Voici les consÃ©quences''', 'Voici les actions menÃ©es Ã  court terme''', 'Aucune non conformite', 'Voici le commentaire de non conformitÃ©''', 'oui', 'Voici le commentaire d''action sur le produit', 'Voici les besoins d''actions:', 'Amelioration', 'RÃ©alisateur', '12/04/2012', 'Realisation?', '', 'Voici la vÃ©rification de l''efficacitÃ© des actions menÃ©es.''', '15/11/11', 'GB', 'test'),
(4, '20111116-09:26-1', '2011-11-16', 1, 'Amelioration', 'Guillaume', '11-', 'Logiciel<br>', 'marque1', 'Bureau', 'Type1', 'Materiel 1', 'toto0101a', 'Voici les faits''', 'Voici les causes''', 'Voici les consÃ©quences''', 'Voici les actions menÃ©es Ã  court terme''', 'Aucune non conformite', 'Voici le commentaire de non conformitÃ©''', 'oui', 'Voici le commentaire d''action sur le produit', 'Voici les besoins d''actions:', 'Amelioration', 'RÃ©alisateur', '12/04/2012', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `ID_group` tinyint(4) NOT NULL,
  `Group_name` varchar(16) character set utf8 NOT NULL,
  UNIQUE KEY `Group_name` (`Group_name`),
  KEY `ID_group` (`ID_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `marque`
--

CREATE TABLE IF NOT EXISTS `marque` (
  `ID_marque` smallint(6) NOT NULL auto_increment,
  `Nom_marque` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY  (`ID_marque`),
  UNIQUE KEY `Nom_marque` (`Nom_marque`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `marque`
--

INSERT INTO `marque` (`ID_marque`, `Nom_marque`, `Createur`, `Date`) VALUES
(1, 'marque1', 'Guillaume', '2011-08-12'),
(2, 'Marque2', 'Guillaume', '2011-08-12'),
(3, 'Marque3', 'Guillaume', '2011-08-12'),
(6, '', 'guillaume', '2011-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `materiel`
--

CREATE TABLE IF NOT EXISTS `materiel` (
  `ID_materiel` smallint(6) NOT NULL auto_increment,
  `Nom_materiel` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY  (`ID_materiel`),
  UNIQUE KEY `Nom_marque` (`Nom_materiel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `materiel`
--

INSERT INTO `materiel` (`ID_materiel`, `Nom_materiel`, `Createur`, `Date`) VALUES
(1, 'Materiel 1', 'Guillaume', '2011-08-16'),
(2, 'Materiel 2', 'guillaume', '2011-08-16'),
(3, 'Materiel 3', 'guillaume', '2011-08-16'),
(4, 'Materiel 4', 'guillaume', '2011-08-16'),
(5, '', 'guillaume', '2011-08-16'),
(6, 'Modem', 'guillaume', '2011-08-18'),
(7, 'materiel julien', 'guillaume', '2011-08-24'),
(8, 'toto', 'guillaume', '2011-09-05');

-- --------------------------------------------------------

--
-- Table structure for table `nature`
--

CREATE TABLE IF NOT EXISTS `nature` (
  `ID_nature` smallint(4) NOT NULL auto_increment,
  `Nature` varchar(32) character set utf8 default NULL,
  `Type` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  UNIQUE KEY `Nature` (`Nature`),
  KEY `ID_nature` (`ID_nature`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=49 ;

--
-- Dumping data for table `nature`
--

INSERT INTO `nature` (`ID_nature`, `Nature`, `Type`, `Createur`, `Date`) VALUES
(1, '', 'Vide', 'guillaume', '2011-11-15'),
(2, 'Echantillon', 'Mesurage', 'guillaume', '2011-11-15'),
(3, 'Analyseur', 'Mesurage', 'guillaume', '2011-11-15'),
(4, 'Meteo', 'Mesurage', 'guillaume', '2011-11-15'),
(5, 'Maintenance', 'Mesurage', 'guillaume', '2011-11-15'),
(6, 'Validation_technique', 'Mesurage', 'guillaume', '2011-11-15'),
(7, 'Station_acquisition', 'Mesurage', 'guillaume', '2011-11-15'),
(8, '_Autre_mesurage', 'Mesurage', 'guillaume', '2011-11-15'),
(9, 'Bureautique', 'Infrastructure', 'guillaume', '2011-11-15'),
(10, 'Imprimante', 'Infrastructure', 'guillaume', '2011-11-15'),
(11, 'Logiciel', 'Infrastructure', 'guillaume', '2011-11-15'),
(12, 'Serveur_xair', 'Infrastructure', 'guillaume', '2011-11-15'),
(13, 'Electricite', 'Infrastructure', 'guillaume', '2011-11-15'),
(14, 'Climatiseur', 'Infrastructure', 'guillaume', '2011-11-15'),
(15, 'Vehicule', 'Infrastructure', 'guillaume', '2011-11-15'),
(16, 'Station/Batiment', 'Infrastructure', 'guillaume', '2011-11-15'),
(17, 'Prestataire_externe', 'Infrastructure', 'guillaume', '2011-11-15'),
(18, '_Autre_infrastructure', 'Infrastructure', 'guillaume', '2011-11-15'),
(19, 'Validation_environnementale', 'Etude', 'guillaume', '2011-11-15'),
(20, 'Bibliographie', 'Etude', 'guillaume', '2011-11-15'),
(21, 'Realisation_etude', 'Etude', 'guillaume', '2011-11-15'),
(22, 'Diagnostique/Conseil', 'Etude', 'guillaume', '2011-11-15'),
(23, '_Autre_etude', 'Etude', 'guillaume', '2011-11-15'),
(24, 'Administration', 'Secretariat_comptabilite', 'guillaume', '2011-11-15'),
(25, 'Financier', 'Secretariat_comptabilite', 'guillaume', '2011-11-15'),
(26, '_Autre_secretariat/compta', 'Secretariat_comptabilite', 'guillaume', '2011-11-15'),
(27, 'Audit', 'Qualite', 'guillaume', '2011-11-15'),
(28, 'Procedure', 'Qualite', 'guillaume', '2011-11-15'),
(29, 'Reclamation_client', 'Qualite', 'guillaume', '2011-11-15'),
(30, 'Revue_direction', 'Qualite', 'guillaume', '2011-11-15'),
(31, 'Sauvegarde', 'Qualite', 'guillaume', '2011-11-15'),
(32, '_Autre_qualite', 'Qualite', 'guillaume', '2011-11-15'),
(33, 'Recuperation_donnees', 'Modelisation', 'guillaume', '2011-11-15'),
(34, 'Inventaire', 'Modelisation', 'guillaume', '2011-11-15'),
(35, 'Modelisation', 'Modelisation', 'guillaume', '2011-11-15'),
(36, '_Autre_modelisation', 'Modelisation', 'guillaume', '2011-11-15'),
(37, 'Etalonnage', 'Mesurage', 'guillaume', '2011-11-15'),
(38, 'Diffusion_courante', 'Information_alerte', 'guillaume', '2011-11-15'),
(39, 'Diffusion_periodique', 'Information_alerte', 'guillaume', '2011-11-15'),
(40, 'Alerte/Astreinte', 'Information_alerte', 'guillaume', '2011-11-15'),
(41, 'Communication', 'Information_alerte', 'guillaume', '2011-11-15'),
(42, '_Autre_information/alerte', 'Information_alerte', 'guillaume', '2011-11-15'),
(43, 'Strategie', 'Direction', 'guillaume', '2011-11-15'),
(44, 'Securite', 'Direction', 'guillaume', '2011-11-15'),
(45, 'Plannification', 'Direction', 'guillaume', '2011-11-15'),
(46, '_Autre_direction', 'Direction', 'guillaume', '2011-11-15'),
(47, 'Suggestion_partie_prenante', 'Direction', 'guillaume', '2011-11-15'),
(48, 'Suggestion_personnel', 'Direction', 'guillaume', '2011-11-15');

-- --------------------------------------------------------

--
-- Table structure for table `num_serie`
--

CREATE TABLE IF NOT EXISTS `num_serie` (
  `ID_num_serie` smallint(6) NOT NULL auto_increment,
  `Num_serie` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY  (`ID_num_serie`),
  UNIQUE KEY `Nom_marque` (`Num_serie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `num_serie`
--

INSERT INTO `num_serie` (`ID_num_serie`, `Num_serie`, `Createur`, `Date`) VALUES
(1, 'toto0101a', 'guillaume', '2011-08-16'),
(2, 'toto0101b', 'guillaume', '2011-08-16'),
(3, '', 'guillaume', '2011-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `ID_site` smallint(6) NOT NULL auto_increment,
  `Nom_site` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY  (`ID_site`),
  UNIQUE KEY `Nom_marque` (`Nom_site`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`ID_site`, `Nom_site`, `Createur`, `Date`) VALUES
(1, 'site2', 'Guillaume', '0000-00-00'),
(2, 'site3', 'Guillaume', '2011-08-12'),
(3, 'site1', 'Guillaume', '2011-08-12'),
(4, 'site4', 'guillaume', '2011-08-16'),
(5, '', 'guillaume', '2011-08-16'),
(6, 'Morvan', 'guillaume', '2011-08-18'),
(7, 'Bureau', 'guillaume', '2011-11-15');

-- --------------------------------------------------------

--
-- Table structure for table `type_action`
--

CREATE TABLE IF NOT EXISTS `type_action` (
  `ID_type_action` tinyint(4) NOT NULL auto_increment,
  `type_action` varchar(18) character set utf8 NOT NULL,
  PRIMARY KEY  (`ID_type_action`),
  UNIQUE KEY `type_action` (`type_action`),
  KEY `ID_type_action` (`ID_type_action`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `type_action`
--

INSERT INTO `type_action` (`ID_type_action`, `type_action`) VALUES
(1, 'Amelioration'),
(2, 'Dysfonctionnement');

-- --------------------------------------------------------

--
-- Table structure for table `type_materiel`
--

CREATE TABLE IF NOT EXISTS `type_materiel` (
  `ID_type` smallint(6) NOT NULL auto_increment,
  `Nom_type` varchar(32) character set utf8 NOT NULL,
  `Createur` varchar(16) character set utf8 NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY  (`ID_type`),
  UNIQUE KEY `Nom_marque` (`Nom_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `type_materiel`
--

INSERT INTO `type_materiel` (`ID_type`, `Nom_type`, `Createur`, `Date`) VALUES
(1, 'Type1', 'Guillaume', '2011-08-12'),
(2, 'Type3', 'Guillaume', '2011-08-12'),
(3, 'Type2', 'Guillaume', '2011-08-12'),
(4, 'Type4', 'guillaume', '2011-08-16'),
(5, 'Type5', 'guillaume', '2011-08-16'),
(6, '', 'guillaume', '2011-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID_user` smallint(4) NOT NULL auto_increment,
  `username` varchar(16) character set utf8 NOT NULL,
  `group` varchar(16) character set utf8 NOT NULL,
  PRIMARY KEY  (`ID_user`),
  UNIQUE KEY `username` (`username`,`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--

