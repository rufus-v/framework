<?php

/**
 * Class Database
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Kernel;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public function __construct()
    {
         // Eloquent ORM
         $capsule = new Capsule();
         $capsule->addConnection(config('database'));
         $capsule->bootEloquent();
    }
}