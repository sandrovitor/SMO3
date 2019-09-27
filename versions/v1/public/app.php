<?php
// Autoload do Composer
require_once('../vendor/autoload.php');

// Usa a biblioteca Router.
use Aura\Router\RouterContainer;

// Iniciar o container do Router
$routerContainer = new RouterContainer();
$route = $routerContainer->getMap();


/*
 |
 |  Configura as rotas do aplicativo
 |
 */
$route->get('inicio', '/', function(){return 'Boom!';});