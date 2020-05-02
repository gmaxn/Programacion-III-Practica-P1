<?php
require_once __DIR__ . '\..\entities\Product.php';
require_once __DIR__ . '\..\helpers\Response.php';
require_once __DIR__ . '\..\helpers\Authentication.php';
require_once __DIR__ . '\..\helpers\PhotoUploader.php';

class ProductsController {

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

            case 'POST/productos/stock':

                $headers = getallheaders();
                $jwt = $headers['token'];

                $productDto = new stdClass();
                $productDto->type = $_POST['producto'] ?? false;
                $productDto->brand = $_POST['marca'] ?? false;
                $productDto->price = $_POST['precio'] ?? false;
                $productDto->stock = $_POST['stock'] ?? false;
                $productDto->image = $_FILES['foto'] ?? null;

                echo $this->postProductsCreate($productDto, $jwt);
            break;

            case 'GET/productos/stock':

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

    // POST/stock
    function postProductsCreate($productDto, $jwt) {

        $response = new Response();

        try {

            $userContext = Authentication::authorize($jwt);



            if(!isset($userContext->role))
            {
                throw new Exception('Role null or empty');
            }

            if($userContext->role == 'admin')
            {

                $validationResult = Product::validate($productDto);
                if(!$validationResult->isValid)
                {
                    $response->status = 'failure';
                    $response->data = $validationResult->errorMessage;
                    return json_encode($response); 
                }
                
                $product = new Product (          
                    $productDto->type,
                    $productDto->brand,
                    $productDto->price,
                    $productDto->stock,
                    $productDto->image
                );
                
                $product->save('JSON');
                
                //$photoUploader = new PhotoUploader(getenv('DEFAULT_IMG_DIR'), getenv('DEFAULT_IMG_DIR'), );
                PhotoUploader::uploadPhoto($productDto->image['tmp_name'], $product->image);
                //PhotoUploader::addWaterMark($$product->image);
            
                if($product) {
            
                    $response->status = 'succeed';
                    $response->data = $product;
                }
                
                $response = json_encode($response);
            
                return $response;
            }

            $response->status = 'failure';
            $response->data = 'Non authorized user';
        }
        catch(Exception $e) {
            
            $response->status = 'failure';
            $response->data = $e->getMessage();
        }

        return json_encode($response);
        
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