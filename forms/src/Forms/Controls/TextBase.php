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
 * @package    Nette::Forms
 * @version    $Id$
 */

/*namespace Nette::Forms;*/



require_once dirname(__FILE__) . '/../../Forms/Controls/FormControl.php';



/**
 * Implements the basic functionality common to text input controls.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @package    Nette::Forms
 */
abstract class TextBase extends FormControl
{
	/** @var string */
	protected $emptyValue = '';

	/** @var string */
	protected $tmpValue;

	/** @var array */
	protected $filters = array();



	/**
	 * Sets control's value.
	 * @param  string
	 * @return void
	 */
	public function setValue($value)
	{
		$value = (string) $value;
		foreach ($this->filters as $filter) {
			$value = (string) call_user_func($filter, $value);
		}
		$this->tmpValue = $this->value = $value === $this->emptyValue ? '' : $value;
	}



	/**
	 * Loads HTTP data.
	 * @param  array
	 * @return void
	 */
	public function loadHttpData($data)
	{
		$name = $this->getName();
		if (isset($data[$name]) && is_scalar($data[$name])) {
			$this->tmpValue = $data[$name];
			$encoding = $this->getForm()->getEncoding();
			$this->tmpValue = iconv($encoding, $encoding . '//IGNORE', $this->tmpValue);
		} else {
			$this->tmpValue = NULL;
		}
		$this->setValue($this->tmpValue);
	}



	/**
	 * Sets the special value which is treated as empty string.
	 * @param  string
	 * @return TextBase  provides a fluent interface
	 */
	public function setEmptyValue($value)
	{
		$this->emptyValue = $value;
		return $this;
	}



	/**
	 * Returns the special value which is treated as empty string.
	 * @return string
	 */
	final public function getEmptyValue()
	{
		return $this->emptyValue;
	}



	/**
	 * Appends input string filter callback.
	 * @param  callback
	 * @return TextBase  provides a fluent interface
	 */
	public function addFilter($filter)
	{
		$this->filters[] = $filter;
		return $this;
	}



	public function notifyRule(Rule $rule)
	{
		if (is_string($rule->operation) && strcasecmp($rule->operation, ':regexp') === 0) {
			foreach ((array) $rule->arg as $regexp) {
				if (strncmp($regexp, '/', 1)) {
					throw new /*::*/InvalidArgumentException('Regular expression must be JavaScript compatible.');
				}
			}
		}
		parent::notifyRule($rule);
	}



	/**
	 * Min-length validator: has control's value minimal length?
	 * @param  TextBase
	 * @param  int  length
	 * @return bool
	 */
	public static function validateMinLength(TextBase $control, $length)
	{
		// bug #33268 iconv_strlen works since PHP 5.0.5
		return iconv_strlen($control->getValue(), $control->getForm()->getEncoding()) >= $length;
	}



	/**
	 * Max-length validator: is control's value length in limit?
	 * @param  TextBase
	 * @param  int  length
	 * @return bool
	 */
	public static function validateMaxLength(TextBase $control, $length)
	{
		return iconv_strlen($control->getValue(), $control->getForm()->getEncoding()) <= $length;
	}



	/**
	 * Length validator: is control's value length in range?
	 * @param  TextBase
	 * @param  array  min and max length pair
	 * @return bool
	 */
	public static function validateLength(TextBase $control, $range)
	{
		if (!is_array($range)) {
			$range = array($range, $range);
		}
		$len = iconv_strlen($control->getValue(), $control->getForm()->getEncoding());
		return $len >= $range[0] && $len <= $range[1];
	}



	/**
	 * Email validator: is control's value valid email address?
	 * @param  TextBase
	 * @return bool
	 */
	public static function validateEmail(TextBase $control)
	{
		return preg_match('/^[^@]+@[^@]+\.[a-z]{2,6}$/i', $control->getValue());
	}



	/**
	 * URL validator: is control's value valid URL?
	 * @param  TextBase
	 * @return bool
	 */
	public static function validateUrl(TextBase $control)
	{
		return preg_match('/^.+\.[a-z]{2,6}(\\/.*)?$/i', $control->getValue());
	}



	/**
	 * Regular expression validator: matches control's value regular expression?
	 * @param  TextBase
	 * @param  string
	 * @return bool
	 */
	public static function validateRegexp(TextBase $control, $regexps)
	{
		foreach ((array) $regexps as $regexp) {
			if (preg_match($regexp, $control->getValue())) return TRUE;
		}
		return FALSE;
	}



	/**
	 * Numeric validator: is a control's value decimal number?
	 * @param  TextBase
	 * @return bool
	 */
	public static function validateNumeric(TextBase $control)
	{
		return preg_match('/^-?[0-9]+$/', $control->getValue());
	}



	/**
	 * Float validator: is a control's value float number?
	 * @param  TextBase
	 * @return bool
	 */
	public static function validateFloat(TextBase $control)
	{
		return preg_match('/^-?[0-9]*[.,]?[0-9]+$/', $control->getValue());
	}



	/**
	 * Rangle validator: is a control's value number in specified range?
	 * @param  TextBase
	 * @param  array  min and max value pair
	 * @return bool
	 */
	public static function validateRange(TextBase $control, $range)
	{
		return $control->getValue() >= $range[0] && $control->getValue() <= $range[1];
	}

}
