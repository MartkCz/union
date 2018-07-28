<?php

declare(strict_types=1);

namespace Abc;

/**
 * Interface
 * @author John Doe
 */
interface Interface1
{
	public function func1();
}


interface Interface2
{
}


abstract class Class1 implements Interface1
{
	/** @return Class1 */
	public function func1()
	{
	}


	abstract protected function func2();
}


class Class2 extends Class1 implements Interface2
{
	/**
	 * Public
	 * @var int
	 */
	public $public;

	/** @var int */
	protected $protected = 10;

	private $private = [];

	static public $static;


	/**
	 * Func3
	 * @return Class1
	 */
	private function &func3(array $a = [], Class2 $b = null, \Abc\Unknown $c, \Xyz\Unknown $d, callable $e, $f = Unknown::ABC, $g)
	{
	}


	final public function func2()
	{
	}
}


class Class3
{
	public $prop1;
}


class Class4
{
	const THE_CONSTANT = 9;
}

/** */
class Class5
{
	public function func1(\A $a, ?\B $b, ?\C $c = null, \D $d = null, \E $e, ?int $i = 1, ?array $arr = [])
	{
	}


	public function func2(): ?\stdClass
	{
	}


	public function func3(): void
	{
	}
}


class Class6
{
	private const THE_PRIVATE_CONSTANT = 9;
	public const THE_PUBLIC_CONSTANT = 9;
}
