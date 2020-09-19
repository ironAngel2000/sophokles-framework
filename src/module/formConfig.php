<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 07.04.2019
 * Time: 13:00.
 */

namespace Sophokles\Module;

use Sophokles\Database\query;
use Sophokles\Dataset\dataset;
use Sophokles\Dataset\typeJson;
use Sophokles\Dataset\typeText;
use Sophokles\Translation\lang;

class formConfig
{
    /** @var array $arrFields */
    protected $arrFields = [];

    /** @var array $arrFields */
    protected $arrListGroups = [];

    /** @var string $fieldgroup */
    protected $fieldgroup = '';

    /** @var integer $index */
    protected $index = 0;

    /**
     * Constructor.
     *
     * @return self
     */
    public function __construct()
    {
        $this->arrFields = [];
        $this->index = 0;
    }

    /**
     * Define a for the fields
     *
     *  @var string $groupname
     */

    public function defineGroupName(string $groupname)
    {
        $this->fieldgroup = $groupname;
    }

    /**
     * Generates new object of formfield.
     *
     * @var string
     *
     * @return formField
     */
    public function newField(string $name): formField
    {
        $formField = new formField();
        $this->arrFields[$this->index] = $formField;
        $formField->setName($name);
        $formField->setGroup($this->fieldgroup);

        $this->index++;

        return $formField;
    }


    public function newListGroup(string $name): formListGroup
    {
        $formListGroup = new formListGroup();
        $this->arrListGroups[$this->index] = $formListGroup;
        $formListGroup->setName($name);
        $formListGroup->setGroup($this->fieldgroup);

        $this->index++;

        return $formListGroup;
    }

    /**
     * Get the twig form.
     *
     * @param dataset $dataset
     *
     * @return string
     */
    public function renderForm(dataset $dataset): array
    {
        $ret = [];

        for($ind =0; $ind < $this->index; $ind++){

            if(isset($this->arrFields[$ind])){
                $obj = $this->arrFields[$ind];
            }
            elseif(isset($this->arrListGroups[$ind])){
                $obj = $this->arrListGroups[$ind];
            }


            if($obj instanceof formField){
                $ret[] = $this->renderFormField($obj, $dataset);
            }

            if($obj instanceof formListGroup){
                $ret[] = $this->renderListGroup($obj,$dataset);
            }

        }

        return $ret;
    }

    protected function renderFormField(formField $objField, dataset $dataset) : array
    {
        $row = [];

        $val = null;
        if(trim($objField->getColumn())!=='') {
            if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                if ($dataset->{$objField->getColumn()} instanceof typeJson) {
                    $val = $dataset->{$objField->getColumn()}->getFieldVal($objField->getJsonAdress());
                } else {
                    $val = (new typeJson($dataset->{$objField->getColumn()}->getVal()))->getFieldVal($objField->getJsonAdress());
                }
            } else {
                $val = $dataset->{$objField->getColumn()}->getVal();
            }
        }

