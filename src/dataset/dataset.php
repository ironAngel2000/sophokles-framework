<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50.
 */

namespace Sophokles\Dataset;

use Sophokles\Database\querybuilder;
use Sophokles\Database\database;
use Sophokles\Database\statement;
use Sophokles\Database\tablescheme;
use Sophokles\Database\query;

abstract class dataset
{
    /** @var array $arrDbResult */
    private $arrDbResult;

    /** @var array $arrFields */
    private $arrFields = [];

    /** @var array $setSortParameter */
    protected $setSortParameter;

    /** @var string $table */
    protected $table;

    /** @var int $cloneIndex */
    protected $cloneIndex = 0;

    /** @var int $databasenr */
    private $databasenr = 1;

    /** @var tableschme $objTableSchem */
    protected $objTableScheme;

    /** @var sorting $sorting */
    protected $objSorting;

    abstract protected function defineTableScheme();

    abstract protected function defineSorting();

    abstract protected function initClass();

    /**
     * Constructor.
     *
     * @return self
     */
    final public function __construct()
    {
        $this->objSorting = new sorting();
        $this->initClass();
        $this->__init();

    }

    private function __init()
    {
        $this->defineSorting();
        $this->defineTableScheme();
        if ($this->objTableScheme instanceof tablescheme) {
            $arrCols = $this->objTableScheme->getColumns();
            if (count($arrCols)) {
                foreach ($arrCols as $arrCol) {
                    $name = $arrCol['n'];

                    $type = explode(' ', $arrCol['o'])[0];
                    $type = explode('(', $type)[0];
                    $type = strtolower(trim($type));

                    switch ($type) {
                        case 'int':
                            //nobreak;
                        case 'tinyint':
                            //nobreak;
                        case 'smalint':
                            //nobreak;
                        case 'mediumint':
                            //nobreak;
                        case 'bigint':
                            $this->{$name} = new typeInt();
                            break;
                        case 'decimal':
                            //nobreak;
                        case 'float':
                            //nobreak;
                        case 'double':
                            //nobreak;
                        case 'real':
                            $this->{$name} = new typeFloat();
                            break;
                        case 'json':
                            $this->{$name} = new typeJson();
                            break;
                        default:
                            $this->{$name} = new typeText();
                            break;
                    }

                    $this->arrFields[] = $name;
                }
            }
        }
    }

    public function __clone()
    {
        if (isset($this->sorting)) {
            $this->sorting = clone $this->sorting;
        }
        $this->objTableScheme = clone $this->objTableScheme;

        foreach ($this->arrFields as $name) {
            $this->{$name} = clone $this->{$name};
        }

        ++$this->cloneIndex;
    }

    public function tableSchemeUpdate()
    {
        if ($this->objTableScheme instanceof tablescheme) {
            $this->objTableScheme->update();
        }
    }

    /**
     * Override the tablename.
     *
     * @param string $newTablename
     */
    public function overrideTable(string $newTablename)
    {
        $this->tabele = $newTablename;
    }

    /**
     * Select new Database for the abstract module.
     *
     * @parm int $newNr
     */
    protected function setDatabase(int $newNr = 1)
    {
        $this->databasenr = $newNr;
    }

    protected function readDbResult(statement $objStatment)
    {
        $query = database::getQuery($this->databasenr);
        $pdo = $query->execute($objStatment);

        $found = 0;

        if ($pdo instanceof \PDOStatement) {
            $this->arrDbResult = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $found = count($this->arrDbResult);
        }

        if ($found) {
            $this->moveFirst();
        } else {
            $this->clearFields();
        }

        return $found;
    }

    private function writeDataSetRowtoFields()
    {
        $key = key($this->arrDbResult);
        foreach ($this->arrFields as $colName) {
  
            if(isset($this->arrDbResult[$key][$colName])){

                $class = get_class($this->{$colName});
                
                if(stristr($class,'typeText')){
                    $this->arrDbResult[$key][$colName] = trim($this->arrDbResult[$key][$colName]);
                }
                if(stristr($class,'typeJson')){
                    $this->arrDbResult[$key][$colName] = trim($this->arrDbResult[$key][$colName]);
                }
                if(stristr($class,'typeInt')){
                    $this->arrDbResult[$key][$colName] = (int) ($this->arrDbResult[$key][$colName]);
                }
                if(stristr($class,'typeFloat')){
                    $this->arrDbResult[$key][$colName] = (float) ($this->arrDbResult[$key][$colName]);
                }

                $this->{$colName} = new $class($this->arrDbResult[$key][$colName]);
            }
        }
    }

