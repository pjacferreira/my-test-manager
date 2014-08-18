<?php

/* Test Center - Compliance Testing Application (Metadata Service)
 * Copyright (C) 2014 Paulo Ferreira <pf at sourcenotes.org>
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

use Phalcon\Mvc\Micro\Collection as MicroCollection;

/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
  $app->response->setStatusCode(404, "Not Found")->sendHeaders();
  echo $app['view']->render('404');
});

/* NOTES:
 * This Application Required yaml extension to installed and enabled in PHP
 * so that we can use yaml_parse_file()
 * 
 * Please See:
 * 
 * http://www.php.net/manual/en/book.yaml.php
 * and
 * https://code.google.com/p/php-yaml/wiki/InstallingWithPecl
 * 
 * Basically:
 * Unix
 *
 * 1. Install LibYAML using your favorite method. For example on a Ubuntu machine sudo apt-get install libyaml-dev will get what you need.
 * 2. sudo pecl install yaml-beta
 * 3. Edit your php.ini settings and add "extension=yaml.so"
 * 4. See if it worked with php --re yaml 
 */