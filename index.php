<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

$path_info = $_SERVER['PATH_INFO'] ?? '';

$resource = '/' . (explode('/', $path_info)[1] ?? '');


switch ($resource) {

    case '/personas':

    break;

    case '/alumnos':

    break;

    default:
        echo 'Requested URL:' . $path_info . "\n";
        echo $resource . ' is not a valid resource';
    break;
}