Zend Expressive Content Validation 
====
[![Build Status](https://travis-ci.org/mvlabs/ze-content-validation.svg?branch=master)](https://travis-ci.org/mvlabs/ze-content-validation)

Introduction
------------

Zend Expressive Content Validation is a Middleware for automating validation of incoming input.

Allows the following:

- Defining named input filters.
- Mapping named input filters to routes.
- Returning an `ApiProblemResponse` with validation error messages on invalid input.

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
                'name' => 'displayName',
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

If your are using the Expressive's ConfigManager ([zendframework/zend-config-aggregator](https://github.com/zendframework/zend-config-aggregator)), you can just pass the class name to it like this:

```php
$aggregator = new ConfigAggregator(
    [
        ZE\ContentValidation\ConfigProvider::class,
        new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    ], 
    $cacheConfig['config_cache_path']
);

return $aggregator->getMergedConfig();

    
```
more [about config manager](https://zendframework.github.io/zend-expressive/features/modular-applications/).


In alternative, to use the built-in ConfigProvider, create a config file with this contents:

```php
<?php
return (new ZE\ContentValidation\ConfigProvider())->__invoke();
```

### Validating
In the following request, an email value is provided with an invalid format, and the displayName field is omitted 
entirely:
```json
POST /users HTTP/1.1
Accept: application/json
Content-Type: application/json; charset=utf-8

{
    "email": "foo",
    "password": "mySecretPassword!"
    
}
```

The response:

```json
HTTP/1.1 422 Unprocessable Entity
Content-Type: application/problem+json

{
  "detail": "Validation Failed"
  "status": 422,
  "title": "Unprocessable Entity",
  "type": "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html",
  "errors": {
    "email": {
        "emailAddressInvalidFormat": "The input is not a valid email address. Use the basic format local-part@hostname"
    },
    "displayName": {
      "isEmpty": "Value is required and can't be empty"
    }    
  },
}
```
