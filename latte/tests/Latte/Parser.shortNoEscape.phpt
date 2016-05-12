<?php

/**
 * Test: Latte\Parser and former shortNoEscape.
 */

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

Assert::contains('LR\Filters::escapeHtmlText(!="<>")', $latte->compile('{!="<>"}')); // short-no-escape is not supported
