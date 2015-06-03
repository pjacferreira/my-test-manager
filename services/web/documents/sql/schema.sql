-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 14, 2015 at 11:03 AM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `t_users`
--

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
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_USER_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_USER_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_containers`
--

CREATE TABLE IF NOT EXISTS `t_containers` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `id_root` int(11) DEFAULT NULL COMMENT 'Root Container ID',
  `type` char(1) NOT NULL COMMENT 'Type of Contained Object',
  `name` varchar(40) NOT NULL COMMENT 'Display Name of Entry',
  `id_parent` int(11) DEFAULT NULL COMMENT 'Parent Container ID',
  `id_link` int(11) DEFAULT NULL COMMENT 'If Not Container then this is the ID of Linked Object',
  `type_owner` char(1) DEFAULT NULL COMMENT 'Type of the Owning Object',
  `id_owner` int(11) DEFAULT NULL COMMENT 'ID of Object that Owns this Container or Entry',
  `singlelevel` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow Child Containers?',
  `id_creator` int(11) NOT NULL COMMENT 'ID of User that Created the Entry',
  `dt_creation` datetime NOT NULL COMMENT 'TimeStamp (UTC) of Creation',
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Organization',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Organization',
  PRIMARY KEY (`id`),
  KEY `FK_PARENT_CONTAINER` (`id_parent`),
  KEY `FK_CONTAINER_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_CONTAINER_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Containers' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_organizations`
--

