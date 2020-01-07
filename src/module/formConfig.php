<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 07.04.2019
 * Time: 13:00.
 */

namespace Sophokles\Module;

use Sophokles\Sophokles\lang;
use Sophokles\Dataset\dataset;

class formConfig
{
    /** @var array $arrFields */
    protected $arrFields;

    /**
     * Constructor.
     *
     * @return self
     */
    public function __construct()
    {
        $this->arrFields = [];
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
        $this->arrFields[] = $formField;
        $formField->setName($name);

        return $formField;
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

        foreach ($this->arrFields as $objField) {
            if ($objField instanceof formField) {
                if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                    $val = $dataset->{$objField->getColumn()}->getFieldVal($objField->getJsonAdress());
                } else {
                    $val = $dataset->{$objField->getColumn()}->getVal();
                }

                switch ($objField->getType()) {
                    case formField::TYPE_HIDDEN:
                        //nobreak
                    case formField::TYPE_PASSWORD:
                        //nobreak
                    case formField::TYPE_TEXT:

                        $row = [];

                        $row['name'] = 'inp'.$objField->getName();
                        $row['label'] = $objField->getLabel();
                        $row['placeholder'] = $objField->getPlaceholder();
                        $row['value'] = $val;
                        $row['description'] = $objField->getDescription();
                        $row['type'] = 'form-'.$objField->getType();
                        $row['required'] = $objField->getRequired();

                        $ret[] = $row;

                        break;
                    case formField::TYPE_OBJLISTREFERENCE:
                        $clasname = $objField->getClassname();
                        $objVal = new $clasname();

                        $row['name'] = 'inp'.$objField->getName();
                        $row['label'] = $objField->getLabel();
                        $row['placeholder'] = $objField->getPlaceholder();
                        $row['value'] = $val;
                        $row['description'] = $objField->getDescription();
                        $row['type'] = 'form-'.$objField->getType();
                        $row['required'] = $objField->getRequired();
                        $list = $objVal->getReferenceList([0],[], $val);
                        array_unshift($list,'{"uniqueid":"'.uniqid('',false).'","id":0,"ref_id":0,"sorting":0,"name":"'.lang::get('no parent').'","jsondata":"[]"}');
                        $row['list'] = $list;

                        $ret[] = $row;
                        break;
                    case formField::TYPE_OBJLIST:
                        $clasname = $objField->getClassname();
                        $objVal = new $clasname();

                        $row['name'] = 'inp'.$objField->getName();
                        $row['label'] = $objField->getLabel();
                        $row['placeholder'] = $objField->getPlaceholder();
                        $row['value'] = $val;
                        $row['description'] = $objField->getDescription();
                        $row['type'] = 'form-'.$objField->getType();
                        $row['required'] = $objField->getRequired();
                        $row['list'] = $objVal->getReferenceList();

                        $ret[] = $row;
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

                        $ret[] = $row;
                        break;
                }
            }
        }

        return $ret;
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
            $formName = 'inp'.$objField->getName();

            if (isset($_POST[$formName])) {
                if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                    $dataset->{$objField->getColumn()}->setFieldVal($objField->getJsonAdress(), $_POST[$formName]);
                } else {
                    $dataset->{$objField->getColumn()}->setVal($_POST[$formName]);
                }
            }
            else{
                if (is_array($objField->getJsonAdress()) && count($objField->getJsonAdress()) > 0) {
                    $dataset->{$objField->getColumn()}->setFieldVal($objField->getJsonAdress(), '');
                }
            }
        }


        $dataset->save();
    }
}
