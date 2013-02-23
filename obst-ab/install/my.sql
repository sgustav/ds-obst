-- DIESE DATEI NICHT ÄNDERN! NACH DER INSTALLATION >KANN< DIESE DATEI ENTFERNT WERDEN...
--
-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 03. Mai 2007 um 21:15
-- Server Version: 5.0.38
-- PHP-Version: 5.2.1
-- 
-- Datenbank: `obst`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_config`
-- 

CREATE TABLE `xdb_config` (
  `cfg_name` varchar(25) collate latin1_general_ci NOT NULL,
  `cfg_value` varchar(25) collate latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_report_comments`
-- 

CREATE TABLE `xdb_report_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `report_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_report_groups`
-- 

CREATE TABLE `xdb_report_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_reports`
-- 

CREATE TABLE `xdb_reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `realtime` bigint(20) unsigned NOT NULL default '0',
  `lastcomment` int(10) unsigned NOT NULL default '0',
  `hash` varchar(32) NOT NULL,
  `world` int(11) NOT NULL,
  `time` bigint(20) unsigned NOT NULL default '0',
  `winner` tinyint(4) NOT NULL default '1',
  `luck` varchar(5) NOT NULL default '0',
  `moral` tinyint(4) unsigned NOT NULL default '0',
  `attacker_nick` varchar(30) NOT NULL,
  `attacker_village` varchar(50) NOT NULL,
  `attacker_coords` varchar(10) NOT NULL,
  `attacker_continent` tinyint(3) NOT NULL default '0',
  `defender_nick` varchar(30) NOT NULL,
  `defender_village` varchar(50) NOT NULL,
  `defender_coords` varchar(10) NOT NULL,
  `defender_continent` tinyint(3) NOT NULL default '0',
  `troops` tinyint(1) unsigned NOT NULL default '0',
  `troops_att_spear` int(11) unsigned NOT NULL default '0',
  `troops_att_sword` int(11) unsigned NOT NULL default '0',
  `troops_att_axe` int(11) unsigned NOT NULL default '0',
  `troops_att_archer` int(11) unsigned NOT NULL default '0',
  `troops_att_spy` int(11) unsigned NOT NULL default '0',
  `troops_att_light` int(11) unsigned NOT NULL default '0',
  `troops_att_marcher` int(11) unsigned NOT NULL default '0',
  `troops_att_heavy` int(11) unsigned NOT NULL default '0',
  `troops_att_ram` int(11) unsigned NOT NULL default '0',
  `troops_att_catapult` int(11) unsigned NOT NULL default '0',
  `troops_att_priest` int(11) unsigned NOT NULL default '0',
  `troops_att_knight` int(11) unsigned NOT NULL default '0',
  `troops_att_snob` int(11) unsigned NOT NULL default '0',
  `troops_attl_spear` int(11) unsigned NOT NULL default '0',
  `troops_attl_sword` int(11) unsigned NOT NULL default '0',
  `troops_attl_axe` int(11) unsigned NOT NULL default '0',
  `troops_attl_archer` int(11) unsigned NOT NULL default '0',
  `troops_attl_spy` int(11) unsigned NOT NULL default '0',
  `troops_attl_light` int(11) unsigned NOT NULL default '0',
  `troops_attl_marcher` int(11) unsigned NOT NULL default '0',
  `troops_attl_heavy` int(11) unsigned NOT NULL default '0',
  `troops_attl_ram` int(11) unsigned NOT NULL default '0',
  `troops_attl_catapult` int(11) unsigned NOT NULL default '0',
  `troops_attl_priest` int(11) unsigned NOT NULL default '0',
  `troops_attl_knight` int(11) unsigned NOT NULL default '0',
  `troops_attl_snob` int(11) unsigned NOT NULL default '0',
  `troops_def_spear` int(11) unsigned NOT NULL default '0',
  `troops_def_sword` int(11) unsigned NOT NULL default '0',
  `troops_def_axe` int(11) unsigned NOT NULL default '0',
  `troops_def_archer` int(11) unsigned NOT NULL default '0',
  `troops_def_spy` int(11) unsigned NOT NULL default '0',
  `troops_def_light` int(11) unsigned NOT NULL default '0',
  `troops_def_marcher` int(11) unsigned NOT NULL default '0',
  `troops_def_heavy` int(11) unsigned NOT NULL default '0',
  `troops_def_ram` int(11) unsigned NOT NULL default '0',
  `troops_def_catapult` int(11) unsigned NOT NULL default '0',
  `troops_def_priest` int(11) unsigned NOT NULL default '0',
  `troops_def_knight` int(11) unsigned NOT NULL default '0',
  `troops_def_snob` int(11) unsigned NOT NULL default '0',
  `troops_defl_spear` int(11) unsigned NOT NULL default '0',
  `troops_defl_sword` int(11) unsigned NOT NULL default '0',
  `troops_defl_axe` int(11) unsigned NOT NULL default '0',
  `troops_defl_archer` int(11) unsigned NOT NULL default '0',
  `troops_defl_spy` int(11) unsigned NOT NULL default '0',
  `troops_defl_light` int(11) unsigned NOT NULL default '0',
  `troops_defl_marcher` int(11) unsigned NOT NULL default '0',
  `troops_defl_heavy` int(11) unsigned NOT NULL default '0',
  `troops_defl_ram` int(11) unsigned NOT NULL default '0',
  `troops_defl_catapult` int(11) unsigned NOT NULL default '0',
  `troops_defl_priest` int(11) unsigned NOT NULL default '0',
  `troops_defl_knight` int(11) unsigned NOT NULL default '0',
  `troops_defl_snob` int(11) unsigned NOT NULL default '0',
  `wall` tinyint(1) unsigned NOT NULL default '0',
  `wall_before` tinyint(4) unsigned NOT NULL default '0',
  `wall_after` tinyint(4) unsigned NOT NULL default '0',
  `catapult` tinyint(1) unsigned NOT NULL default '0',
  `catapult_before` tinyint(4) unsigned NOT NULL default '0',
  `catapult_after` tinyint(4) unsigned NOT NULL default '0',
  `catapult_building` varchar(20) NOT NULL,
  `spied` tinyint(1) unsigned NOT NULL default '0',
  `spied_wood` int(10) unsigned NOT NULL default '0',
  `spied_loam` int(10) unsigned NOT NULL default '0',
  `spied_iron` int(10) unsigned NOT NULL default '0',
  `buildings` tinyint(1) unsigned NOT NULL default '0',
  `buildings_main` int(11) unsigned NOT NULL default '0',
  `buildings_barracks` int(11) unsigned NOT NULL default '0',
  `buildings_stable` int(11) unsigned NOT NULL default '0',
  `buildings_garage` int(11) unsigned NOT NULL default '0',
  `buildings_snob` int(11) unsigned NOT NULL default '0',
  `buildings_smith` int(11) unsigned NOT NULL default '0',
  `buildings_place` int(11) unsigned NOT NULL default '0',
  `buildings_statue` int(11) unsigned NOT NULL default '0',
  `buildings_market` int(11) unsigned NOT NULL default '0',
  `buildings_wood` int(11) unsigned NOT NULL default '0',
  `buildings_stone` int(11) unsigned NOT NULL default '0',
  `buildings_iron` int(11) unsigned NOT NULL default '0',
  `buildings_farm` int(11) unsigned NOT NULL default '0',
  `buildings_storage` int(11) unsigned NOT NULL default '0',
  `buildings_hide` int(11) unsigned NOT NULL default '0',
  `buildings_wall` int(11) unsigned NOT NULL default '0',
  `spied_troops_out` tinyint(1) unsigned NOT NULL default '0',
  `spied_troops_out_spear` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_sword` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_axe` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_archer` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_spy` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_light` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_marcher` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_heavy` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_ram` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_catapult` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_priest` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_knight` int(10) unsigned NOT NULL default '0',
  `spied_troops_out_snob` int(10) unsigned NOT NULL default '0',
  `spied_troops_village` varchar(50) NOT NULL,  
  `troops_out` tinyint(1) unsigned NOT NULL default '0',
  `troops_out_spear` int(10) unsigned NOT NULL default '0',
  `troops_out_sword` int(10) unsigned NOT NULL default '0',
  `troops_out_axe` int(10) unsigned NOT NULL default '0',
  `troops_out_archer` int(11) unsigned NOT NULL default '0',
  `troops_out_spy` int(10) unsigned NOT NULL default '0',
  `troops_out_light` int(10) unsigned NOT NULL default '0',
  `troops_out_marcher` int(11) unsigned NOT NULL default '0',
  `troops_out_heavy` int(10) unsigned NOT NULL default '0',
  `troops_out_ram` int(10) unsigned NOT NULL default '0',
  `troops_out_catapult` int(10) unsigned NOT NULL default '0',
  `troops_out_priest` int(11) unsigned NOT NULL default '0',
  `troops_out_knight` int(11) unsigned NOT NULL default '0',
  `troops_out_snob` int(10) unsigned NOT NULL default '0',
  `booty` tinyint(1) unsigned NOT NULL default '0',
  `booty_wood` int(10) unsigned NOT NULL default '0',
  `booty_loam` int(10) unsigned NOT NULL default '0',
  `booty_iron` int(10) unsigned NOT NULL default '0',
  `booty_all` int(10) unsigned NOT NULL default '0',
  `booty_max` int(10) unsigned NOT NULL default '0',
  `mood` tinyint(1) unsigned NOT NULL default '0',
  `mood_before` tinyint(4) NOT NULL default '0',
  `mood_after` tinyint(4) NOT NULL default '0',
  `dot` varchar(10) NOT NULL default 'grey',
  `no_information` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_reservations`
