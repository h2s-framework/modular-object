# H2S Framework modular objects

This module creates classes dynamically, based on the configuration file. 
This allows for the creation of classes with different attributes and methods, objects can be shared between modules
and extended by each module.

## Example

Configuration in dm.yml
```yaml
arguments:
  \Siarko\ModularObject\Registry:
    objects:
      \Siarko\Request\Request: # generated class name; All the config below is used to create \Siarko\ModularObject\Object\Config
        baseClassName: \Siarko\Request\AbstractRequest # optional - class will extend this base class
        builders: # optional - class will use these builders in constructor
          - \Siarko\Request\Builder\BaseUrl # these must implement ConstructorBuilderInterface
        properties: # All config below is used to create \Siarko\ModularObject\Object\PropertyConfig
          baseUrl: # name of property
            type: string # type of property
            nullable: true # optional, defaults to false, describes if property can be null
            setter: false # optional, defaults to true, setter will be generated
            default: null # optional, default value of property
            builders: # optional, list of builders that will be used to set this property on first getter call
              - \Siarko\Request\Builder\BaseUrl # these must implement PropertyBuilderInterface
```
