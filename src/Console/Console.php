<?php

/**
 * Class Console
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console;

class Console
{
    use ConsoleInformation;

    /**
     * The Setting instance
     *
     * @var Setting
     */
    private $setting;

    /**
     * The COMMAND instance
     *
     * @var Command
     */
    private $command;

    /**
     * The Loader instance
     *
     * @var Loader
     */
    private $kernel;

    /**
     * The custom command registers
     *
     * @var array
     */
    private $registers = [];

    /**
     * Defines if console booted
     *
     * @var bool
     */
    private $booted;

    /**
     * The ArgOption instance
     *
     * @return ArgOption
     */
    private $arg;

    /**
     * The command list
     *
     * @var array
     */
    const COMMAND = [
        'make',
        'generate',
        'gen',
        'help',
        'clear',
    ];

    /**
     * The action list
     *
     * @var array
     */
    const ACTION = [
        'controller',
        'middleware',
        'model',
    ];

    /**
     * Rufus constructor.
     *
     * @param  Setting $setting
     *
     * @return void
     */
    public function __construct(Setting $setting)
    {
        $this->arg = new ArgOption();

        if ($this->arg->getParameter('trash')) {
            $this->throwFailsCommand(' Mal uso de comandos', 'help');
        }

        $this->setting = $setting;

        $this->command = new Command($setting, $this->arg);
    }


    /**
     * Launch Rufus task runner
     *
     * @return void
     * @throws
     */
    public function run()
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        foreach ($this->setting->getBootstrap() as $item) {
            require $item;
        }

        $command = $this->arg->getParameter('command');

        if (array_key_exists($command, $this->registers)) {
            try {
                return $this->registers[$command]($this->arg);
            } catch (\Exception $exception) {
                echo Color::red($exception->getMessage());
                echo Color::green($exception->getTraceAsString());

                exit(1);
            }
        }

        try {
            $this->call($command);
        } catch (\Exception $exception) {
            echo Color::red($exception->getMessage());
            echo Color::green($exception->getTraceAsString());

            exit(1);
        }
    }

    /**
     * Calls a command
     *
     * @param  string $command
     *
     * @return void
     * @throws
     */
    private function call($command)
    {
        if (!in_array($command, static::COMMAND)) {
            $this->throwFailsCommand(" El comando '$command' no existe.", 'help');
        }

        if (!$this->arg->getParameter('action')) {
            if ($this->arg->getParameter('target') == 'help') {
                $this->help($command);

                exit(0);
            }
        }

        try {
            call_user_func_array(
                [$this, $command],
                [$this->arg->getParameter('target')]
            );
        } catch (\Exception $e) {
            echo $e->getMessage();

            exit(1);
        }
    }

    /**
     * Add a custom order to the store
     *
     * @param string $command
     * @param callable $cb
     *
     * @return rufus
     */
    public function addCommand($command, $cb)
    {
        $this->registers[$command] = $cb;

        return $this;
    }

    /**
     * Create files
     *
     * @return void
     *
     * @throws \ErrorException
     */
    private function  make()
    {
        $action = $this->arg->getParameter('action');

        if (!in_array($action, static::ACTION)) {
            $this->throwFailsCommand(' Esta acción no existe', 'help make');
        }

        $this->command->call(
            'generate',
            $action,
            $this->arg->getParameter('target')
        );
    }


    /**
     * Allows generate a resource on a controller
     *
     * @return void
     */
    private function generate()
    {
        $action = $this->arg->getParameter('action');

        if (!in_array($action, ['resource'])) {
            $this->throwFailsAction(' Esta acción no existe', 'help generate');
        }

        $this->command->call(
            'generate',
            $action,
            $this->arg->getParameter('target')
        );
    }

    /**
     * Alias of generate
     *
     * @return void
     */
    private function gen()
    {
        $this->generate();
    }

    /**
     * Remove the caches
     *
     * @return void
     *
     * @throws \ErrorException
     */
    private function clear()
    {
        $target = $this->arg->getParameter('action');

        $this->command->call('make', 'clear', $target);
    }

    /**
     * Display global help or helper command.
     *
     * @param  string|null $command
     * @return int
     */
    private function help($command = null)
    {
        if ($command === null) {
            $usage = <<<USAGE
  Uso del corredor de tareas rufus: php rufus command:action [name] --option
   \033[0;32m COMMAND\033[00m:
   \033[0;33m help\033[00m Mostrar ayudante de comando
   \033[0;32m GENERATE\033[00m crear una nueva clave de aplicación y recursos
   \033[0;33m generate:resource\033[00m   Crear nuevo controlador REST

   \033[0;32m MAKE\033[00m Crea una clase de usuario
   \033[0;33m make:middleware\033[00m      Crear nuevo middleware
   \033[0;33m make:controller\033[00m      Crear nuevo controlador
   \033[0;33m make:model\033[00m           Crear nuevo modelo

   \033[0;32m CLEAR\033[00m Para borrar la información de la caché [not supported]
   \033[0;33m clear:view\033[00m          Vista de la información en caché
   \033[0;33m clear:cache\033[00m         Borrar información de caché
   \033[0;33m clear:all\033[00m           Borrar toda la información de la caché
   
USAGE;
            echo $usage;
            return 0;
        }

        switch ($command) {
            case 'help':
                echo "\033[0;33m help\033[00m display command helper\n";
                break;
            case 'make':
                echo <<<U
\n\033[0;32m create\033[00m Crear una clase de usuario\n
    [option]
    --no-plain  Crea un controlador simple [disponible en make:controller]
    * puedes usar --no-plain --with-model en el mismo comando
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m make:controller name [option]  Para crear un nuevo controlador
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m make:middleware name           Para crear un nuevo middleware
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m make:model name [option]       Para crear un nuevo modelo
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m make help                      Para mostrar informacion de ayudas
U;

                break;
            case 'generate':
                echo <<<U
    \n\033[0;32m generate\033[00m crear una clave de recurso y aplicación
    [option]
    --model=[model_name] Define the usable model
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m generate:resource name [option]   Para crear un nuevo REST controller
    \033[0;33m$\033[00m php \033[0;34m rufus\033[00m generate help                     Para mostrar informacion de ayudas
U;
                break;

            case 'clear':
                echo <<<U
\n\033[0;32m clear\033[00m Para borrar la información de la caché\n
   \033[0;33m$\033[00m php \033[0;34m rufus\033[00m clear:view             Clear view cached information
   \033[0;33m$\033[00m php \033[0;34m rufus\033[00m clear:cache\033[00m    Borrar información de caché
   \033[0;33m$\033[00m php \033[0;34m rufus\033[00m clear:all\033[00m      Borrar toda la información de la caché
U;
                break;
        }

        exit(0);
    }
}