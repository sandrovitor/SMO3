<?php
include('../app/SessionMessage.php');

class Model
{
    private $caracteres = 'abcdefghijlkmnopqrstuvxyzwABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
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

    function randChar(int $tamanhoString = 16, bool $somenteLetras = FALSE) {
        $t = '';

        for($i=0; $i < $tamanhoString; $i++) {
            if($somenteLetras === FALSE) { // LETRAS E NÚMEROS
                $t .= $this->caracteres[mt_rand(0, 61)];
            } else { // SOMENTE LETRAS
                $t .= $this->caracteres[mt_rand(0, 51)];
            }
        }

        return $t;
    }
}