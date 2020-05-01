<?php
require_once __DIR__ . '\..\repos\PersonasRepository.php';

class User {

    public $id;
    public $email;
    public $password;
    public $role;

    public function __construct ($id, $email, $password, $role = 'user') {

        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}

class Persona extends User {

    public $firstname;
    public $lastname;
    public $dni;
    public $healthInsurance;

    public function __construct($email, $password, $role, $firstname, $lastname, $dni, $healthInsurance, $id = null) {
        
        parent:: __construct(

            ($id ?? strtotime('now')), 
            $email, 
            $password, 
            $role
        );

        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->dni = $dni;
        $this->healthInsurance = $healthInsurance;
    }

    public function save($saveType = 'Serialized') {

        switch($saveType)
        {
            case 'Serialized':
                $filename = __DIR__ . '\..\data\personas.txt';
                PersonasRepository::saveSerialized($filename, $this);
            break;
     
            case 'JSON':
                $filename = __DIR__ . '\..\data\personas.json';
                PersonasRepository::saveJSON($filename, $this->toJSON());
            break;

            case 'CSV':
                $filename = __DIR__ . '\..\data\personas.csv';
                PersonasRepository::saveCSV($filename, $this->toCSV());
            break;

            default:
                $filename = __DIR__ . '\..\data\personas.txt';
                PersonasRepository::saveSerialized($filename, $this);
            break;
        }
    }

    public static function findByEmail($readType, $email)
    {
        switch($readType)
        {
            case 'Serialized':
                $filename = __DIR__ . '\..\data\personas.txt';
                $list = PersonasRepository::readSerialized($filename);
            break;
            
            case 'JSON':
                $filename = __DIR__ . '\..\data\personas.json';
                $list = PersonasRepository::readJSON($filename);
            break;
            
            case 'CSV':
                $filename = __DIR__ . '\..\data\personas.csv';
                $list = PersonasRepository::readCSV($filename);
            break;

            default:
                $filename = __DIR__ . '\..\data\personas.txt';
                $list = PersonasRepository::readSerialized($filename);
            break;
        }

        foreach ($list as $persona) {

            if ($persona->email == $email) {

                return $persona;
            }
        }
        return false;
    }

    public function toJSON() {

        return json_encode($this);
    }

    public function toCSV() {

        return $this->id . ',' . 
               $this->email . ',' . 
               $this->password . ',' . 
               $this->role . ',' . 
               $this->firstname . ',' . 
               $this->lastname . ',' .
               $this->dni . ',' . 
               $this->healthInsurance . PHP_EOL; 
    }
}