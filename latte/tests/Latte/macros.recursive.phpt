<?php

/**
 * Test: Latte\Engine: general HTML test.
 */

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

$template = <<<'EOD'
<ul>
	<li>{$hello->{'item'}}</li>
	<li>{function(){}}</li>
	<li>{function(){return}}</li>
	<li>{function() { return; } }</li>
	<li>{function(){return}|upper}</li>
	<li>{function() { return; } |upper}</li>
	<li>{function() { return; } |upper:$item->{10}}</li>
</ul>

EOD;

Assert::matchFile(
	__DIR__ . '/expected/macros.recursive.phtml',
	$latte->compile($template)
);
