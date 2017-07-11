<?php

/**
 * Test: Nette\ComponentModel\Container::attached()
 */

declare(strict_types=1);

use Nette\ComponentModel\Container;
use Nette\ComponentModel\IComponent;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class TestClass extends Container implements ArrayAccess
{
	function attached(IComponent $obj): void
	{
		Notes::add(get_class($this) . '::ATTACHED(' . get_class($obj) . ')');
	}


	function detached(IComponent $obj): void
	{
		Notes::add(get_class($this) . '::detached(' . get_class($obj) . ')');
	}


	function offsetSet($name, $component)
	{
		$this->addComponent($component, $name);
	}


	function offsetGet($name)
	{
		return $this->getComponent($name, true);
	}


	function offsetExists($name)
	{
		return $this->getComponent($name) !== null;
	}


	function offsetUnset($name)
	{
		$this->removeComponent($this->getComponent($name, true));
	}
}


class A extends TestClass
{
}
class B extends TestClass
{
}
class C extends TestClass
{
}
class D extends TestClass
{
}
class E extends TestClass
{
}

$d = new D;
$d['e'] = new E;
$b = new B;
$b->monitor('a');
$b['c'] = new C;
$b['c']->monitor('a');
$b['c']['d'] = $d;

// 'a' becoming 'b' parent
$a = new A;
$a['b'] = $b;
Assert::same([
	'C::ATTACHED(A)',
	'B::ATTACHED(A)',
], Notes::fetch());


// removing 'b' from 'a'
unset($a['b']);
Assert::same([
	'C::detached(A)',
	'B::detached(A)',
], Notes::fetch());

// 'a' becoming 'b' parent
$a['b'] = $b;

Assert::same('b-c-d-e', $d['e']->lookupPath('A'));
Assert::same($a, $d['e']->lookup('A'));
Assert::same('b-c-d-e', $d['e']->lookupPath());
Assert::same($a, $d['e']->lookup(null));
Assert::same('c-d-e', $d['e']->lookupPath('B'));
Assert::same($b, $d['e']->lookup('B'));

Assert::same($a['b-c'], $b['c']);
Notes::fetch(); // clear


class FooForm extends TestClass
{
	protected function validateParent(\Nette\ComponentModel\IContainer $parent): void
	{
		parent::validateParent($parent);
		$this->monitor(__CLASS__);
	}
}

class FooControl extends TestClass
{
	protected function validateParent(\Nette\ComponentModel\IContainer $parent): void
	{
		parent::validateParent($parent);
		$this->monitor('FooPresenter');
		$this->monitor('TestClass'); // double
	}
}

class FooPresenter extends TestClass
{
}

$presenter = new FooPresenter();
$presenter['control'] = new FooControl();
$presenter['form'] = new FooForm();
$presenter['form']['form'] = new FooForm();

Assert::same([
	'FooControl::ATTACHED(FooPresenter)',
	'FooForm::ATTACHED(FooForm)',
], Notes::fetch());
