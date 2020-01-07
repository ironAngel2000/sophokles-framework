<?php

namespace Sophokles\Controller;

use Sophokles\Controller\abstractController;
use Sophokles\Sophokles\Helper\coreFunctions;

trait controllerRoutes
{
    /** @var string $fileBase */
    protected static $fileBase;

    /** @var string $browserUrl */
    protected static $browserUrl;

    /** @var string $routingPath */
    protected static $routingPath;

    /** @var array $routes */
    protected static $routes = [];


    /**
     * Initial function for controller.
     */
    protected function init()
    {
        self::$fileBase = __DIR__.'/';
        $this->setUrlPath();

        if(method_exists($this,'modinit')){
            $this->modinit();
        }
    }

    /**
     * execute the controller class.
     */
    protected function run()
    {

        if (count(self::$routes)) {

            foreach (self::$routes as $arrRoute) {
                $path = trim($arrRoute[0]);

                    $browsUrl = self::$routingPath;
                    if(trim($browsUrl)===''){
                        $browsUrl = '/';
                    }

                    if(coreFunctions::left($path,1)!=='/'){
                        $path = '/'.$path;
                    }
                    if(coreFunctions::right($path,1)!=='/'){
                        $path = $path.'/';
                    }

                    if (substr($browsUrl, 0, strlen($path)) === $path) {
                        $objRoute = new $arrRoute[1];

                        if ($objRoute instanceof abstractControllerBase) {

                            $objRoute->setBaseRoute($path);
                            $objRoute->setUrlPath(self::$browserUrl);
                            $objRoute->execute();
                        }
                        else{
                            trigger_error('No controller object defined in "'.$arrRoute[1].'"',E_USER_ERROR);
                        }
                        break;
                    }
            }
        }
        else{
            if(method_exists($this,'modrun')){
                $this->modrun();
            }

        }
    }

    /**
     * get the Filebase.
     *
     * @return string
     */
    public function getFileBase()
    {
        return self::$fileBase;
    }

    /**
     * read the browser path, path can override.
     *
     * @ var string $pathOverride
     */
    public function setUrlPath(string $pathOverride = '')
    {
        if (trim($pathOverride) !== '') {
            self::$browserUrl = trim($pathOverride);
        } else {

            if (isset($_SERVER['REDIRECT_SCRIPT_URL']) && trim($_SERVER['REDIRECT_SCRIPT_URL']) != '') {
                self::$browserUrl = trim($_SERVER['REDIRECT_SCRIPT_URL']);
            } elseif (isset($_SERVER['REDIRECT_URL']) && trim($_SERVER['REDIRECT_URL']) != '') {
                self::$browserUrl = trim($_SERVER['REDIRECT_URL']);
            } else {
                self::$browserUrl = trim($_SERVER['REQUEST_URI']);
            }
        }

        self::$browserUrl = str_replace('//', '/', self::$browserUrl);

        self::$browserUrl = trim(self::$browserUrl);

        if (self::$browserUrl !== '/' && coreFunctions::right(self::$browserUrl, 1) !== '/') {
            self::$browserUrl .= '/';
        }

        self::$routingPath = self::$browserUrl;

    }

    /**
     * get the actual url.
     *
     * @return string
     */
    public function getAktUrl()
    {
        return self::$browserUrl;
    }

    /**
     * register routing table to a new controller object.
     *
     * @param string $path
     * @param string $controllObject
     */
    public static function registerRoutePath(string $path, string $controllObject)
    {
        $pathFound = false;
        if(count(self::$routes)){
            foreach(self::$routes as $key=>$arrRoute){
                if(trim($arrRoute[0])===trim($path)){
                    $pathFound = true;
                    self::$routes[$key] = [$path, $controllObject];
                }
            }
        }

        if($pathFound === false){
            if(trim($path)==='/'){
                self::$routes[0] = [$path, $controllObject];
            }
            else{
                $key = count(self::$routes) + 1;
                self::$routes[$key] = [$path, $controllObject];
            }
        }

        \krsort(self::$routes);
    }

    /**
     * route to path
     */
    public static function route(string $path, int $httpStatus = 301)
    {
        coreFunctions::HTTPStatus($httpStatus);
        header('Refresh: 0, url = '.$path);
    }
}
