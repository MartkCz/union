<?php

/**
 * Test: Nette\Utils\Arrays::grep() errors.
 */

use Nette\Utils\Arrays;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


Assert::exception(function () {
	Arrays::grep(['a', '1', 'c'], '#*#');
}, 'Nette\Utils\RegexpException', 'Compilation failed: nothing to repeat at offset 0 in pattern: #*#');


Assert::exception(function () {
	Arrays::grep(['a', "1\xFF", 'c'], '#\d#u');
}, 'Nette\Utils\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');
