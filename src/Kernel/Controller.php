<?php

/**
 * Class  Controller
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Kernel;

class Controller
{
    public function __construct()
    {
		
        // Run default middlewares
		$this->middleware(config('app.middlewares.default'), true);
    }

    /**
	 * Run middlewares at first
	 *
	 * @param array $middleware
	 * @param bool $default
	 * @return void
	 */
	protected function middleware(array $middlewares, bool $default = false)
	{
		if ($default === false) {
			$list = config('app.middlewares.manual');

			foreach ($middlewares as $middleware) {
				$middleware = ucfirst($middleware);
				if (array_key_exists($middleware, $list)) {
					if (class_exists($list[$middleware])) {
						call_user_func_array([new $list[$middleware], 'handle'], []);
					}
				}
			}
		} else {
			foreach ($middlewares as $key => $val) {
				if (class_exists($val)) {
					call_user_func_array([new $val, 'handle'], []);
				}
			}
		}
	}

	/**
     * Get the current user request
     *
     * @return \Rufus\Http\Request
     */
    public function request()
    {
        return request();
    }


	/**
 	 * 
	 *
 	*/
    public function validate($data, $rules)
    {
      return new \Rufus\Validation\Validation($data, $rules);
    }
  
}