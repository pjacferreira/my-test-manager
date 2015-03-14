# TestCenter : Managed Testing #

Open Source Project, licensed under the [AGPL](http://www.gnu.org/licenses/agpl-3.0.html), directed towards simplifying the Management of User [Acceptance Testing](http://en.wikipedia.org/wiki/Acceptance_testing) or [Functional Testing](http://en.wikipedia.org/wiki/Functional_testing).

It uses a service based architecture, currently based on PHP and [Phalcon 1.3.x](http://phalconphp.com/), which allows for easy integration with other front-ends or applications.
In the works is an AJAX client front-end [QOOXDOO 4.x](http://qooxdoo.org/) that will provide full access to the functionality provided by the server.

## Features (Active and Planned) ##

### Web Client ###
  * Template Based AJAX Web Client (in development)

### Server ###
  * JSON Based Web-Services (being re-adapted to a more CRUD like model)
  * User Management (Multi-user system, with the ability to limit individual access at all levels. FULL SECURITY MODEL is still under development)
  * Organization Management (Allows the Test Structure for Multiple Organizations, to be managed by a single server)
  * Project Managements (Allows Multiple Projects to be Created under a Single Organization)
  * Test Scripts (The Basis for allow Testing)
  * Test Sets (A group of tests, to be run or re-run by a single user)
  * Runs (The result state of the run of a single test set)


(Planned)
  * Test Dependency Structure (A way to relate tests, so that the run sequence does not violate any dependencies)
  * Requirement/Functional/Feature Dependency Structure (A way to associate a test with with a certain required feature, function or user requirements, to allow for focused or overall views of the current testing state)

## Requirements ##
  * PHP 5.2+
  * MySQL 5.x
  * Symfony 2.x (Preferred 2.0.13)
  * (Client Only) Qooxdoo 2.1