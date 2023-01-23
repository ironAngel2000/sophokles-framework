<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 10.03.2019
 * Time: 03:00
 */

namespace Sophokles\Database;

class querybuilder
{
    /** @var string $table */
    protected $table;

    /** @var array $condition */
    protected $condition;

    /** @var array $sort */
    protected $sort;

    /** @var integer $condIndex */
    protected $condIndex;

    /** @var array $groupConnector */
    protected $groupConnector;

    /** @var array $inSelect */
    protected $inSelect;

    /** @var string $distinctColumn */
    protected $distinctColumn;

    /** @var array $arrCols */
    protected $arrCols;

    /** @var array $arrLimit */
    protected $arrLimit;


    /**
     * Konstruktro
     * @param string $table
     * @return self
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->condition = [];
        $this->sort = [];
        $this->condIndex = 0;
        $this->groupConnector = [];
        $this->inSelect = [];
        $this->distinctColumn = '';
        $this->arrLimit = [];
    }

    /**
     * Add simple Condition
     *
     * @param string $column
     * @param mixed $value
     * @param string $equal
     * @param string $conector
     * @return self
     */
    public function setCondition(string $column, $value, string $equal = '=', string $conector = 'AND')
    {
        $newCond = [];
        $newCond['type'] = 'simple';
        $newCond['col'] = $column;
        $newCond['val'] = $value;
        $newCond['equal'] = $equal;
        $newCond['conector'] = $conector;
        $this->condition[$this->condIndex][] = $newCond;

        return $this;
    }

    /**
     * Add Text Like Condition
     *
     * @param string $column
     * @param mixed $value
     * @param string $matrix
     * @param string $conector
     * @return self
     */
    public function setLikeCondition(string $column, $value, string $matrix = '%#%', string $conector = 'AND')
    {
        $newCond = [];
        $newCond['type'] = 'like';
        $newCond['col'] = $column;
        $newCond['val'] = $value;
        $newCond['matrix'] = $matrix;
        $newCond['conector'] = $conector;
        $this->condition[$this->condIndex][] = $newCond;

        return $this;
    }

    /**
     * Add Number Logical Bit Condition
     *
     * @param string $column
     * @param integer $value
     * @param string $conector
     * @return self
     */
    public function setBitCondition(string $column, int $value, string $conector = 'AND')
    {
        $newCond = [];
        $newCond['type'] = 'bit';
        $newCond['col'] = $column;
        $newCond['val'] = $value;
        $newCond['conector'] = $conector;
        $this->condition[$this->condIndex][] = $newCond;

        return $this;
    }

    /**
     * Add Condition for JsonField
     *
     * @param string $column
     * @param mixed $value
     * @param array $jsonAdress
     * @param string $conector
     * @return self
     */
    public function setJsonCondition(string $column, $value, array $jsonAdress, string $conector = 'AND')
    {
        $newCond = [];
        $newCond['type'] = 'json';
        $newCond['col'] = $column;
        $newCond['val'] = $value;
        $newCond['jsadr'] = $jsonAdress;
        $newCond['conector'] = $conector;
        $this->condition[$this->condIndex][] = $newCond;

        return $this;
    }

    /**
     * New logical condition
     * @param string $connector
     * @return self
     */
    public function newConditionGroup(string $connector = 'AND')
    {
        $this->condIndex++;
        $this->groupConnector[$this->condIndex] = $connector;

        return $this;
    }

    /**
     * setSorting
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function setSort(string $column, string $direction = 'ASC')
    {
        $this->sort[] = [$column, $direction];

        return $this;
    }

    /**
     * set Limitation
     *
     * @param integer $from
     * @param integer $count
     * @return self
     */
    public function setLimit(int $from = 0, int $count = 30)
    {
        $this->arrLimit['f'] = $from;
        $this->arrLimit['c'] = $count;
        return $this;
    }

    /**
     * setSorting
     *
     * @param string $column
     * @param querybuilder $objBuilder
     * @param string $conector
     * @return self
     */
    public function setInselectCondition($column, querybuilder $objBuilder, string $refColumn, string $conector = 'AND')
    {
        $newCond = [];
        $newCond['type'] = 'inselect';
        $newCond['col'] = $column;
        $newCond['objBuild'] = $objBuilder;
        $newCond['conector'] = $conector;
        $newCond['refCol'] = $refColumn;

        $this->condition[$this->condIndex][] = $newCond;

        return $this;
    }

    /**
     * set Distinct Column
     *
     * @param string $column
     * @return self
     */
    public function setDistinctColumn(string $column)
    {
        $this->distinctColumn = $column;

        return $this;
    }


