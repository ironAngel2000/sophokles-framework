<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 23.03.2019
 * Time: 06:45.
 */

namespace Sophokles\Module;

use Sophokles\Dataset\dataset;

abstract class abstractModule
{
    /** @var dataset $objDataset */
    protected $objDataset;

    /** @var abstractModule $refObjModule */
    protected $refObjModule;

    /** @var array $parentFields */
    protected $parentFields;

    /** @var abstractModule $appendObjModule */
    protected $appendObjModule;

    /** @var array $appendKeyFields */
    protected $appendKeyFields;

    /** @var formConfig $formConfig */
    protected $formConfig;

    /**
     * Constuctor.
     *
     * @var dataset
     *
     * @return self
     */
    public function __construct(dataset $objDataset)
    {
        $this->objDataset = $objDataset;
        $this->formConfig = new formConfig();

        $this->parentFields = [];

        $this->registerTranslation();
        $this->setFormConfig();

        $this->appendObjModule = null;
    }

    public function __clone()
    {
        if ($this->appendObjModule !== null) {
            $this->appendObjModule = clone $this->appendObjModule;
        }

        $this->objDataset = clone $this->objDataset;
        $this->formConfig = clone $this->formConfig;
    }

    /**
     * Get the dataset object.
     *
     * @return dataset
     */
    public function getObjDataset()
    {
        return $this->objDataset;
    }

    /**
     * Set a reference object for database dependencies.
     *
     * @var abstractModule
     * @var array[string]  $parentFields
     */
    public function appendReferenceObject(abstractModule $objModule, array $parentFields)
    {
        $arrRefPrimary = $objModule->getObjDataset()->getPrimaryFields();
        if (count($arrRefPrimary) !== count($parentFields)) {
            trigger_error('The values array is not similar to the primary fields of the reference object ', E_USER_ERROR);
        }

        $this->appendObjModule = $objModule;
        $this->appendKeyFields = $parentFields;
    }

    /**
     * Define a recursion strukture.
     *
     * @var string
     */
    public function setRecrusiveDepenency(string $parentField)
    {
        $arrRefPrimary = $this->objDataset->getPrimaryFields();
        if (count($arrRefPrimary) > 1) {
            trigger_error('Only database tables with one primary field can set to a recursion', E_USER_ERROR);
        }

        $this->parentFields = [$parentField];
    }

    /**
     * Returns the append reference object.
     *
     * @return module|null
     */
    public function getAppendObject()
    {
        return $this->appendObjModule;
    }

    /**
     * Returns the recursion status of object.
     *
     * @return bool
     */
    public function getRecursiveReferenceObject(): bool
    {
        if (\is_array($this->parentFields) && count($this->parentFields)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * return the path of the Classfile.
     */
    public function getDir()
    {
        $reflector = new \ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }

    /**
     * get the fromconfig object.
     *
     * @return formConfig
     */
    public function getFormConfig(): formConfig
    {
        return $this->formConfig;
    }

    /**
     * Returns the database entries for refercence selection list.
     *
     * @return array
     */
    public function getReferenceList($arrParam = [], $arrSort = [], int $checkRecursionId = 0): array
    {
        $ret = [];
        $index = 0;

        if(!is_array($arrParam)){
            $arrParam = [];
        }

        if ($this->objDataset->getEntriesbyParam($arrParam, $arrSort)) {
            do {
                $useIndex = true;

                if($checkRecursionId!==0){
                    $useIndex = false;

                    if($this->isRecursiveActiveRecord($checkRecursionId)==false){
                        $useIndex = true;
                    }
                }

                if ($useIndex === true) {
                    $ret[$index] = json_encode($this->objDataset->getRecord2Array());
                    $index++;

                    $recursObj = null;

                    if ($this->getRecursiveReferenceObject()) {
                        $recursObj = clone $this;
                    }

                    if ($recursObj instanceof abstractModule) {
                        $chParam = [];
                        foreach ($this->parentFields as $keyNr => $colName) {
                            $prColName = $this->objDataset->getPrimaryFields()[$keyNr];
                            $chParam[$colName] = $this->objDataset->{$prColName}->getVal();
                        }

                        $childs = $recursObj->getReferenceList($chParam, $arrSort);
                        if (\is_array($childs) && count($childs)) {
                            $ret[$index] = $childs;
                            ++$index;
                        }
                    }
                } // ENDIF $useIndex !== true

            } while ($this->objDataset->moveNext());
        }

        return $ret;
    }

    /**
     * check if id is active in recursive structure
     *
     * @param integer $checkId
     * @return boolean
     */
    protected function isRecursiveActiveRecord(int $checkId): bool
    {
        $ret = false;

        $recursObj = clone $this->objDataset;
        $pid = $checkId;
        while ((int)$pid != 0 && $ret == false) {
            if ((int)$pid == ((int) $this->objDataset->ref_id->getVal())) {
                $ret = true;
            } else {
                $recursObj->getEntries([$pid]);
                $pid = $recursObj->ref_id->getVal();
            }
        }

        return $ret;
    }

    /**
     * The translation file for the module.
     *
     * @return string
     */
    abstract protected function registerTranslation();

    /**
     * define the form config settings.
     */
    abstract public function setFormConfig();

    /**
     * Returns the path for twig templates.
     *
     * @return string
     */
    abstract public function getTwigDir();

    /**
     * Returns the database entries for administration selection list.
     *
     * @return array
     */
    abstract public function getAdminList(): array;
    
}
