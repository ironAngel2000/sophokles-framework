<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 10.03.2019
 * Time: 14:15
 */

namespace Sophokles\Database;

final class tablescheme
{

    /** @var string $tableName */
    protected $tableName;

    /** @var array $arrColumns */
    protected $arrColumns;

    /** @var array $arrKeys */
    protected $arrKeys;

    /** @var array $arrTableOptions */
    protected $arrTableOptions;

    /** @var integer $databaseNr */
    protected $databaseNr;

    /** @var bool $primaryset */
    protected $primaryset;

    /** @var array $primaryFields */
    protected $primaryFields;

    /** @var bool $isautoincrement */
    protected $isautoincrement;

    /** @var string $databaseEngine */
    protected $databaseEngine;

    /** @var integer $databaseVersion */
    protected $databaseVersion;


    /**
     * Constuctor
     *
     * @param string $tableName
     * @return self
     */
    final public function __construct(string &$tableName)
    {
        if (!defined('TABLEPREFIX')) {
            define('TABLEPREFIX', 'spk');
        }

        if (trim(TABLEPREFIX) !== '') {
            $tableName = TABLEPREFIX . '_' . $tableName;
        }

        $this->tableName = $tableName;
        $this->databaseNr = 1;

        $this->arrTableOptions = [];
        $this->arrTableOptions['charset'] = 'utf8mb4';
        $this->arrTableOptions['collate'] = 'utf8mb4_unicode_ci';
        $this->arrTableOptions['engine'] = 'innoDB';

        $this->arrColumns = [];
        $this->arrKeys = [];

        $this->primaryset = false;
        $this->primaryFields = [];
        $this->isautoincrement = false;

        $this->addColumn('deleted', FieldType::INT)
            ->length(11)
            ->index();

        $this->addColumn('uniqueid', FieldType::VARCHAR)
            ->length(150)
            ->unique();

    }

    /**
     * Check Version and Engine of the Database MariaDB or MySQL
     */

    final protected function checkDatabaseEnginge()
    {
        $query = "SHOW VARIABLES like '%version%' ";

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);

        $arrResult = $dbo->fetchAll();

        foreach ($arrResult as $arrEntry) {
            switch (trim($arrEntry['Variable_name'])) {
                case 'version_comment':
                    $engine = $arrEntry['Value'];
                    break;
                case 'version':
                    $this->databaseVersion = (float)$arrEntry['Value'];
                    break;
            }
        }


