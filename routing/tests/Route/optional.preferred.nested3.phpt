<?php

/**
 * Test: Nette\Application\Routers\Route with 'required' optional sequences III.
 */

declare(strict_types=1);

use Nette\Application\Routers\Route;


require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/Route.php';


$route = new Route('[!<lang [a-z]{2}>[-<sub>]/]<name>[/page-<page>]', [
	'sub' => 'cz',
	'lang' => 'cs',
]);

testRouteIn($route, '/cs-cz/name', [
	'lang' => 'cs',
	'sub' => 'cz',
	'name' => 'name',
	'page' => null,
	'test' => 'testvalue',
], '/cs/name?test=testvalue');

testRouteIn($route, '/cs-xx/name', [
	'lang' => 'cs',
	'sub' => 'xx',
	'name' => 'name',
	'page' => null,
	'test' => 'testvalue',
], '/cs-xx/name?test=testvalue');

testRouteIn($route, '/name', [
	'name' => 'name',
	'sub' => 'cz',
	'lang' => 'cs',
	'page' => null,
	'test' => 'testvalue',
], '/cs/name?test=testvalue');