    /**
     * Jump to first Datarow in Dataset
     * returns false when no Datarow fond.
     *
     * @return bool
     */
    public function moveFirst()
    {
        if (is_array($this->arrDbResult)) {
            if (reset($this->arrDbResult) !== false) {
                $this->writeDataSetRowtoFields();

                return true;
            } else {
                $this->clearFields();

                return false;
            }
        } else {
            $this->clearFields();

            return false;
        }
    }

    /**
     * Jump to next Datarow in Dataset
     * returns false when no Datarow fond.
     *
     * @return bool
     */
    public function moveNext()
    {
        if (is_array($this->arrDbResult)) {
            if (next($this->arrDbResult) !== false) {
                $this->writeDataSetRowtoFields();

                return true;
            } else {
                $this->clearFields();

                return false;
            }
        }

        return false;
    }

    /**
     * Jump to previous Datarow in Dataset
     * returns false when no Datarow fond.
     *
     * @return bool
     */
    public function movePrev()
    {
        if (is_array($this->arrDbResult)) {
            if (prev($this->arrDbResult) !== false) {
                $this->writeDataSetRowtoFields();

                return true;
            } else {
                $this->clearFields();

                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Clear the Values of the current Fields.
     */
    protected function clearFields()
    {
        foreach ($this->arrFields as $colName) {
            $this->{$colName}->clearValue();
        }
    }

    /**
     * Get all entries. Return the number of found Results.
     *
     * @var array
     *
     * @return int
     */
    public function getEntries(array $primaryValues = [])
    {
        $queryConf = new querybuilder($this->table);

        if (count($primaryValues)) {
            $priFields = $this->objTableScheme->getPrimaryFields();

            if (count($primaryValues) != count($priFields)) {
                trigger_error('The values array is not similar to the primary fields of the table "'.$this->table.'": '.print_r($priFields, true), E_USER_ERROR);
            }

            foreach ($priFields as $key => $colName) {
                $queryConf->setCondition($colName, $primaryValues[$key]);
                $queryConf->setSort($colName);
            }
        }

        return $this->readDbResult($queryConf->getQuerySelect());
    }

    /**
     * Get all entries. Return the number of found Results.
     *
     * @var array
     *
     * @return int
     */
    public function getEntriesbyParam(array $parameter = [], array $arrSort = [])
    {
        $queryConf = new querybuilder($this->table);

        if (count($parameter)) {
            foreach ($parameter as $colName => $value) {
                $queryConf->setCondition($colName, $value);
                $queryConf->setSort($colName);
            }
        }

        if (count($arrSort)) {
            foreach ($arrSort as $colName => $direction) {
                $queryConf->setSort($colName, $direction);
            }
        }

        return $this->readDbResult($queryConf->getQuerySelect());
    }

    /**
     * Get the primary key fields of the table.
     *
     * @return array
     */
    public function getPrimaryFields()
    {
        return $this->objTableScheme->getPrimaryFields();
    }

    public function getRecord2Array(): array
    {
        $ret = [];

        foreach ($this->objTableScheme->getColumns() as $arrCol) {
            $ret[$arrCol['n']] = $this->{$arrCol['n']}->getVal();
        }

        return $ret;
    }

    public function save($noSort = false)
    {
        $tmpObj = clone $this;

        if(trim($this->uniqueid->getVal())===''){
            $this->uniqueid->setVal(\uniqid('',false));
        }

        $priFields = $this->objTableScheme->getPrimaryFields();

        $arrPVal = [];
        foreach ($priFields as $colName) {
            $arrPVal[] = $this->{$colName}->getVal();
        }

        $queryConf = new querybuilder($this->table);

        $sortField = $this->objSorting->getSortColumn();
        if (trim($sortField) !== '') {
            if ($this->{$sortField}->getVal() == 0) {
                $this->{$sortField}->setVal(99999999);
            }
        }

        if ($tmpObj->getEntries($arrPVal) == 0) {
            $arrPVal = [];
            foreach ($this->arrFields as $colName) {
                $arrPVal[$colName] = $this->{$colName}->getVal();
            }

            if ($this->objTableScheme->isAutoincrement()) {
                foreach ($priFields as $colName) {
                    unset($arrPVal[$colName]);
                }
            }

            $queryConf->getQueryInsert($arrPVal);

            database::getQuery()->execute($queryConf->getQueryInsert($arrPVal));

            if ($this->objTableScheme->isAutoincrement()) {
                $this->{$priFields[0]}->setVal(query::$lastId);
            }
        } else {
            $arrPVal = [];
            foreach ($this->arrFields as $colName) {
                $arrPVal[$colName] = $this->{$colName}->getVal();
            }

            foreach ($priFields as $key => $colName) {
                $queryConf->setCondition($colName, $this->{$colName}->getVal());
                $queryConf->setSort($colName);
            }

            database::getQuery()->execute($queryConf->getQueryUpdate($arrPVal));
        }

        if (trim($sortField) !== '' && $noSort !== true) {
            $start_sort = true;

            $sortFunkt = $this->objSorting->getListFunction();
            if (!method_exists($this, $this->objSorting->getListFunction())) {
                $start_sort = false;
            }

            if (count($this->objSorting->getReferenceColumns()) == 0) {
                $start_sort = false;
            } else {
                foreach ($this->objSorting->getReferenceColumns() as $colname) {
                    if ($colName !== null) {
                        if (!property_exists($this, $colName)) {
                            $start_sort = false;
                        }
                    }
                }
            }

            if (!property_exists($this, $sortField)) {
                $start_sort = false;
            }

            if ($start_sort === true) {
                $this->sort_position();
            }
        }
    }

    private function sort_position()
    {
        $tmpObj = clone $this;

        $arrParam = [];
        foreach ($this->objSorting->getReferenceColumns() as $fieldname) {
            if ($fieldname == null) {
                $arrParam[] = null;
            } else {
                $arrParam[] = $this->{$fieldname}->getVal();
            }
        }

        $sortField = $this->objSorting->getSortColumn();
        $arrPrimary = $this->objTableScheme->getPrimaryFields();

        $arrFunkt = array($tmpObj, $this->objSorting->getListFunction());

        if (call_user_func_array($arrFunkt, $arrParam)) {
            do {
                $isSame = true;
                foreach ($arrPrimary as $field) {
                    if ($this->{$field}->getVal() !== $tmpObj->{$field}->getVal()) {
                        $isSame = false;
                    }
                }

                if ($isSame == false) {
                    $setReihe = false;
                    if ($tmpObj->{$sortField}->getVal() >= $this->{$sortField}->getVal()) {
                        $setReihe = true;
                    }

                    if ($setReihe == true) {
                        $aktReihe = $tmpObj->{$sortField}->getVal();
                        ++$aktReihe;
                        $tmpObj->{$sortField}->setVal($aktReihe);
                        $tmpObj->save(true);
                    }
                }
            } while ($tmpObj->moveNext());
        }

        unset($tmpObj);

        $tmpObj = clone $this;

        $arrFunkt = array($tmpObj, $this->objSorting->getListFunction());

        if (call_user_func_array($arrFunkt, $arrParam)) {
            $aktR = 0;
            //*
            do {
                ++$aktR;
                $tmpObj->{$sortField}->setVal($aktR);
                $tmpObj->save(true);

                $isSame = true;
                foreach ($arrPrimary as $field) {
                    if ($this->{$field}->getVal() !== $tmpObj->{$field}->getVal()) {
                        $isSame = false;
                    }
                }

                if ($isSame == true) {
                    $aktReihe = $tmpObj->{$sortField}->getVal();
                    $this->{$sortField}->setVal($aktReihe);
                }
            } while ($tmpObj->moveNext());
            //*/
        }
    }

    public function delete()
    {
        $queryConf = new querybuilder($this->table);
        $priFields = $this->objTableScheme->getPrimaryFields();

        foreach ($priFields as $key => $colName) {
            $queryConf->setCondition($colName, $this->{$colName}->getVal());
            $queryConf->setSort($colName);
        }

        database::getQuery()->execute($queryConf->getQueryDelete());

        $this->clearFields();
    }
}
