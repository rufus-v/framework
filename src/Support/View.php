<?php

/**
 * Class View
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Support;

use Rufus\Edge\Edge;
use Rufus\Edge\Cache\FileCache;
use Rufus\Edge\Loader\FileLoader;

class View
{
    /**
     * @var config
     */
    protected static $instance;

    /**
     * Instance
     */
    public static function instance()
    {
        
        if (!self::$instance instanceof self) {
            self::$instance = new  self();
        }
        return self::$instance;
    }

    /**
     * Render view
     */
    public function render($view, $data = [], $cache = null)
    {
        $paths = ['../resources/views'];

        $loader = new FileLoader($paths);
        $loader->addFileExtension('.html');
        $loader->addFileExtension('.xml');
        $loader->addFileExtension('.php');
        $loader->addFileExtension('.edge');
        $loader->addFileExtension('.twig');


        if ($cache === false) {
            $edge = new Edge($loader);
        } else {
            $edge = new Edge($loader, null, new FileCache(storage_path('cache')));
        }

        echo $edge->render($view, $data);

    }
}