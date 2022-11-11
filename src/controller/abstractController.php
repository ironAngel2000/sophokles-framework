<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 24.03.2019
 * Time: 09:20.
 */

namespace Sophokles\Controller;

abstract class abstractController extends abstractControllerBase
{

    use controllerRoutes;

    /**
     * Contructor.
     *
     * @return self
     */
    public function __construct()
    {
    }

}
