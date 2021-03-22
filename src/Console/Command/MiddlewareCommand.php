<?php

/**
 * Class MiddlewareCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\Generator;

class MiddlewareCommand extends AbstractCommand
{
    /**
     * Add middleware
     *
     * @param string $middleware
     *
     * @return void
     */
    public function generate(string $middleware)
    {
        $generator = new Generator(
            $this->setting->getMiddlewareDirectory(),
            $middleware
        );

        if ($generator->fileExists()) {
            echo "\033[0;31m El middleware ya existe.\033[00m\n";

            exit(1);
        }

        $generator->write('middleware', [
            'baseNamespace' => $this->namespaces['middleware']
        ]);

        echo "\033[0;32m El middleware sea creado.\033[00m\n";

        exit(0);
    }
}