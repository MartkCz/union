<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\DI\Config\Adapters;

use Nette;


/**
 * Reading and generating PHP files.
 */
class PhpAdapter implements Nette\DI\Config\IAdapter
{
	use Nette\SmartObject;

	/**
	 * Reads configuration from PHP file.
	 * @param  string  file name
	 */
	public function load(string $file): array
	{
		return require $file;
	}


	/**
	 * Generates configuration in PHP format.
	 */
	public function dump(array $data): string
	{
		return "<?php // generated by Nette \nreturn " . Nette\PhpGenerator\Helpers::dump($data) . ';';
	}

}
