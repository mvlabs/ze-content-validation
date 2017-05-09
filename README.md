Zend Expressive Content Validation 
====
[![Build Status](https://travis-ci.org/mvlabs/ze-content-validation.svg?branch=master)](https://travis-ci.org/mvlabs/ze-content-validation)

Introduction
------------

Zend Expressive Content Validation is a Middleware for automating validation of incoming input.

Allows the following:

- Defining named input filters.
- Mapping named input filters to routes.
- On invalid input throws an ApiException with validation error messages that will be handled by 
`LosMiddleware\ApiProblem\ApiProblem::class` as `Zend\Expressive\FinalHandler`.

Installation
------------

Run the following `composer` command:

```console
$ composer require mvlabs/ze-content-validation
```

Configuration
=============

The ze-content-validation key is a mapping between routes names as the key, and the value being an array of
mappings that determine which HTTP method to respond to and what input filter to map to for the given request. 
The keys for the mapping can either be an HTTP method or `*` wildcard for applying to any http method.

Example:
```php
'ze-content-validation' => [
    'user.add' => [
        'POST' =>  \App\InputFilter\UserInputFilter::class
    ],
],
```
In the above example, the \App\InputFilter\UserInputFilter will be selected for POST requests.

#### input_filter_spec

`input_filter_spec` is for configuration-driven creation of input filters.  The keys for this array
will be a unique name, but more often based off the service name it is mapped to under the
`options => validation` key in the routes configuration file. The values will be an input filter configuration array, as is
described in the ZF2 manual [section on input
filters](http://zf2.readthedocs.org/en/latest/modules/zend.input-filter.intro.html).

Example:

```php
    'input_filter_specs' => [
        'App\\InputFilter\\LoginInputFilter' => [
            0 => [
                'name' => 'username',
                'required' => true,
                'filters' =>[],
                'validators' => [
                     0 => [
                        'name' => 'not_empty',
                     ]   
                ],
                
            ],
            1 => [
                'name' => 'password',
                'required' => true,
                'filters' => [],
                'validators' => [
                    0 => [
                        'name' => 'not_empty',
                    ],
                    1 => [
                        'name' => 'string_length',
                        'options' => [
                            'min' => 8, 
                            'max' => 12
                        ],
                    ],
                ],                
            ],
        ],
    ],
```

### Provided configuration
To get things easily working, a ConfigProvider is included, which automatically registers all the dependencies in the 
service container (including the `Zend\Expressive\FinalHandler` service).

If your are using the Expressive's ConfigManager ([mtymek/expressive-config-manager](https://github.com/mtymek/expressive-config-manager)), you can just pass the class name to it like this:

```php
return (new Zend\Expressive\ConfigManager\ConfigManager(
    [
        ZE\ContentValidation\ConfigProvider::class,
        new Zend\Expressive\ConfigManager\ZendConfigProvider('config/{autoload/{{,*.}global,{,*.}local},params/generated_config}.php'),
    ],
    'data/cache/app_config.php'))->getMergedConfig();
```
more [about config manager](https://zendframework.github.io/zend-expressive/cookbook/modular-layout/).


In alternative, to use the built-in ConfigProvider, create a config file with this contents:

```php
<?php
return (new ZE\ContentValidation\ConfigProvider())->__invoke();
```

