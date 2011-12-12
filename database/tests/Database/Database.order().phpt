<?php

/**
 * Test: Nette\Database Search and order items.
 *
 * @author     Jakub Vrana
 * @author     Jan Skrasek
 * @package    Nette\Database
 * @subpackage UnitTests
 */

require_once dirname(__FILE__) . '/connect.inc.php';



$apps = array();
foreach ($connection->table('book')->where('title LIKE ?', '%t%')->order('title')->limit(3) as $book) {
	$apps[] = $book->title;
}

Assert::equal(array(
	'1001 tipu a triku pro PHP',
	'Nette',
), $apps);
