<?php
require_once __DIR__ . '\..\entities\Product.php';
require_once __DIR__ . '\..\helpers\Response.php';
require_once __DIR__ . '\..\helpers\Authentication.php';
require_once __DIR__ . '\..\helpers\PhotoUploader.php';
require_once __DIR__ . '\..\entities\Order.php';
require_once __DIR__ . '\..\entities\OrderItem.php';


class ProductsController
{
    private $path_info;
    private $request_method;

    function getRoute()
    {

        return $this->request_method . $this->path_info;
    }
    function __construct()
    {

        $this->path_info = $_SERVER['PATH_INFO'] ?? '';
        $this->request_method = $_SERVER['REQUEST_METHOD'] ?? '';
    }
    function start()
    {

        switch ($this->getRoute()) {

            case 'POST/productos/stock':

                $headers = getallheaders();
                $jwt = $headers['token'];

                $type = $_POST['producto'] ?? false;
                $brand = $_POST['marca'] ?? false;
                $price = $_POST['precio'] ?? false;
                $stock = $_POST['stock'] ?? false;
                $image = $_FILES['foto'] ?? null;

                echo $this->postProductsCreate($type, $brand, $price, $stock, $image, $jwt);
                break;

            case 'GET/productos/stock':

                $headers = getallheaders();
                $jwt = $headers['token'];

                echo $this->getProductsList($jwt);
                break;

            case 'POST/productos/ventas':

                $headers = getallheaders();
                $jwt = $headers['token'];

                $productId = $_POST['id_producto'] ?? false;
                $quantity = $_POST['cantidad'] ?? false;
                $email = $_POST['ususario'] ?? false;
                echo $this->postOrdersGenerate($productId, $quantity, $email, $jwt);
                break;

            case 'GET/productos/ventas':

                $headers = getallheaders();
                $jwt = $headers['token'];
                echo $this->getOrders($jwt);
                break;

            default:
                echo 'Metodo no esperado';
                break;
        }
    }
    // POST/productos/stock
    function postProductsCreate($type, $brand, $price, $stock, $image, $jwt)
    {
        $validationResult = $this->postProductsCreateValidation($type, $brand, $price, $stock, $image);
        $response = new Response('failure', $validationResult->errorMessage);

        if (!$validationResult->isValid) {

            try {

                $authorizationResult = Authentication::authorize($jwt);
                $response = new Response('failure', $authorizationResult->errorMessage);

                if ($authorizationResult->data->userContext->role == 'admin') {

                    $product = new Product(
                        $type,
                        $brand,
                        $price,
                        $stock,
                        $image
                    );

                    $product->save('JSON');

                    PhotoUploader::uploadPhoto($image['tmp_name'], $product->image);
                    PhotoUploader::addWaterMark($product->image, getenv('PHOTO_WATERMARK_DIR'), $product->image, true);
                    PhotoUploader::crop($product->image, $product->image, 600, 600, true);

                    $response->status = 'succeed';
                    $response->data = $product;
                }
            } catch (Exception $e) {

                $response = new Response('failure', $e->getMessage());
            }
        }

        return json_encode($response);
    }
    // GET/productos/stock
    function getProductsList($jwt)
    {
        try {

            $authorizationResult = Authentication::authorize($jwt);
            $response = new Response('failure', $authorizationResult->errorMessage);

            if ($authorizationResult->data->userContext->role == 'admin') {
                $personas = Product::getProductsList();

                $response->status = 'succeed';
                $response->data = $personas;
            }
        } catch (Exception $e) {

            $response = new Response('failure', $e->getMessage());
        }

        return json_encode($response);
    }
    // POST/productos/ventas
    function postOrdersGenerate($productId, $quantity, $email, $jwt)
    {
        try {

            $authorizationResult = Authentication::authorize($jwt);
            $response = new Response('failure', $authorizationResult->errorMessage);


            if ($authorizationResult->isValid) {
                // 1. check stock --> products entity
                $product = Product::getProductById($productId);

                if ($product->stock >= $quantity) {

                    // 2. generate order --> order entity
                    $order = new Order(
                        $authorizationResult->data->userContext->userId,
                        array(new OrderItem(
                            $product->id,
                            $product->price,
                            (int) $quantity
                        ))
                    );
                    $order->save();
                }

                // 3. reduce stock --> products entity
                Product::updateStock($product->id, $product->stock - $quantity);

                $response = new Response('succeed', $order);
            }
        } catch (Exception $e) {

            $response = new Response('failure', $e->getMessage());
        }

        return json_encode($response);
    }
    // POST/productos/ventas
    function getOrders($jwt)
    {
        try {

            $authorizationResult = Authentication::authorize($jwt);
            $response = new Response('failure', $authorizationResult->errorMessage);

            if ($authorizationResult->isValid) {

                $orders = Order::getOrdersByUserType($authorizationResult->data->userContext);

                $response = new Response('succeed', $orders);
            }
        } catch (Exception $e) {

            $response = new Response('failure', $e->getMessage());
        }
        return json_encode($response);
    }
    /////////////////////////
    // REQUEST VALIDATIONS //
    /////////////////////////
    private function postProductsCreateValidation($type, $brand, $price, $stock, $image)
    {
        $validationResults = array(

            Validator::in($type, array('vacuna', 'medicamento')),
            Validator::required($brand, 'brand'),
            Validator::prices($price),
            Validator::units($stock)
        );

        foreach ($validationResults as $result) {

            if (!$result->isValid) {

                return $result;
            }
        }

        return new ValidationResult('succeed', 'is valid request', true);
    }
}
