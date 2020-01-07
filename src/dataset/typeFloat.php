<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class typeFloat{

    /** @param float $value */
    private $value;

    /**
     * Contructor
     * 
     * @param int $initVal
     * @return self
     */
    public function __construct(float $initVal=0){
        $this->setVal($initVal);
    }

    /**
     * Convert to string
     * 
     * @return string
     */
    public function __toString(){
        return trim($this->value);
    }

    /**
     * Get Value
     * 
     * @return float
     */
    public function getVal()
    {
        return  (float) ($this->value);
    }

    /**
     * Return US-Formated Number as String
     * 
     * @return string
     */
    public function getUSFormatedVal()
    {
        return number_format((float) ($this->value),10,'.','');
    }

    /**
     * Set Value
     * 
     * @param float $numval
     * @return void
     */
    public function setVal($numval){

        if($this->float) $this->value = (float) ($numval);
        else $this->value = (int) ($numval);
    }

    /**
     * Reset the Value
     * 
     * @return void
     */
    public function clearValue(){
        $this->value = 0;
    }

};
