<?php

/**
 * Class ResourceControllerCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\Generator;
use Rufus\Support\Str;
use Rufus\Console\Color;

class ResourceControllerCommand extends AbstractCommand
{
    /**
     * Command used to set up the resource system.
     *
     * @param  string $controller
     *
     * @return void
     * @throws
     */
    public function generate($controller)
    {
        // We create command generator instance
        $generator = new Generator(
            $this->setting->getControllerDirectory(),
            $controller
        );

        // We check if the file already exists
        if ($generator->fileExists()) {
            echo Color::danger(' El controlador ya existe');

            exit(1);
        }

        // We create the resource url prefix
        $prefix = preg_replace("/controller/i", "", strtolower($controller));
        $model = ucfirst($prefix);
        $filename = ucfirst($prefix);
        $prefix = '/'.trim($prefix, '/');

        $model_namespace = '';

        $options = $this->arg->options();

        // Comprobamos si existe --with-view. Si eso existe,
        // lanzamos la pregunta
        if ($options->has('--with-view')
            && $this->arg->readline(" ¿Quieres que cree las vistas asociadas? ")
        ) {
            $model = preg_replace("/controller/i", "", strtolower($controller));

            $model = strtolower($model);
            $filename = strtolower($filename);

            $this->createDefaultView($model, $filename);
        }

        $prefix = Str::plurial(Str::snake($prefix));

        $this->createResourceController(
            $generator,
            $prefix,
            $controller,
            $model_namespace
        );

        exit(0);
    }

    /**
     * Create rest controller
     *
     * @param Generator $generator
     * @param string $prefix
     * @param string $controller
     * @param string $model_namespace
     *
     * @return void
     */
    private function createResourceController(
        Generator $generator,
        $prefix,
        $controller,
        $model_namespace = ''
    ) {
        $generator->write('controller/rest', [
            'modelNamespace' => $model_namespace,
            'prefix' => $prefix,
            'baseNamespace' => $this->namespaces['controller']
        ]);

        echo Color::green(' El controlador Rest sea creado');
    }

    /**
     * Create the default view for rest Generation
     *
     * @param string $modal
     * @param string $filename
     *
     * @return void
     */
    private function createDefaultView($model, $filename)
    {
        $view = \Rufus\Support\Import::file('config/view');
       @mkdir($view['path']."/".$model, 0766);

        // We create the default CRUD view
        foreach (["create", "edit", "show", "index"] as $value) {
            $filename = "$model/$value".$view['extension'];

            touch($view['path'].'/'.$filename);

            echo "$filename creado\n";
        }
    }
}