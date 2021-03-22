<?php

/**
 * Class ControllerCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\Generator;

class ControllerCommand extends AbstractCommand
{
    /**
     * The add controller command
     *
     * @param string $controller
     *
     * @return void
     */
    public function generate($controller)
    {
        $generator = new Generator(
            $this->setting->getControllerDirectory(),
            $controller
        );

        if ($generator->fileExists()) {
            echo "\033[0;31m El controlador ya existe.\033[00m\n";

            exit(1);
        }

        if ($this->arg->options('--no-plain')) {
            $generator->write('controller/no-plain', [
                'baseNamespace' => $this->namespaces['controller']
            ]);
        } else {
            $generator->write('controller/controller', [
                'baseNamespace' => $this->namespaces['controller']
            ]);
        }

        echo "\033[0;32m El controlador sea creado.\033[00m\n";

        exit(0);
    }
}