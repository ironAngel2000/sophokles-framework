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
    const TYPE_TEXT_INLINE = 'textinline';
    const TYPE_PASSWORD = 'password';
    const TYPE_OBJLIST = 'objectlist';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_OBJLISTREFERENCE = 'objectreference';
    const TYPE_FILE = 'file';
    const TYPE_SELECT = 'select';
    const TYPE_TEXTFORMAT = 'textformat';
    const TYPE_TEXTMULTILINE = 'textmultiline';
    const TYPE_MULTIPLESELECT = 'multipleselect';
    const TYPE_FIRTNAM_LASTNAME = 'firstlastname';
    const TYPE_STREETNO = 'streetno';
    const TYPE_POSTCODECITY = 'postcodecity';
    const TYPE_TITLE = 'title';
    const TYPE_TEXTOUTPUT = 'textoutput';

    /** @var string $name */
    protected $name = '';

    /** @var string $column */
    protected $column = '';

    /** @var array $jsonAdress */
    protected $jsonAdress = [];

    /** @var string $type */
    protected $type = '';

    /** @var string $label */
    protected $label = '';

    /** @var string $description */
    protected $description = '';

    /** @var string $placeholder */
    protected $placeholder = '';

    /** @var bool $required */
    protected $required = false;

    /** @var string $classname */
    protected $classname = '';

    /** @var string $objectMethod */
    protected $objectMethod = 'getReferenceList';

    /** @var array $objectInitValues */
    protected $objectInitValues = [];

    /** @var array $objectMethodParameters */
    protected $objectMethodParameters = [];

    /** @var string $value */
    protected $value = '';

    /** @var string $group */
    protected $group = '';

    /** @var abstractModule $module */
    protected $module;

    /** @var string $referenceValue */
    protected $referenceValue = '';

    /** @var string $fallbackValue */
    protected $fallbackValue = '';

    /** @var array $selectList */
    protected $selectList = [];

    /** @var bool $multipleSelect */
    protected $multipleSelect = false;

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


    /**
     * Get the value of group
     *
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * Set the value of group
     *
     * @param string $group
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }


    /**
     * @return abstractModule
     */
    public function getModule(): abstractModule
    {
        if(!$this->module instanceof abstractModule){
            trigger_error('No Module defined', E_USER_ERROR);
        }
        else {
            return $this->module;
        }
    }

    /**
     * @param abstractModule $module
     */
    public function setModule(abstractModule $module): void
    {
        $this->module = $module;
    }


    /**
     * @return string
     */
    public function getReferenceValue(): string
    {
        return $this->referenceValue;
    }

    /**
     * @param string $referenceValue
     */
    public function setReferenceValue(string $referenceValue): void
    {
        $this->referenceValue = $referenceValue;
    }


    /**
     * @return string
     */
    public function getFallbackValue(): string
    {
        return $this->fallbackValue;
    }

    /**
     * @param string $fallbackValue
     */
    public function setFallbackValue(string $fallbackValue): void
    {
        $this->fallbackValue = $fallbackValue;
    }

    /**
     * @return array
     */
    public function getSelectList(): array
    {
        return $this->selectList;
    }

    /**
     * @param array $selectList
     */
    public function setSelectList(array $selectList): void
    {
        $this->selectList = $selectList;
    }

    public function addSelectListEntry($key, $label): void
    {
        if(!is_array($this->selectList)){
            $this->selectList = [];
        }

        $this->selectList[$key] = $label;

    }

    /**
     * @return array
     */
    public function getObjectInitValues(): array
    {
        return $this->objectInitValues;
    }

    /**
     * @param array $objectInitValues
     */
    public function setObjectInitValues(array $objectInitValues): void
    {
        $this->objectInitValues = $objectInitValues;
    }

    /**
     * @return string
     */
    public function getObjectMethod(): string
    {
        return $this->objectMethod;
    }

    /**
     * @param string $objectMethod
     */
    public function setObjectMethod(string $objectMethod): void
    {
        $this->objectMethod = $objectMethod;
    }

    /**
     * @return array
     */
    public function getObjectMethodParameters(): array
    {
        return $this->objectMethodParameters;
    }

    /**
     * @param array $objectMethodParameters
     */
    public function setObjectMethodParameters(array $objectMethodParameters): void
    {
        $this->objectMethodParameters = $objectMethodParameters;
    }

    /**
     * @return bool
     */
    public function isMultipleSelect(): bool
    {
        return $this->multipleSelect;
    }

    /**
     * @param bool $multipleSelect
     */
    public function setMultipleSelect(bool $multipleSelect): void
    {
        $this->multipleSelect = $multipleSelect;
    }


}