CREATE TABLE IF NOT EXISTS `t_organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `id_container` int(11) NOT NULL,
  `id_creator` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Organization',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Organization',
  PRIMARY KEY (`id`),
  KEY `FK_ORG_TO_CONTAINER` (`id_container`),
  KEY `FK_ORG_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_ORG_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Organizations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_user_orgs`
--

CREATE TABLE IF NOT EXISTS `t_user_orgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_organization` int(11) NOT NULL,
  `permissions` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USER_ORGANIZATION` (`id_user`,`id_organization`),
  KEY `FK_UO_USER_TO_PROJECT` (`id_user`),
  KEY `FK_UO_ORG_TO_ORG` (`id_organization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link User<-->Organization' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_projects`
--

CREATE TABLE IF NOT EXISTS `t_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_organization` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `description` longtext,
  `id_container` int(11) NOT NULL,
  `id_creator` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Project',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Project',
  PRIMARY KEY (`id`),
  KEY `FK_PROJECT_TO_CONTAINER` (`id_container`),
  KEY `FK_PROJECT_TO_ORG` (`id_organization`),
  KEY `FK_PROJECT_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_PROJECT_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Projects' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_projects_settings`
--

CREATE TABLE IF NOT EXISTS `t_projects_settings` (
  `id_project` int(11) NOT NULL,
  `id_run_pass` int(11) NOT NULL COMMENT 'Run (Default) Pass Code',
  `id_run_incomplete` int(11) NOT NULL COMMENT 'Run (Default) Incomplete Code',
  `id_run_fail` int(11) NOT NULL COMMENT 'Run (Default) Fail Code',
  `id_step_pass` int(11) NOT NULL COMMENT 'Play Step (Default) Pass Code',
  `id_step_fail` int(11) NOT NULL COMMENT 'Play Step (Default) Fail Code',
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Settings',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Settings',
  KEY `FK_SETTINGS_TO_PROJECT` (`id_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Projects Settings and Defaults';

-- --------------------------------------------------------

--
-- Table structure for table `t_user_projects`
--

CREATE TABLE IF NOT EXISTS `t_user_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `permissions` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_USER_PROJECT` (`id_user`,`id_project`),
  KEY `FK_UP_USER_TO_PROJECT` (`id_user`),
  KEY `FK_UP_PROJECT_TO_PROJECT` (`id_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link User<-->Project' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_tests`
--

CREATE TABLE IF NOT EXISTS `t_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `id_container` int(11) DEFAULT NULL,
  `state` int(11) NOT NULL COMMENT 'Current Development State (CREATED, DEVELOPMENT, READY)',
  `renumber` boolean NOT NULL DEFAULT 0,
  `id_creator` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Test',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Test',
  `id_owner` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_TEST_TO_PROJECT` (`id_project`),
  KEY `FK_TEST_TO_CONTAINER` (`id_container`),
  KEY `FK_TEST_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_TEST_TO_USER_MODIFIER` (`id_modifier`),
  KEY `FK_TEST_TO_USER_OWNER` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tests' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_test_steps`
--

CREATE TABLE IF NOT EXISTS `t_test_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_test` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `description` longtext,
  `id_creator` int(11) NOT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Test Step',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Test Step',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IU_TEST_SEQUENCE` (`id_test`,`sequence`),
  KEY `FK_STEP_TO_TEST` (`id_test`),
  KEY `FK_STEP_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_STEP_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Test Steps' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_sets`
--

CREATE TABLE IF NOT EXISTS `t_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `description` longtext,
  `id_container` int(11) DEFAULT NULL,
  `state` int(11) NOT NULL COMMENT 'Current Development State (CREATED, DEVELOPMENT, READY)',
  `renumber` boolean NOT NULL DEFAULT 0,
  `id_creator` int(11) DEFAULT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Modify the Set',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp (UTC) of the Last Modification of the Set',
  `id_owner` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_SET_TO_PROJECT` (`id_project`),
  KEY `FK_SET_TO_CONTAINER` (`id_container`),
  KEY `FK_SET_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_SET_TO_USER_MODIFIER` (`id_modifier`),
  KEY `FK_SET_TO_USER_OWNER` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Test Sets' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_set_tests`
--

CREATE TABLE IF NOT EXISTS `t_set_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_set` int(11) DEFAULT NULL,
  `id_test` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ST_TO_SET` (`id_set`),
  KEY `FK_ST_TO_TEST` (`id_test`),
  UNIQUE KEY `IU_SET_TEST` (`id_set`, `id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link Test Set<-->Test' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_runs`
--

CREATE TABLE IF NOT EXISTS `t_runs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Run Unique ID',
  `id_project` int(11) DEFAULT NULL COMMENT 'Project the Run Belongs To',
  `id_set` int(11) NOT NULL COMMENT 'Test Set the Run is Based On',
  `name` varchar(60) NOT NULL COMMENT 'Unique Run Name',
  `description` longtext DEFAULT NULL COMMENT 'Run Description/Reason',
  `id_container` int(11) DEFAULT NULL,
  `id_current_ple` int(11) DEFAULT NULL COMMENT 'Current/Last Run Playlist Entry',
  `state` int(11) NOT NULL COMMENT 'Current Development State (CREATED, OPEN, CLOSED)',
  `run_code` int(11) DEFAULT NULL COMMENT 'Result Code for Run',
  `comment` longtext DEFAULT NULL COMMENT 'Comment on Run Result',
  `id_creator` int(11) NOT NULL COMMENT 'User that created the Run',
  `dt_creation` datetime NOT NULL COMMENT 'Timestamp of Run Creation',
  `id_modifier` int(11) DEFAULT NULL COMMENT 'User that last Modified the Run',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp of Last Modification',
  `id_owner` int(11) NOT NULL COMMENT 'User that Currently Owns the Run',
  PRIMARY KEY (`id`),
  KEY `FK_RUN_TO_PROJECT` (`id_project`),
  KEY `FK_RUN_TO_SET` (`id_set`),
  KEY `FK_RUN_TO_PLAYLIST` (`id_current_ple`),
  KEY `FK_RUN_TO_CONTAINER` (`id_container`),
  KEY `FK_RUN_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_RUN_TO_USER_MODIFIER` (`id_modifier`),
  KEY `FK_RUN_TO_USER_OWNER` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Runs' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_run_playlists`
--

CREATE TABLE IF NOT EXISTS `t_playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Play List Entry Unique ID',
  `id_run` int(11) DEFAULT NULL COMMENT 'Play List''s Run',
  `sequence` int(11) NOT NULL COMMENT 'Play List Entry Sequence Number',
  `id_test` int(11) DEFAULT NULL COMMENT 'Play List Entry Direct Link to Test',
  `id_step` int(11) DEFAULT NULL COMMENT 'Play List Entry Direct Link to Test Step',
  `run_code` int(11) DEFAULT NULL COMMENT 'Result Code for Run',
  `comment` longtext DEFAULT NULL COMMENT 'Entry Comment on Result of Run',
  `id_modifier` int(11) DEFAULT NULL COMMENT 'Last User to Run the Entry',
  `dt_modified` datetime DEFAULT NULL COMMENT 'Timestamp of Last Run',
  PRIMARY KEY (`id`),
  KEY `FK_PLE_TO_RUN` (`id_run`),
  KEY `FK_PLE_TO_TEST` (`id_test`),
  KEY `FK_PLE_TO_TEST_STEP` (`id_step`),
  KEY `FK_PLE_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Run Entries' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_i18n`
--
-- References
-- http://en.wikipedia.org/wiki/ISO_639-1
-- http://en.wikipedia.org/wiki/ISO_3166

CREATE TABLE IF NOT EXISTS `t_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` char(2) NOT NULL COMMENT 'ISO 639-1 Two Letter Country Code',
  `language3` char(3) NOT NULL COMMENT 'ISO 639-2 Three Letter Country Code',
  `country` char(2) DEFAULT NULL COMMENT 'ISO 3166 Alpha 2 Country Code',
  `country3` char(3) DEFAULT NULL COMMENT 'ISO 3166 Alpha 3 Country Code',
  `countryN3` int(3) DEFAULT NULL COMMENT 'ISO 3166 Numeric 3 Country Code',
  `s_description` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IU_I18N` (`id`,`language`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available System I18N Codes' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_states`
--

CREATE TABLE IF NOT EXISTS `t_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_i18n` int(11) NOT NULL COMMENT 'I18N Code for Description',
  `group` int(11) NOT NULL COMMENT 'State Group (1 - Run Codes, 2 - Step Codes)',
  `code` int(11) NOT NULL COMMENT 'State Code',
  `s_description` varchar(80) NOT NULL,
  `l_description` longtext,
  `id_creator` int(11) DEFAULT NULL,
  `dt_creation` datetime NOT NULL,
  `id_modifier` int(11) DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IU_STATE_I18N` (`id_i18n`,`group`,`code`),
  KEY `FK_STATE_TO_I18N` (`id_i18n`),
  KEY `FK_STATE_TO_USER_CREATOR` (`id_creator`),
  KEY `FK_STATE_TO_USER_MODIFIER` (`id_modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='States' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_documents`
--

CREATE TABLE IF NOT EXISTS `t_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_owner` int(11) NOT NULL,
  `ownertype` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `path` varchar(255) NOT NULL,
  `apptype` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Documents' AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_users`
--
ALTER TABLE `t_users`
  ADD CONSTRAINT `FK_USER_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_USER_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_containers`
--
ALTER TABLE `t_containers`
  ADD CONSTRAINT `FK_CONTAINER_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_CONTAINER_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_PARENT_CONTAINER` FOREIGN KEY (`id_parent`) REFERENCES `t_containers` (`id`);

--
-- Constraints for table `t_organizations`
--
ALTER TABLE `t_organizations`
  ADD CONSTRAINT `FK_ORG_TO_CONTAINER` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_ORG_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_ORG_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_user_orgs`
--
ALTER TABLE `t_user_orgs`
  ADD CONSTRAINT `FK_UO_USER_TO_PROJECT` FOREIGN KEY (`id_user`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_UO_ORG_TO_ORG` FOREIGN KEY (`id_organization`) REFERENCES `t_organizations` (`id`);

--
-- Constraints for table `t_projects`
--
ALTER TABLE `t_projects`
  ADD CONSTRAINT `FK_PROJECT_TO_ORG` FOREIGN KEY (`id_organization`) REFERENCES `t_organizations` (`id`),
  ADD CONSTRAINT `FK_PROJECT_TO_CONTAINER` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_PROJECT_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_PROJECT_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_projects_settings`
--
ALTER TABLE `t_projects_settings`
  ADD CONSTRAINT `FK_SETTINGS_TO_PROJECT` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`);

--
-- Constraints for table `t_user_projects`
--
ALTER TABLE `t_user_projects`
  ADD CONSTRAINT `FK_UP_USER_TO_PROJECT` FOREIGN KEY (`id_user`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_UP_PROJECT_TO_PROJECT` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`);


--
-- Constraints for table `t_tests`
--
ALTER TABLE `t_tests`
  ADD CONSTRAINT `FK_TEST_TO_PROJECT` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_TEST_TO_CONTAINER` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_TEST_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_TEST_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_TEST_TO_USER_OWNER` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`);
  
--
-- Constraints for table `t_test_steps`
--
ALTER TABLE `t_test_steps`
  ADD CONSTRAINT `FK_STEP_TO_TEST` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`),
  ADD CONSTRAINT `FK_STEP_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_STEP_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_sets`
--
ALTER TABLE `t_sets`
  ADD CONSTRAINT `FK_SET_TO_PROJECT` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_SET_TO_CONTAINER` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_SET_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_SET_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_SET_TO_USER_OWNER` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_set_tests`
--
ALTER TABLE `t_set_tests`
  ADD CONSTRAINT `FK_ST_TO_TEST` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`),
  ADD CONSTRAINT `FK_ST_TO_SET` FOREIGN KEY (`id_set`) REFERENCES `t_sets` (`id`);

