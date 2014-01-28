<?php

/**
 * Test: Nette\Forms\Controls\CsrfProtection.
 *
 * @author     David Grudl
 */

use Nette\Forms\Form,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$_SERVER['REQUEST_METHOD'] = 'POST';


$form = new Form;

$input = $form->addProtection('Security token did not match. Possible CSRF attack.');

$form->fireEvents();

Assert::same( array('Security token did not match. Possible CSRF attack.'), $form->getErrors() );
Assert::match('<input type="hidden" name="_token_" value="%S%">', (string) $input->getControl());
