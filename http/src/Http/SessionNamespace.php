<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com
 *
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com
 * @category   Nette
 * @package    Nette::Web
 * @version    $Id$
 */

/*namespace Nette::Web;*/



require_once dirname(__FILE__) . '/../Object.php';



/**
 * Session namespace for Session.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette::Web
 * @property   mixed
 */
final class SessionNamespace extends /*Nette::*/Object implements /*::*/IteratorAggregate
{
	/** @var int  limit whether expiration is number of seconds starting from current time or timestamp */
	const EXPIRATION_DELTA_LIMIT = 31622400; // 366 days

	/** @var array  session data storage */
	private $data;

	/** @var array  session metadata storage */
	private $meta;

	/** @var bool */
	public $warnOnUndefined = FALSE;



	/**
	 * Do not call directly. Use Session::getNamespace().
	 */
	public function __construct(& $data, & $meta)
	{
		$this->data = & $data;
		$this->meta = & $meta;
	}



	/**
	 * Returns an iterator over all namespace variables.
	 * @return ::ArrayIterator
	 */
	public function getIterator()
	{
		if (isset($this->data)) {
			return new /*::*/ArrayIterator($this->data);
		} else {
			return new /*::*/ArrayIterator;
		}
	}



	/**
	 * Sets a variable in this session namespace.
	 *
	 * @param  string  name
	 * @param  mixed   value
	 * @return void
	 * @throws ::InvalidArgumentException
	 */
	public function __set($name, $value)
	{
		if ($name === '') {
			throw new /*::*/InvalidArgumentException("The key must be a non-empty string.");
		}

		$this->data[$name] = $value;
	}



	/**
	 * Gets a variable from this session namespace.
	 *
	 * @param  string    name
	 * @return mixed
	 * @throws ::InvalidArgumentException
	 */
	public function &__get($name)
	{
		if ($name === '') {
			throw new /*::*/InvalidArgumentException("The key must be a non-empty string.");
		}

		if ($this->warnOnUndefined && !array_key_exists($name, $this->data)) {
			trigger_error("The variable '$name' does not exist", E_USER_WARNING);
		}

		return $this->data[$name];
	}



	/**
	 * Determines if a variable in this session namespace is set.
	 *
	 * @param  string    name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}



	/**
	 * Unsets a variable in this session namespace.
	 *
	 * @param  string    name
	 * @return void
	 * @throws ::InvalidArgumentException
	 */
	public function __unset($name)
	{
		if ($name === '') {
			throw new /*::*/InvalidArgumentException("The key must be a non-empty string.");
		}

		unset($this->data[$name], $this->meta['EXP'][$name]);
		Session::clean();
	}



	/**
	 * Sets the expiration of the namespace or specific variables.
	 * @param  int     time in seconds
	 * @param  mixed   optional list of variables / single variable to expire
	 * @return void
	 */
	public function setExpiration($seconds, $variables = NULL)
	{
		if ($seconds <= 0) {
			$this->removeExpiration($variables);
			return;
		}

		if ($seconds <= self::EXPIRATION_DELTA_LIMIT) {
			$seconds += time();
		}

		if ($variables === NULL) {
			// to entire namespace
			$this->meta['EXP'][''] = $seconds;

		} elseif (is_array($variables)) {
			// to variables
			foreach ($variables as $variable) {
				$this->meta['EXP'][$variable] = $seconds;
			}

		} else {
			$this->meta['EXP'][$variables] = $seconds;
		}
	}



	/**
	 * Removes the expiration from the namespace or specific variables.
	 * @param  mixed   optional list of variables / single variable to expire
	 * @return void
	 */
	public function removeExpiration($variables = NULL)
	{
		if ($variables === NULL) {
			// from entire namespace
			unset($this->meta['EXP']['']);

		} elseif (is_array($variables)) {
			// from variables
			foreach ($variables as $variable) {
				unset($this->meta['EXP'][$variable]);
			}
		} else {
			unset($this->meta['EXP'][$variables]);
		}

		Session::clean();
	}



	/**
	 * Cancels the current session namespace.
	 * @return void
	 */
	public function remove()
	{
		$this->data = NULL;
		$this->meta = NULL;
		Session::clean();
	}

}
