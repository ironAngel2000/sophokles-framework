<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 31.03.2019
 * Time: 07:35.
 */

namespace Sophokles\Sophokles\Helper;

use Sophokles\Translation\lang;



class twigFunctions extends \Twig_Extension
{
    public function __construct()
    {
    }

    public function getFunctions()
    {
        return [
            'instanceof' => new \Twig_Function('isInstanceof', function ($object, $instance) {
                return is_a($object, $instance, true) ? true : false;
            }),
            'getLang' => new \Twig_Function('getLang', function ($string) {
                return lang::get($string);
            }),
            'decodeJson' => new \Twig_Function('decodeJson', function ($string) {
                return \json_decode($string, true);
            }),
            'encodeJson' => new \Twig_Function('encodeJson', function ($string) {
                return \json_encode($string);
            }),
            'isArray' => new \Twig_Function('isArray', function ($var) {
                return \is_array($var);
            }),
        ];
    }
}
