<?php

/**
 * Test: Nette\Latte\Engine: general HTML test.
 *
 * @author     David Grudl
 */

use Nette\Latte,
	Nette\Templating\FileTemplate,
	Nette\Utils\Html,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$latte = new Latte\Engine;
$template = new FileTemplate(__DIR__ . '/templates/general.latte');
$template->registerFilter($latte);
$template->registerHelper('translate', 'strrev');
$template->registerHelper('join', 'implode');
$template->registerHelperLoader('Nette\Latte\Runtime\Filters::loader');

$template->hello = '<i>Hello</i>';
$template->xss = 'some&<>"\'/chars';
$template->people = array('John', 'Mary', 'Paul', ']]> <!--');
$template->menu = array('about', array('product1', 'product2'), 'contact');
$template->el = Html::el('div')->title('1/2"');

$path = __DIR__ . '/expected/' . basename(__FILE__, '.phpt');
Assert::matchFile("$path.phtml", $template->compile());
Assert::matchFile("$path.html", $template->__toString(TRUE));
