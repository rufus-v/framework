<?php

/**
 * Class Import
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Support;

use Rufus\Exception\ExceptionHandler;

class Import
{
	/**
	 * Include custom file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function file($file)
	{
		if (!file_exists($file . '.php'))
            throw new ExceptionHandler('Archivo no encontrado.', '<b>File : </b>' . $file . '.php');

		return require $file . '.php';
	}

	/**
	 * Include config file
	 *
	 * @param string $file
	 * @return mixed
	 */
	public static function config($file)
	{
		
		if (!file_exists('../config/' . $file . '.php'))
            throw new ExceptionHandler('Archivo no encontrado.', '<b>Config : </b>' .  'config/' . $file . '.php');

		return require '../config/' . $file . '.php';
	}
}
