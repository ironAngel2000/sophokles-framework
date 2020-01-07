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
        if(!defined('TABLEPREFIX')){
            define('TABLEPREFIX','spk');
        }

        if(trim(TABLEPREFIX)!==''){
            $tableName = TABLEPREFIX.'_'.$tableName;
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

        $this->addColumn('deleted',"INT(11) NOT NULL DEFAULT '0'");
        $this->addKey('deleted',['deleted'],'INDEX');

        $this->addColumn('uniqueid','VARCHAR(150) NULL DEFAULT NULL');
        $this->addKey('uniqueid',['uniqueid'],'UNIQUE');

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

        foreach ($arrResult as $arrEntry){
            switch (trim($arrEntry['Variable_name'])){
                case 'version_comment':
                    $engine = $arrEntry['Value'];
                    break;
                case 'version':
                    $this->databaseVersion = (float) $arrEntry['Value'];
                    break;
            }
        }


        if(stripos($engine,'MariaDB')!==false){
            $this->databaseEngine = 'MariaDB';
        }
        else{
            $this->databaseEngine = 'MySQL';
        }

    }

    /**
     * Replace FieldType JSON for MariaDB or MySQL lower 5.7
     */

    final protected function checkJsonField($operator)
    {

        if(strtoupper(substr(trim($operator),0,4))==='JSON'){

            if($this->databaseEngine==='MariaDB' || $this->databaseVersion < 5.7){
                $operator = substr($operator,5,strlen($operator));

                $operator = 'TEXT '.$operator;

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

        return $this;
    }

    /**
     * Add a Column to Scheme
     *
     * @param string $name
     * @param string $dbOption
     * @return self
     */
    final public function addColumn($name,$dbOption)
    {
        if(stristr($dbOption, 'AUTO_INCREMENT')){
            if($this->primaryset){
                trigger_error("Only one PRIMARY key for each table allowed, please no not user AUTO_INCREMENT for this column", E_USER_ERROR);
            }
            else{
                //$dbOption = str_replace('AUTO_INCREMENT', '' ,$dbOption);
                //$this->primaryset = true;
                $this->primaryFields[] = $name;
                $this->isautoincrement = true;

                $this->addKey($name,[$name],'PRIMARY');
            }
        }
        $this->arrColumns[] = ['n'=>$name,'o'=>$dbOption];

        return $this;
    }

    /**
     * Define Table Keys
     *
     * @param string name
     * @param array $arrColumns
     * @param string $type
     * @return self
     */
    final public function addKey(string $name, array $arrColumns, string $type='')
    {
        if(trim(strtoupper($type))==='PRIMARY' && $this->primaryset){
            trigger_error("Only one PRIMARY key for each table allowed", E_USER_ERROR);
        }
        elseif(trim(strtoupper($type))==='PRIMARY'){
            $this->primaryFields = $arrColumns;
        }

        $this->arrKeys[] = ['n'=>$name,'c'=>$arrColumns,'t'=>trim(strtoupper($type))];

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

        $query = "select 1 from `".$this->tableName."` LIMIT 1";
        $statement->setStatment($query);

        $pdo = $objQuery->execute($statement);

        if($pdo->errorCode()!=='00000'){
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

        foreach($this->arrColumns as $arrCol){

            if(!isset($arrFields[$arrCol['n']])){


                $arrCol['o'] = $this->checkJsonField($arrCol['o']);

                if(stristr($arrCol['o'] ,'AUTO_INCREMENT')){

                    $arrCol['o'] .= " FIRST , ADD PRIMARY KEY (`".$arrCol['n']."`)";

                    $query = "ALTER TABLE `".$this->tableName."` ADD `".$arrCol['n']."` ".$arrCol['o'];

                }
                else{
                    $query = "ALTER TABLE `".$this->tableName."` ADD `".$arrCol['n']."` ".$arrCol['o'];
                }


                $statement = new statement();
                $statement->setStatment($query);
                $dbo = $objQuery->execute($statement);
            }
        }

        $arrFields = $this->getTableStructure();
        end($arrFields);
        $lKey = key($arrFields);

        if(trim($lKey)!=='deleted'){

            $query = "ALTER TABLE `".$this->tableName."` MODIFY `deleted` varchar(150) AFTER `".$lKey."`";

            $statement = new statement();
            $statement->setStatment($query);
            $dbo = $objQuery->execute($statement);
            $lKey = 'deleted';
        }

        if(trim($lKey)!=='uniqueid'){

            $query = "ALTER TABLE `".$this->tableName."` MODIFY `uniqueid` varchar(150) AFTER `".$lKey."`";

            $statement = new statement();
            $statement->setStatment($query);
            $dbo = $objQuery->execute($statement);

        }


        $statement = new statement();
        $query = "SHOW INDEX FROM `".$this->tableName."`";
        $statement->setStatment($query);
        $pdo = $objQuery->execute($statement);

        $arrResult = $pdo->fetchAll();

        $arrKeys = [];

        if(is_array($arrResult)){
            foreach($arrResult as $arrEntry){
                $name = $arrEntry['Key_name'];
                $type = '';
                if($name === 'PRIMARY'){
                    $type = 'PRIMARY';
                }
                elseif($arrEntry['Non_unique']==='0'){
                    $type = 'UNIQUE';
                }

                if(!isset($arrKeys[$name])){
                    $arrKeys[$name] = ['t'=>$type,'c'=>[]];
                }

                $arrKeys[$name]['c'][] = $arrEntry['Column_name'];

            }
        }

        foreach($this->arrKeys as $arrKEntry){
            $n = $arrKEntry['n'];
            $t = $arrKEntry['t'];
            $c = $arrKEntry['c'];

            $setKey = false;

            if(!isset($arrKeys[$n])){
                $setKey = true;
            }
            else{
                if($t!==$arrKeys[$n]['t']){
                    $setKey = true;
                }
                if($c !== $arrKeys[$n]['c']){
                    $setKey = true;
                }

                if($setKey===true){
                    //`ALTER TABLE ``spk_adminuser`` DROP INDEX ``username``;`
                    $statement = new statement();
                    $query = "ALTER TABLE `".$this->tableName."`  DROP INDEX `".$n."`";
                    $statement->setStatment($query);
                    $pdo = $objQuery->execute($statement);
                }

            }

            if($setKey===true){
                $this->createKey($arrKEntry);
            }

        }


        if(trim($querySecond)!==''){
            $statement->setStatment($querySecond);
            $dbo = $objQuery->execute($statement);
        }
    }

    /**
     * Query for createing Table in Database
     *
     * @return void
     */
    final protected function createTable()
    {

        $querySecond = '';

        $query = "CREATE TABLE `".$this->tableName."` (";



        $i=0;
        foreach ($this->arrColumns as $arrColumn){

            $arrColumn['o'] = $this->checkJsonField($arrColumn['o']);

            if($i>0){
                $query .= ", ";
            }

            if(stristr($arrColumn['o'] ,'AUTO_INCREMENT')){

                $querySecond = "ALTER TABLE `".$this->tableName."`  MODIFY `".$arrColumn['n']."` ".$arrColumn['o'].";";

                $arrColumn['o'] = str_replace('AUTO_INCREMENT',' ',$arrColumn['o']);
            }



            $query .= "`".$arrColumn['n']."` ".$arrColumn['o'];

            $i++;
        }


        $query .= ") ENGINE=".$this->arrTableOptions['engine']." DEFAULT CHARSET=".$this->arrTableOptions['charset']." COLLATE=".$this->arrTableOptions['collate'].";";

        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();
        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);

        if(count($this->arrKeys)){
            foreach($this->arrKeys as $arrKey){
                $this->createKey($arrKey);
            }
        }

        if(trim($querySecond)!==''){
            $statement->setStatment($querySecond);
            $dbo = $objQuery->execute($statement);
        }
    }

    /**
     * Query for creating Keys in Database
     *
     * @param array $arrKeyEntry
     * @return void
     */
    final protected function createKey($arrKeyEntry)
    {
        //ALTER TABLE `spk_adminuser` ADD UNIQUE(`uniqueid`);
        $objQuery = database::getQuery($this->databaseNr);
        $statement = new statement();

        $query = "ALTER TABLE `".$this->tableName."` ";

        if($arrKeyEntry['t'] === 'PRIMARY'){
            $query .= "ADD PRIMARY KEY (";
        }
        elseif($arrKeyEntry['t'] === 'UNIQUE'){
            //ALTER TABLE `spk_adminuser` ADD UNIQUE(`uniqueid`);
            $query .= "ADD  UNIQUE `".$arrKeyEntry['n']."` (";

        }
        else{
            $query .= "ADD  KEY `".$arrKeyEntry['n']."` (";
        }



        $j = 0;
        foreach($arrKeyEntry['c'] as $col){
            if($j>0){
                $query .= ", ";
            }

            $query .= "`".$col."`";

            $j++;
        }
        $query .= ")";

        $statement->setStatment($query);
        $dbo = $objQuery->execute($statement);
    }

    final protected function getTableStructure()
    {
        $objQuery = database::getQuery($this->databaseNr);

        $statement = new statement();
        $query = "SHOW COLUMNS FROM `".$this->tableName."`";
        $statement->setStatment($query);
        $pdo = $objQuery->execute($statement);

        $arrResult = $pdo->fetchAll();

        $arrFields = [];

        if(is_array($arrResult)){
            foreach($arrResult as $arrEntry){
                $arrFields[$arrEntry['Field']] = $arrEntry['Type'];
            }
        }

        return $arrFields;
    }

}
