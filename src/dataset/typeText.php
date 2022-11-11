<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class typeText
{
    /** @param string $value */
    protected $value;

    /**
     * Contructor
     * 
     * @param string $initVal
     * @return self
     */
    public function __construct(string $initVal=''){
        $this->setVal($initVal);
    }

    /**
     * Convert to string
     * 
     * @return string
     */
    public function __toString(){
        return $this->value;
    }

     /**
     * Set Value
     * 
     * @param string $strval
     * @return void
     */
    public function setVal(string $strval){
        $this->value = $strval;
    }

     /**
     * Get Value
     * 
     * @return string
     */
    public function getVal(){
        return $this->value;
    }

     /**
     * Reset the Value
     * 
     * @return void
     */
    public function clearValue(){
        $this->value = '';
    }

}