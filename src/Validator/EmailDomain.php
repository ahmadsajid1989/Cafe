<?php


namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class EmailDomain extends Constraint
{
    public $domains;
    public $message = 'The email "%email%" has not a valid bongo email.';
}

