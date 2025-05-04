<?php

namespace Siarko\ModularObject\Object;

use Siarko\Api\Factory\AbstractFactory;

class PropertyFactory extends AbstractFactory
{

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data = []): object
    {
        return parent::_create(PropertyConfig::class, $data);
    }
}