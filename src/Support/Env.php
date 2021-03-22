<?php

/**
 * Class Env
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Support;

use Dotenv;

class Env
{
    /**
     * @var Env
     */
    protected static $instance;

    /**
     * @var array Valor de la variable de entorno
     */
    protected $dotenv ;

    public function __construct($fileName)
    {
        $this->dotenv  = Dotenv\Dotenv::create("../$fileName")->load();
    }


    public function get($key, $default = null)
    {
        if (!strlen($key)) { // Devuelve todas las variables de entorno sin pasar la clave
            return $this->dotenv ;
        }

        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') { // Eliminar comillas dobles
            return substr($value, 1, -1);
        }

        return $value;
    }


    public static function instance($fileName = '.env')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($fileName);
        }

        return self::$instance;
    }
}