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
-- Dumping data for table `t_projects_settings`
--

INSERT INTO `t_projects_settings` (`id_project`, `id_test_dc_state`, `id_test_dud_state`, `id_test_dr_state`, `id_set_dc_state`, `id_set_dud_state`, `id_set_dr_state`, `id_run_dc_state`, `id_run_dud_state`, `id_run_dr_state`, `id_run_drns_state`, `id_run_drip_state`, `id_run_drc_state`) VALUES
(1, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6),
(2, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6),
(3, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6),
(4, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6),
(5, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6),
(6, 1, 2, 3, 1, 2, 3, 1, 2, 3, 4, 5, 6);

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
(10, 4, 'F', 'Tests',    4, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(11, 4, 'F', 'Group 1', 10, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(12, 4, 'F', 'Group 2', 10, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(13, 4, 'F', 'Sets',     4, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(14, 4, 'F', 'Runs',     4, NULL, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(15, NULL, 'F', 'Test 1-1', NULL, NULL, 'T', 1, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(16, 4, 'T', 'Test 1-1', 11, 1, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(17, NULL, 'F', 'Test 1-2', NULL, NULL, 'T', 2, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(18, 4, 'T', 'Test 1-2', 11, 2, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(19, NULL, 'F', 'Test 1-3', NULL, NULL, 'T', 3, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(20, 4, 'T', 'Test 1-3', 11, 3, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(21, NULL, 'F', 'Test 2-1', NULL, NULL, 'T', 4, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(22, 4, 'T', 'Test 2-1', 12, 4, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(23, NULL, 'F', 'Test 2-2', NULL, NULL, 'T', 4, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(24, 4, 'T', 'Test 2-2', 12, 5, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(25, NULL, 'F', 'Test 2-2', NULL, NULL, 'T', 4, 1, 1, '2015-01-01 00:00:00', NULL, NULL),
(26, 4, 'T', 'Test 2-3', 12, 6, 'P', 1, 0, 1, '2015-01-01 00:00:00', NULL, NULL),
(27, NULL, 'F', 'Set 1', NULL, NULL, 'S', 6, 1, 1, '2015-05-11 18:09:21', NULL, NULL),
(28, 4, 'S', 'Set 1', 13, 1, 'P', 1, 0, 1, '2015-05-11 18:09:22', NULL, NULL),
(29, NULL, 'F', 'Set 2', NULL, NULL, 'S', 6, 1, 1, '2015-05-11 18:09:21', NULL, NULL),
(30, 4, 'S', 'Set 2', 13, 2, 'P', 2, 0, 1, '2015-05-11 18:09:22', NULL, NULL),
(31, NULL, 'F', 'Set 3', NULL, NULL, 'S', 6, 1, 1, '2015-05-11 18:09:21', NULL, NULL),
(32, 4, 'S', 'Set 3', 13, 3, 'P', 3, 0, 1, '2015-05-11 18:09:22', NULL, NULL);

--
-- Test Entries  for `t_tests`
--

INSERT INTO `t_tests` (`id`, `id_project`, `name`, `description`, `id_container`, `state`, `renumber`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`, `id_owner`) VALUES
(1, 1, 'Test 1-1', 'Group 1 - Test 1', 15, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(2, 1, 'Test 1-2', 'Group 1 - Test 2', 17, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(3, 1, 'Test 1-3', 'Group 1 - Test 3', 19, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(4, 1, 'Test 2-1', 'Group 2 - Test 1', 21, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(5, 1, 'Test 2-2', 'Group 2 - Test 2', 23, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(6, 1, 'Test 2-3', 'Group 2 - Test 3', 25, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL);

--
-- Test Entries  for `t_test_steps`
--

INSERT INTO `t_test_steps` (`id`, `id_test`, `sequence`, `title`, `description`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`) VALUES
(1, 1, 100, 'Step 1', 'Test 1-1 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(2, 1, 200, 'Step 2', 'Test 1-1 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(3, 1, 300, 'Step 3', 'Test 1-1 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL),
(4, 2, 100, 'Step 1', 'Test 1-2 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(5, 2, 200, 'Step 2', 'Test 1-2 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(6, 2, 300, 'Step 3', 'Test 1-2 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL),
(7, 3, 100, 'Step 1', 'Test 1-3 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(8, 3, 200, 'Step 2', 'Test 1-3 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(9, 3, 300, 'Step 3', 'Test 1-3 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL),
(10, 4, 100, 'Step 1', 'Test 2-1 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(11, 4, 200, 'Step 2', 'Test 2-2 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(12, 4, 300, 'Step 3', 'Test 2-3 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL),
(13, 5, 100, 'Step 1', 'Test 2-1 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(14, 5, 200, 'Step 2', 'Test 2-2 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(15, 5, 300, 'Step 3', 'Test 2-3 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL),
(16, 6, 100, 'Step 1', 'Test 2-1 : Step 1', 1, '2015-01-01 00:00:00', NULL, NULL),
(17, 6, 200, 'Step 2', 'Test 2-2 : Step 2', 1, '2015-01-01 00:00:00', NULL, NULL),
(18, 6, 300, 'Step 3', 'Test 2-3 : Step 3', 1, '2015-01-01 00:00:00', NULL, NULL);

--
-- Test Entries  for `t_sets`
--

INSERT INTO `t_sets` (`id`, `id_project`, `name`, `description`, `id_container`, `state`, `renumber`, `id_creator`, `dt_creation`, `id_modifier`, `dt_modified`, `id_owner`) VALUES
(1, 1, 'Set 1', 'Set 1', 27, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(2, 1, 'Set 2', 'Set 2', 29, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL),
(3, 1, 'Set 3', 'Set 3', 31, 0, 0, 1, '2015-01-01 00:00:00', NULL, NULL, NULL);

--
-- Test Entries  for `t_set_tests`
--

INSERT INTO `t_set_tests` (`id`, `id_set`, `id_test`, `sequence`) VALUES
(1, 1, 1, 100),
(2, 1, 4, 200),
(3, 2, 2, 100),
(4, 2, 5, 200),
(5, 3, 3, 100),
(6, 3, 6, 200);
