<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 08:48
 */

namespace Dion\Foa\Repositories;


use Illuminate\Http\ResponseTrait;

class FailedObject
{
    use ResponseTrait;

    public $errors = [];

    public function __construct(\Illuminate\Support\MessageBag $errors)
    {
        $this->errors = $errors;
    }

    public function codeFailure()
    {
        return (object)[
            'status' => 'error',
            'errors' => $this->errors
        ];
    }

    public function toResponse()
    {
        //@TODO make a test and implement correct
    }
}