        switch ($objField->getType()) {
            case formField::TYPE_FILE:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['placeholder'] = $objField->getPlaceholder();
                $row['value'] = $val;
                $row['description'] = $objField->getDescription();
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $row['refmodule'] = $objField->getModule();
                break;
            case formField::TYPE_FIRTNAM_LASTNAME:
                //nobreak
            case formField::TYPE_STREETNO:
                //nobreak
            case formField::TYPE_POSTCODECITY:
                //nobreak
            case formField::TYPE_HIDDEN:
                //nobreak
            case formField::TYPE_PASSWORD:
                //nobreak
            case formField::TYPE_TEXTFORMAT:
                //nobreak
            case formField::TYPE_TEXTMULTILINE:
                //nobreak
            case formField::TYPE_TEXT_INLINE:
                //nobreak
            case formField::TYPE_TEXT:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['placeholder'] = $objField->getPlaceholder();
                $row['value'] = $val;
                $row['description'] = $objField->getDescription();
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $row['group'] = $objField->getGroup();
                break;
            case formField::TYPE_OBJLISTREFERENCE:
                $clasname = $objField->getClassname();
                $objVal = new $clasname(...$objField->getObjectInitValues());
                if($objVal instanceof abstractModule) {
                    $pField = $objVal->getObjDataset()->getPrimaryFields()[0];
                }

                if($val==0 && (int) $objField->getFallbackValue()!==0){
                    $val = (int) $objField->getFallbackValue();
                }

                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['placeholder'] = $objField->getPlaceholder();
                $row['value'] = $val;
                $row['description'] = $objField->getDescription();
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $checkVal = $dataset->{$pField}->getVal();
                if(trim($objField->getReferenceValue())!==''){
                    $checkVal = (int) trim($objField->getReferenceValue());
                }
                $list = $objVal->getReferenceList(0,$checkVal);
                array_unshift($list,'{"uniqueid":"'.uniqid('',false).'","id":0,"ref_id":0,"sorting":0,"name":"'.lang::get('no parent').'","jsondata":"[]"}');
                $row['list'] = $list;
                $row['group'] = $objField->getGroup();
                break;
            case formField::TYPE_OBJLIST:
                $clasname = $objField->getClassname();
                $objVal = new $clasname(...$objField->getObjectInitValues());

                if($val==0 && (int) $objField->getFallbackValue()!==0){
                    $val = (int) $objField->getFallbackValue();
                }

                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['placeholder'] = $objField->getPlaceholder();
                $row['value'] = $val;
                if($objField->getFallbackValue()){
                    $row['value'] = $objField->getFallbackValue();
                }
                $row['description'] = $objField->getDescription();
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $row['multiple'] = $objField->isMultipleSelect();

                if(count($objField->getObjectMethodParameters())){
                    $row['list'] = $objVal->{$objField->getObjectMethod()}(... $objField->getObjectMethodParameters());
                }
                else{
                    $row['list'] = $objVal->{$objField->getObjectMethod()}(0);
                }
                $row['group'] = $objField->getGroup();
                break;
            case formField::TYPE_CHECKBOX:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['placeholder'] = $objField->getPlaceholder();
                $row['value'] = $val;
                $row['description'] = $objField->getDescription();
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $row['inpVal'] = $objField->getValue();
                $row['group'] = $objField->getGroup();
                break;
            case formField::TYPE_MULTIPLESELECT:
                //nobreak
            case formField::TYPE_SELECT:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['value'] = $val;
                $row['type'] = 'form-'.$objField->getType();
                $row['required'] = $objField->getRequired();
                $row['list'] = $objField->getSelectList();
                break;
            case formField::TYPE_TEXTOUTPUT:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['type'] = 'form-'.$objField->getType();
                $row['value'] = $val;
                $row['defValue'] = $objField->getFallbackValue();
                break;
            case formField::TYPE_TITLE:
                $row['name'] = 'inp'.$objField->getName();
                $row['label'] = $objField->getLabel();
                $row['type'] = 'form-'.$objField->getType();
                break;
        }


