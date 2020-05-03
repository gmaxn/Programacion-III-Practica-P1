<?php
require_once __DIR__ . '\..\entities\Product.php';
require_once __DIR__ . '\..\helpers\Response.php';
require_once __DIR__ . '\..\helpers\Authentication.php';
require_once __DIR__ . '\..\helpers\PhotoUploader.php';
require_once __DIR__ . '\..\entities\Order.php';
require_once __DIR__ . '\..\entities\OrderItem.php';


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

                $headers = getallheaders();
                $jwt = $headers['token'];

                echo $this->postProductsList($jwt);
            break;

            case 'POST/productos/ventas':

                $headers = getallheaders();
                $jwt = $headers['token'];

                $orderDto = new stdClass();
                $orderDto->productId = $_POST['id_producto'] ?? false;
                $orderDto->quantity = $_POST['cantidad'] ?? false;
                $orderDto->user = $_POST['ususario'] ?? false;

                echo $this->postOrderGenerate($jwt, $orderDto);
            break;


                
            default:

                echo 'Metodo no esperado';
            break;
        }
    }
    // POST/productos/stock
    function postProductsCreate($productDto, $jwt) {

        $response = new Response();

        try {

            $userContext = Authentication::authorize($jwt);

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
            
                return json_encode($response);
            }

            $response->status = 'failure';
            $response->data = 'Not authorized';
        }
        catch(Exception $e) {
            
            $response->status = 'failure';
            $response->data = $e->getMessage();
        }

        return json_encode($response);
    }
    // GET/productos/stock
    function postProductsList($jwt) {

        $response = new Response();
        
        try {

            $userContext = Authentication::authorize($jwt);

            if(!isset($userContext->role))
            {
                throw new Exception('Role null or empty');
            }

            if($userContext->role == 'admin')
            {

                $personas = Product::getProductsList();

                $response->status = 'succeed';
                $response->data = $personas;

                return json_encode($response);
            }

            $response->status = 'failure';
            $response->data = 'Not authorized';
        }
        catch(Exception $e) {

            $response->status = 'failure';
            $response->data = $e->getMessage();
        }

        return json_encode($response);
    }
    // POST/productos/ventas
    function postOrderGenerate($jwt, $orderDto) {

        $response = new Response();
        
        try {

            $userContext = Authentication::authorize($jwt);

            // 1. check stock --> products entity
            $product = Product::getProductById($orderDto->productId);         
            if($product->stock >= $orderDto->quantity) {

                // 2. generate order --> order entity
                $order = new Order(
                    $userContext->userId,
                    array(new OrderItem(
                        $product->id,
                        $product->price,  
                        $orderDto->quantity
                    )));

                $order->save();
            }

            // 3. reduce stock --> products entity
            Product::updateStock($product->id, $orderDto->quantity);

            $response->status = 'succeed';
            $response->data = $order;

            return json_encode($response);
        }
        catch(Exception $e) {

            $response->status = 'failure';
            $response->data = $e->getMessage();
        }

        return json_encode($response);
    }
}