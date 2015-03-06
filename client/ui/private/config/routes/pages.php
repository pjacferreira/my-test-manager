<?php

/**
 * @copyright 2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Create Routes for Page Generator
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new PagesController());

// Base Route Prefix
$routes->setPrefix('/page');

// Associate Routes with Controller Functions
$routes->map('/', 'indexAction');
$routes->map('/{name}', 'indexAction');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