        if (stripos($engine, 'MariaDB') !== false) {
            $this->databaseEngine = 'MariaDB';
        } else {
            $this->databaseEngine = 'MySQL';
        }

    }

    /**
     * Replace FieldType JSON for MariaDB or MySQL lower 5.7
     */

    final protected function checkJsonField($operator)
    {

        if (strtoupper(substr(trim($operator), 0, 4)) === 'JSON') {

            if ($this->databaseEngine === 'MariaDB' || $this->databaseVersion < 5.7) {
                $operator = substr($operator, 5, strlen($operator));

                $operator = 'TEXT ' . $operator;

            }

        }

        return $operator;
    }

    /**
     * Select instance of database
     *
     * @param integer $dbNr
     * @return self
     */
    final public function setDatabasenr(int $dbNr)
    {
        $this->databaseNr = $dbNr;

        return $this;
    }

    /**
     * Upadate Changes of the Database Table
     *
     * @return self
     */
    final public function update()
    {

        $this->checkDatabaseEnginge();

        $this->checkTable();

        $this->checkColumns();

        $this->tableKeys();

        $this->checkAutoIncrement();

        return $this;
    }

    /**
     * Add a Column to Scheme
     *
     * @param string $name
     * @param string $dbOption
     * @return tablecolumn
     */
    final public function addColumn($name, FieldType $fieldType): tablecolumn
    {
        $objColumn = new tablecolumn($name, $fieldType);
        $this->arrColumns[] = &$objColumn;
        return $objColumn;
    }

    /**
     * Define Table Keys
     *
     * @param string name
     * @param array $arrColumns
     * @param string $type
     * @return self
     */
    final public function addKey(array $arrColumns, bool $isUnique)
    {

        $this->arrKeys[] = ['c' => $arrColumns, 'u' => $isUnique];

        return $this;
    }

    /**
     * Retrun the Array of columns configuration
     *
     * @return array
     */
    final public function getColumns()
    {
        return $this->arrColumns;
    }

    /**
     * Retrun the Array of keys configuration
     *
     * @return array
     */
    final public function getKeys()
    {
        return $this->arrKeys;
    }

    /**
     * Retrun the Array of primary Fields
     *
     * @return array
     */
    final public function getPrimaryFields()
    {
        return $this->primaryFields;
    }

    /**
     * Retrun the status autoincrement
     *
     * @return bool
     */
    final public function isAutoincrement()
    {
        return $this->isautoincrement;
    }


    /**
     * Database check if Table exists
     *
     * @return void
     */
    final protected function checkTable()
    {

        $objQuery = database::getQuery($this->databaseNr);

        $statement = new statement();

        $query = 'SHOW TABLES';
        $statement->setStatment($query);

        $pdo = $objQuery->execute($statement);

        $tables = $pdo->fetchAll();
        $tableFound = false;

        foreach ($tables as $arrTableRecord) {
            if (isset($arrTableRecord[0]) && $arrTableRecord[0] === $this->tableName) {
                $tableFound = true;
                break;
            }
        }


        if ($tableFound === false) {
            $this->createTable();
        }
    }

    /**
     * Check if all Colums and Keys are in Table
     *
     * @return void
     */
    final protected function checkColumns()
    {
        $objQuery = database::getQuery($this->databaseNr);

        $arrFields = $this->getTableStructure();

        $querySecond = '';

        $query = 'SHOW COLUMNS FROM `' . $this->tableName . '`';

        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);

        $dbFields = $dbo->fetchAll();

        foreach ($this->arrColumns as $objColumn) {
            if ($objColumn instanceof tablecolumn) {

                $name = $objColumn->columnName();

                $found = false;

                if (is_array($dbFields)) foreach ($dbFields as $dbFieldEntry) {
                    if (isset($dbFieldEntry['Field']) && $dbFieldEntry['Field'] === $name) {
                        $found = true;
                        break;
                    }
                }

                if ($found === false) {
                    $query = "ALTER TABLE `" . $this->tableName . '`' . $objColumn->getAddStatement();
                    $statement = new statement();
                    $statement->setStatment($query);
                    $dbo = $objQuery->execute($statement);
                }

            }

        }

    }

    /**
     * Query for createing Table in Database
     *
     * @return void
     */
    final protected function createTable()
    {

        $query = "CREATE TABLE `" . $this->tableName . "` (";

        $i = 0;
        foreach ($this->arrColumns as $objColumn) {
            if ($objColumn instanceof tablecolumn) {


                if ($i > 0) {
                    $query .= ', ';
                }

                $query .= PHP_EOL;
                $query .= str_replace('ADD COLUMN', '', $objColumn->getAddStatement());
                $i++;

            }

        }

        $query .= ") ENGINE=" . $this->arrTableOptions['engine'] . " DEFAULT CHARSET=" . $this->arrTableOptions['charset'] . " COLLATE=" . $this->arrTableOptions['collate'] . ";";

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);

    }

    /**
     * Query for creating Keys in Database
     *
     * @param array $arrKeyEntry
     * @return void
     */
    final protected function tableKeys()
    {
        $query = 'SHOW INDEX FROM ' . $this->tableName;
        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);
        $indexes = $dbo->fetchAll();

        $arrIndex = [];

        foreach ($indexes as $indexEntry) {

            $type = $indexEntry['Non_unique'];
            $keyName = $indexEntry['Key_name'];
            $column = $indexEntry['Column_name'];

            $arrIndex[$keyName]['columns'][] = $column;
            $arrIndex[$keyName]['unique'] = ($type === 0);
        }

        foreach ($this->arrColumns as $objColumn) {
            if ($objColumn instanceof tablecolumn) {
                if ($objColumn->isPrimary()) {
                    if (!isset($arrIndex['PRIMARY'])) {
                        $this->addPrimareyKey([$objColumn->columnName()]);
                    }
                } elseif ($objColumn->isUnique()) {
                    $this->checkIndexExists([$objColumn->columnName()], true, $arrIndex);
                } elseif ($objColumn->isIndex()) {
                    $this->checkIndexExists([$objColumn->columnName()], false, $arrIndex);
                }
            }
        }

        foreach ($this->arrKeys as $arrKeyEntry) {
            $this->checkIndexExists($arrKeyEntry['c'], $arrKeyEntry['u'], $arrIndex);
        }

    }

    final function checkAutoIncrement()
    {
        $query = "SHOW COLUMNS FROM " . $this->tableName . " WHERE Extra LIKE '%auto_increment%' ";
        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $pdo = $objQuery->execute($statement);
        $result = $pdo->fetchAll();

        $field = '';
        foreach ($result as $entry) {
            $field = $entry['Field'];
        }
        if (trim($field) === '') {

            foreach ($this->arrColumns as $objColumn) {
                if ($objColumn instanceof tablecolumn) {
                    if ($objColumn->isAutoincrement()) {
                        $type = match ($objColumn->columnType()) {
                            FieldType::BIGINT => 'BIGINT',
                            default => 'INT',
                        };
                        $query = 'ALTER TABLE `'.$this->tableName.'` ALTER `'.$objColumn->columnName().'` DROP DEFAULT;';
                        $statement = new statement();
                        $statement->setStatment($query);
                        $pdo = $objQuery->execute($statement);

                        $query = 'ALTER TABLE `'.$this->tableName.'` MODIFY `'.$objColumn->columnName().'` '.$type.' NOT NULL AUTO_INCREMENT;';
                        $statement = new statement();
                        $statement->setStatment($query);
                        $pdo = $objQuery->execute($statement);
                        break;
                    }
                }
            }

        }
    }

    private function checkIndexExists(array $arrCols, bool $isUnique, array $arrIndex)
    {

        $indexFound = false;
        $indexValid = false;
        $indexName = '';
        foreach ($arrIndex as $indName => $indexEntry) {
            if (count($indexEntry['columns']) === count($arrCols)) {
                $colsEqual = true;
                foreach ($arrCols as $col) {
                    if (!in_array($col, $indexEntry['columns'])) {
                        $colsEqual = false;
                        break;
                    }
                }
                if ($colsEqual === true) {
                    $indexFound = true;
                    $indexValid = ($indexEntry['unique'] !== $isUnique);
                    $indexName = $indName;
                    break;
                }
            }
        }
        if ($indexFound === true && $indexValid === false) {
            $this->dropIndex($indName);
            $indexFound = false;
        }

        if ($indexFound === false) {
            if ($isUnique) {
                $this->addUniqueKey($arrCols);
            } else {
                $this->addIndexKey($arrCols);
            }
        }
    }

    private function dropIndex(string $indeName)
    {
        $objQuery = database::getQuery($this->databaseNr);
        $query = 'DROP INDEX `' . $indeName . '` ON ' . $this->tableName;
        $statement = new statement();
        $statement->setStatment($query);
        $pdo = $objQuery->execute($statement);
    }

    private function addPrimareyKey(array $cols)
    {
        $query = 'ALTER TABLE `' . $this->tableName . '` ADD PRIMARY KEY (';
        $i = 0;
        foreach ($cols as $col) {
            if ($i > 0) {
                $query .= ', ';
            }
            $query .= '`' . $col . '`';
            $i++;
        }
        $query .= ')';

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);
    }

    private function addUniqueKey(array $cols)
    {
        $keyName = '';
        $colQuery = '';
        $i = 0;
        foreach ($cols as $col) {
            if ($i > 0) {
                $colQuery .= ', ';
                $keyName .= '_';
            }
            $keyName .= $col;
            $colQuery .= '`' . $col . '`';
            $i++;
        }

        $query = 'ALTER TABLE `' . $this->tableName . '` ADD UNIQUE KEY `' . $keyName . '`  (' . $colQuery . ')';

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);
    }

    private function addIndexKey(array $cols)
    {
        $keyName = '';
        $colQuery = '';
        $i = 0;
        foreach ($cols as $col) {
            if ($i > 0) {
                $colQuery .= ', ';
                $keyName .= '_';
            }
            $keyName .= $col;
            $colQuery .= '`' . $col . '`';
            $i++;
        }

        $query = 'ALTER TABLE `' . $this->tableName . '` ADD KEY `' . $keyName . '`  (' . $colQuery . ')';

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);
    }

    final protected function getTableStructure()
    {
        $objQuery = database::getQuery($this->databaseNr);

        $statement = new statement();
        $query = "SHOW COLUMNS FROM `" . $this->tableName . "`";
        $statement->setStatment($query);
        $pdo = $objQuery->execute($statement);

        $arrResult = $pdo->fetchAll();

        $arrFields = [];

        if (is_array($arrResult)) {
            foreach ($arrResult as $arrEntry) {
                $arrFields[$arrEntry['Field']] = $arrEntry['Type'];
            }
        }

        return $arrFields;
    }

}
