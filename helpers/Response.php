<?php

class Response {

    public $status;
    public $data;

    public function __construct($status = 'failure', $data = 'Operation failed') {

        $this->status = $status;
        $this->data = $data;      
    }
}