    /**
     * Get the SELECT Statemnt for queryobject
     *
     * @param array $fields
     * @return statement
     */
    public function getQuerySelect($fields = [])
    {
        $statement = new statement();
        $this->arrCols = [];

        $query = "SELECT ";

        if (trim($this->distinctColumn) !== '') {
            $query .= " DISTINCT `" . $this->distinctColumn . "` ";
        } else {
            if (count($fields) == 0) {
                $query .= " * ";
            } else {
                $i = 0;
                foreach ($fields as $field) {
                    if ($i > 0) {
                        $query .= ", ";
                    }
                    $query .= " `" . $field . "` ";
                    $i++;
                }
            }
        }

        $query .= " FROM `" . $this->table . "` ";

        $condition = $this->getConditon();

        if (trim($condition) !== '') {
            $query .= " WHERE (" . $condition . ") ";
            $query .= " AND (deleted = 0) ";
        } else {
            $query .= " WHERE deleted = 0 ";
        }

        $query .= " ORDER BY ";
        $i = 0;
        if (count($this->sort)) {

            foreach ($this->sort as $arrSort) {
                if ($i > 0) {
                    $query .= ", ";
                }

                if (\substr($arrSort[0], 0, 1) === '"' || \substr($arrSort[0], 0, 1) === "'") {
                    $query .= " " . $arrSort[0] . " " . $arrSort[1] . " ";
                } else {
                    $query .= " `" . $arrSort[0] . "` " . $arrSort[1] . " ";
                }

                $i++;
            }
        }

        if ($i > 0) {
            $query .= ", ";
        }
        $query .= " `deleted` ASC ";

        if (count($this->arrLimit)) {
            $query .= " LIMIT " . $this->arrLimit['f'] . " , " . $this->arrLimit['c'];
        }

        $statement->setStatment($query);

        if (count($this->arrCols)) {
            foreach ($this->arrCols as $col => $value) {
                $statement->setArguments($col, $value);
            }
        }

        return $statement;
    }

    /**
     * Get the UPDATE Statemnt for queryobject
     *
     * @param array $fields
     * @return statement
     */
    public function getQueryUpdate($fields)
    {
        $statement = new statement();
        $this->arrCols = [];

        if (isset($fields['uniqueid']) && trim($fields['uniqueid']) === '') {
            $fields['uniqueid'] = uniqid('', false);
        }

        $query = "UPDATE ";
        $query .= " `" . $this->table . "` ";

        $query .= " SET ";

        $i = 0;
        foreach ($fields as $col => $val) {
            $uique = uniqid('', false);
            $this->arrCols[$uique] = $val;

            if ($i > 0) {
                $query .= ", ";
            }

            $query .= "`" . $col . "` = :" . $uique;

            $i++;

        }

        $query .= " ";


        $condition = $this->getConditon();

        if (trim($condition) !== '') {
            $query .= " WHERE " . $condition . " ";
        }


        $statement->setStatment($query);

        if (count($this->arrCols)) {
            foreach ($this->arrCols as $col => $value) {
                $statement->setArguments($col, $value);
            }
        }

        return $statement;
    }

    /**
     * Get the INSERT Statemnt for queryobject
     *
     * @param array $fields
     * @return statement
     */
    public function getQueryInsert($fields)
    {
        $statement = new statement();
        $this->arrCols = [];

        $query = "INSERT ";
        $query .= "INTO `" . $this->table . "` ";

        if (isset($fields['uniqueid']) && trim($fields['uniqueid']) === '') {
            $fields['uniqueid'] = uniqid('', false);
        }

        $query .= " (";


        $i = 0;
        foreach ($fields as $col => $val) {

            if ($val === Null) {
            } else {
                if ($i > 0) {
                    $query .= ", ";
                }

                $query .= " `" . $col . "`";
                $i++;
            }


        }
        $query .= " ) ";

        $query .= " VALUES (";

        $i = 0;
        foreach ($fields as $col => $val) {

            if ($val === Null) {
            } else {
                $uique = uniqid('', false);
                $this->arrCols[$uique] = $val;

                if ($i > 0) {
                    $query .= ", ";
                }

                $query .= " :" . $uique;

                $i++;
            }


        }

        $query .= " ) ";


        $statement->setStatment($query);

        if (count($this->arrCols)) {
            foreach ($this->arrCols as $col => $value) {
                $statement->setArguments($col, $value);
            }
        }

        return $statement;
    }

    /**
     * Get the DELETE Statemnt for queryobject
     *
     * @param array $fields
     * @return statement
     */
    public function getQueryDelete()
    {
        $statement = new statement();
        $this->arrCols = [];

        $query = "DELETE ";
        $query .= "FROM `" . $this->table . "` ";

        $condition = $this->getConditon();

        if (trim($condition) !== '') {
            $query .= " WHERE " . $condition . " ";
        }

        $statement->setStatment($query);

        if (count($this->arrCols)) {
            foreach ($this->arrCols as $col => $value) {
                $statement->setArguments($col, $value);
            }
        }

        return $statement;
    }


