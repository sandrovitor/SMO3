<?php
include_once('Model.php');

class Bairro extends Model {
    protected $pdo;


    function __construct()
    {
        parent::__construct();
    }

    
    function listaBairro()
    {
        $abc = $this->pdo->query('SELECT ter.id, ter.bairro, ter.regiao as regiao_numero, (SELECT config.valor FROM config WHERE config.opcao = CONCAT("regiao_", regiao_numero)) as regiao_nome FROM ter WHERE 1 ORDER BY ter.regiao ASC, ter.bairro ASC');
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

}