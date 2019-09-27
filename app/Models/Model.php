<?php
include('../app/SessionMessage.php');

class Model
{
    protected $driver = "mysql";
    protected $host = "localhost";
    protected $banco = "smo";
    protected $tabela = "";
    protected $username = "root";
    protected $password = "";
    protected $prefix = "";

    protected $pdo;

    function __construct ()
    {
        
        $dsn = $this->driver.':host='.$this->host.';dbname='.$this->banco;

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
        } catch(PDOException $e) {
            print 'Erro no Banco de Dados';
            die();
        }
        //var_dump($this->pdo);
    }
}