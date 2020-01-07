<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 09.03.2019
 * Time: 07:00.
 */

namespace Sophokles\Database;

use PDO;
use PDOException;

class query
{
    /** @var object dbCon */
    private $dbCon;

    /** @var object dbConfig */
    private $dbConfig;

    /** @var bool $debug */
    protected static $debug = false;

    /** @var float $starttime */
    protected static $starttime = 0;

    /** @var mixed $lastId */
    public static $lastId;

    public function __construct($dbNr = 1)
    {
        if ($dbNr !== 1) {
            $class = '\System\Config\db'.$dbNr.'.php';
            $objConfig = new $class();
        } else {
            $objConfig = new \System\Config\db();
        }
        if (!$objConfig instanceof dbconfig) {
            trigger_error("Config Class must be instace of \Sophokles\Database\dbconfig", E_USER_ERROR);
        }

        $this->dbConfig = &$objConfig;

        $dsn = 'mysql:host='.$this->dbConfig->getHost().';';
        if (trim($this->dbConfig->getSocket()) !== '') {
            $dsn .= 'port='.$this->dbConfig->getSocket().';';
        } else {
            $dsn .= 'port='.$this->dbConfig->getPort().';';
        }
        $dsn .= 'dbname='.$this->dbConfig->getDatabase().'';

        $this->dbCon = new PDO($dsn, $this->dbConfig->getUser(), $this->dbConfig->getPassword(), array(PDO::ATTR_PERSISTENT => false));
        $this->dbCon->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }

    /**
     * Datenbankabfrage in der Datenbank ausfuehren.
     *
     * @param statement $objStatement
     *
     * @return \PDOStatement
     */
    public function execute(statement $objStatement)
    {
        $ret = false;

        try {
            $stmt = $this->dbCon->prepare($objStatement->getStatment());
            if (!$stmt) {
                trigger_error(PDOStatement::errorInfo()[2], E_ERROR);
            }

            $arrStatement = null;

            if (count($objStatement->getArguments()) > 0) {
                $arrStatement = $objStatement->getArguments();

                foreach ($objStatement->getArguments() as $col => $value) {
                    switch (gettype($value)) {
                        case 'integer':
                            $stmt->bindParam(':'.$col, $arrStatement[$col], PDO::PARAM_INT);
                            break;
                        default:
                            $stmt->bindParam(':'.$col, $arrStatement[$col]);
                            break;
                    }
                }
            }

            $stmt->execute();
            $ret = $stmt;
        } catch (PDOException $e) {
            $failure = sprintf('Invalid query: %s', PDOStatement::errorInfo()[2])."\r\n";
            trigger_error($failure, E_ERROR);
        }

        if (query::getDebugMode()) {
            echo '<pre>';
            $stmt->debugDumpParams();
            echo '</pre>';
        }

        self::$lastId = $this->dbCon->lastInsertId();

        return $ret;
    }

    /**
     * Verbindungsobjekt zurueckgeben.
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->dbCon;
    }

    /**
     * Debugmodus einschalten.
     */
    public static function enableDebug()
    {
        self::$debug = true;
        self::$starttime = microtime(true);
    }

    /**
     * Debugmodus ausschalten.
     */
    public static function disableDebug()
    {
        self::$debug = false;
    }

    /**
     * Debugmodus abruen.
     *
     * @return bool
     */
    public static function getDebugMode()
    {
        return self::$debug;
    }

    /**
     * Startzeit abfrufen.
     *
     * @return float
     */
    public static function getStartTieme()
    {
        return self::$starttime;
    }
}
