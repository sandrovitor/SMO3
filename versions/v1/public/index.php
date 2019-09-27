<?php
// Autoload do Composer
require_once('../vendor/autoload.php');

// Usa a biblioteca Router.
use Aura\Router\RouterContainer;
use App\Http\Controller;

// create a server request object
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

// create the router container and get the routing map
$routerContainer = new Aura\Router\RouterContainer();
$map = $routerContainer->getMap();


/*
 |-----------------------------
 |  Define as rotas! 
 |  >> Arquivo routes/web.php
 |-----------------------------
 */
// add a route to the map, and a handler for it
require_once(__DIR__.'/../routes/web.php');





// get the route matcher from the container ...
$matcher = $routerContainer->getMatcher();

// .. and try to match the request to a route.
$route = $matcher->match($request);
if (! $route) {
    echo "Não foi encontrado uma rota para o caminho solicitado";
    exit;
}

// add route attributes to the request
foreach ($route->attributes as $key => $val) {
    $request = $request->withAttribute($key, $val);
}

var_dump($route->handler);

// dispatch the request to the route handler.
// (consider using https://github.com/auraphp/Aura.Dispatcher
// in place of the one callable below.)
$callable = $route->handler;
$response = $callable($request);

//var_dump($response, $response->getHeaders());

// emit the response
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();