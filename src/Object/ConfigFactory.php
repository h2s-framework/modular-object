<?php

namespace Siarko\ModularObject\Object;

use Siarko\Api\Factory\AbstractFactory;

class ConfigFactory extends AbstractFactory
{

    public function create(array $data = []): Config
    {
        return parent::_create(Config::class, $data);
    }
}