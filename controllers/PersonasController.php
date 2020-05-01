<?php
require_once __DIR__ . '\..\entities\Persona.php';
require_once __DIR__ . '\..\helpers\Response.php';

class PersonasController {

    private $path_info;
    private $request_method;

    function getRoute() {

        return $this->request_method . $this->path_info;
    }

    function __construct() {

        $this->path_info = $_SERVER['PATH_INFO'] ?? '';
        $this->request_method = $_SERVER['REQUEST_METHOD'] ?? '';
    }

    function start() {

        switch($this->getRoute()) {

            case 'POST/usuario':

                $personasDto = new stdClass();
                $personasDto->email = $_POST['email'] ?? false;
                $personasDto->password = $_POST['clave'] ?? false;
                $personasDto->role = $_POST['tipo'] ?? false;
                $personasDto->firstname = $_POST['nombre'] ?? false;
                $personasDto->lastname = $_POST['apellido'] ?? false;
                $personasDto->dni = $_POST['dni'] ?? false;
                $personasDto->healthInsurance = $_POST['obra_social'] ?? false;

                
                //print_r($personasDto);
                echo $this->postPersonasCreate($personasDto);
            break;
                
            default:

                echo 'Metodo no esperado';
            break;
        }
    }

    // POST/personas/signin
    function postPersonasCreate($personasDto) {
    
        $response = new Response();

    
        $persona = new Persona (
            
            $personasDto->email,
            password_hash($personasDto->password, PASSWORD_DEFAULT),
            $personasDto->role,
            $personasDto->firstname, 
            $personasDto->lastname,
            $personasDto->dni,
            $personasDto->healthInsurance
        );


        
        $persona->save('CSV');
    
        if($persona) {
    
            $response->status = 'succeed';
            $response->data = $persona;
        }
        
        $response = json_encode($response);
    
        echo $response;
    }
}
