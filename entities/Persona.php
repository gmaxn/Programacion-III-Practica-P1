<?php
require_once __DIR__ . '\..\repos\PersonasRepository.php';
require_once __DIR__ . '\..\helpers\ValidationResult.php';

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

    public function save() {

        $filename = getenv('PERSONAS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch($ext)
        {
            case 'TXT':
                PersonasRepository::saveSerialized($filename, $this);
            break;
     
            case 'JSON':
                PersonasRepository::saveJSON($filename, $this->toJSON());
            break;

            case 'CSV':
                PersonasRepository::saveCSV($filename, $this->toCSV());
            break;

            default:
                PersonasRepository::saveSerialized($filename, $this);
            break;
        }
    }
    public static function findByEmail($email) {

        $filename = getenv('PERSONAS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch($ext)
        {
            case 'TXT':
                $list = PersonasRepository::readSerialized($filename);
            break;
            
            case 'JSON':
                $list = PersonasRepository::readJSON($filename);
            break;
            
            case 'CSV':
                $list = PersonasRepository::readCSV($filename);
            break;

            default:
                throw new Exception('Incompatible save type exception');
            break;
        }

        foreach ($list as $persona) {

            if ($persona->email == $email) {
                
                return $persona;
            }
        }
        return false;
    }
    public static function findById($id) {

        $filename = getenv('PERSONAS_FILENAME');
        $ext = strtoupper(array_reverse(explode('.', $filename))[0]);

        switch($ext)
        {
            case 'TXT':
                $list = PersonasRepository::readSerialized($filename);
            break;
            
            case 'JSON':
                $list = PersonasRepository::readJSON($filename);
            break;
            
            case 'CSV':
                $list = PersonasRepository::readCSV($filename);
            break;

            default:
                throw new Exception('Incompatible save type exception');
            break;
        }

        foreach ($list as $persona) {

            if ($persona->id == $id) {
                
                return $persona;
            }
        }
        return false;
    }
    public static function validate($personasDto) {

        $result = new ValidationResult();

        foreach($personasDto as $key => $value) {

            if($value == null || $value == '')
            {
                $result->isValid = false;
                $result->errorMessage = $key . ' is null or empty';
                $result->status = 'failure';

                return $result;
            }
        }

        $result->isValid = true;
        $result->errorMessage = null;
        $result->status = 'succeed';

        return $result;
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