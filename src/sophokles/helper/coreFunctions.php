<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 31.03.2019
 * Time: 05:55.
 */

namespace Sophokles\Sophokles\Helper;

use System\Config\sysconfig;

final class coreFunctions
{
    private function __construct()
    {
    }

    /**
     * Ausgabe n Zeichen vom Anfang eines Strings.
     *
     * @param string $string
     * @param int    $lenght
     * @param bool   $wholeWord
     *
     * @return string
     */
    public static function left($string, $lenght, $wholeWord = false)
    {
        if ($wholeWord !== true) {
            $wert = substr(trim($string), 0, $lenght);
        } else {
            $wert = substr(trim($string), 0, $lenght);
            $fpos = strripos($wert, ' ');
            $wert = substr(trim($string), 0, $fpos);
        }

        return $wert;
    }

    /**
     * Ausgabe n Zeichen von Ende eines Strings.
     *
     * @param string $string
     * @param int    $lenght
     *
     * @return string
     */
    public static function right($string, $lenght)
    {
        $len = strlen(trim($string));
        $wrechts = $len - $lenght;
        $wert = substr(trim($string), $wrechts, $len);

        return $wert;
    }

    /**
     * Ausgabe n Zeichen eines Strings ab Position1 bis Position 2.
     *
     * @param string $string
     * @param int    $pos1
     * @param int    $pos2
     *
     * @return string
     */
    public static function mid($string, $pos1, $pos2)
    {
        $wert = substr(trim($string), $pos1, $pos2);

        return $wert;
    }

    /**
     * HTTP Protocol defined status codes.
     *
     * @param int $num
     */
    public static function HTTPStatus($num, $redirect = false)
    {
        $http[100] = '100 Continue';
        $http[101] = '101 Switching Protocols';
        $http[200] = '200 OK';
        $http[201] = '201 Created';
        $http[202] = '202 Accepted';
        $http[203] = '203 Non-Authoritative Information';
        $http[204] = '204 No Content';
        $http[205] = '205 Reset Content';
        $http[206] = '206 Partial Content';
        $http[300] = '300 Multiple Choices';
        $http[301] = '301 Moved Permanently';
        $http[302] = '302 Found';
        $http[303] = '303 See Other';
        $http[304] = '304 Not Modified';
        $http[305] = '305 Use Proxy';
        $http[307] = '307 Temporary Redirect';
        $http[400] = '400 Bad Request';
        $http[401] = '401 Unauthorized';
        $http[402] = '402 Payment Required';
        $http[403] = '403 Forbidden';
        $http[404] = '404 Not Found';
        $http[405] = '405 Method Not Allowed';
        $http[406] = '406 Not Acceptable';
        $http[407] = '407 Proxy Authentication Required';
        $http[408] = '408 Request Time-out';
        $http[409] = '409 Conflict';
        $http[410] = '410 Gone';
        $http[411] = '411 Length Required';
        $http[412] = '412 Precondition Failed';
        $http[413] = '413 Request Entity Too Large';
        $http[414] = '414 Request-URI Too Large';
        $http[415] = '415 Unsupported Media Type';
        $http[416] = '416 Requested range not satisfiable';
        $http[417] = '417 Expectation Failed';
        $http[500] = '500 Internal Server Error';
        $http[501] = '501 Not Implemented';
        $http[502] = '502 Bad Gateway';
        $http[503] = '503 Service Unavailable';
        $http[504] = '504 Gateway Time-out';

        ob_clean();

        header($_SERVER['SERVER_PROTOCOL'].' '.$http[$num]);
        header('Status: '.$http[$num]);

        if ($redirect === true) {
            $aufruf = '';
            if (defined('BASIS')) {
                $aufruf = BASIS;
            }

            if (coreFunctions::right($aufruf, 1) == '/') {
                $len = ((int) strlen($aufruf));
                --$len;
                $aufruf = self::right($aufruf, $len);
            }

            $aufruf .= 'errorpage/'.urlencode($http[$num]).'/';

            $aufruf = self::getHttpUrl(true).$aufruf;

            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $aufruf);
            curl_exec($handle);
            curl_close($handle);
            exit;
        }
    }

    const HEADER_TYPE_JSON = 1;
    const HEADER_TYPE_PDF = 2;
    const HEADER_TYPE_JPEG = 3;
    const HEADER_TYPE_PNG = 4;
    const HEADER_TYPE_XML = 5;

    /**
     * Output Header Content Type.
     *
     *
     * @param int    $type
     * @param bool   $download
     * @param string $filename
     */
    public static function getHeader(int $type, bool $download = false, string $filename = '')
    {
        switch ($type) {
            case coreFunctions::HEADER_TYPE_JSON:
                header('Content-Type: application/json');
                break;
            case coreFunctions::HEADER_TYPE_PDF:
                header('Content-Type: application/pdf');
                break;
            case coreFunctions::HEADER_TYPE_JPEG:
                header('Content-Type: image/jpeg');
                break;
            case coreFunctions::HEADER_TYPE_PNG:
                header('Content-Type: image/png');
                break;
            case coreFunctions::HEADER_TYPE_XML:
                header('Content-Type: text/xml');
                break;
            default:
                break;
        }

        if ($download === true && trim($filename) != '') {
            header('Content-Disposition: attachment; filename="'.$filename.'"');
        }
    }

    /**
     * Passwort verschluesseln.
     *
     * derzeit mit einer SHA 512 Verschluessleung
     *
     * @param string $passwort
     *
     * @return string
     */
    public static function crypt_password(string $passwort)
    {
        $passwort = crypt($passwort, '$6$'.sysconfig::getPwCode());

        return $passwort;
    }

    
    private static function intval32bits($value){
        $value = ((int) $value & 0xFFFFFFFF);

        if ($value & 0x80000000)
            $value = -((~$value & 0xFFFFFFFF) + 1);

        return $value;
    }


    /**
     * Pruefung ob eine Checksumme einen binaeren wert enthaelt
     *
     * @param integer $checksumme
     * @param integer $pruefwert
     * @return boolean
     */
    public static function checkBinary($checksumme,$pruefwert,$test=false){

        if($test===true){
            var_dump(self::intval32bits($checksumme));
            var_dump(self::intval32bits($pruefwert));
            var_dump((self::intval32bits($checksumme) & self::intval32bits($pruefwert)));
        }

        return ((self::intval32bits($checksumme) & self::intval32bits($pruefwert))== self::intval32bits($pruefwert));
    }


}
