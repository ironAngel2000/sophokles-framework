<?php

namespace Sophokles\Database;

class tablecolumn
{

    protected int $len = 0;
    protected int $accuracy = 0;
    protected bool $notNullv = false;
    protected $defValue = null;
    protected $indexType = null;
    protected bool $boolAutoincrement = false;
    protected $enumVals = [];

    public function __construct(
        protected string    $colName,
        protected FieldType $type
    )
    {
    }

    public function length(int $length, int $digits = 0)
    {
        $this->len = $length;
        $this->accuracy = $digits;
        return $this;
    }

    public function notNull()
    {
        $this->notNullv = true;
        return $this;
    }

    public function defaultValue($value)
    {
        $this->defValue = $value;
        return $this;
    }

    public function index()
    {
        $this->indexType = 'index';
        return $this;
    }

    public function unique()
    {
        $this->indexType = 'unique';
        return $this;
    }

    public function primary()
    {
        $this->indexType = 'primary';
        $this->notNullv = false;
        return $this;
    }

    public function autoincrement()
    {
        switch ($this->type) {
            case FieldType::INT:
                //no break
            case FieldType::BIGINT:
                $this->indexType = 'primary';
                $this->notNullv = false;
                $this->boolAutoincrement = true;
                break;
            default:
                user_error('only int / bigint fields can be autoincrement', E_USER_WARNING);
                break;
        }

        return $this;
    }

    public function enum(array $values)
    {
        $this->enumVals = $values;
        return $this;
    }

    public function columnName()
    {
        return $this->colName;
    }

    public function columnType()
    {
        return $this->type;
    }

    public function isPrimary()
    {
        return $this->indexType === 'primary';
    }

    public function isIndex()
    {
        return $this->indexType === 'index';
    }

    public function isUnique()
    {
        return $this->indexType === 'unique';
    }

    public function isAutoincrement()
    {
        $ret = false;
        if($this->type === FieldType::INT || $this->type === FieldType::BIGINT){
            $ret = $this->boolAutoincrement;
        }
        return $ret;
    }

    public function getAddStatement()
    {
        $statement = 'ADD COLUMN ' . $this->colName;

        switch ($this->type) {
            case FieldType::INT:
                $statement .= ' ' . $this->numberStatement('INT');
                break;
            case FieldType::BIGINT:
                $statement .= ' ' . $this->numberStatement('BIGINT');
                break;
            case FieldType::BIT:
                $statement .= ' ' . $this->bitStatement();
                break;
            case FieldType::BLOB:
                $statement .= ' ' . $this->blobStatement();
                break;
            case FieldType::BOOLEAN:
                $statement .= ' ' . $this->booleanStatement();
                break;
            case FieldType::CHAR:
                $statement .= ' ' . $this->charStatement('CHAR');
                break;
            case FieldType::VARCHAR:
                $statement .= ' ' . $this->charStatement('VARCHAR');
                break;
            case FieldType::DECIMAL:
                $statement .= ' ' . $this->floatStatement('DECIMAL');
                break;
            case FieldType::DATE:
                $statement .= ' ' . $this->dateTimeStatement('DATE');
                break;
            case FieldType::DATETIME:
                $statement .= ' ' . $this->dateTimeStatement('DATETIME');
                break;
            case FieldType::TIME:
                $statement .= ' ' . $this->dateTimeStatement('TIME');
                break;
            case FieldType::TIMESTAMP:
                $statement .= ' ' . $this->timeStampStatement();
                break;
            case FieldType::JSON:
                $statement .= ' JSON';
                break;
            case FieldType::TEXT:
                $statement .= ' TEXT DEFAULT NULL ';
                break;
            case FieldType::ENUM:
                $statement .= ' ' . $this->enumStatement();
                break;
        }

        return $statement;
    }

    private function numberStatement(string $type): string
    {
        $statement = $type;

        if ($this->len > 0) {
            $statement .= '(' . $this->len . ')';
        }

        $statement .= ' NOT NULL ';
        $statement .= ' DEFAULT ' . (int)$this->defValue;

        return $statement;
    }

    private function bitStatement(): string
    {
        $statement = 'BIT NOT NULL';

        if ($this->defValue === 0 || $this->defValue === 1) {
            $statement .= ' DEFAULT ' . $this->defValue;
        }

        return $statement;
    }

    private function blobStatement(): string
    {
        $statement = 'BLOB';
        if ($this->notNullv) {
            $statement .= ' NOT NULL DEFAULT EMPTY_BLOB()';
        }

        return $statement;
    }

    private function booleanStatement(): string
    {
        $statement = 'BOOLEAN';

        if ($this->defValue) {
            $statement .= ' DEFAULT true';
        } else {
            $statement .= ' DEFAULT false';
        }

        return $statement;
    }

    private function charStatement(string $type): string
    {
        $statement = $type;

        if ($this->len > 0) {
            $statement .= '(' . $this->len . ')';
        } else {
            $statement .= '(51)';
        }

        if ($this->notNullv) {
            $statement .= ' NOT NULL';
            $statement .= " DEFAULT '" . trim($this->defValue) . "'";
        } else if (trim($this->defValue) !== '') {
            $statement .= " DEFAULT '" . trim($this->defValue) . "'";
        }

        return $statement;
    }

    private function floatStatement(string $type): string
    {
        $statement = $type;

        $len = 12;
        $acc = 2;
        if ($this->len > 0) {
            $len = $this->len;
        }
        if ($this->accuracy > 0) {
            $acc = $this->accuracy;
        }

        $statement .= '(' . $len . ',' . $acc . ')';

        $statement .= ' DEFAULT ' . (float)$this->defValue;

        return $statement;
    }

    private function dateTimeStatement(string $type): string
    {
        $statement = $type;

        if ($this->notNullv) {
            $statement .= ' NOT NULL';
        }

        if($this->defValue==='now()') {
            $statement .= ' DEFAULT now()';
        } elseif (trim($this->defValue)!==''){
            $statement .= " DEFAULT '" . trim($this->defValue) . "'";
        } elseif ($this->notNullv) {
            $statement .= " DEFAULT '" . trim($this->defValue) . "'";
        }

        return $statement;
    }

    private function timeStampStatement(): string
    {
        $statement = 'TIMESTAMP NOT NULL';

        if($this->defValue==='now()') {
            $statement .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif (trim($this->defValue)!==''){
            $statement .= ' DEFAULT ' . (int)$this->defValue . ' ';
        }

        return $statement;
    }

    private function enumStatement(): string
    {
        $statement = 'ENUM';

        if(count($this->enumVals) < 2){
            user_error('please define enum values. more than one', E_USER_WARNING);
        }

        $statement.= '(';
        foreach ($this->enumVals as $i=>$enumVal) {
            if($i > 0){
                $statement .= ',';
            }
            $statement .= "'".$enumVal."'";
        }
        $statement .= ') NOT NULL';

        if (trim($this->defValue)!==''){
            $statement .= " DEFAULT '" . trim($this->defValue) . "'";
        }

        return $statement;
    }

}
