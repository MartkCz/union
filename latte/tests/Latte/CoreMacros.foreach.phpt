<?php

/**
 * Test: Latte\Macros\CoreMacros: {foreach ...}
 */

use Latte\Macros\CoreMacros;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$compiler = new Latte\Compiler;
CoreMacros::install($compiler);

$prefix = '<?php $iterations = 0; '
	. 'if (isset($this->params[\'value\'])) trigger_error(\'Variable $value overwritten in foreach.\'); '
	. 'foreach ($iterator = $this->global->its[] = new Latte\Runtime\CachingIterator(';

function expandMacro($compiler, $args, $modifiers = NULL) {
	$node = $compiler->expandMacro('foreach', $args, $modifiers);
	$node->content = ' $iterator ';
	$node->closing = TRUE;
	$node->macro->nodeClosed($node);
	return $node;
}

Assert::same($prefix . '$array) as $value) { ?>',  expandMacro($compiler, '$array as $value')->openingCode);
Assert::same(
	'<?php $iterations = 0; '
	. 'if (isset($this->params[\'key\'])) trigger_error(\'Variable $key overwritten in foreach.\'); '
	. 'if (isset($this->params[\'value\'])) trigger_error(\'Variable $value overwritten in foreach.\'); '
	. 'foreach ($iterator = $this->global->its[] = new Latte\Runtime\CachingIterator($array) as $key => $value) { ?>',
	expandMacro($compiler, '$array as $key => $value')->openingCode
);

Assert::same(
	'<?php $iterations = 0; '
	. 'foreach ($iterator = $this->global->its[] = new Latte\Runtime\CachingIterator($array) as $key => $value) { ?>',
	expandMacro($compiler, '$array as $key => $value', '|nocheck')->openingCode
);

Assert::same($prefix . '$obj->data("A as B")) as $value) { ?>',  expandMacro($compiler, '$obj->data("A as B") as $value')->openingCode);
Assert::same($prefix . '$obj->data(\'A as B\')) as $value) { ?>',  expandMacro($compiler, '$obj->data(\'A as B\') as $value')->openingCode);
Assert::same($prefix . '$obj->data("X as Y, Z as W")) as $value) { ?>',  expandMacro($compiler, '$obj->data("X as Y, Z as W") as $value')->openingCode);

Assert::same(
	'<?php $iterations = 0; '
	. 'if (isset($this->params[\'value\'])) trigger_error(\'Variable $value overwritten in foreach.\'); '
	. 'foreach ($array as $value) { ?>',
	expandMacro($compiler, '$array as $value', '|noiterator')->openingCode
);

Assert::exception(function () use ($compiler) {
	expandMacro($compiler, '$array as $value', '|filter');
}, 'Latte\CompileException', 'Only modifiers |noiterator and |nocheck are allowed here.');
