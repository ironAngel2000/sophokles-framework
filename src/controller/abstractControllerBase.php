<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 24.03.2019
 * Time: 09:20.
 */

namespace Sophokles\Controller;

use Sophokles\Module\abstractModule;

abstract class abstractControllerBase
{
    /** @var string $baseRoute */
    protected $baseRoute;

    /** @var string $urlPath */
    protected $urlPath;

    /** @var array $moduleParameter */
    protected $moduleParameter = [];

    /** @var abstractModule $objModule */
    protected $objModule;

    /**
     * Initial function for controller.
     */
    abstract protected function init();

    /**
     * execute the controller class.
     */
    abstract protected function run();

    /**
     * Set the base route path of the controller.
     *
     * @param string $newBase
     */
    public function setBaseRoute(string $newBase)
    {
        $this->baseRoute = $newBase;
    }

    /**
     * get the base route path of the controller.
     *
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->baseRoute;
    }

    /**
     * Set the browser path.
     *
     * @param string $newPath
     */
    public function setUrlPath(string $newPath)
    {
        $this->urlPath = $newPath;
    }

    /**
     * get the browser path.
     *
     * @return string
     */
    public function getUrlPath()
    {
        return $this->urlPath;
    }

    /**
     * executes the Contorller
     */
    final public function execute()
    {
        $this->init();

        if (method_exists($this, 'traitInit')) {
            $this->traitInit();
        }

        $this->run();
    }
}
