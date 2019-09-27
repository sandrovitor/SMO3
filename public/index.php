<?php
// Kernel do SMO
//require_once('../config/smo.php');

// Controllers
@require_once('../app/Controllers/PageController.php');
@require_once('../app/Controllers/AdmController.php');

// MODELS
@require_once('../app/Models/Mapa.php');
@require_once('../app/Models/Registros.php');
@require_once('../app/Models/User.php');
@require_once('../app/Models/Config.php');
@require_once('../app/Models/Bairro.php');
@require_once('../app/Models/LOG.php');

// Autoload do Composer
require_once('../vendor/autoload.php');




date_default_timezone_set('America/Bahia');


// Roteador
$router = new AltoRouter();

/*
    Ajuda: http://altorouter.com/usage/mapping-routes.html

 */
/*
 |------------------------------
 |  Lista de rotas
 |------------------------------
 */
include_once('../routes/web.php');

$match = $router->match();



// Verifica se a URI bate com alguma rota salva
if(is_array($match) && is_callable($match['target'])) {
    // Rota encontrada, sem CONTROLLER

    //var_dump($match['params']);
    $writePage = call_user_func_array($match['target'], array($match['params']));
    //var_dump($writePage);
    echo $writePage;
} else if(is_array($match) && strrpos($match['target'],'#') > 0) {
    // Rota encontrada, com CONTROLLER

    $x = explode('#', $match['target']);
    $writePage = call_user_func_array($x, array($match['params']));
    echo $writePage;
} else {
    // Nenhuma rota encontrada

    // Exibe erro 404
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
 