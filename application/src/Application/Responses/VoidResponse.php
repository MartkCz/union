<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Application\Responses;

use Nette;


/**
 * No response.
 */
class VoidResponse implements Nette\Application\IResponse
{
	use Nette\SmartObject;

	/**
	 * @return void
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
	}

}
