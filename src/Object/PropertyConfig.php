<?php

namespace Siarko\ModularObject\Object;

use Siarko\ModularObject\Api\Builder\PropertyBuilderInterface;

class PropertyConfig
{

    /**
     * @param string|null $type
     * @param bool $nullable
     * @param mixed|null $default
     * @param bool $setter
     * @param PropertyBuilderInterface[] $builders
     */
    public function __construct(
        private readonly ?string $type = null,
        private readonly bool $nullable = false,
        private readonly mixed $default = null,
        private readonly bool $setter = true,
        private readonly array $builders = []
    )
    {
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function hasSetter(): bool
    {
        return $this->setter;
    }

    public function getBuilders(): array
    {
        return $this->builders;
    }

}