<?php

/**
 * Class Config
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console;

class Config
 {

     /**
      * @var Confif
      */
     protected static $instance;

     /**
      * Init config of console
      */
    public function init()
    {
        /**
         * Create command instance and set filename
         */
        $setting = new \Rufus\Console\Setting(__DIR__);
        $setting->setNamespaces($this->namespaces());

        /**
        * Change preset destination
        */
        $setting->setControllerDirectory('app/Controllers');
        $setting->setMiddlewareDirectory('app/Middlewares');
        $setting->setModelDirectory('app/Models');
        return $setting;
    }

    /**
     * Get app namespace
     *
     * @return array
     */
    public function namespaces()
    {
        return [
            'controller' => 'App\\Controllers',
            'middleware' => 'App\\Middlewares',
            'model'      => 'App\\Models',
        ];
    }

    /**
     * Instance
     */
    public static function instance()
    {
        if(!self::$instance  instanceof self ){
            self::$instance = new self();
        }
        return self::$instance;
    }

}