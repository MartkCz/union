<?php

/**
 * This file is part of the Tracy (http://tracy.nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Tracy;

use Tracy;


/**
 * IDebugPanel implementation helper.
 *
 * @author     David Grudl
 * @internal
 */
final class DefaultBarPanel implements IBarPanel
{
	private $id;

	public $data;


	public function __construct($id)
	{
		$this->id = $id;
	}


	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 */
	public function getTab()
	{
		ob_start();
		require __DIR__ . "/templates/bar.{$this->id}.tab.phtml";
		return ob_get_clean();
	}


	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();
		if (is_file(__DIR__ . "/templates/bar.{$this->id}.panel.phtml")) {
			require __DIR__ . "/templates/bar.{$this->id}.panel.phtml";
		}
		return ob_get_clean();
	}

}
