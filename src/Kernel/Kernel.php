<?php

/**
 * Class Kernel
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Kernel;

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;
use Rufus\Support\Env;
use Rufus\Routes\Route;
use Rufus\Facades\Facade;
use Rufus\Support\Import;

class Kernel
{

    /**
     * The rufus framework version.
     *
     * @var string
     */
    public const VERSION = '1.0';

    /**
     * @var Config
     */
    protected static $instance;

    /**
     * 
     */
    public function run()
    {  
        // Initialize aliases
        $this->aliases();
        
        // Initialize Config
        $this->config();

    }

    /**
     * Config
     */
    private function config()
    {
        // CHARSET
        mb_internal_encoding('UTF-8');

        // Dotenv
        new Env('');

        // BASE_URL
        define('BASE_URL', config('app.url'));

        // TIME_ZONE
        date_default_timezone_set(config('app.timezone'));

        // Defained routes
        Import::file('../routes/web');

        // Eloquent Database
        new Database();

        // Pagina no encontrada
        Route::error(function(){view(config('view.404'));});
        
        // Initialize error
        $this->error();

        // Starting Router
        Route::run();
        
    }

    /**
     * Aliases
     *
     * @return void
     */
    private function aliases()
    {
        $app = config('app');
        
        // Prepare aliases
        Facade::setFacadeApplication($app);

        // Create Aliases
        foreach (config('app.aliases') as $key => $aliase) {
            if (!class_exists($key)) {
                class_alias($aliase, $key);
            }
        }
    }

    
    /**
     * Error reporting
     */
    private function error()
    {
        // error reporting
        switch (config('app.env')) {
            // Notificar todos los errores de PHP
            case 'local':
                $whoops = new Whoops();
                $whoops->pushHandler(new PrettyPageHandler());
                $whoops->register();
                return $this;
            break;
            
            // Desactivar toda notificación de error
            case 'testing':
            case 'production':
                error_reporting(0);
            break;

            default:
                header('HTTP/1.1 503 Servicio No Disponible.', true, 503);
                echo "<center><h3'>El entorno de la aplicación no está configurado correctamente.</h3 style='mergin-top'></center>";
            exit(1); // EXIT_ERROR
        }
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Instance
     */
    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
}
