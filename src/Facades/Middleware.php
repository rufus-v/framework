<?php

/**
 * Class Middleware Facades
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Facades;

use Rufus\Facades\Facade;

class Middleware extends Facade
{

	/**
	 * Get the registered name of the component.
	 * 
	 * @param string
	 */
	protected static function getFacadeAccessor()
	{
		return 'Middleware';
	}

}