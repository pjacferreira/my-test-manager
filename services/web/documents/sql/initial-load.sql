-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net

-- -----------------------------------------------------------------------------
-- Test Center - Compliance Testing Application (Web Services)
-- Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
--
-- You should have received a copy of the GNU Affero General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
-- -----------------------------------------------------------------------------

--
-- Database: `testcenter`
--

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `id_creator`, `id_modifier`, `name`, `first_name`, `last_name`, `password`, `s_description`, `l_description`, `dt_creation`, `dt_modified`, `suspended`) VALUES
(1, NULL, NULL, 'admin', NULL, NULL, '21232f297a57a5a743894a0e4a801fc3', NULL, NULL, '2014-01-01 00:00:00', NULL, 0);

--
-- Dumping data for table `t_i18n`
--

INSERT INTO `t_i18n` (`id`, `language`, `language3`, `country`, `country3`, `countryN3`, `s_description`) VALUES
(1, 'en', 'eng', NULL, NULL, NULL, 'English');

--
-- Dumping data for table `t_states`
--

INSERT INTO `t_states` (`id`, `id_i18n`, `type`, `code`, `s_description`, `l_description`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`) VALUES
(1, 1, 1, 0, 'Created', NULL, 1, '2014-01-01 00:00:00', NULL, NULL),
(2, 1, 1, 100, 'Under Development', NULL, 1, '2014-01-01 00:00:00', NULL, NULL),
(3, 1, 1, 900, 'Ready', NULL, 1, '2014-01-01 00:00:00', NULL, NULL),
(4, 1, 2, 0, 'Not Started', NULL, 1, '2014-01-01 00:00:00', NULL, NULL),
(5, 1, 2, 100, 'Run in Progress ', NULL, 1, '2014-01-01 00:00:00', NULL, NULL),
(6, 1, 2, 900, 'Completed', NULL, 1, '2014-01-01 00:00:00', NULL, NULL);
