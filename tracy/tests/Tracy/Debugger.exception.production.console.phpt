<?php

/**
 * Test: Nette\Diagnostics\Debugger exception in production & console mode.
 *
 * @author     David Grudl
 * @package    Nette\Diagnostics
 */

use Nette\Diagnostics\Debugger;



require __DIR__ . '/../bootstrap.php';



Debugger::$productionMode = TRUE;
header('Content-Type: text/plain');

Debugger::enable();

function shutdown() {
	Assert::match('ERROR:%A%', ob_get_clean());
	die(0);
}
ob_start();
Debugger::$onFatalError[] = 'shutdown';


throw new Exception('The my exception', 123);
