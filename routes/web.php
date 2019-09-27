<?php
/*
 |------------------------------
 |  Lista de rotas
 |------------------------------
 */
// LOGIN
$router->map('GET', '/login', 'PageController#login', 'login');
$router->map('POST', '/login', 'PageController#loga');

// LOGOUT
$router->map('GET', '/logout', 'PageController#logout', 'logout');

// INICIO
$router->map('GET', '/', 'PageController#index','homepage');

// CONSULTA
$router->map('GET', '/consulta/?', 'PageController#consulta', 'consulta');
$router->map('GET', '/surdo/[i:surdoid]', 'PageController#surdo', 'surdoInfo');
// CONSULTA > PESQUISA
$router->map('GET',
    '/consulta/pesquisa/[*:nome]/[*:bairro]/[*:turno]/[*:idade]/[*:be]/[*:oculto]/[*:encontrado]/[yes|not:desativado]',
    'PageController#consultaPesquisa');
    // Exemplo: /consulta/pesquisa/~null~/1/~null~/~null~/~null~/~null~/yes/not

// CONSULTA > ID DO SURDO
$router->map('GET', '/consulta/id/[i:id]', 'PageController#consultaId');



// REGISTROS
$router->map('GET', '/registros/?', 'PageController#registros', 'registros');
    $router->map('POST', '/registros/ultimos/?', 'PageController#registrosUltimos');

    $router->map('GET', '/registros/novo/?', 'PageController#registrosNovo', 'registrosNovo');
    $router->map('POST', '/registros/novo', function(){
        $auth = new Auth();
        if($auth->authorized() == false) {
            // Redireciona para página de login
            header('Location: '.$router->generate('login'));
            exit();
        }
        Registros::novo( $_POST );
        header('Location: /registros/novo');
    }, 'registrosNovoPOST');
        $router->map('GET', '/registros/novo/[i:id]', 'PageController#registrosNovo');

    $router->map('GET', '/registros/buscar/?', 'PageController#registrosBuscar', 'registrosBuscar');
        $router->map('POST', '/registros/buscar/[i:regid]', 'PageController#registrosConsulta'); // Retorna SOMENTE UM registro específico!
        $router->map('GET', '/registros/buscar/[i:surdoid]', 'PageController#registrosBuscar'); // Retorna registros de SOMENTE UM surdo específico!
        $router->map('POST', '/registros/buscar/surdo/[i:surdoid]/publicador/[i:pubid]/?', 'PageController#registrosConsulta'); // Retorna alguns registros (por SURDOS E PUBLICADORES)!
        $router->map('POST', '/registros/buscar/surdo/[i:surdoid]/publicador/[i:pubid]/[:querystr]?', 'PageController#registrosConsulta'); // Retorna alguns registros (por SURDOS E PUBLICADORES) + QUERY STRING!
    
    $router->map('GET', '/registros/editar/[i:regid]/?', function(){echo '<kbd>Em desenvolvimento...</kbd>';});
    $router->map('POST', '/registros/deleta/[i:regid]/?', 'PageController#registroDeleta');


// CADASTRO
$router->map('GET', '/cadastro', 'PageController#cadastro', 'cadastro');
    $router->map('GET', '/cadastro/novo', 'PageController#cadastro', 'cadastroNovo');
    $router->map('POST', '/cadastro/novo', function(){
        $mapa = new Mapa();
        $x = $mapa->preCadastroNovo($_POST);
        if($x == true) {
            SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Surdo salvo no sistema.', 'tipo' => 'success'));
            header('Location: /cadastro');
        } else {
            SessionMessage::novo(array('titulo' => 'Falha', 'texto' => 'Occoreu uma falha. <br><i>'.$x.'</i>', 'tipo' => 'warning'));
            header('Location: /cadastro');
        }
    }, 'cadastroNovoPOST');
    $router->map('GET', '/cadastro/editar/[i:surdoid]', 'PageController#cadastroEditar', 'cadastroEditar');
    $router->map('POST', '/cadastro/editar', function(){
        $mapa = new Mapa();

        $x = $mapa->preCadastroEditar($_POST);
        if($x == true) {
            SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Surdo salvo no sistema.', 'tipo' => 'success'));
            header('Location: /cadastro');
        } else {
            SessionMessage::novo(array('titulo' => 'Falha', 'texto' => 'Occoreu uma falha. <br><i>'.$x.'</i>', 'tipo' => 'warning'));
            header('Location: /cadastro');
        }
    }, 'cadastroEditarPOST');

