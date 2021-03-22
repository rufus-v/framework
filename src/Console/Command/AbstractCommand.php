<?php

/**
 * Class AbstractCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\ArgOption;
use Rufus\Console\ConsoleInformation;
use Rufus\Console\Setting;

class AbstractCommand
{
    use ConsoleInformation;

     /**
     * Store dirname
     *
     * @var Setting
     */
    protected $setting;

    /**
     * The application namespace
     *
     * @var array
     */
    protected $namespaces;

     /**
     * The Arg Option instance
     *
     * @var ArgOption
     */
    protected $arg;

    /**
     * AbstractCommand constructor
     *
     * @param Setting $setting
     * @param ArgOption $arg
     *
     * @return void
     */
    public function __construct(Setting $setting, ArgOption $arg)
    {
        $this->setting = $setting;

        $this->arg = $arg;

        $this->namespaces = $setting->getNamespaces();
    }
}