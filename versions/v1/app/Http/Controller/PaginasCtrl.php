<?php
namespace App\Http\Controller;

class PaginasCtrl
{
    protected $response = Zend\Diactoros\Response;

    function __construct() {
        $response = new Zend\Diactoros\Response();
    }

    function index() {

        $response->getBody()->write('Bem vindo!');

        return $response;
    }
}