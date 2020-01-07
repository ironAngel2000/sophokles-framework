<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 07.04.2019
 * Time: 13:00.
 */

namespace Sophokles\Module;

class formField
{
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_OBJLIST = 'objectlist';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_OBJLISTREFERENCE = 'objectreference';

    /** @var string $name */
    protected $name;

    /** @var string $column */
    protected $column;

    /** @var array $jsonAdress */
    protected $jsonAdress;

    /** @var string $type */
    protected $type;

    /** @var string $label */
    protected $label;

    /** @var string $description */
    protected $description;

    /** @var string $placeholder */
    protected $placeholder;

    /** @var bool $required */
    protected $required;

    /** @var string $classname */
    protected $classname;

    /** @var string $value */
    protected $value;

    public function __construct()
    {
        $this->description = '';
        $this->label = '';
        $this->type = formField::TYPE_TEXT;
        $this->jsonAdress = [];
        $this->column = '';
        $this->name = '';
        $this->placeholder = '';
        $this->required = false;
        $this->classname = '';
        $this->value = '';
    }

    /**
     * Get the value of description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description.
     *
     * @var string
     *
     * @return self
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the value of label.
     *
     * @var string
     *
     * @return self
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type.
     *
     * @var string
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of jsonAdress.
     *
     * @return array
     */
    public function getJsonAdress(): array
    {
        return $this->jsonAdress;
    }

    /**
     * Set the value of jsonAdress.
     *
     * @var array
     *
     * @return self
     */
    public function setJsonAdress(array $jsonAdress)
    {
        $this->jsonAdress = $jsonAdress;

        return $this;
    }

    /**
     * Get the value of column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Set the value of column.
     *
     * @var string
     *
     * @return self
     */
    public function setColumn(string $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @var string
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of placeholder.
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * Set the value of placeholder.
     *
     * @var string
     *
     * @return self
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get the value of required.
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set the value of required.
     *
     * @var bool
     *
     * @return self
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get the value of classname.
     *
     * @return string
     */
    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * Set the value of classname.
     *
     * @param string $classname
     *
     * @return self
     */
    public function setClassname(string $classname)
    {
        $this->classname = $classname;

        return $this;
    }


    /**
     * Get the value of value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the value of value.
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value)
    {
        $this->value = $value;

        return $this;
    }    


}
