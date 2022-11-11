<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 24.03.2019
 * Time: 09:20.
 */

namespace Sophokles\Controller;


class controller extends abstractController
{
    use \System\Traits\systemController;

    /**
     * Contructor.
     *
     * @return self
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Override the url path.
     *
     * @param string $newPath
     */
    public function overrideUrlPath(string $newPath)
    {
        $this->setUrlPath($newPath);
    }

    /**
     * Initial function for controller.
     */
    protected function init()
    {
        parent::init();
    }

    /**
     * execute the controller class.
     */
    protected function run()
    {
        parent::run();
    }
}
