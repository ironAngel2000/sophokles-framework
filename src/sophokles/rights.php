<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 06.03.2019
 * Time: 07:40.
 */

namespace Sophokles\Sophokles;

use Sophokles\Sophokles\Helper\coreFunctions;

final class rights
{
    private static $arrRights = [];
    private static $index = 0;
    private static $arrRightNames = [];
    
    
    private function __construct()
    {

    }

    public static function registerRight(string $name, string $description = '')
    {
        $name = strtoupper($name);
        $pow = pow(2,self::$index);
        self::$arrRights[$name] = $pow;
        self::$arrRightNames[$name] = $description;
        define($name, $pow);
        self::$index++;
    }

    public static function getRights() : array
    {
        return self::$arrRights;
    }

    public static function checkRight(int $checksum, string $name) : bool
    {
        $name = strtoupper($name);
        $ret = false;

        if(isset(self::$arrRights[$name]) && coreFunctions::checkBinary($checksum,self::$arrRights[$name])){
            $ret = true;
        }

        return $ret;
    }

    public static function getRightName($right) : string
    {
        $right = strtoupper($right);

        return self::$arrRightNames[$right];
    }

}