-- 

CREATE TABLE `xdb_reservations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `koords` varchar(10) collate latin1_general_ci NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `xdb_users`
-- 

CREATE TABLE `xdb_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate latin1_general_ci NOT NULL,
  `pass` varchar(32) collate latin1_general_ci NOT NULL,
  `email` varchar(50) collate latin1_general_ci NOT NULL,
  `admin` tinyint(1) unsigned NOT NULL default '0',
  `user_level` int(11) unsigned NOT NULL default '5',
  `activated` tinyint(1) unsigned NOT NULL default '0',
  `worlds` text collate latin1_general_ci NOT NULL,
  `lastlogin` int(10) unsigned NOT NULL default '0',
  `can_reports_comment` tinyint(1) unsigned NOT NULL,
  `can_reports_comments_delete` tinyint(1) unsigned NOT NULL,
  `can_reports_delete` tinyint(1) unsigned NOT NULL,
  `can_reports_mass_edit` tinyint(1) unsigned NOT NULL,
  `can_reports_mass_edit_regroup` tinyint(1) unsigned NOT NULL,
  `can_reports_mass_edit_setworld` tinyint(1) unsigned NOT NULL,
  `can_reports_mass_edit_delete` tinyint(1) unsigned NOT NULL,
  `can_reports_parse` tinyint(1) unsigned NOT NULL,
  `can_reports_view` tinyint(1) unsigned NOT NULL,
  `can_users` tinyint(1) unsigned NOT NULL,
  `can_users_edit` tinyint(1) unsigned NOT NULL,
  `can_users_rights` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;
