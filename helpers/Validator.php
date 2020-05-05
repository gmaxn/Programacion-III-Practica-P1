<?php

class Validator
{
    public static function names($name)
    {
        $name = strtolower($name);

        $validationResult = new ValidationResult('suceed', null, true);

        if (!preg_match('/^' . '[a-z]{3,18}' . '$/', $name)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Name must be alphabetic and and between 3 and 18 characters';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function emails($email)
    {

        $validationResult = new ValidationResult('suceed', null, true);

        if (!preg_match('/^' . '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+' . '$/', $email)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Invalid email adress';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function passwords($password)
    {

        $validationResult = new ValidationResult('suceed', null, true);

        if (!preg_match('/^' . '\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*' . '$/', $password)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'password must be between 8 - 30 characters contain at least 1 uppercase 1 lowercase and 1 number';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function units($value, $signed = false)
    {

        $validationResult = new ValidationResult('suceed', null, true);

        if ($signed && !preg_match('~^((?:\+|-)?[0-9]+)$~', $value)) {

            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Value must be integer';
            $validationResult->isValid = false;
        }

        if (!$signed && !preg_match('~^([0-9]+)$~', $value)) {

            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Value must be not signed integer';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function prices($value, $signed = false)
    {


        $validationResult = new ValidationResult('suceed', null, true);

        if ($signed && !is_numeric($value)) {

            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Value must be numeric';
            $validationResult->isValid = false;
        }

        if (!$signed && !(is_numeric($value) && $value > 0)) {

            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Value must be numeric and unsigned';
            $validationResult->isValid = false;
        }

        return $validationResult;

        return $signed ? is_numeric($value) : (is_numeric($value) && $value > 0);
    }
    public static function dnis($name)
    {
        $validationResult = new ValidationResult('suceed', null, true);

        if (!preg_match('/^' . '[0-9]{8}' . '$/', $name)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'DNI must be numeric string 8 char long';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function in($value, $values)
    {

        $validationResult = new ValidationResult('suceed', null, true);

        if (!in_array($value, $values)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage = 'Value must be one of the followings: ' . implode(', ', $values);
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
    public static function required($value, $valueName)
    {

        $validationResult = new ValidationResult('suceed', null, true);

        if (!isset($value)) {
            $validationResult->status = 'failure';
            $validationResult->errorMessage =  $valueName . ' is required';
            $validationResult->isValid = false;
        }

        return $validationResult;
    }
}
class ValidationResult
{

    public $status;
    public $errorMessage;
    public $isValid;

    public function __construct($status = 'failure', $errorMessage = 'invalid request', $isValid = false)
    {
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->isValid = $isValid;
    }
}
