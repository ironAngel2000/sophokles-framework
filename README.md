# sophokles-framework

## Getting Started

### About


### Installation

Use Sophokles Framework by Composer 

```bash
composer require sophokles/framework
```
or
```json
{
    "require": {
        "sophokles/framework": "1.*",
    }
}
```
in your composer.json

The Framework needs follow strukture
```dir
/
  local
    traits
   system
     config
     traits 
``` 
with the follow call in your Browser this directories and files will be created
```url
[yourdomain]/vendor/sophokles/framework/setup/setup.php
```
## Models
### Create database models
```php
/**
* @property typeInt $id,
* @property typeInt $refField,
* @property typeInt $order,
* @property typeText $name,
* @property typeFloat $amount,
* @property typeText $description,
* @property typeText $created,
* @property typeInt $updated,
*/
class MyModel extends dataset
{
    
    protected $table = 'table_name';

    protected function defineTableScheme()
    {
        $schema  = & $this->objTableScheme;
        $schema->addColumn('id', FieldType::INT)
            ->length(11)
            ->autoincrement() // for Autoincrement use onlie INT or BIGINT
            ->primary();
            
        $schema->addColumn('refField', FieldType::INT)
            ->length(11);
            
        $schema->addColumn('order', FieldType::INT)
            ->length(11);
            
        $schema->addColumn('name', FieldType::VARCHAR)
            ->length(64)
            ->index();
            
        $schema->addColumn('amount', FieldType::DECIMAL)
            ->length(8, 2);
            
        $schema->addColumn('description', FieldType::TEXT)
            ->length(64);
            
        $schema->addColumn('created', FieldType::DATETIME)
             ->defaultValue('now()');
        
        $schema->addColumn('updated', FieldType::TIMESTAMP)
             ->defaultValue('now()');

        $schema->addKey(['refField', 'order'], false);

    }

    protected function defineSorting()
    {
        $this->objSorting->setSortColumn('order');
        $this->objSorting->addReferenceColumn('refField');
        $this->objSorting->setListFunction('listForOrderFunction');
    }

    protected function initClass()
    {
        // TODO: Implement initClass() method.
    }

    public function listForOrderFunction(int $refValue)
    {
        return $this->getEntriesbyParam(['refField' => $refValue], ['order' => 'ASC']);
    }
}
```
### Create table in Database
(Tested on mySQL 8)
```php
(new MyModel())->tableSchemeUpdate();
```

### handle diferent MySql - Databases
```php
namespace System\Config;

class db2 extends \Sophokles\Database\dbconfig
{
    public function __construct()
    {
        $this->setHost('host');
        $this->setDatabase('database');
        $this->setUser('user');
        $this->setPassword('password');
        $this->setPort(3306);
    }
}
```
```php
$myObj = new MyModel(2);
```
###
