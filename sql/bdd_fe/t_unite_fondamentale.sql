-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mardi 29 Janvier 2008 à 21:55
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.0
-- 
-- Base de données: `bilan_carbone_personnel`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `t_unite_fondamentale`
-- 

CREATE TABLE `t_unite_fondamentale` (
  `unite_fond_id` int(11) NOT NULL auto_increment,
  `unite_fond_nom` varchar(100) default NULL,
  `unite_fond_symbole` varchar(100) default NULL,
  `unite_fond_nature_unite_id` int(11) default NULL,
  PRIMARY KEY  (`unite_fond_id`),
  KEY `unite_fond_nature_unite_id` (`unite_fond_nature_unite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

-- 
-- Contenu de la table `t_unite_fondamentale`
-- 

INSERT INTO `t_unite_fondamentale` (`unite_fond_id`, `unite_fond_nom`, `unite_fond_symbole`, `unite_fond_nature_unite_id`) VALUES 
(1, 'tonne', 't', 2),
(2, 'kilogramme équivalent Carbone', 'kg equ. C', 4),
(3, 'kilowattheure', 'kWh', 1),
(4, 'kiloeuro', 'k€', 5),
(5, 'semaine', 'semaine', 8),
(6, 'mètre carré', 'm2', 7),
(7, 'passager', 'passager', 6),
(8, 'kilomètre', 'km', 3),
(9, 'personne', 'personne', 6),
(10, 'véhicule', 'véhicule', 6),
(11, 'animal', 'animal', 6),
(12, 'année', 'an', 8),
(13, 'logement', 'logement', 6),
(14, 'litre', 'l', 9),
(15, 'appareil', 'appareil', 6),
(16, 'kilogramme', 'kg', 2),
(17, 'kilogramme de méthane', 'kg de CH4', 2),
(18, 'vêtement', 'vêtement', 6),
(19, 'cent kilomètres', '100 km', 3);
