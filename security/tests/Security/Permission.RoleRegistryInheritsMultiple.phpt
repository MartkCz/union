<?php

/**
 * Test: Nette\Security\Permission Tests basic Role multiple inheritance.
 */

declare(strict_types=1);

use Nette\Security\Permission;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$acl = new Permission;
$acl->addRole('parent1');
$acl->addRole('parent2');
$acl->addRole('child', ['parent1', 'parent2']);

Assert::same([
	'parent1',
	'parent2',
], $acl->getRoleParents('child'));


Assert::true($acl->roleInheritsFrom('child', 'parent1'));
Assert::true($acl->roleInheritsFrom('child', 'parent2'));

$acl->removeRole('parent1');
Assert::same(['parent2'], $acl->getRoleParents('child'));
Assert::true($acl->roleInheritsFrom('child', 'parent2'));
