<?php

/**
 * Class ExceptionHandler
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Exception;

use Exception;

class ExceptionHandler
{
	public function __construct($title, $body)
	{
		throw new Exception(strip_tags($title . ': ' . $body), 1);		
	}
}