// TERRITÓRIO PESSOAL
$router->map('GET', '/tpessoal', 'PageController#tpessoal', 'tpessoal');

// REDES SOCIAIS
$router->map('GET', '/social', 'PageController#social', 'social');

// CAMPANHA
$router->map('GET', '/campanha', 'PageController#campanha', 'campanha');

// PERFIL
$router->map('GET', '/perfil', 'PageController#perfil', 'perfil');

/*
 *  ADMINISTRAÇÃO
 */

$router->map('GET', '/admin', 'AdmController#index', 'admIndex');
    $router->map('POST', '/admin/funcao/', 'AdmController#functions', 'admFunctions');
    $router->map('POST', '/admin/impressao/', 'AdmController#impressao', 'admImpressao');
    $router->map('GET', '/admin/surdo', 'AdmController#surdo', 'admSurdo');
    $router->map('GET', '/admin/publicador', 'AdmController#publicador', 'admPublicador');
    $router->map('GET', '/admin/sistema', 'AdmController#sistema', 'admSistema');
    $router->map('GET', '/admin/bd', 'AdmController#bd', 'admBd');
        /**
         * ADM SURDOS
         */
        $router->map('GET', '/admin/surdo/novo', 'AdmController#surdoNovo', 'admSurdoNovo');
        $router->map('POST', '/admin/surdo/novo', function(){
            AdmController::authorized(4);

            $mapa = new Mapa();
            $x = $mapa->surdoNovo($_POST);
            if($x == true) {
                SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Surdo salvo no sistema.', 'tipo' => 'success'));
                header('Location: /admin/surdo/novo');
            } else {
                SessionMessage::novo(array('titulo' => 'Falha', 'texto' => 'Occoreu uma falha. <br><i>'.$x.'</i>', 'tipo' => 'warning'));
                header('Location: /admin/surdo/novo');
            }
        }, 'admSurdoNovoPOST');
        $router->map('GET', '/admin/surdo/ver', 'AdmController#surdoVer', 'admSurdoVer');
        $router->map('GET', '/admin/surdo/editar/[i:surdoid]', 'AdmController#surdoEditar', 'admSurdoEditar');
        $router->map('POST', '/admin/surdo/editar', function(){
            AdmController::authorized(4);

            $mapa = new Mapa();
            $x = $mapa->surdoEditar($_POST);
            if($x == true) {
                SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Surdo salvo no sistema.', 'tipo' => 'success'));
                header('Location: /admin/surdo/editar/'.$_POST['id']);
            } else {
                SessionMessage::novo(array('titulo' => 'Falha', 'texto' => 'Occoreu uma falha. <br><i>'.$x.'</i>', 'tipo' => 'warning'));
                header('Location: /admin/surdo/editar/'.$_POST['id']);
            }
        }, 'admSurdoSalva');
        $router->map('GET', '/admin/surdo/pendencias', 'AdmController#surdoPendencias', 'admSurdoPendencias');
        $router->map('POST', '/admin/surdo/pendencias', function(){
            return AdmController::surdoPendAction($_POST);
        }, 'admSurdoPendAction');

        /**
         * ADM PUBLICADORES
         */


        /**
         * ADM SISTEMA
         */

        $router->map('GET', '/admin/sistema/config', 'AdmController#sisConfig', 'admSisConfig');
        $router->map('GET', '/admin/sistema/bairros', 'AdmController#sisBairros', 'admSisBairros');
        $router->map('GET', '/admin/sistema/vermapas', 'AdmController#sisVerMapas', 'admSisVerMapas');
        $router->map('GET', '/admin/sistema/editarmapas', 'AdmController#sisEditarMapas', 'admSisEditarMapas');
        $router->map('GET', '/admin/sistema/impressao', 'AdmController#sisImpressao', 'admSisImpressao');
        $router->map('GET', '/admin/sistema/log', 'AdmController#sisLOG', 'admSisLOG');


/*
    * FIM ADMINISTRAÇÃO
    */

/*
 *  ROTAS PARA RETORNO DE DADOS
 */
// LISTA PENDÊNCIAS
$router->map('POST', '/pendencias', 'PageController#pendencias');




/*
 *  TESTE
 * 
 */
$router->map('GET', '/teste/[*:nome]', function(){
    return $_SERVER['REQUEST_URI'];
});
