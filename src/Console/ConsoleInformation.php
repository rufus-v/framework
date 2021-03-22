<?php

/**
 * Class ConsoleInformation
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console;

use Rufus\Console\Color;

trait ConsoleInformation
{
    /**
     * Throw fails command
     *
     * @param string $message
     * @param string $command
     * @throws \ErrorException
     */
    public function throwFailsCommand($message, $command = null)
    {
        echo Color::red($message)."\n";

        if (!is_null($command)) {
            echo Color::green(sprintf(' Escribe "php rufus %s" para más información', $command));
        }

        exit(1);
    }
}