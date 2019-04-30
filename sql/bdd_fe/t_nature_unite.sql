-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mardi 29 Janvier 2008 à 21:54
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.0
-- 
-- Base de données: `bilan_carbone_personnel`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `t_nature_unite`
-- 

CREATE TABLE `t_nature_unite` (
  `nature_unite_id` int(11) NOT NULL auto_increment,
  `nature_unite_nom` varchar(100) default NULL,
  PRIMARY KEY  (`nature_unite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Contenu de la table `t_nature_unite`
-- 

INSERT INTO `t_nature_unite` (`nature_unite_id`, `nature_unite_nom`) VALUES 
(1, 'énergie'),
(2, 'masse'),
(3, 'longueur'),
(4, 'pouvoir de réchauffement global'),
(5, 'monétaire'),
(6, 'discret'),
(7, 'surface'),
(8, 'temps'),
(9, 'volume');
