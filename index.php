<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . '\controllers\PersonasController.php';
require_once __DIR__ . '\controllers\ProductsController.php';

$path_info = $_SERVER['PATH_INFO'] ?? '';

$resource = '/' . (explode('/', $path_info)[1] ?? '');


switch ($resource) {

    case '/personas':
        $controller = new PersonasController();
        $controller->start();
    break;

    case '/productos':
        $controller = new ProductsController();
        $controller->start();
    break;

    default:

        echo 'Requested URL:' . $path_info . "\n";
        echo $resource . ' is not a valid resource';
        //$controller = new PersonasController();
        //$controller->start();
    break;
}