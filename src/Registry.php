<?php

namespace Siarko\ModularObject;


use Siarko\ModularObject\Object\Config;
use Siarko\ModularObject\Object\ConfigFactory;

class Registry
{

    private array $objects = [];

    public function __construct(
        ConfigFactory $configFactory,
        array $objects = []
    )
    {
        array_walk($objects, function($config, $type) use ($configFactory) {
            $config['typeName'] = $type;
            $this->objects[$type] = $configFactory->create($config);
        });
    }

    public function isRegistered(string $typeName): bool
    {
        return array_key_exists($this->normalizeTypeName($typeName), $this->objects);
    }

    public function getConfig(string $typeName): ?Config
    {
        return $this->objects[$this->normalizeTypeName($typeName)] ?? null;
    }

    private function normalizeTypeName(string $typeName): string
    {
        if(!str_starts_with($typeName, '\\')){
            $typeName = '\\' . $typeName;
        }
        return $typeName;
    }
}