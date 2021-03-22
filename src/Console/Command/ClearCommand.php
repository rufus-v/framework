<?php

/**
 * Class ClearCommand
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Console\Command;

use Rufus\Console\Color;
use Rufus\Console\ConsoleInformation;

class ClearCommand extends AbstractCommand
{
    use ConsoleInformation;
    
    /**
     * Clear cache
     *
     * @param string $action
     *
     * @return void
     */
    public function make($action)
    {
        if (!in_array($action, ['view', 'cache', 'session', 'log', 'all'])) {
            $this->throwFailsCommand(' Borrar objetivo no válido', 'clear help');
        }

        $this->clear($action);

        echo Color::green("$action cache clear.");
    }

    /**
     * Clear action
     *
     * @param string $action
     *
     * @return void
     */
    private function clear($action)
    {
        if ($action == 'all') {
            $this->unlinks($this->setting->getVarDirectory().'/view/*/*');
            foreach (glob($this->setting->getVarDirectory().'/view/*') as $dirname) {
                @rmdir($dirname);
            }

            $this->unlinks($this->setting->getVarDirectory().'/cache/*');
            $this->unlinks($this->setting->getVarDirectory().'/session/*');
            $this->unlinks($this->setting->getVarDirectory().'/logs/*');

            return;
        }

         if ($action == 'view') {
             $this->unlinks($this->setting->getVarDirectory().'/view/*/*');

             foreach (glob($this->setting->getVarDirectory().'/view/*') as $dirname) {
                 @rmdir($dirname);
             }

             return;
         }

        if ($action == 'cache') {
            $this->unlinks($this->setting->getVarDirectory().'/cache/*');

            return;
        }

        // if ($action == 'session') {
        //     $this->unlinks($this->setting->getVarDirectory().'/session/*');

        //     return;
        // }

        if ($action == 'log') {
            $this->unlinks($this->setting->getVarDirectory().'/logs/*');

            return;
        }
    }

    /**
     * Delete file
     *
     * @param  string $dirname
     *
     * @return void
     */
    private function unlinks($dirname)
    {
        $glob = glob($dirname);

        foreach ($glob as $item) {
            if (!preg_match('/.gitkeep/', $item)) {
                @unlink($item);
            }
        }
    }
}