<?php

namespace Siarko\ModularObject\Generator\Developer\Object;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use Siarko\DependencyManager\Generator\IGenerator;
use Siarko\ModularObject\Builder\Builder;
use Siarko\ModularObject\Object\Config;
use Siarko\ModularObject\Object\PropertyConfig;
use Siarko\ModularObject\Registry;
use Siarko\Utils\Code\ClassStructureProvider;

class ModularObjectGenerator implements IGenerator
{

    private const PROPERTY_INITIALIZED = '__initializedProperties';
    private const PROPERTY_BUILDER = 'builder';

    public function __construct(
        private readonly Registry $config,
        private readonly ClassStructureProvider $classStructureProvider,
    )
    {
    }

    function canGenerate(string $className): bool
    {
        return $this->config->isRegistered($className);
    }

    function generate(string $fullClassName): string
    {
        $config = $this->config->getConfig($fullClassName);
        if(!$config){
            throw new \RuntimeException('Cannot generate modular object class for "' . $fullClassName. '" - config not found');
        }
        $classFile = new PhpFile();
        $class = $classFile->addClass($fullClassName);
        $this->generateClassCode($class, $config);
        return $classFile;
    }

    private function generateClassCode(ClassType $class, Config $config): void
    {
        if($config->getBaseClassName()){
            $class->setExtends($config->getBaseClassName());
        }
        $this->generateConstructor($class, $config);
        $this->generateCommonProperties($class, $config);
        foreach ($config->getProperties() as $name => $property) {
            $this->generateProperty($name, $class, $property);
        }
    }

    private function generateProperty(string $propertyName, ClassType $class, PropertyConfig $propertyConfig): void
    {
        $property = $class->addProperty($propertyName);
        if($typeName = $propertyConfig->getType()){
            $property->setType($typeName);
        }
        $property->setNullable($propertyConfig->isNullable());
        if($propertyConfig->isNullable()){
            $property->setValue($propertyConfig->getDefault());
        }else if($propertyConfig->getDefault() !== null){
            $property->setValue($propertyConfig->getDefault());
        }

        $this->generateMethods($class, $property, $propertyConfig);

    }

    private function generateMethods(ClassType $class, Property $property, PropertyConfig $propertyConfig): void
    {
        $propertyName = $property->getName();

        $getterBody = '';
        if(!empty($propertyConfig->getBuilders())){
            $getterBody .= 'if($this->'.self::PROPERTY_INITIALIZED.'[\'' . $propertyName . "'] ?? false) {\n";
            $getterBody .= "\t\$this->" . $propertyName . ' = $this->builder->buildProperty($this, $this->'.$propertyName.',"'.$propertyName.'");' . "\n";
            $getterBody .= "\t\$this->" . self::PROPERTY_INITIALIZED . '[\'' . $propertyName . "'] = true;\n";
            $getterBody .= "}\n";
        }
        $getterBody .= 'return $this->' . $propertyName . ';';

        $getter = $class->addMethod('get' . ucfirst($propertyName))->setReturnType($propertyConfig->getType());
        $getter->setBody($getterBody);

        if($propertyConfig->hasSetter()){
            $setter = $class->addMethod('set' . ucfirst($propertyName));
            $setter->setReturnType('void');
            $setterParameter = $setter->addParameter($propertyName, $propertyConfig->isNullable() ? null : $propertyConfig->getDefault());
            if($type = $propertyConfig->getType()){
                $setterParameter->setType($type);
            }
            $setterParameter->setNullable($propertyConfig->isNullable());
            $setter->setBody('$this->' . $propertyName . ' = $' . $propertyName . ';');
        }
    }

    private function generateConstructor(ClassType $class, Config $config): void
    {
        $parentParameters = null;
        if($baseClassName = $config->getBaseClassName()){
            $baseClassStructure = $this->classStructureProvider->get($baseClassName);
            $parentConstructor = $baseClassStructure->getMethod('__construct');
            $parentParameters = $parentConstructor?->getParameters();
        }

        $method = $class->addMethod('__construct');
        $constructorBody = '';
        if(!empty($parentParameters)){
            $parentParameterNames = implode(', ', array_map(function($param) use($method) {
                $constructorParam = $method->addParameter('_parent_'.$param->getName());
                if($type = $param->getType()){
                    $constructorParam->setType($type);
                }
                $constructorParam->setNullable($param->isNullable());
                if($param->isNullable()){
                    $constructorParam->setDefaultValue($param->getDefaultValue());
                }else if(!is_null($defaultValue = $param->getDefaultValue())){
                    $constructorParam->setDefaultValue($defaultValue);
                }
                return '$_parent_' . $param->getName();
                },
                $parentParameters
            ));
            $constructorBody .= 'parent::__construct('.$parentParameterNames.');'."\n";
        }

        $method->addParameter('builder')->setType(Builder::class);
        $constructorBody .= '$builder->build($this);' . "\n";
        $constructorBody .= '$this->' . self::PROPERTY_BUILDER . ' = $builder;' . "\n";

        $method->setBody($constructorBody);
    }

    private function generateCommonProperties(ClassType $class, Config $config): void
    {
        $class->addProperty(self::PROPERTY_INITIALIZED)
            ->setType('array')
            ->setValue([])
            ->setPrivate();

        $class->addProperty(self::PROPERTY_BUILDER)
            ->setType(Builder::class)
            ->setPrivate();
    }
}