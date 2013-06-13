<?php

/**
 * Test: Tracy\Debugger logging exceptions in log message.
 *
 * @author     David Grudl
 * @package    Tracy
 */

use Tracy\Debugger;



require __DIR__ . '/../bootstrap.php';



// Setup environment
Debugger::$logDirectory = TEMP_DIR . '/log';
Tester\Helpers::purge(Debugger::$logDirectory);


function foo($fp) {
	throw new Exception;
}


for ($i = 0; $i < 3; $i++) {
	$path = TEMP_DIR . "/$i";
	try {
		$files[] = $file = fopen(TEMP_DIR . "/$i", 'w');
		foo($file);
	} catch(Exception $e) {
		$name[] = Debugger::log($e);
	}
}

while (--$i > 0) {
	Assert::same($name[0], $name[$i]);
}
