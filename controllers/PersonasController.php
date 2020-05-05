<?php
require_once __DIR__ . '\..\entities\Persona.php';
require_once __DIR__ . '\..\helpers\Response.php';
require_once __DIR__ . '\..\helpers\Authentication.php';
require_once __DIR__ . '\..\helpers\Validator.php';

class PersonasController
{
    private $path_info;
    private $request_method;

    function __construct()
    {
        $this->path_info = $_SERVER['PATH_INFO'] ?? '';
        $this->request_method = $_SERVER['REQUEST_METHOD'] ?? '';
    }
    function getRoute()
    {
        return $this->request_method . $this->path_info;
    }
    function start()
    {
        switch ($this->getRoute()) 
        {
            case 'POST/personas/signin':

                $email = $_POST['email'] ?? null;
                $password = $_POST['clave'] ?? null;
                $role = $_POST['tipo'] ?? null;
                $firstname = $_POST['nombre'] ?? null;
                $lastname = $_POST['apellido'] ?? null;
                $dni = $_POST['dni'] ?? null;
                $healthInsurance = $_POST['obra_social'] ?? null;

                echo $this->postPersonasCreate($email, $password, $role, $firstname, $lastname, $dni, $healthInsurance);
                break;

            case 'POST/personas/login':

                $email = $_POST['email'] ?? false;
                $password = $_POST['clave'] ?? false;

                echo $this->postPersonasLogin($email, $password);
                break;

            default:
                echo 'Metodo no esperado';
                break;
        }
    }
    // POST/personas/signin
    function postPersonasCreate($email, $password, $role, $firstname, $lastname, $dni, $healthInsurance)
    {
        $validationResult = $this->createValidation($email, $password, $role, $firstname, $lastname, $dni, $healthInsurance);
        $response = new Response('failure', $validationResult->errorMessage);

        if ($validationResult->isValid) {

            $persona = new Persona(
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $role,
                $firstname,
                $lastname,
                $dni,
                $healthInsurance
            );

            $persona->save();

            $response = new Response();
            $response->status = 'succeed';
            $response->data = $persona;
        }

        return json_encode($response);
    }
    // POST/personas/login
    function postPersonasLogin($email, $password)
    {
        $validationResult = $this->loginValidation($email, $password);
        $response = new Response('failure', $validationResult->errorMessage);

        if ($validationResult->isValid) {

            try {

                $token = Authentication::authenticate($email, $password);
                $response->status = 'succeed';
                $response->data = array('token' => $token);

            } catch (Exception $e) {

                $response = new Response('failure', $e->getMessage());
            }
        }

        return  json_encode($response);
    }
    /////////////////////////
    // REQUEST VALIDATIONS //
    /////////////////////////
    private function createValidation($email, $password, $role, $firstname, $lastname, $dni, $healthInsurance)
    {
        $validationResults = array(

            Validator::emails($email),
            Validator::passwords($password),
            Validator::in($role, array('admin', 'user')),
            Validator::names($firstname),
            Validator::names($lastname),
            Validator::dnis($dni)
        );

        foreach ($validationResults as $result) {
            if (!$result->isValid) {
                return $result;
            }
        }

        return new ValidationResult('succeed', 'is valid request', true);
    }
    private function loginValidation($email, $password)
    {
        $validationResults = array(

            Validator::emails($email),
            Validator::passwords($password)
        );

        foreach ($validationResults as $result) {
            
            if (!$result->isValid) {
                return $result;
            }
        }

        return new ValidationResult('succeed', 'is valid request', true);
    }
}
