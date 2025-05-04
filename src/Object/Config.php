<?php

namespace Siarko\ModularObject\Object;

use Siarko\ModularObject\Api\Builder\ConstructorBuilderInterface;

class Config
{


    /**
     * @param PropertyFactory $factory
     * @param string|null $baseClassName
     * @param ConstructorBuilderInterface[] $builders
     * @param PropertyConfig[] $properties
     */
    public function __construct(
        PropertyFactory $factory,
        private readonly ?string $baseClassName = null,
        private readonly array $builders = [],
        private readonly array $properties = [],
    )
    {
    }

    public function getBaseClassName(): ?string
    {
        return $this->baseClassName;
    }

    public function getBuilders(): array
    {
        return $this->builders;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}