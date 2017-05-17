# foa
Fast Objects Access API package (using Laravel 5.4)

## Reason of foa - inspired by MongoDB
Building foa was inspired by MongoDB and the flow of developing with node.js. But in some cases I can't use modern techniques - for example deploying at webspaces where only MySQL and PHP are allowed to run.
To can also being fast at development, I build this package to provide a simple way for setup an API server which I can use at frontend development.

## Requirements

Laravel passport need to be installed and setup need to be done, see: https://laravel.com/docs/5.4/passport


## Testings 
The package contains some basic unit tests. Run /vendor/bin/php vendor/Dion/foa/tests to test the package. Unit tests use DatabaseTransactions and WithoutMiddleware traits.