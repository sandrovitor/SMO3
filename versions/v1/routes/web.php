<?php
use App\Http\Controller;
/*
 |-----------------------------
 |  Define as rotas! [by Aura\Router]
 |  
 |  Padrão:
 |  $map->VERBOHTTP(NOME, URI, CLOSURE);
 |  > VERBOHTTP: GET, POST, PATCH, DELETE, OPTIONS, HEADE
 |  > NOME: nome da rota.
 |  > URI: Endereço da rota (URL ou URI). Aceita tokens
 |  > CLOSURE: Função ou método de algum Controller
 |  
 |-----------------------------
 */
$map->get('blog.read', '/blog/{id}', function ($request) {
    $id = (int) $request->getAttribute('id');
    $response = new Zend\Diactoros\Response();
    $response->getBody()->write("You asked for blog entry {$id}.");
    return $response;
});

$map->get('inicio','/inicio', function($request) {
    $response = new Zend\Diactoros\Response();
    $response->getBody()->write("Você chamou a tela de início.");
    return $response;
});

$map->get('inicio2', '/', function($request) {
    $x = new PaginasCtrl();
});