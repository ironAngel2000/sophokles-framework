<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class typeJson
{
   
    /** @var array $arrData */
    protected $arrData;

    /** @var mixed $fldVal */
    protected $fldVal;

    /**
     * Constructor
     * 
     * @param string JSON $json
     * @return self
     */
    public function __construct($json = null) {
        $this->fldVal = $json;
        if ($json!==null){
            $this->arrData = json_decode($json, true);
        }
        else{
            $this->arrData = [];
        }
    }


    /**
     * @param array $arrLocation
     * 
     * @return Ambigous <string, number>
     */
    public function getFieldVal(array $arrLocation){

        if(!is_array($arrLocation)){
            trigger_error('Array as parameter expected', E_USER_ERROR);
        }


        $tVal = $this->arrData;
        $retVal = null;
        foreach($arrLocation as $locAdr){
            if(isset($tVal[$locAdr])){
                $retVal = $tVal[$locAdr];
                $tVal = &$tVal[$locAdr];
            }
            else{
                $retVal = null;
            }
        }

        return $retVal;
    }

    /**
     * Set new value to array located by parameter as array
     * 
     * @param array $arrLocation
     * @param mixed $data
     * @return void
     */
    public function setFieldVal(array $arrLocation, $data)
    {

        if(!is_array($arrLocation)){
            trigger_error('Array as parameter expected', E_USER_ERROR);
        }

        $nVal = [];

        $mVal = &$nVal;
        $i=0;
        foreach($arrLocation as $locAdr){
            if(!isset($mVal[$locAdr])){
                $mVal[$locAdr] = [];
            }
            $mVal = &$mVal[$locAdr];
            $i++;
            if($i==count($arrLocation)){
                $mVal = $data;
            }
        }

        if(!is_array($this->arrData)){
            $this->arrData = [];
        }

        $this->arrData = $this->meltArray($this->arrData, $nVal);

        $this->fldVal = json_encode($this->arrData);
    }

    /**
     * Set JSON value to local array
     * 
     * @param string JSON $json
     * @return void
     */
    public function setVal(string $json)
    {
        $arrJson = json_decode($json, true);
        $this->arrData = $arrJson;
    }

    /**
     * Get get the JSON value
     * 
     * @return string JSON
     */
    public function getVal(){
        return $this->getJsonString();
    }

    /**
     * Get get the JSON value
     * 
     * @return string JSON
     */
    public function getJsonString(){
        return json_encode($this->arrData);
    }

    /**
     * Get get the array
     * 
     * @return array
     */
    public function getDataArray(){
        return $this->arrData;
    }


    /**
     * Reset the value
     * 
     * @return void
     */
    public function clearValue(){
        $this->arrData = [];
        $this->fldVal = json_encode($this->arrData);
    }

    /**
     * Reset the value on a location
     * 
     * @return void
     */
    public function removeField(array $arrLocation){

        if(!is_array($arrLocation)){
            trigger_error('Array as parameter expected', E_USER_ERROR);
        }


        $mVal = &$this->arrData;
        $i = 0;
        foreach($arrLocation as $locAdr){
            $i++;
            if(isset($mVal[$locAdr])){
                if($i==count($arrLocation)){
                    unset($mVal[$locAdr]);
                    break;
                }
            }
            else{
                break;
            }
            $mVal = &$mVal[$locAdr];
        }

        $this->fldVal = json_encode($this->arrData);
    } 

    /**
     * Melt 2 Arrays
     * 
     * @param array $array1
     * @param array $array 2
     * @return array
     */
    private function meltArray(array $array1,array $array2)
    {
       $ret = [];

       $equalKey = false;

       if(count($array1)){
           foreach($array1 as $key=>$value){
               if(isset($array2[$key])){
                   $equalKey = $key;
                   break;
               }
           }
       }

       if($equalKey===false){

            if(count($array1)){
                foreach($array1 as $arrKey=>$arrValue){
                    $ret[$arrKey] = $arrValue;
                }
            }

            if(count($array2)){
                foreach($array2 as $arrKey=>$arrValue){
                    $ret[$arrKey] = $arrValue;
                }
            }

       }
       else{
           $eqSet = false;

           if(count($array1)){
                foreach($array1 as $arrKey=>$arrValue){
                    if($arrKey !== $equalKey){
                        $ret[$arrKey] = $arrValue;
                    }
                    elseif($eqSet===false){
                        if(is_array($array1[$equalKey]) && is_array($array2[$equalKey])){
                            $ret[$equalKey] = $this->meltArray( $array1[$equalKey], $array2[$equalKey]);
                            $eqSet = true;
                        }
                        else{
                            if($array1[$equalKey] === $array2[$equalKey]){
                                $ret[$equalKey] = $array1[$equalKey];
                                $eqSet = true;
                            }
                            else{
                                $ret[$equalKey] = $array2[$equalKey];
                                $eqSet = true;
                            }
                        }
                    }

                }
            }

           if(count($array2)){
                foreach($array2 as $arrKey=>$arrValue){
                    if($arrKey !== $equalKey){
                        $ret[$arrKey] = $arrValue;
                    }
                    elseif($eqSet===false){
                        if(is_array($array1[$equalKey]) && is_array($array2[$equalKey])){
                            $ret[$equalKey] = $this->meltArray( $array1[$equalKey], $array2[$equalKey]);
                            $eqSet = true;
                        }
                        if($array1[$equalKey] === $array2[$equalKey]){
                            $ret[$equalKey] = $array2[$equalKey];
                            $eqSet = true;
                        }
                        else{
                            $ret[$equalKey] = $array1[$equalKey];
                            $eqSet = true;
                        }
                    }
                }
            }
        
       }
   
       return $ret;
   }
}