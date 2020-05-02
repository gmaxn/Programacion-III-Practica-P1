<?php
require_once __DIR__ . '\..\entities\Persona.php';
require_once __DIR__ . '\..\helpers\Response.php';
require_once __DIR__ . '\..\helpers\Authentication.php';

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

            case 'POST/personas/signin':

                $personasDto = new stdClass();
                $personasDto->email = $_POST['email'] ?? false;
                $personasDto->password = $_POST['clave'] ?? false;
                $personasDto->role = $_POST['tipo'] ?? false;
                $personasDto->firstname = $_POST['nombre'] ?? false;
                $personasDto->lastname = $_POST['apellido'] ?? false;
                $personasDto->dni = $_POST['dni'] ?? false;
                $personasDto->healthInsurance = $_POST['obra_social'] ?? false;

                echo $this->postPersonasCreate($personasDto);
            break;

            case 'POST/personas/login':

                $loginDto = new stdClass();
                $loginDto->email = $_POST['email'] ?? false;
                $loginDto->password = $_POST['clave'] ?? false;

                echo $this->postPersonasLogin($loginDto);
            break;
                
            default:

                echo 'Metodo no esperado';
            break;
        }
    }

    // POST/personas/signin
    function postPersonasCreate($personasDto) {
    
        $response = new Response();

        $validationResult = Persona::validate($personasDto);
        if(!$validationResult->isValid)
        {
            $response->status = 'failure';
            $response->data = $validationResult->errorMessage;
            return json_encode($response); 
        }


        $persona = new Persona (          

            $personasDto->email,
            password_hash($personasDto->password, PASSWORD_DEFAULT),
            $personasDto->role,
            $personasDto->firstname, 
            $personasDto->lastname,
            $personasDto->dni,
            $personasDto->healthInsurance
        );
        
        $persona->save();
            
        $response->status = 'succeed';
        $response->data = $persona;
        
        $response = json_encode($response);
    
        return $response;
    }

    // POST/login
    function postPersonasLogin($loginDto) {

        $response = new Response();

        try {

            $result = Authentication::validateCredentials($loginDto->email, $loginDto->password);

            if($result) {
            
                $jwt = new stdClass();
                $jwt->token = $result;
                $response->status = 'succeed';
                $response->data = $jwt;
            }
        }
        catch(Exception $e) {

            $response->status = 'failure';
            $response->data = $e->getMessage();
        }

        $response = json_encode($response);

        echo $response;
    }
}