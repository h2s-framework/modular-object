<?php

namespace Siarko\ModularObject\Api\Builder;

interface PropertyBuilderInterface
{

    public function buildProperty(object $object, mixed $value, string $propertyName): mixed;

}