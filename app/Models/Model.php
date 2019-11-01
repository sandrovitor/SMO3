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
    protected $charset = "";

    protected $pdo;

    function __construct ()
    {
        $caminho = substr(__DIR__, 0, strrpos(__DIR__, '/app'));
        $caminho .= '/.env';

        if(file_exists($caminho)) {
            $handler = fopen($caminho, 'r');
            $arquivo = fread($handler, filesize($caminho));
            $config = explode("\n", $arquivo);
            foreach($config as $c) {
                $a = explode('=', $c);
                switch($a[0]) {
                    case 'DB_CONNECTION':
                        $this->driver = trim($a[1]);
                        break;

                    case 'DB_HOST':
                        $this->host = trim($a[1]);
                        break;

                    case 'DB_DATABASE':
                        $this->banco = trim($a[1]);
                        break;
                        
                    case 'DB_USERNAME':
                        $this->username = trim($a[1]);
                        break;

                    case 'DB_PASSWORD':
                        $this->password = trim($a[1]);
                        break;
                        
                    case 'DB_CHARSET':
                        $this->charset = trim($a[1]);
                        break;
                        
                }
            }
        }
        
        if($this->charset == '') {
            $dsn = $this->driver.':host='.$this->host.';dbname='.$this->banco;
        } else {
            $dsn = $this->driver.':host='.$this->host.';dbname='.$this->banco.';charset='.$this->charset;
        }
        

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

    function contaPendencias()
    {
        $qtd = 0;
        $pend = array();

        // Pré-cadastro
        $abc = $this->pdo->query('SELECT id FROM `pre_cadastro` WHERE pendente = TRUE');
        if($abc->rowCount() > 0) {
            $qtd++;
            array_push($pend, array('titulo' => 'Pré-cadastro:', 'texto' => 'Há '. $abc->rowCount() .' pendências aguardando análise.', 'tipo' => 'warning', 'link' => '/admin/surdo/pendencias'));
        }

        // Perfis expirados.
        $hoje = new DateTime();
        $abc = $this->pdo->query('SELECT id FROM `login` WHERE bloqueado = 0 AND expira <= "'.$hoje->format('Y-m-d H:i:s').'"');
        if($abc->rowCount() > 0) {
            $qtd++;
            array_push($pend, array('titulo' => 'Publicador(es) expirado(s):', 'texto' => 'Há '. $abc->rowCount() .' publicador(es) com o perfil expirado.', 'tipo' => 'danger', 'link' => '/admin/publicadores/ver'));
        }

        // Perfis proximo de expirar
        $futuro = new DateTime();
        $futuro->add(new DateInterval('P60D'));
        $abc = $this->pdo->query('SELECT id FROM `login` WHERE bloqueado = 0 AND expira > "'.$hoje->format('Y-m-d H:i:s').'" AND expira <= "'.$futuro->format('Y-m-d H:i:s').'"');
        if($abc->rowCount() > 0) {
            $qtd++;
            array_push($pend, array('titulo' => 'Publicador(es) perto de expirar:', 'texto' => 'Há '. $abc->rowCount() .' publicador(es) que vão expirar nos próximos 60 dias.', 'tipo' => 'warning', 'link' => '/admin/publicadores/ver'));
        }

        $retorno = array('qtd' => $qtd, 'dados' => $pend);
        return $retorno;
    }
}