    /**
     * Condition Builder
     *
     * @return string
     */
    protected function getConditon()
    {
        $ret = '';

        foreach ($this->condition as $grpNr => $arrCond) {

            if (is_array($arrCond)) {
                if (isset($this->groupConnector[$grpNr]) && trim($this->groupConnector[$grpNr]) != '') {
                    $ret .= " " . trim($this->groupConnector[$grpNr]) . " ";
                }

                $ret .= " ( ";

                $merkVerbCond = '';

                foreach ($arrCond as $arrBedinung) {
                    if (trim($merkVerbCond) != '') {
                        $ret .= " " . trim($merkVerbCond) . " ";
                    }

                    $ret .= " ( ";
                    switch ($arrBedinung['type']) {
                        case 'like':
                            $uique = uniqid('', false);

                            $val = $arrBedinung['matrix'];
                            $val = str_replace('#', $arrBedinung['val'], $val);

                            $this->arrCols[$uique] = $val;

                            $ret .= "`" . $arrBedinung['col'] . "` like :" . $uique;

                            break;
                        case 'bit':
                            $uique = uniqid('', false);
                            $this->arrCols[$uique] = $arrBedinung['val'];

                            $ret .= "`" . $arrBedinung['col'] . "` & :" . $uique . " = :" . $uique;  // [FELD] & [WERT] = [WERT]

                            break;
                        case 'json':
                            $uique = uniqid('', false);

                            $this->arrCols[$uique] = $this->getJsonSearch($arrBedinung['jsadr'], $arrBedinung['val']);

                            $ret .= "`" . $arrBedinung['col'] . "` like :" . $uique;

                            break;
                        case 'inselect':
                            $arrBedinung['objBuild']->setDistinctColumn($arrBedinung['refCol']);
                            $statment = $arrBedinung['objBuild']->getQuerySelect();
                            $ret .= "`" . $arrBedinung['col'] . "` IN (" . $statment->getStatment() . ") ";

                            $arrPrm = $statment->getArguments();
                            if (count($arrPrm)) {
                                foreach ($arrPrm as $col => $val) {
                                    $this->arrCols[$col] = $val;
                                }
                            }

                            break;
                        default: // simple condition

                            $uique = uniqid('', false);
                            $this->arrCols[$uique] = $arrBedinung['val'];

                            $ret .= "`" . $arrBedinung['col'] . "` " . $arrBedinung['equal'] . " :" . $uique;
                            break;
                    }

                    $ret .= " ) ";

                    $merkVerbCond = $arrBedinung['conector'];

                }

                $ret .= ") ";

            }

        }


        return $ret;
    }


    /**
     * Return the JsonSearch string for SQL queries
     *
     * @param array $arrLocation
     * @param string $phrase
     * @param bool $fulltext
     * @return string
     */
    protected function getJsonSearch(array $arrLocation, string $phrase, $fulltext = false)
    {
        $ret = false;


        if (!is_array($arrLocation) && trim($arrLocation) != '') {
            $arrLocation = array($arrLocation);
        }
        $keyStr = '';

        if (is_array($arrLocation)) {
            $keyStr = '';
            foreach ($arrLocation as $key) {
                if (trim($keyStr) != '') $keyStr .= ':{%';
                $keyStr .= json_encode($key);
            }

            $keyStr .= ':';
            $jsonPhrase = json_encode($phrase);

            $jsonPhrase = str_replace('"', '', $jsonPhrase);

            $endChar = '%';

            if (trim(intval($jsonPhrase)) === trim($jsonPhrase)) {
                if ($fulltext === true) $jsonPhrase = '%' . $jsonPhrase;
            } else {
                if ($fulltext === true) {
                    $jsonPhrase = '%' . $jsonPhrase . '%';
                    $endChar = '';
                } else $jsonPhrase = '"' . $jsonPhrase . '"';
            }

            $keyStr1 = '%' . $keyStr . $jsonPhrase . ',' . $endChar;
            $keyStr2 = '%' . $keyStr . $jsonPhrase . '}' . $endChar;
            $keyStr3 = '%' . $keyStr . '"' . $jsonPhrase . '",' . $endChar;
            $keyStr4 = '%' . $keyStr . '"' . $jsonPhrase . '"}' . $endChar;

            $keyStr = '%' . $keyStr . $jsonPhrase . '%';

        }
        for ($i = 1; $i <= 2; $i++) {
            $keyStr = str_replace('\\', '\\\\', $keyStr);
            $keyStr1 = str_replace('\\', '\\\\', $keyStr1);
            $keyStr2 = str_replace('\\', '\\\\', $keyStr2);
            $keyStr3 = str_replace('\\', '\\\\', $keyStr3);
            $keyStr4 = str_replace('\\', '\\\\', $keyStr4);
        }


        $ret = $keyStr;
        return $ret;
    }

}