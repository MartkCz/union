<?php

/**
 * Test: Nette\Security\Permission Ensures that assertions on privileges work properly.
 */

declare(strict_types=1);

use Nette\Security\Permission;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


function falseAssertion()
{
	return false;
}


function trueAssertion()
{
	return true;
}


$acl = new Permission;
$acl->allow(null, null, 'somePrivilege', 'trueAssertion');
Assert::true($acl->isAllowed(null, null, 'somePrivilege'));

$acl->allow(null, null, 'somePrivilege', 'falseAssertion');
Assert::false($acl->isAllowed(null, null, 'somePrivilege'));
