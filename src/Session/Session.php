<?php

/**
 * Class Session
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Session;

class Session
{
    /**
     * Add an item to the session
     * @param string $key the session array key
     * @param string $value the value for the session element
     */
    public static  function add($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key the session array key.
     * @param string $ruta the path it will take if the session does not exist. (Optional)
     */
    public static  function close($key, $ruta = '')
    {
        @session_start();
        if (empty($_SESSION[$key])) {
            header('Location: ' . route($ruta));
        }
    }

    /**
     * @param string $key the session array key.
     * @param string $ruta the path it will take if the session exists
     */
    public static  function isset($key, $ruta = '')
    {
        @session_start();
        if (isset($_SESSION[$key])) {
            header('Location: ' . route($ruta));
        }
    }

    /**
     * Delete session
     *
     * @param string nullable $storage
     * @return void
     */
    public static function delete($storage = null)
    {
        if (is_null($storage)) {
            session_unset();
        } else {
            unset($_SESSION[$storage]);
        }
    }

    /**
     * Session destroy
     *
     * @return void
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
}
