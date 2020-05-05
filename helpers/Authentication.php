<?php
require_once __DIR__ . '\..\vendor\autoload.php';
require_once __DIR__ . '\..\entities\Persona.php';
require_once __DIR__ . '\..\config\environment.php';

use \Firebase\JWT\JWT;

class Authentication
{
    public static function authenticate($email, $password)
    {
        $persona =  Persona::findByEmail($email);

        if ($persona) {

            $hashedpass = $persona->password;

            if (password_verify($password, $hashedpass)) {

                return self::generateToken(
                    $persona->id,
                    $persona->email,
                    $persona->firstname,
                    $persona->lastname,
                    $persona->role,
                    strtotime('now'),
                    strtotime('now') + 60
                );
            } else {

                throw new Exception('email and password do not match');
            }
        }

        throw new Exception('email not registered');
    }
    private static function generateToken($userId, $email, $firstname, $lastname, $role, $iat, $exp)
    {

        $payload = array(
            "iat" => $iat,
            "exp" => $exp,
            "userId" => $userId,
            "email" => $email,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "role" => $role,
        );

        return JWT::encode($payload, getenv('ACCESS_TOKEN_SECRET'));
    }
    public static function authorize($token)
    {
        $authorizationResult = new AuthorizationResult('failure', 'unauthorized', false);

        try {

            $decoded = new stdClass();

            $decoded->userContext = JWT::decode($token, getenv('ACCESS_TOKEN_SECRET'), array('HS256'));
            
            $authorizationResult->status = 'succeed';
            $authorizationResult->data = $decoded;
            $authorizationResult->isValid = true;
            
            return $authorizationResult;

        } catch (\Throwable $th) {

            if ($th->getMessage() == 'Malformed UTF-8 characters') {

                throw new Exception('Invalid token');
            }

            throw new Exception($th->getMessage());
        }
    }
}

class AuthorizationResult {

    public $status;
    public $data;
    public $isValid;

    public function __construct($status = 'failure', $errorMessage = 'unauthorized', $isValid = false)
    {
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->isValid = $isValid;
    }
}
