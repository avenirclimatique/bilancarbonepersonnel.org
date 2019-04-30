-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mardi 29 Janvier 2008 à 21:52
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.0
-- 
-- Base de données: `bilan_carbone_personnel`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `t_element_unite`
-- 

CREATE TABLE `t_element_unite` (
  `element_unite_id` int(11) NOT NULL auto_increment,
  `element_unite_unite_id` int(11) default NULL,
  `element_unite_unite_fond_id` int(11) default NULL,
  `element_unite_position` enum('numerateur','denominateur') default NULL,
  PRIMARY KEY  (`element_unite_id`),
  KEY `element_unite_unite_id` (`element_unite_unite_id`),
  KEY `element_unite_unite_fond_id` (`element_unite_unite_fond_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

-- 
-- Contenu de la table `t_element_unite`
-- 

INSERT INTO `t_element_unite` (`element_unite_id`, `element_unite_unite_id`, `element_unite_unite_fond_id`, `element_unite_position`) VALUES 
(1, 1, 1, 'denominateur'),
(2, 2, 2, 'numerateur'),
(3, 2, 3, 'denominateur'),
(4, 3, 4, 'denominateur'),
(5, 1, 2, 'numerateur'),
(6, 3, 2, 'numerateur'),
(7, 4, 2, 'numerateur'),
(8, 4, 15, 'denominateur'),
(9, 5, 2, 'numerateur'),
(10, 5, 5, 'denominateur'),
(11, 6, 2, 'numerateur'),
(12, 6, 6, 'denominateur'),
(13, 7, 2, 'numerateur'),
(14, 7, 7, 'denominateur'),
(15, 7, 8, 'denominateur'),
(16, 8, 2, 'numerateur'),
(17, 8, 8, 'denominateur'),
(18, 9, 16, 'numerateur'),
(19, 9, 12, 'denominateur'),
(20, 9, 9, 'denominateur'),
(21, 10, 17, 'numerateur'),
(22, 10, 12, 'denominateur'),
(23, 10, 11, 'denominateur'),
(24, 11, 3, 'numerateur'),
(25, 11, 12, 'denominateur'),
(26, 12, 16, 'numerateur'),
(27, 12, 15, 'denominateur'),
(28, 13, 16, 'numerateur'),
(29, 13, 18, 'denominateur'),
(30, 14, 3, 'numerateur'),
(31, 14, 6, 'denominateur'),
(32, 14, 12, 'denominateur'),
(33, 15, 6, 'numerateur'),
(34, 16, 3, 'numerateur'),
(35, 16, 13, 'denominateur'),
(36, 16, 12, 'denominateur'),
(37, 17, 14, 'numerateur'),
(38, 17, 19, 'denominateur');