--
-- Constraints for table `t_runs`
--
ALTER TABLE `t_runs`
  ADD CONSTRAINT `FK_RUN_TO_PROJECT` FOREIGN KEY (`id_project`) REFERENCES `t_projects` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_SET` FOREIGN KEY (`id_set`) REFERENCES `t_sets` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_CONTAINER` FOREIGN KEY (`id_container`) REFERENCES `t_containers` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_PLAYLIST` FOREIGN KEY (`id_current_ple`) REFERENCES `t_playlists` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_RUN_TO_USER_OWNER` FOREIGN KEY (`id_owner`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_run_playlists`
--
ALTER TABLE `t_playlists`
  ADD CONSTRAINT `FK_PLE_TO_RUN` FOREIGN KEY (`id_run`) REFERENCES `t_runs` (`id`),
  ADD CONSTRAINT `FK_PLE_TO_TEST` FOREIGN KEY (`id_test`) REFERENCES `t_tests` (`id`),
  ADD CONSTRAINT `FK_PLE_TO_TEST_STEP` FOREIGN KEY (`id_step`) REFERENCES `t_test_steps` (`id`),
  ADD CONSTRAINT `FK_PLE_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

--
-- Constraints for table `t_states`
--
ALTER TABLE `t_states`
  ADD CONSTRAINT `FK_STATE_TO_I18N` FOREIGN KEY (`id_i18n`) REFERENCES `t_i18n` (`id`),
  ADD CONSTRAINT `FK_STATE_TO_USER_CREATOR` FOREIGN KEY (`id_creator`) REFERENCES `t_users` (`id`),
  ADD CONSTRAINT `FK_STATE_TO_USER_MODIFIER` FOREIGN KEY (`id_modifier`) REFERENCES `t_users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;