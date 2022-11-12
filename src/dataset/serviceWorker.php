<?php

namespace Sophokles\Dataset;

abstract class serviceWorker
{
    abstract function beforeCreate(dataset &$object);
    abstract function afterCreate(dataset &$object);

    abstract function beforeUpdate(dataset &$object);
    abstract function afterUpdate(dataset &$object);

    abstract function beforeQuery(dataset &$object);
    abstract function afterQuery(dataset &$object);

    abstract function beforeDelete(dataset &$object);
    abstract function afterDelete(dataset &$object);
}