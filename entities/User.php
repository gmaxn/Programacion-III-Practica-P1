<?php

class User {

    public $id;
    public $email;
    public $password;
    public $role;

    public function _construct ($id, $email, $password, $role = 'user') {

        $this->id = $id;
        $this->$email = $email;
        $this->$password = $password;
        $this->$role = $role;
    }
}

class Persona extends User {

    public $firstname;
    public $lastname;
    public $dni;
    public $healthInsurance;

    public function __construct($firstname, $lastname, $dni, $healthInsurance) {
        
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->dni = $dni;
        $this->healthInsurance = $healthInsurance;
    }
}