        return $row;
    }

    protected function renderListGroup(formListGroup $listGroup, dataset $dataset) : array
    {
        $row = [];

        $row['name'] = $listGroup->getName();
        $row['label'] = $listGroup->getLabel();
        $row['description'] = $listGroup->getDescription();
        $row['type'] = 'form-listGroup';

        $row['elements'] = [];

        foreach ($listGroup->getFormFields() as $label=> $arrFormField){

            $elements = [];

            foreach ($arrFormField as $formField){
                if($formField instanceof formField){
                    $elements[] = $this->renderFormField($formField, $dataset);
                }
            }

            $row['elements'][$label] = $elements;

        }

        return $row;
    }

    /**
     * Save the form from post data.
     *
     * @param array $primary
     */
    public function saveForm(array $primaryVal, dataset &$dataset)
    {

        $dataset->getEntries($primaryVal);

        foreach ($this->arrFields as $objField) {

            $this->saveObjField($objField, $dataset);

        }

        foreach ($this->arrListGroups as $lstGrooup){
            if($lstGrooup instanceof formListGroup){

                foreach ($lstGrooup->getFormFields() as $arrFields){
                    foreach($arrFields as $objField){
                        $this->saveObjField($objField, $dataset);
                    }
                }

            }
        }
        $dataset->save();
    }

    protected function saveObjField(formField $objField, dataset &$dataset)
    {

        $formName = 'inp'.$objField->getName();

        switch ($objField->getType()){
            case formField::TYPE_MULTIPLESELECT:

                $saveVal = 0;

                foreach ($objField->getSelectList() as $key=>$jsonEntry){

                    $tmpFormName = $formName.'_'.$key;

                    if(isset($_POST[$tmpFormName])){
                        $saveVal += (int) $_POST[$tmpFormName];
                    }
                }

                if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                    $dataset->{$objField->getColumn()}->setFieldVal($objField->getJsonAdress(), $saveVal);
                } else {
                    $dataset->{$objField->getColumn()}->setVal($saveVal);
                }

                break;
            case formField::TYPE_FIRTNAM_LASTNAME:
                //nobreak
            case formField::TYPE_POSTCODECITY:
                //nobreak
            case formField::TYPE_STREETNO:
                if (isset($_POST[$formName.'_1']) && isset($_POST[$formName.'_2'])) {

                    if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {

                        $adr1 = $objField->getJsonAdress();
                        $adr1[] = '1';

                        $adr2 = $objField->getJsonAdress();
                        $adr2[] = '2';

                        if($dataset->{$objField->getColumn()} instanceof typeJson){
                            $dataset->{$objField->getColumn()}->setFieldVal($adr1, $_POST[$formName.'_1']);
                            $dataset->{$objField->getColumn()}->setFieldVal($adr2, $_POST[$formName.'_2']);
                        }
                        elseif($dataset->{$objField->getColumn()} instanceof typeText){
                            $objJson = new typeJson($dataset->{$objField->getColumn()}->getVal());
                            $objJson->setFieldVal($adr1, $_POST[$formName.'_1']);
                            $objJson->setFieldVal($adr2, $_POST[$formName.'_2']);
                            $dataset->{$objField->getColumn()}->setVal($objJson->getJsonString());
                        }
                    } elseif($dataset->{$objField->getColumn()} instanceof typeText){

                        $adr1 = [];
                        $adr1[] = '1';

                        $adr2 = [];
                        $adr2[] = '2';

                        $objJson = new typeJson($dataset->{$objField->getColumn()}->getVal());
                        $objJson->setFieldVal($adr1, $_POST[$formName.'_1']);
                        $objJson->setFieldVal($adr2, $_POST[$formName.'_2']);
                        $dataset->{$objField->getColumn()}->setVal($objJson->getJsonString());
                    }


                }
                break;
            default:
                if (isset($_POST[$formName])) {
                    if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                        if($dataset->{$objField->getColumn()} instanceof typeJson){
                            $dataset->{$objField->getColumn()}->setFieldVal($objField->getJsonAdress(), $_POST[$formName]);
                        }
                        elseif($dataset->{$objField->getColumn()} instanceof typeText){
                            $objJson = new typeJson($dataset->{$objField->getColumn()}->getVal());
                            $objJson->setFieldVal($objField->getJsonAdress(), $_POST[$formName]);
                            $dataset->{$objField->getColumn()}->setVal($objJson->getJsonString());
                        }
                    } else {
                        $dataset->{$objField->getColumn()}->setVal($_POST[$formName]);
                    }
                }
                else{
                    if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                        $dataset->{$objField->getColumn()}->setFieldVal($objField->getJsonAdress(), '');
                    }
                }
                break;
        }

    }

}
