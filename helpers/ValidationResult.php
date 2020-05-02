<?php

class ValidationResult {

    public $status;
    public $errorMessage;
    public $isValid;

    public function __construct($status = 'failure', $errorMessage = 'invalid request', $isValid = false) {

        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->isValid = $isValid;  
    }
}