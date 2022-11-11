<?php

namespace Sophokles\Dataset;

class typeBoolean
{

    /** @var bool $value */
    private $value;

    /**
     * Contructor
     *
     * @param bool $initVal
     * @return self
     */
    public function __construct(bool $initVal=false){
        $this->setVal($initVal);
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString(){
        return $this->value ? 'true': 'false';
    }


    /**
     * Get Value
     *
     * @return bool
     */
    public function getVal(){
        return  $this->value;
    }

    /**
     * Set Value
     *
     * @param bool $boolVal
     * @return void
     */
    public function setVal(bool $boolVal){
        $this->value = $boolVal;
    }

    /**
     * Reset the Value
     *
     * @return void
     */
    public function clearValue(){
        $this->value = false;
    }

}
