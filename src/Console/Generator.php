<?php

/**
 * Class Generator
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console;

class Generator
{
    use ConsoleInformation;
    
    /**
     * The base directory where that are going to generate
     *
     * @var string
     */
    private $base_dir;

    /**
     * The generate name
     *
     * @var string
     */
    private $name;

    /**
     * GeneratorCommand constructor
     *
     * @param string $base_dir
     * @param string $name
     */
    public function __construct($base_dir, $name)
    {
        $this->base_dir = $base_dir;

        $this->name = $name;
    }

    /**
     * Check if filename is valide
     *
     * @param $filename
     */
    public function filenameIsValide($filename)
    {
        if (is_null($filename)) {
            echo Color::red('El nombre del archivo no es válido..');

            exit(1);
        }
    }

    /**
     * Check if controller exists
     *
     * @return bool
     */
    public function fileExists()
    {
        $this->filenameIsValide($this->name);

        return file_exists($this->getPath()) || is_dir($this->base_dir."/".$this->name);
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->base_dir."/".$this->name.".php";
    }

    /**
     * Check if controller exists
     *
     * @return bool
     */
    public function exists()
    {
        $this->filenameIsValide($this->name);

        if (file_exists($this->getPath())) {
            return true;
        }

        return false;
    }

    /**
     * Write file
     *
     * @param string $type
     * @param array $data
     * @return bool
     */
    public function write($type, array $data = [])
    {
        $dirname = dirname($this->name);

        if (!is_dir($this->base_dir)) {
            @mkdir($this->base_dir);
        }


        if ($dirname != '.') {
            @mkdir($this->base_dir.'/'.trim($dirname, '/'), 0777, true);

            $namespace = '\\'.str_replace('/', '\\', ucfirst(trim($dirname, '/')));
        } else {
            $namespace = '';
        }

        $classname = ucfirst(
            \Rufus\Support\Str::camel(basename($this->name))
        );

        $template = $this->makeStub($type, array_merge([
            'namespace' => $namespace,
            'className' => $classname
        ], $data));

        return file_put_contents($this->getPath(), $template);
    }

    /**
     * Stub render
     *
     * @param string $type
     * @param array $data
     * @return bool|mixed|string
     */
    public function makeStub($type, $data = [])
    {
        $content = file_get_contents(__DIR__.'/stub/'.$type.'.stub');

        foreach ($data as $key => $value) {
            $content = str_replace('{'.$key.'}', $value, $content);
        }

        return $content;
    }

    /**
     * Set writing filename
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the base directory
     *
     * @param string $base_dir
     */
    public function setBaseDirectory($base_dir)
    {
        $this->base_dir = $base_dir;
    }
}