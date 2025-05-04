<?php

namespace Siarko\ModularObject\Builder;

use Siarko\ModularObject\Registry;

class Builder
{

    public function __construct(
        private readonly Registry $registry
    )
    {
    }

    public function build(object $object): void
    {
        $config = $this->registry->getConfig($object::class);
        foreach ($config->getBuilders() as $builder) {
            $builder->buildObject($object);
        }
    }

    public function buildProperty(object $object, mixed $value, string $propertyName): mixed
    {
        $config = $this->registry->getConfig($object::class);
        if (($propertyConfig = $config->getProperties()[$propertyName] ?? false)) {
            return null;
        }
        foreach ($propertyConfig->getBuilders() as $builder) {
            $value = $builder->buildProperty($object, $value, $propertyName);
        }
        return $value;
    }

}