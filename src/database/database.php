<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 10.03.2019
 * Time: 03:00
 */

namespace Sophokles\Database;

class database
{
    
    /** @var array querry */
    private static $querry = [];

    /**
     * Konstruktor
     * 
     * @return querry
     */
    public function __construct()
    {
        trigger_error("Please use static method ::getQuery", E_USER_ERROR);
    }

    /**
     * neue Datenbank verbinden
     * 
     * @return query
     */
    public static function getQuery($dbNr=1)
    {
        if(!isset(self::$querry[$dbNr]) || !self::$querry[$dbNr] instanceof query){
            self::$querry[$dbNr] = new query($dbNr);
        }

        return self::$querry[$dbNr];
    }
}