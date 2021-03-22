<?php

/**
 * Class Config
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Support;

class Config
{

	/**
     * @var Config
     */
    protected static $instance;

	/**
	 * Get config item
	 *
	 * @param string $params
	 * @return mixed
	 */
	public function get($params)
	{
		// Explode items
		$keys 	= explode('.', $params);

		// Set config file
		$file 	= $keys[0];

		// Get config file
		$config = Import::config($file);

		// Remove file item from array
		array_shift($keys);

		// Find the item that requested
		foreach($keys as $key) {
			$config = $config[$key];
		}

		// return the item
		return $config;
	}

	public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}