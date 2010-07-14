<?php

/**
 * Test: Nette\Security\Permission Tests basic Role multiple inheritance.
 *
 * @author     David Grudl
 * @category   Nette
 * @package    Nette\Security
 * @subpackage UnitTests
 */

use Nette\Security\Permission;



require __DIR__ . '/../initialize.php';



$acl = new Permission;
$acl->addRole('parent1');
$acl->addRole('parent2');
$acl->addRole('child', array('parent1', 'parent2'));

T::dump( $acl->getRoleParents('child') );

Assert::true( $acl->roleInheritsFrom('child', 'parent1') );
Assert::true( $acl->roleInheritsFrom('child', 'parent2') );

$acl->removeRole('parent1');
T::dump( $acl->getRoleParents('child') );
Assert::true( $acl->roleInheritsFrom('child', 'parent2') );



__halt_compiler() ?>

------EXPECT------
array(
	"parent1"
	"parent2"
)

array(
	"parent2"
)
