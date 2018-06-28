# foa
Fast Objects Access API package (using Laravel 5.4)

## Reason of foa - inspired by MongoDB
Building foa was inspired by MongoDB and the flow of developing with node.js. But in some cases I can't use modern techniques - for example deploying at webspaces where only MySQL and PHP are allowed to run.
To can also being fast at development, I build this package to provide a simple way for setup an API server which I can use at frontend development.

## Requirements

Laravel passport need to be installed and setup need to be done, see: https://laravel.com/docs/5.4/passport

http://image.intervention.io/getting_started/installation#laravel

## Testings 
The package contains some basic unit tests. Run /vendor/bin/php vendor/Dion/foa/tests to test the package. Unit tests use DatabaseTransactions and WithoutMiddleware traits.


# Seeting up an ObjectType

```php
[
    'name' => 'Car',
    'schema' => [
        'type' => 'text',
        'price' => 'float',
        'model' => 'date' //Baujahr
    ],
    'validation' => [
        'type' => 'min:3'
    ],
    'relations' => [
    
    ],
    'setup' => [
        'schema' => 'min' //all schema attributes are required, more are possible
        // 'schema' => 'exact' // only schema attributes are allowed, they are required
        // 'schema' => 'sometimes' // schema attributes are possible, when then they are casted
    ]
]
```