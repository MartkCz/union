<?php

/**
 * Test: Nette\Forms\TextBase validators.
 *
 * @author     David Grudl
 * @category   Nette
 * @package    Nette\Forms
 * @subpackage UnitTests
 */

use Nette\Forms\TextBase,
	Nette\Forms\TextInput;



require __DIR__ . '/../initialize.php';



$control = new TextInput();
$control->value = '';
Assert::false( TextBase::validateEmail($control) );


$control->value = '@.';
Assert::false( TextBase::validateEmail($control) );


$control->value = 'name@a-b-c.cz';
Assert::true( TextBase::validateEmail($control) );


$control->value = "name@\xc5\xbelu\xc5\xa5ou\xc4\x8dk\xc3\xbd.cz"; // name@�lu�ou�k�.cz
Assert::true( TextBase::validateEmail($control) );


$control->value = "\xc5\xbename@\xc5\xbelu\xc5\xa5ou\xc4\x8dk\xc3\xbd.cz"; // �name@�lu�ou�k�.cz
Assert::false( TextBase::validateEmail($control) );
