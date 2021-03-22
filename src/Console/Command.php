<?php

/**
 * Class Command
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console;

use Rufus\Console\Command\AbstractCommand;

class Command extends AbstractCommand
{
    /**
     * List of command actions
     *
     * @var array
     */
    private $actions = [
        'clear' => \Rufus\Console\Command\ClearCommand::class,
        'controller' => \Rufus\Console\Command\ControllerCommand::class,
        'middleware' => \Rufus\Console\Command\MiddlewareCommand::class,
        'model' => \Rufus\Console\Command\ModelCommand::class,
        'resource' => \Rufus\Console\Command\ResourceControllerCommand::class,
    ];

    /**
     * The call command
     *
     * @param string $action
     * @param string $command
     * @param array $rest
     *
     * @return mixed
     */
    public function call($command, $action, ...$rest)
    {
        $class = $this->actions[$action];

        $instance = new $class($this->setting, $this->arg);

        if (method_exists($instance, $command)) {
            return call_user_func_array([$instance, $command], $rest);
        }
    }
}