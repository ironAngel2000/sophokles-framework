<?php

namespace Sophokles\Module;

class formListGroup
{
    /** @var string $name */
    protected $name = '';

    /** @var string $label */
    protected $label = '';

    /** @var string $description */
    protected $description = '';

    /** @var string $group */
    protected $group = '';

    /** @var array $formFields */
    protected $formFields = [];

    /**
     * add an FormFiled to the ListGroup
     *
     * @var string $label
     * @var formField $field
     *
     */
    public function addFormFields(string $label, formField $field)
    {
        if(!isset($this->formFields[$label])){
            $this->formFields[$label] = [];
        }

        $this->formFields[$label][] = $field;
    }

    /**
     * Retrurns the list of Formfields
     *
     * @return array
     */
    public function getFormFields() : array
    {
        return $this->formFields;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }



    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }


}
