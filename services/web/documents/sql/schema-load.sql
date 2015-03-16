-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 05, 2014 at 09:18 AM
-- Server version: 5.5.32
-- PHP Version: 5.3.10-1ubuntu3.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `testcenter`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_containers`
--

DROP TABLE IF EXISTS `t_containers`;
CREATE TABLE IF NOT EXISTS `t_containers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `ownertype` int(11) NOT NULL,
  `singlelevel` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C5F9997B1BB9D5A2` (`id_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Containers' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_container_entries`
--

DROP TABLE IF EXISTS `t_container_entries`;
CREATE TABLE IF NOT EXISTS `t_container_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_container` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `id_link` int(11) NOT NULL,
  `linktype` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_53902B9E4797503C` (`id_container`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Container Entries' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_documents`
--

DROP TABLE IF EXISTS `t_documents`;
CREATE TABLE IF NOT EXISTS `t_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_owner` int(11) NOT NULL,
  `ownertype` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `path` varchar(255) NOT NULL,
  `apptype` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Documents' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_organizations`
--

DROP TABLE IF EXISTS `t_organizations`;
CREATE TABLE IF NOT EXISTS `t_organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_docroot` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_676499BE2C8888AD` (`id_docroot`),
  KEY `IDX_676499BE629B4313` (`id_creator`),
  KEY `IDX_676499BEFB643568` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Organizations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_projects`
--

DROP TABLE IF EXISTS `t_projects`;
CREATE TABLE IF NOT EXISTS `t_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_organization` int(11) DEFAULT NULL,
  `id_root` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `description` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8872AFFE9DD4E559` (`id_root`),
  KEY `IDX_8872AFFEE22F160E` (`id_organization`),
  KEY `IDX_8872AFFE629B4313` (`id_creator`),
  KEY `IDX_8872AFFEFB643568` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Projects' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_runs`
--

DROP TABLE IF EXISTS `t_runs`;
CREATE TABLE IF NOT EXISTS `t_runs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Run ID',
  `id_project` int(11) DEFAULT NULL,
  `id_set` int(11) DEFAULT NULL,
  `id_playlist_pos` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL,
  `run_group` varchar(60) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `open` tinyint(1) NOT NULL,
  `state` int(11) NOT NULL,
  `state_code` int(11) NOT NULL,
  `comment` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3A4C126DF12E799E` (`id_project`),
  KEY `IDX_3A4C126D78B0CD86` (`id_set`),
  KEY `IDX_3A4C126DB0CD893E` (`id_playlist_pos`),
  KEY `IDX_3A4C126D629B4313` (`id_creator`),
  KEY `IDX_3A4C126DFB643568` (`id_modifier`),
  KEY `IDX_3A4C126D21E5A74C` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Runs' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_run_playlists`
--

DROP TABLE IF EXISTS `t_run_playlists`;
CREATE TABLE IF NOT EXISTS `t_run_playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_run` int(11) DEFAULT NULL,
  `id_test` int(11) DEFAULT NULL,
  `id_step` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `state_code` int(11) NOT NULL,
  `comment` longtext,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BE9F9952CED24C9A` (`id_run`),
  KEY `IDX_BE9F9952535F620E` (`id_test`),
  KEY `IDX_BE9F9952C899E23E` (`id_step`),
  KEY `IDX_BE9F9952FB643568` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Run Entries' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_sets`
--

DROP TABLE IF EXISTS `t_sets`;
CREATE TABLE IF NOT EXISTS `t_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL,
  `set_group` varchar(60) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `state` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2EFB2CA3F12E799E` (`id_project`),
  KEY `IDX_2EFB2CA3629B4313` (`id_creator`),
  KEY `IDX_2EFB2CA3FB643568` (`id_modifier`),
  KEY `IDX_2EFB2CA321E5A74C` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Test Sets' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_set_tests`
--

DROP TABLE IF EXISTS `t_set_tests`;
CREATE TABLE IF NOT EXISTS `t_set_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_set` int(11) DEFAULT NULL,
  `id_test` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DFB59CE78B0CD86` (`id_set`),
  KEY `IDX_DFB59CE535F620E` (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link Test Set<-->Test' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_statecodes`
--

DROP TABLE IF EXISTS `t_statecodes`;
CREATE TABLE IF NOT EXISTS `t_statecodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_state` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `code` int(11) NOT NULL,
  `s_description` varchar(80) NOT NULL,
  `l_description` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_783E05E04D1693CB` (`id_state`),
  KEY `IDX_783E05E0629B4313` (`id_creator`),
  KEY `IDX_783E05E0FB643568` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='State Codes' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_states`
--

DROP TABLE IF EXISTS `t_states`;
CREATE TABLE IF NOT EXISTS `t_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `s_description` varchar(80) NOT NULL,
  `l_description` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_18A50FB3629B4313` (`id_creator`),
  KEY `IDX_18A50FB3FB643568` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='States' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_tests`
--

DROP TABLE IF EXISTS `t_tests`;
CREATE TABLE IF NOT EXISTS `t_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) DEFAULT NULL,
  `id_docroot` int(11) DEFAULT NULL,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `test_group` varchar(60) DEFAULT NULL,
  `description` longtext,
  `state` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_ACD19A272C8888AD` (`id_docroot`),
  KEY `IDX_ACD19A27F12E799E` (`id_project`),
  KEY `IDX_ACD19A27629B4313` (`id_creator`),
  KEY `IDX_ACD19A27FB643568` (`id_modifier`),
  KEY `IDX_ACD19A2721E5A74C` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tests' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_test_steps`
--

