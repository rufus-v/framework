<?php

/**
 * Class ModelCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\Color;
use Rufus\Console\Generator;

class ModelCommand extends AbstractCommand
{
    /**
     * Add Model
     *
     * @param string $model
     *
     * @return mixed
     */
    public function generate($model)
    {
        $generator = new Generator(
            $this->setting->getModelDirectory(),
            $model
        );

        if ($generator->fileExists()) {
            echo Color::red("\033[0;31m El modelo ya existe.\033[00m\n");

            exit(1);
        }

        $generator->write('model/model', [
            'baseNamespace' => $this->namespaces['model']
        ]);

        echo Color::green("\033[0;32m modelo sea creado.\033[00m\n");
    }
}