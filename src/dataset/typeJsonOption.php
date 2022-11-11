<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 16.03.2019
 * Time: 07:50
 */

namespace Sophokles\Dataset;

class typeJsonOption extends typeJson
{
    /**
     * Constructor
     * 
     * @param string JSON $json
     * @return self
     */
    public function __construct($json = null) {
        parent::__construct($json);
    }

    /**
     * Set a value according to a key
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setKeyValue(string $key, $value)
    {
        parent::setField(array($key), $value);
    }

    /**
     * Get a value according to a key
     * 
     * @param string $key
     * @return mixed
     */
    public function getKeyValue(string $key)
    {
        return parent::getField(array($key));
    }

    /**
     * Remove a value according to a key
     * 
     * @param string $key
     * @return void
     */
    public function delKeyValue($key = null)
    {
        parent::removeField(array(key));
    }

}