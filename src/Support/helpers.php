<?php

/**
 * Helpers
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Rufus\Support\View;
use Rufus\Support\Env;
use Rufus\Support\Config;
use Rufus\Http\Request;

if (!function_exists('config')) {

    /**
     * Get Config Parameters
     *
     * @param string $params
     * @return mixed
     */
    function config($key = null)
    {
        return Config::instance()->get($key);

    }
}


if (!function_exists('dep')) {

    /**
     * Format the code.
     */
    function dep($data, $stop = false)
    {
        echo  '<pre>';
         print_r($data);
        echo '</pre>';

        if ($stop === true)
        die();
    }
}

if (!function_exists('clean')) {

    /**
     * Delete excess spaces between words.
     */
    function clean($cadena)
    {
        $string = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $cadena);
        $string = trim($string); //Elimina espacios en blanco al pricio y al final
        $string = stripslashes($string); //Elimina las \ invertidas
        $string = str_ireplace('<script>', '', $string);
        $string = str_ireplace('</script>', '', $string);
        $string = str_ireplace('<script src>', '', $string);
        $string = str_ireplace('<script type=>', '', $string);
        $string = str_ireplace('SELECT * FROM', '', $string);
        $string = str_ireplace('DELETE FROM', '', $string);
        $string = str_ireplace('INSERT INTO', '', $string);
        $string = str_ireplace('SELECT count(*) FROM', '', $string);
        $string = str_ireplace('DROP TABLE', '', $string);
        $string = str_ireplace("OR '1'='1", '', $string);
        $string = str_ireplace('OR "1"="1"', '', $string);
        $string = str_ireplace('OR `1`=`1`', '', $string);
        $string = str_ireplace('is null; --', '', $string);
        $string = str_ireplace('is null; __', '', $string);
        $string = str_ireplace("LIKE '", '', $string);
        $string = str_ireplace('LIKE "', '', $string);
        $string = str_ireplace('LIKE `', '', $string);
        $string = str_ireplace("OR 'a'='a", '', $string);
        $string = str_ireplace('OR "a"="a', '', $string);
        $string = str_ireplace('OR `a`=`a`', '', $string);
        $string = str_ireplace('OR `a`=`a`', '', $string);
        $string = str_ireplace('--', '', $string);
        $string = str_ireplace('^', '', $string);
        $string = str_ireplace('[', '', $string);
        $string = str_ireplace(']', '', $string);
        $string = str_ireplace('==', '', $string);
        return $string;
    }
}

if (!function_exists('asset')) {

    /**
     * Get assets
     *
     * @param string $file
     * @return string
     */
    function asset($file, $version = null)
    {
        if (!file_exists('../public/' . $file))
            throw new Rufus\Exception\ExceptionHandler('Archivo no encontrado', '<b>Asset : </b> ' . $file);

        return (is_null($version)) ?  url(str_replace('public/', '',$file)) :  '../public/' . $file . '?' . $version;
    }
}

if (!function_exists('storage_path')) {
   
    /**
    * Get the path to the storage folder.
    *
    * @param  string  $path
    * @return string
    */
   function storage_path($path = ''){
       return  '../storage/'. $path;
   }
}

if (!function_exists('redirect')) {

    /**
     * Redirect to specified url
     *
     * @param string $url
     * @param integer $delay
     * @return void
     */
    function redirect($url = null, $delay = 0)
    {
        if ($delay > 0) {
            header('Refresh:' . $delay . ';url=' . $url);
        } else {
            header('Location:' . $url);
        }
    }
}

if (!function_exists('env')) {
    
    /**
     * Get the value of the environment variable
     *
     * @param string $key
     * @param string $default Defaults
     *
     * @return array | bool | false | null | string
     */
    function env($key, $default = null)
    {
        return Env::instance()->get($key, $default);

    }
}

if (!function_exists('view')) {

    /**
     * Include View File
     * @param $file string
     * @param $vars array
     * @param $cache boolean
     * @return void
     */
    function view($view, $data = [], $cache = false)
    {
        return view::instance()->render($view, $data, $cache);
    }
}

if (!function_exists('url')) {

    /**
     * Make internal link
     *
     * @return string
     */
    function url($url)
    {
        return BASE_URL . '/' . $url;
    }
}

if (!function_exists('request')) {

    /**
     * Represents the Request class
     * @return \Rufus\Http\Request
     */
    function request($params = null)
    {
        $requestMethod = Request::instance()->getRequestMethod();

        switch ($requestMethod) {
            case 'GET'      : $request = Request::instance()->get(); break;
            case 'POST'     : $request = Request::instance()->post(); break;
            case 'PUT'      : $request = Request::instance()->put(); break;
            case 'PATCH'    : $request = Request::instance()->patch(); break;
            case 'DELETE'   : $request = Request::instance()->delete(); break;
            default         : $request = Request::instance()->all();
        }

        if (is_null($params))
            return $request;

        if (is_array($params)) {
            foreach ($params as $param) {
                $data[$param] = $request[$param];
            }
            return $data;
        }

        return $request[$params];
    }
}

if (!function_exists('route')) {

    /**
     * Get URL of the Named Route
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    function route($name, $params = [])
    {
        return url(Rufus\Routes\Route::getUrl($name, $params));
    }
}

if (!function_exists('slug')) {

    /**
     * URL Slug Generator
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    function slug($str, $sep = '-')
    {
        return \Rufus\Support\Str::slug($str, $sep);
    }
}