<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class sorting
{
    /** @var string $sortColumn */
    private $sortColumn;

    /** @var string $listFunction */
    private $listFunction;

    /** @var array $colParam */
    private $colParam;

    /**
     * Constructor
     * 
     * @return self
     */
    public function __construct()
    {
        $this->sortColumn = '';
        $this->listFunction = '';
        $this->colParam = [];
    }

    /**
     * define Sorting Column
     * 
     * @param string $colName
     * @return self
     */
    public function setSortColumn(string $colName)
    {
        $this->sortColumn = $colName;
        return $this;
    }

    /**
     * define listing Funktion
     * 
     * @param string $functName
     * @return self
     */
    public function setListFunction(string $functName)
    {
        $this->listFunction = $functName;
        return $this;
    }

    /**
     * define the database columns used as parameter for the listing function
     * 
     * @param string $colName
     * @return self
     */
    public function addReferenceColumn(string $colName)
    {
        if(!is_array($this->colParam)){
            $this->colParam = [];
        }

        $this->colParam[] = $colName;
    }

    /**
     * returns the sorting column name
     * 
     * @return string
     */
    public function getSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * returns the listing function
     * 
     * @return string
     */
    public function getListFunction()
    {
        return $this->listFunction;
    }

    /**
     * returns the columns used as reference
     * 
     * @return array
     */
    public function getReferenceColumns()
    {
        return $this->colParam;
    }
}