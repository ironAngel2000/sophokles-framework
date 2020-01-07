<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class typeInt{

    /** @var int $value */
    private $value;

    /**
     * Contructor
     * 
     * @param int $initVal
     * @return self
     */
    public function __construct(int $initVal=0){
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
     * @return int
     */
    public function getVal(){
       return (int) ($this->value);
    }

    /**
     * Set Value
     * 
     * @param int $numval
     * @return void
     */
    public function setVal(int $numval){
       $this->value = (int) ($numval);
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