DROP TABLE IF EXISTS `t_test_steps`;
CREATE TABLE IF NOT EXISTS `t_test_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_test` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `IDX_6BA99109535F620E` (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Test Steps' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

DROP TABLE IF EXISTS `t_users`;
CREATE TABLE IF NOT EXISTS `t_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_creator` int(11) DEFAULT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `first_name` varchar(40) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `s_description` varchar(80) DEFAULT NULL,
  `l_description` longtext,
  `dt_creation` datetime NOT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA32C390629B4313` (`id_creator`),
  KEY `IDX_AA32C390FB643568` (`id_modifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `id_creator`, `id_modifier`, `name`, `first_name`, `last_name`, `password`, `s_description`, `l_description`, `dt_creation`, `dt_modified`) VALUES
(1, NULL, NULL, 'admin', NULL, NULL, '21232f297a57a5a743894a0e4a801fc3', NULL, NULL, '2014-08-05 10:22:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_user_orgs`
--

DROP TABLE IF EXISTS `t_user_orgs`;
CREATE TABLE IF NOT EXISTS `t_user_orgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_organization` int(11) NOT NULL,
  `permissions` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USER_ORGANIZATION` (`id_user`,`id_organization`),
  KEY `IDX_72F4CAE6B3CA4B` (`id_user`),
  KEY `IDX_72F4CAEE22F160E` (`id_organization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link User<-->Organization' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_user_projects`
--

DROP TABLE IF EXISTS `t_user_projects`;
CREATE TABLE IF NOT EXISTS `t_user_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `permissions` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USER_PROJECT` (`id_user`,`id_project`),
  KEY `IDX_9906D2656B3CA4B` (`id_user`),
  KEY `IDX_9906D265F12E799E` (`id_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link User<-->Project' AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_containers`
--
ALTER TABLE `t_containers`
  ADD CONSTRAINT `FK_C5F9997B1BB9D5A2` FOREIGN KEY (`id_parent`) REFERENCES `t_containers` (`id`);

--
-- Constraints for table `t_container_entries`
--
ALTER TABLE `t_container_entries`
  ADD CONSTRAINT `FK_53902B9E4797503C` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`);

--
-- Constraints for table `t_organizations`
--
ALTER TABLE `t_organizations`
  ADD CONSTRAINT `FK_676499BE2C8888AD` FOREIGN KEY (`id_docroot`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_676499BE629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_676499BEFB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_projects`
--
ALTER TABLE `t_projects`
  ADD CONSTRAINT `FK_8872AFFE629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_8872AFFE9DD4E559` FOREIGN KEY (`id_root`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_8872AFFEE22F160E` FOREIGN KEY (`id_organization`) REFERENCES `t_organizations` (`id`),
  ADD CONSTRAINT `FK_8872AFFEFB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_runs`
--
ALTER TABLE `t_runs`
  ADD CONSTRAINT `FK_3A4C126D21E5A74C` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_3A4C126D629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_3A4C126D78B0CD86` FOREIGN KEY (`id_set`) REFERENCES `t_sets` (`id`),
  ADD CONSTRAINT `FK_3A4C126DB0CD893E` FOREIGN KEY (`id_playlist_pos`) REFERENCES `t_run_playlists` (`id`),
  ADD CONSTRAINT `FK_3A4C126DF12E799E` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_3A4C126DFB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_run_playlists`
--
ALTER TABLE `t_run_playlists`
  ADD CONSTRAINT `FK_BE9F9952535F620E` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`),
  ADD CONSTRAINT `FK_BE9F9952C899E23E` FOREIGN KEY (`id_step`) REFERENCES `t_test_steps` (`id`),
  ADD CONSTRAINT `FK_BE9F9952CED24C9A` FOREIGN KEY (`id_run`) REFERENCES `t_runs` (`id`),
  ADD CONSTRAINT `FK_BE9F9952FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_sets`
--
ALTER TABLE `t_sets`
  ADD CONSTRAINT `FK_2EFB2CA321E5A74C` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_2EFB2CA3629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_2EFB2CA3F12E799E` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_2EFB2CA3FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_set_tests`
--
ALTER TABLE `t_set_tests`
  ADD CONSTRAINT `FK_DFB59CE535F620E` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`),
  ADD CONSTRAINT `FK_DFB59CE78B0CD86` FOREIGN KEY (`id_set`) REFERENCES `t_sets` (`id`);

--
-- Constraints for table `t_statecodes`
--
ALTER TABLE `t_statecodes`
  ADD CONSTRAINT `FK_783E05E04D1693CB` FOREIGN KEY (`id_state`) REFERENCES `t_states` (`id`),
  ADD CONSTRAINT `FK_783E05E0629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_783E05E0FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_states`
--
ALTER TABLE `t_states`
  ADD CONSTRAINT `FK_18A50FB3629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_18A50FB3FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_tests`
--
ALTER TABLE `t_tests`
  ADD CONSTRAINT `FK_ACD19A2721E5A74C` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_ACD19A272C8888AD` FOREIGN KEY (`id_docroot`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_ACD19A27629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_ACD19A27F12E799E` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_ACD19A27FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_test_steps`
--
ALTER TABLE `t_test_steps`
  ADD CONSTRAINT `FK_6BA99109535F620E` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`);

--
-- Constraints for table `t_users`
--
ALTER TABLE `t_users`
  ADD CONSTRAINT `FK_AA32C390629B4313` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_AA32C390FB643568` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_user_orgs`
--
ALTER TABLE `t_user_orgs`
  ADD CONSTRAINT `FK_72F4CAE6B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_72F4CAEE22F160E` FOREIGN KEY (`id_organization`) REFERENCES `t_organizations` (`id`);

--
-- Constraints for table `t_user_projects`
--
ALTER TABLE `t_user_projects`
  ADD CONSTRAINT `FK_9906D2656B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_9906D265F12E799E` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
