<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50.
 */

namespace Sophokles\Dataset;

use GraphQL\Type\Definition\Type;
use Sophokles\Database\FieldType;
use Sophokles\Database\querybuilder;
use Sophokles\Database\database;
use Sophokles\Database\statement;
use Sophokles\Database\tablecolumn;
use Sophokles\Database\tablescheme;
use Sophokles\Database\query;
use System\Config\db;

/**
 * @property string $uniqueid
 * @property integer $deleted
 */
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

    /** @var tablescheme $objTableSchem */
    protected $objTableScheme;

    /** @var sorting $sorting */
    protected $objSorting;

    /** @var serviceWorker */
    protected $objServiceWorker;

    /** @var string */
    protected $autoIncrementField = '';

    /** @var array */
    protected $primaryField = [];

    /** @var array */
    protected $cols = [];

    abstract protected function defineTableScheme();

    abstract protected function defineSorting();

    abstract protected function initClass();

    /** @var bool */
    public static $isSorting = false;

    /**
     * Constructor.
     *
     * @return self
     */
    final public function __construct(private int $databasenr = 1)
    {
        if ($databasenr == 0) {
            $this->databasenr = 1;
        }

        $this->objTableScheme = new tablescheme($this->table);
        $this->objSorting = new sorting();
        $this->initClass();


        $this->defineSorting();
        $this->defineTableScheme();

        if (method_exists($this, 'abstractInit')) {
            $this->abstractInit();
        }

        $this->__init();

    }

    private function __init(): void
    {
        $this->cols = [];
        if ($this->objTableScheme instanceof tablescheme) {
            $arrCols = $this->objTableScheme->getColumns();
            if (count($arrCols)) {
                foreach ($arrCols as $objColumn) {
                    if ($objColumn instanceof tablecolumn) {
                        $name = $objColumn->columnName();

                        $this->cols[$name] = $objColumn->columnType();

                        $this->arrFields[] = $name;
                        if ($objColumn->isAutoincrement()) {
                            $this->autoIncrementField = $name;
                            $this->primaryField[] = $name;
                        } elseif ($objColumn->isPrimary()) {
                            $this->primaryField[] = $name;
                        }

                    }
                }
            }
        }
        $this->clearFields();
    }

    public function __clone()
    {
        if (isset($this->sorting)) {
            $this->sorting = clone $this->sorting;
        }
        if (isset($this->objServiceWorker)) {
            $this->objServiceWorker = clone $this->objServiceWorker;
        }
        $this->objTableScheme = clone $this->objTableScheme;

        ++$this->cloneIndex;
    }

    public function addServiceWorker(serviceWorker $objServiceWorker)
    {
        $this->objServiceWorker = $objServiceWorker;
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

    protected function getDatasetToArray()
    {
        $ret = [];

        if ($this->objTableScheme instanceof tablescheme) {
            $arrCols = $this->objTableScheme->getColumns();
            if (count($arrCols)) {
                foreach ($arrCols as $objColumn) {
                    if ($objColumn instanceof tablecolumn) {
                        $name = $objColumn->columnName();
                        $ret[$name] = $this->{$name};
                    }
                }
            }
        }

        return $ret;
    }

    public function toArray(bool $completeResult = false): array
    {
        if ($completeResult !== true) {
            return $this->getDatasetToArray();
        }

        $ret = [];

        $this->moveFirst();

        do {
            $ret[] = $this->getDatasetToArray();
        } while ($this->moveNext());

        return $ret;
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
        if ($this->objServiceWorker instanceof serviceWorker && dataset::$isSorting === false) {
            $this->objServiceWorker->beforeQuery($this);
        }
        $query = database::getQuery($this->databasenr);
        $pdo = $query->execute($objStatment);

        $found = 0;

        if ($pdo instanceof \PDOStatement) {
            $this->arrDbResult = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $found = count($this->arrDbResult);
        }

        if ($this->objServiceWorker instanceof serviceWorker && dataset::$isSorting === false) {
            $this->objServiceWorker->afterQuery($this);
        }

        if ($found > 0) {
            $this->moveFirst();
        } else {
            $this->clearFields();
        }

        return $found;
    }

    protected function executeDeletion(querybuilder $objQuerybuilder, bool $forceHardDelete = false): void
    {
        if ((new db())->getHardDelete() || $forceHardDelete) {
            database::getQuery()->execute($objQuerybuilder->getQueryDelete());
        } else {

            $tmpObj = clone $this;
            if ($tmpObj->readDbResult($objQuerybuilder->getQuerySelect())) {
                do {
                    $tmpObj->delete();
                } while ($tmpObj->moveNext());
            }

        }

    }

    private function writeDataSetRowtoFields()
    {
        $key = key($this->arrDbResult);
        foreach ($this->arrFields as $colName) {

            if (isset($this->arrDbResult[$key][$colName])) {

                $this->{$colName} = $this->arrDbResult[$key][$colName];

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

    public function getDataModel(): array
    {
        return $this->cols;
    }

    /**
     * Clear the Values of the current Fields.
     */
    protected function clearFields()
    {
        foreach ($this->cols as $col => $type) {
            switch ($type) {
                case FieldType::BIT:
                case FieldType::INT:
                case FieldType::BIGINT:
                case FieldType::TIMESTAMP:
                case FieldType::DECIMAL;
                    $this->{$col} = 0;
                    break;
                case FieldType::BOOLEAN;
                    $this->{$col} = false;
                    break;
                default:
                    $this->{$col} = '';
                    break;
            }
        }
    }

    /**
     * Get all entries. Return the number of found Results.
     *
     * @return int
     * @var array
     *
     */
    public function getEntries(array $primaryValues = [])
    {
        $queryConf = new querybuilder($this->table);

        if (count($primaryValues)) {
            $priFields = $this->primaryField;

            if (count($primaryValues) != count($priFields)) {
                trigger_error('The values array is not similar to the primary fields of the table "' . $this->table . '": ' . print_r($priFields, true), E_USER_ERROR);
            }

            foreach ($priFields as $key => $colName) {
                $queryConf->setCondition($colName, $primaryValues[$key]);
                $queryConf->setSort($colName);
            }
        }

        return $this->readDbResult($queryConf->getQuerySelect());
    }

    /**
     * get Entry by unique id
     *
     * @param string $uniqueId
     * @return int
     */
    public function getUniqueEntry(string $uniqueId)
    {
        $queryConf = new querybuilder($this->table);

        $queryConf->setCondition('uniqueid', $uniqueId);
        $queryConf->setSort('uniqueid');

        return $this->readDbResult($queryConf->getQuerySelect());
    }

    /**
     * Get all entries. Return the number of found Results.
     *
     * @return int
     * @var array
     *
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
        return $this->primaryField;
    }

    public function getRecord2Array(): array
    {
        $ret = [];

        foreach ($this->objTableScheme->getColumns() as $arrCol) {
            $ret[$arrCol['n']] = $this->{$arrCol['n']};
        }

        return $ret;
    }

    public function save($noSort = false)
    {
        $tmpObj = clone $this;

        $queryMode = 'update';

        if (trim($this->uniqueid) === '') {
            $queryMode = 'create';
            $this->uniqueid = \uniqid('', false);
        }

        $arrPVal = [];
        foreach ($this->primaryField as $colName) {
            $arrPVal[] = $this->{$colName};
        }

        if (isset($this->objServiceWorker) && dataset::$isSorting === false) {
            switch ($queryMode) {
                case 'update':
                    $this->objServiceWorker->beforeUpdate($this);
                    break;
                case 'create':
                    $this->objServiceWorker->beforeCreate($this);
                    break;
            }
        }

        $queryConf = new querybuilder($this->table);

        $sortField = $this->objSorting->getSortColumn();
        if (trim($sortField) !== '') {
            if ($this->{$sortField} == 0) {
                $this->{$sortField} = 99999999;
            }
        }


        if ((int)$tmpObj->getUniqueEntry($this->uniqueid) === 0) {
            $arrPVal = [];
            foreach ($this->arrFields as $colName) {
                $arrPVal[$colName] = $this->{$colName};
            }

            $queryConf->getQueryInsert($arrPVal);

            database::getQuery()->execute($queryConf->getQueryInsert($arrPVal));

            if ($this->autoIncrementField !== '') {
                $this->{$this->autoIncrementField} = query::$lastId;
            }
        } else {
            $arrPVal = [];
            foreach ($this->arrFields as $colName) {
                $arrPVal[$colName] = $this->{$colName};
            }

            foreach ($this->primaryField as $key => $colName) {
                $queryConf->setCondition($colName, $this->{$colName});
                $queryConf->setSort($colName);
            }

            database::getQuery()->execute($queryConf->getQueryUpdate($arrPVal));
        }

        if (isset($this->objServiceWorker) && dataset::$isSorting === false) {
            switch ($queryMode) {
                case 'update':
                    $this->objServiceWorker->afterUpdate($this);
                    break;
                case 'create':
                    $this->objServiceWorker->afterCreate($this);
                    break;
            }
        }

        if (trim($sortField) !== '' && $noSort !== true) {
            $start_sort = true;
            dataset::$isSorting = true;

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
                $arrParam[] = $this->{$fieldname};
            }
        }

        $sortField = $this->objSorting->getSortColumn();
        $arrPrimary = $this->objTableScheme->getPrimaryFields();

        $arrFunkt = array($tmpObj, $this->objSorting->getListFunction());

        if (call_user_func_array($arrFunkt, $arrParam)) {
            do {
                $isSame = true;
                foreach ($arrPrimary as $field) {
                    if ($this->{$field} !== $tmpObj->{$field}) {
                        $isSame = false;
                    }
                }

                if ($isSame == false) {
                    $setReihe = false;
                    if ($tmpObj->{$sortField} >= $this->{$sortField}) {
                        $setReihe = true;
                    }

                    if ($setReihe == true) {
                        $aktReihe = $tmpObj->{$sortField};
                        ++$aktReihe;
                        $tmpObj->{$sortField} = $aktReihe;
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
                $tmpObj->{$sortField} = $aktR;
                $tmpObj->save(true);

                $isSame = true;
                foreach ($arrPrimary as $field) {
                    if ($this->{$field} !== $tmpObj->{$field}) {
                        $isSame = false;
                    }
                }

                if ($isSame == true) {
                    $aktReihe = $tmpObj->{$sortField};
                    $this->{$sortField} = $aktReihe;
                }
            } while ($tmpObj->moveNext());
            //*/
        }
    }

    public function delete()
    {
        if ($this->objServiceWorker instanceof serviceWorker) {
            $this->objServiceWorker->beforeDelete($this);
        }

        $queryConf = new querybuilder($this->table);

        foreach ($this->primaryField as $key => $colName) {
            $queryConf->setCondition($colName, $this->{$colName});
            $queryConf->setSort($colName);
        }

        if ((new db())->getHardDelete()) {
            database::getQuery()->execute($queryConf->getQueryDelete());
        } else {
            dataset::$isSorting = true;

            $this->deleted = \time();
            $this->save(true);

            dataset::$isSorting = false;
        }


        if ($this->objServiceWorker instanceof serviceWorker) {
            $this->objServiceWorker->afterDelete($this);
        }

        $this->clearFields();
    }
}
