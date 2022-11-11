<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 09.03.2019
 * Time: 07:00
 */

namespace Sophokles\Database;

class statement
{
    /** @var string */
    protected $statment;

    /** @var array */ 
    protected $arguments;
    
    /**
     * Costructor
     * 
     * @return self
     */
    public function __construct()
    {
        $this->arguments = [];
        $this->statment = '';
    }

    /**
     * Get the value of statment
     * @return string
     */ 
    public function getStatment()
    {
        return $this->statment;
    }

    /**
     * Set the value of statment
     *
     * @param strint $statment
     * @return  self
     */ 
    public function setStatment(string $statment)
    {
        $this->statment = $statment;

        return $this;
    }

    /**
     * Get the value of arguments
     * 
     * @return array
     */ 
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set the value of arguments
     * 
     * @param string $column
     * @param mixed $value
     * @return  self
     */ 
    public function setArguments(string $column, $value)
    {
        $this->arguments[$column] = $value;

        return $this;
    }
}