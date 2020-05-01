<?php
require_once __DIR__ . '\..\vendor\autoload.php';
require_once __DIR__ . '\..\entities\Persona.php';
require_once __DIR__ . '\..\config\environment.php';

use \Firebase\JWT\JWT;

class Authentication {

    public static function validateCredentials($email, $password)
    {
        $persona =  Persona::findByEmail('CSV', $email);

        if ($persona) {

            $hashedpass = $persona->password;

            if (password_verify($password, $hashedpass)) {

                return self::generateToken(
                    $persona->email,
                    $persona->firstname,
                    $persona->lastname,
                    $persona->role,
                    strtotime('now'),
                    strtotime('now') +60
                );

            } else {

                throw new Exception('email and password do not match');
            }
        }

        throw new Exception('email not registered');
    }

    private static function generateToken($email, $firstname, $lastname, $role, $iat, $exp) {

        $payload = array(
            "iat" => $iat,
            "exp" => $exp,
            "email" => $email,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "user_type" => $role,
        );

        return JWT::encode($payload, getenv('ACCESS_TOKEN_SECRET'));
    }
}