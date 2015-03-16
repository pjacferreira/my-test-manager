--
-- Dumping data for table `t_users`
--
-- PASSWORD == USER NAME
INSERT INTO `t_users` (`id`, `id_creator`, `id_modifier`, `name`, `first_name`, `last_name`, `password`, `s_description`, `l_description`, `dt_creation`, `dt_modified`, `suspended`) VALUES
(2, 1, NULL, 'test1', NULL, NULL, '5a105e8b9d40e1329780d62ea2265d8a', NULL, NULL, '2015-01-01 00:00:00', NULL, 0),
(3, 1, NULL, 'test2', NULL, NULL, 'ad0234829205b9033196ba818f7a872b', NULL, NULL, '2015-01-01 00:00:00', NULL, 0);


--
-- Dumping data for table `t_containers`
--

INSERT INTO `t_containers` (`id`, `id_root`, `type`, `name`, `id_parent`, `id_link`, `type_owner`, `id_owner`, `singlelevel`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`) VALUES
(1, NULL, 'F', 'org 1',       NULL, NULL, 'O', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(2, NULL, 'F', 'org 2',       NULL, NULL, 'O', 2, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(3, NULL, 'F', 'org 3',       NULL, NULL, 'O', 3, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(4, NULL, 'F', 'project 1-1', NULL, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(5, NULL, 'F', 'project 1-2', NULL, NULL, 'P', 2, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(6, NULL, 'F', 'project 2-1', NULL, NULL, 'P', 3, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(7, NULL, 'F', 'project 2-2', NULL, NULL, 'P', 4, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(8, NULL, 'F', 'project 3-1', NULL, NULL, 'P', 5, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(9, NULL, 'F', 'project 3-2', NULL, NULL, 'P', 6, 0, 1, '2015-01-01 00:00:00', NULL, NULL);

--
-- Dumping data for table `t_organizations`
--

INSERT INTO `t_organizations` (`id`, `id_container`, `id_creator`, `id_modifier`, `name`, `description`, `dt_creation`, `dt_modified`) VALUES
(1, 1, 1, NULL, 'org 1', NULL, '2015-01-01 00:00:00', NULL),
(2, 2, 1, NULL, 'org 2', NULL, '2015-01-01 00:00:00', NULL),
(3, 3, 1, NULL, 'org 3', NULL, '2015-01-01 00:00:00', NULL);

--
-- Dumping data for table `t_projects`
--

INSERT INTO `t_projects` (`id`, `id_organization`, `name`, `description`, `id_container`, `id_creator`, `id_modifier`, `dt_creation`, `dt_modified`) VALUES
(1, 1, 'project 1-1', NULL, 4, 1, NULL, '2015-01-01 00:00:00', NULL),
(2, 1, 'project 1-2', NULL, 5, 1, NULL, '2015-01-01 00:00:00', NULL),
(3, 2, 'project 2-1', NULL, 6, 1, NULL, '2015-01-01 00:00:00', NULL),
(4, 2, 'project 2-2', NULL, 7, 1, NULL, '2015-01-01 00:00:00', NULL),
(5, 3, 'project 3-1', NULL, 8, 1, NULL, '2015-01-01 00:00:00', NULL),
(6, 3, 'project 3-2', NULL, 9, 1, NULL, '2015-01-01 00:00:00', NULL);

--
-- Dumping data for table `t_user_orgs`
--

INSERT INTO `t_user_orgs` (`id`, `id_user`, `id_organization`, `permissions`) VALUES
(1, 1, 1, 'r'),
(2, 1, 2, 'r'),
(3, 1, 3, 'r'),
(4, 2, 1, 'r'),
(5, 2, 2, 'r'),
(6, 3, 2, 'r'),
(7, 3, 3, 'r');

--
-- Dumping data for table `t_user_projects`
--

INSERT INTO `t_user_projects` (`id`, `id_user`, `id_project`, `permissions`) VALUES
(1, 1, 1, 'r'),
(2, 1, 2, 'r'),
(3, 1, 3, 'r'),
(4, 1, 4, 'r'),
(5, 1, 5, 'r'),
(6, 1, 6, 'r'),
(7, 2, 1, 'r'),
(8, 2, 2, 'r'),
(9, 2, 3, 'r'),
(10, 3, 4, 'r'),
(11, 3, 5, 'r'),
(12, 3, 6, 'r');

--
-- Test Entries  for `t_containers`
--

INSERT INTO `t_containers` (`id`, `id_root`, `type`, `name`, `id_parent`, `id_link`, `type_owner`, `id_owner`, `singlelevel`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`) VALUES
(10, 4, 'F', 'folder 1',   4, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(11, 4, 'F', 'folder 2',   4, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(12, 4, 'T', 'test 1',     4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(13, 4, 'T', 'test 2',     4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(14, 4, 'S', 'test set 1', 4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(15, 4, 'S', 'test set 2', 4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(16, 4, 'R', 'run 1',      4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(17, 4, 'R', 'run 2',      4, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(18, 4, 'T', 'test 1-1',  10, NULL, 'P', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL);
