<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012 - 2015 Paulo Ferreira <pf at sourcenotes.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/*
 * USER MODE: Set Services
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use controllers\user\PlayerController as Player;

/*
 * Run Controller
 */
$controller = Player::getInstance();

/*
 * Manage Run
 */
$prefix = '/play/{run:[0-9]+}';

$app->map($prefix . '/open', array($controller, 'runOpen'));
$app->map($prefix . '/close', array($controller, 'runClose'));
$app->map($prefix . '/close/{code:[0-9]+}', array($controller, 'runClose'));

/*
 * Move Current Entry, to the Beginning of the Test
 */
$prefix = '/play/{run:[0-9]+}/test';

$app->map($prefix . '/first', array($controller, 'testFirst'));
$app->map($prefix . '/previous', array($controller, 'testPrevious'));
$app->map($prefix . '/next', array($controller, 'testNext'));
$app->map($prefix . '/last', array($controller, 'testLast'));

/*
 * Position Current Entry (Bounded to the Current Test)
 */
$prefix = '/play/{run:[0-9]+}/test/step';

$app->map($prefix . '/first', array($controller, 'testStepFirst'));
$app->map($prefix . '/previous', array($controller, 'testStepPrevious'));
$app->map($prefix . '/next', array($controller, 'testStepNext'));
$app->map($prefix . '/last', array($controller, 'testStepLast'));

/*
 * Position Current Entry (No Test Boundary)
 */
$prefix = '/play/{run:[0-9]+}/step';

$app->map($prefix . '/first', array($controller, 'stepFirst'));
$app->map($prefix . '/previous', array($controller, 'stepPrevious'));
$app->map($prefix . '/next', array($controller, 'stepNext'));
$app->map($prefix . '/last', array($controller, 'stepLast'));

/*
 * List Steps in the Run
 */
$prefix = '/play/{run:[0-9]+}/steps';

$app->map($prefix . '/list', array($controller, 'listSteps'));
$app->map($prefix . '/count', array($controller, 'countSteps'));

/*
 * Manage Current Step
 */
$prefix = '/play/{run:[0-9]+}/current';

$app->map($prefix, array($controller, 'currentEntry'));
$app->map($prefix . '/test', array($controller, 'currentTest'));
$app->map($prefix . '/step', array($controller, 'currentStep'));
$app->map($prefix . '/pass', array($controller, 'stepPass'));
// $app->map($prefix . '/pass/{code:[0-9]+}', array($controller, 'stepPass'));
$app->map($prefix . '/fail', array($controller, 'stepFail'));
$app->post($prefix . '/comment', array($controller, 'stepComment'));
// $app->map($prefix . '/fail/{code:[0-9]+}', array($controller, 'stepFail'));
