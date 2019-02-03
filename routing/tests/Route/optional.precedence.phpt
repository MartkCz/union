<?php

/**
 * Test: Nette\Application\Routers\Route with optional sequence precedence.
 */

declare(strict_types=1);

use Nette\Application\Routers\Route;


require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/Route.php';


$route = new Route('[<one>/][<two>]', [
]);

testRouteIn($route, '/one', [
	'one' => 'one',
	'two' => null,
	'test' => 'testvalue',
], '/one/?test=testvalue');

$route = new Route('[<one>/]<two>', [
	'two' => null,
]);

testRouteIn($route, '/one', [
	'one' => 'one',
	'two' => null,
	'test' => 'testvalue',
], '/one/?test=testvalue');
