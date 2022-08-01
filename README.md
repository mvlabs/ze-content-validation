Mezzio Content Validation 
====
[![CI-Test](https://github.com/func0der/ze-content-validation/actions/workflows/test.yml/badge.svg)](https://github.com/func0der/ze-content-validation/actions/workflows/test.yml)

Introduction
main

`Mezzio Content Validation` (former `Zend Expressive Content Validation`) is a Mezzio middleware for automating validation of incoming input.

Allows the following:

- Defining named input filters.
- Mapping named input filters to routes.
- Returning a PSR-7 response representation of application/problem with validation error messages on invalid input using 
[Laminas Problem Details](https://github.com/mezzio/mezzio-problem-details)

Installation
------------

Run the following `composer` command:

```console
$ composer require func0der/ze-content-validation
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
`ze-content-validation` key. The values will be an input filter configuration array, as is
described in the Laminas manual [section on input
filters](https://docs.laminas.dev/laminas-inputfilter/intro/).

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
  "detail": "Validation Failed",
  "status": 422,
  "title": "Unprocessable Entity",
  "type": "https://httpstatus.es/422",
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
