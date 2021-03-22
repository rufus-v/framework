<?php

/**
 * Class Route
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Routes;

use Rufus\Exception\ExceptionHandler;

class Route
{

    /**
    * Routes
    */
    private static $routes      = [];
    
    /**
    * Middlewares
    */
    private static $middlewares = [];

    /**
    * Route can redirect
    *
    * @var $redirect
    */
    private $redirect;
    
    /**
    * Base Route
    */
    private static $baseRoute   = '/';
    
    /**
    * Namespace
    */
    private static $namespace   = '';
    
    /**
    * Domain
    */
    private static $domain      = '';
    
    /**
    * IP
    */
    private static $ip          = '';
    
    /**
    * SSL
    */
    private static $ssl         = false;
    
    /**
    * Not Found Callback
    */
    private static $notFound    = '';
    
    /**
    * Groups
    */
    private static $groups      = [];
    
    /**
    * Names
    */
    private static $names       = [];
    
    /**
    * Group Counter
    */
    private static $groupped    = 0;
    
    /**
    * $url
    */
    private static $url    = [
            '{all}'     => '([^/]+)',
            '{num}'     => '([0-9]+)',
            '{alpha}'   => '([a-zA-Z]+)',
            '{alnum}'   => '([a-zA-Z0-9_-]+)'
        ];
    
       /**
        * Namespaces
        */
    private static $namespaces  = [
        'controllers'   => 'App\\Controllers',
        'middlewares'   => 'App\\Middlewares'
    ];
    
    public function __construct() {}
    private function __clone() {}
    
    
    /**
    * Add Route
    *
    * @param string $method
    * @param string $pattern
    * @param string|callable $callback
    */
    public static function route($method, $url, $callback)
    {
            if ($url == '/') {
                $url = self::$baseRoute . trim($url, '/');
            } else {
                if (self::$baseRoute == '/')
                    $url = self::$baseRoute . trim($url, '/');
                else
                    $url = self::$baseRoute . $url;
            }
    
            $uri = $url;
            $url = preg_replace('/[\[{\(].*[\]}\)]/U', '([^/]+)', $url);
            $url = '/^' . str_replace('/', '\/', $url) . '$/';
    
            if (is_callable($callback)) {
                $closure = $callback;
            } elseif (stripos($callback, '@')) {
                if (self::$namespace)
                    $closure = self::$namespaces['controllers'] . '\\' . ucfirst(self::$namespace) . '\\' . $callback;
                else
                    $closure = self::$namespaces['controllers'] . '\\' . $callback;
            }
    
            $routeArray = [
                'uri'       => $uri,
                'method'    => $method,
                'url'       => $url,
                'callback'  => $closure
            ];
    
            if (self::$namespace)
                $routeArray['namespace']    = ucfirst(self::$namespace);
    
            if (!empty(self::$middlewares))
                $routeArray['middlewares']  = self::$middlewares;
    
            if (self::$domain)
                $routeArray['domain']       = self::$domain;
    
            if (self::$ip)
                $routeArray['ip']           = self::$ip;
    
            if (self::$ssl)
                $routeArray['ssl']          = self::$ssl;
    
            self::$routes[] = $routeArray;
    }
 
    /**
    * Set name for a route
    *
    * @param string $name
    * @param array $params
    */
    public static function name($name, $params = [])
    {
            $routeKey = array_search(end(self::$routes), self::$routes);
            self::$routes[$routeKey]['name'] = $name;
    
            return new self;
    }
            
    /**
    * Execute Routing
    */
    public static function run()
    {
            $matched = 0;
    
            foreach (self::$routes as $key => $val) {
    
                if (preg_match($val['url'], self::getCurrentUri(), $params)) {
    
                    // Checking domain
                    $domainCheck    = self::checkDomain($val);
    
                    // Checking IP
                    $ipCheck        = self::checkIp($val);
    
                    // Checking SSL
                    $sslCheck       = self::checkSSL($val);
    
                    // Checking request method
                    $methodCheck    = self::checkMethod($val);
    
                    if ($domainCheck && $methodCheck && $ipCheck && $sslCheck) {
                        $matched++;
    
                        array_shift($params);
    
                        // Checking middlewares
                        if (array_key_exists('middlewares', $val)) {
                            foreach ($val['middlewares'] as $midKey => $midVal) {
                                list($controller, $method) = explode('@', $midVal['callback']);
    
                                if (class_exists($controller)) {
                                    call_user_func_array([new $controller, $method], []);
                                }
                            }
                        }
    
                        if (is_callable($val['callback'])) {
                            call_user_func_array($val['callback'], array_values($params));
                        } else if (stripos($val['callback'], '@') !== false) {
                            list($controller, $method) = explode('@', $val['callback']);
    
                            if (class_exists($controller)) {
                                call_user_func_array([new $controller, $method], array_values($params));
                            } else {
                                self::pageNotFound();
                            }
                        }
    
                        break;
                    }
    
                }
    
            }
    
            if ($matched === 0)
                self::pageNotFound();
    }

    /**
    * Add a Route Using Get Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function get(string $url, $callback)
    {
            static::route('GET', $url, $callback);
            return new static;
    }
    
    /**
    * Add a Route Using Post Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function post(string $url, $callback)
    {
            static::route('POST', $url, $callback);
            return new static;
    }

    /**
    * Add a Route Using Put Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function put(string $url, $callback)
    {
            static::route('PUT', $url, $callback);
            return new static;
    }

    /**
    * Add a Route Using Delete Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function delete(string $url, $callback)
    {
            static::route('DELETE', $url, $callback);
            return new static;
    }

    /**
    * Create a resource route for using controllers.
    * 
    * @param string $url The base route to use eg: /post
    * @param string $callback to handle route eg: PostController
    * @param string $name return route eg: name.index, name.store, name.create, etc.
    */
    public static function resource(string $url, $callback, string $name = '')
    {
            static::get($url,"$callback@index")->name("$name.index");
            static::post("$url","$callback@store")->name("$name.store");
            static::get("$url/create","$callback@create")->name("$name.create");
            static::delete("$url/{id}/delete","$callback@destroy")->name("$name.destroy");
            static::put("$url/{id}/edit","$callback@update")->name("$name.update");
            static::get("$url/{id}/edit","$callback@edit")->name("$name.edit");
            static::get("$url/{id}","$callback@show")->name("$name.show");
            return new static;
    }

    /**
    * Routing Groups
    *
    * @param callable $callback
    */
    public static function group($callback)
    {
            self::$groupped++;
    
            self::$groups[] = [
                'baseRoute'     => self::$baseRoute,
                'middlewares'   => self::$middlewares,
                'namespace'     => self::$namespace,
                'domain'        => self::$domain,
                'ip'            => self::$ip,
                'ssl'           => self::$ssl
            ];
    
            // Call the Callable
            call_user_func($callback);
    
            if (self::$groupped > 0) {
                self::$baseRoute    = self::$groups[self::$groupped-1]['baseRoute'];
                self::$middlewares  = self::$groups[self::$groupped-1]['middlewares'];
                self::$namespace    = self::$groups[self::$groupped-1]['namespace'];
                self::$domain       = self::$groups[self::$groupped-1]['domain'];
                self::$ip           = self::$groups[self::$groupped-1]['ip'];
                self::$ssl          = self::$groups[self::$groupped-1]['ssl'];
            }
    
            self::$groupped--;
    
            if (self::$groupped <= 0) {
                // Reset Base Route
                self::$baseRoute    = '/';
    
                // Reset Middlewares
                self::$middlewares  = [];
    
                // Reset Namespace
                self::$namespace    = '';
    
                // Reset Domain
                self::$domain       = '';
    
                // Reset IP
                self::$ip           = '';
    
                // Reset SSL
                self::$ssl          = false;
            }
    }
    
    /**
    * Add a Route Using Patch Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function patch($url, $callback)
    {
            static::route('PATCH', $url, $callback);
            return new static;
    }
    
    /**
    * Add a new head route
    *
    * @param  string $url
    * @param  string $callback
    * @return object
    */
    public static function head(string $url, $callback)
    {
            static::route(['HEAD'], $url, $callback);
            return new static;
    }

    /**
    * Add a Route Using Options Method
    *
    * @param string $url
    * @param string|callable $callback
    */
    public static function options($url, $callback)
    {
            static::route('OPTIONS', $url, $callback);
            return new static;
    }
    
    /**
    * Add a Route Using Multiple Methods
    *
    * @param array $methods
    * @param string $url
    * @param string|callable $callback
    */
    public static function match($methods, $url, $callback)
    {
            if (is_array($methods) || is_object($methods))
            {
                foreach ($methods as $method)
                {
                    static::route(strtoupper($method), $url, $callback);
                }
            }
    }
    
    /**
    * Set regular expression for parameters in the querystring
    *
    * @param array $expressions
    */
    public function where($expressions)
    {
            $routeKey= array_search(end(self::$routes), self::$routes);
            $pattern = self::_parseUri(self::$routes[$routeKey]['uri'], $expressions);
            $pattern = '/' . implode('/', $pattern);
            $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
    
            self::$routes[$routeKey]['pattern'] = $pattern;
    
            return new self;
    }

    /**
    * Add a new "any" route
    *
    * @param  string $url
    * @param  string $callback
    * @return object
    */
    public static function any(string $url, $callback)
    {
           static::route(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $url, $callback);
            return new static;
    }

    /**
    * Add a new redirect route
    *
    * @param  string $url
    * @param  string $redirect
    * @return object
    */
    public static function redirect(string $url, string $redirect)
    {
            static::route(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $url, $redirect, true);
            return new static;
    }
        
    /**
    * Defining Middlewares
    *
    * @param array $middlewares
    */
    public static function middleware($middlewares)
    {
            foreach ($middlewares as $middleware) {
                self::$middlewares[$middleware] = [
                    'callback'  => self::$namespaces['middlewares'] . '\\' . ucfirst($middleware) . '@handle'
                ];
            }
    
            return new self;
    }
            
    /**
    * Defining Domain
    *
    * @param string $domain
    */
    public static function domain($domain)
    {
            self::$domain = $domain;
            return new self;
    }

    /**
    * Get current method
    *
    * @return string
    */
    private function method()
    {
            return $_SERVER['REQUEST_METHOD'];
    }

    /**
    * Defining Prefix
    *
    * @param string $prefix
    */
    public static function prefix($prefix)
    {
            // Set Base Route
            self::$baseRoute    = '/' . $prefix;
    
            return new self;
    }

    /**
    * Defining Ip Address
    *
    * @param string|array $ip
    */
    public static function ip($ip)
    {
            self::$ip = $ip;
            return new self;
    }
    
    /**
    * Defining Request Scheme
    */
    public static function ssl()
    {
            self::$ssl = true;
            return new self;
    }

    /**
    * Defining Namespace
    *
    * @param string $namespace
    */
    public static function setNamespace($namespace)
    {
            // Set Namespace
            self::$namespace = $namespace;
    
            return new self;
    }
    
    /**
    * Check Domain
    *
    * @param array $params
    * @return bool
    */
    private static function checkDomain($params)
    {
            if (array_key_exists('domain', $params)) {
    
                if ($params['domain'] !== trim(str_replace('www.', '', $_SERVER['SERVER_NAME']), '/'))
                    return false;
    
                return true;
            }
    
            return true;
    }
    
    /**
    * Check Request Method
    *
    * @param array $params
    * @return bool
    */
    private static function checkMethod($params)
    {
            if ($params['method'] !== self::getRequestMethod())
                return false;
    
            return true;
    }
    
    /**
    * Check IP Address
    *
    * @param array $params
    * @return bool
    */
    private static function checkIp($params)
    {
            if (array_key_exists('ip', $params)) {
    
                if (is_array($params['ip'])) {
    
                    if (!in_array($_SERVER['REMOTE_ADDR'], $params['ip']))
                        return false;
    
                    return true;
    
                } else {
    
                    if ($_SERVER['REMOTE_ADDR'] != $params['ip'])
                        return false;
    
                    return true;
    
                }
            }
    
            return true;
    }
    
    /**
    * Check Request Scheme
    *
    * @param array $params
    * @return bool
    */
    private static function checkSSL($params)
    {
            if (array_key_exists('ssl', $params) && $params['ssl'] === true) {
    
                if ($_SERVER['REQUEST_SCHEME'] !== 'https')
                    return false;
    
                return true;
    
            }
    
            return true;
    }
    
    /**
    * Page Not Found Redirection
    */
    private static function pageNotFound()
    {
            if (self::$notFound && is_callable(self::$notFound)) {
                call_user_func(self::$notFound);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                throw new ExceptionHandler("Error", "Controller not found");
            }
    }
    
    /**
    * Get Current URI
    *
    * @return string
    */
    public static function getCurrentUri()
    {
            // Get the current Request URI and remove rewrite base path from it
            $uri = substr($_SERVER['REQUEST_URI'], strlen(self::getBasePath()));
    
            // Don't take query params into account on the URL
            if (strstr($uri, '?')) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }
    
            // Remove trailing slash + enforce a slash at the start
            return '/' . trim($uri, '/');
    }
    
    /**
    * Get Base Path
    *
    * @return string
    */
    public static function getBasePath()
    {
            $scriptName = array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1);
            return implode('/', $scriptName) . '/';
    }
    
    /**
    * Get All Request Headers
    *
    * @return array
    */
    public static function getRequestHeaders()
    {
            // If getallheaders() is available, use that
            if (function_exists('getallheaders')) {
                return getallheaders();
            }
    
            // If getallheaders() is not available, use that
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                    $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
    
            return $headers;
    }
    
    /**
    * Get Request Method
    *
    * @return string
    */
    public static function getRequestMethod()
    {
            // Take the method as found in $_SERVER
            $method = $_SERVER['REQUEST_METHOD'];
    
            // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
            if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
                ob_start();
                $method = 'GET';
            }
    
            // If it's a POST request, check for a method override header
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $headers = self::getRequestHeaders();
                if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                    $method = $headers['X-HTTP-Method-Override'];
                }
            }
    
            return $method;
    }

    /**
    * Get url based on named route
    *
    * @param string $name
    * @param array $params
    * @return string
    */
    public static function getUrl($name, $params = [])
    {
            foreach (self::$routes as $route) {
                if (array_key_exists('name', $route) && $route['name'] == $name) {
                    $uri = $route['uri'];
                    $pattern = self::_parseUri($uri, $params);
                    $pattern = implode('/', $pattern);
                    break;
                }
            }
    
            return $pattern;
    }
    
    /**
    * List All Routes
    *
    * @return array
    */
    public static function getRoutes()
    {
            return self::$routes;
    }
    
    /**
    * Parse url with parameters
    *
    * @param string $uri
    * @param array $expressions
    * @return array
    */
    private static function _parseUri($uri, $expressions = [])
    {
            $pattern    = explode('/', ltrim($uri, '/'));
            foreach ($pattern as $key => $val) {
                if(preg_match('/[\[{\(].*[\]}\)]/U', $val, $matches)) {
                    foreach ($matches as $match) {
                        $matchKey = substr($match, 1, -1);
                        if (array_key_exists($matchKey, $expressions)) {
                            $pattern[$key] = $expressions[$matchKey];
                        }
                    }
                }
            }
    
            return $pattern;
    }
    
    /**
    * Set the 404 handling function
    *
    * @param object|callable $callback
    */
    public static function error($callback)
    {
            self::$notFound = $callback;
    }
    
    public static function __callStatic($method, $args)
    {
            if ($method == 'namespace') {
                self::setNamespace($args[0]);
                return new self;
            }
    }
    
    public function __call($method, $args)
    {
            if ($method == 'namespace') {
                self::setNamespace($args[0]);
                return new self;
            }
    }
    }