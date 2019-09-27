<?php
include_once('Model.php');

class User extends Model {
    protected $tabela = 'login';
    protected $pdo;
    public $info;


    function __construct(int $id = 0)
    {
        parent::__construct();
        if($id != 0)
        {
            $this->info = $this->getInfo($id);
        }
    }

    public function auth($username, $password)
    {
        // Procura no banco de dados usuário e senha.
        $abc = $this->pdo->prepare('SELECT * FROM '.$this->tabela.' WHERE `user` = :username AND `pass` = :pass');
        $abc->bindValue(':username', $username, PDO::PARAM_STR);
        $abc->bindValue(':pass', hash('sha256', $password), PDO::PARAM_STR);

        
        $abc->execute();
        if($abc->rowCount() > 0) {
            $retorno = $abc->fetch(PDO::FETCH_OBJ);

            return $retorno;
        } else {
            return false;
        }
    }

    public function confirmAuth(int $id)
    {
        // Atualiza quantidade de tentativas para 0 e incrementar quantidade de logins
        $abc = $this->pdo->prepare('UPDATE '.$this->tabela.' SET `tentativas` = 0, `qtd_login` = `qtd_login`+1 WHERE `id` = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);

        $abc->execute();
    }

    public function getInfo(int $id)
    {
        // Procura usuário no BD
        $abc = $this->pdo->prepare('SELECT * FROM '.$this->tabela.' WHERE id = :id AND 1=1');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() > 0) {
            $reg = $abc->fetch(PDO::FETCH_OBJ);
            // Remove a coluna de senha
            unset($reg->pass);
            return $reg;
        }

        return false;
        
    }

    public function listaUsuarios(bool $apenasAtivo = TRUE)
    {
        if($apenasAtivo == TRUE) { // Só puxa os logins ativos no sistema
            $abc = $this->pdo->query('SELECT `id`, `nome`, `sobrenome`, `nivel` FROM `login` WHERE `bloqueado` = 0 AND `expira` > "'.date('Y-m-d').'" ORDER BY nome ASC, sobrenome ASC');
        } else { // Puxa todos os logins
            $abc = $this->pdo->query('SELECT `id`, `nome`, `sobrenome`, `nivel` FROM `login` WHERE 1 ORDER BY nome ASC, sobrenome ASC');
        }
        
        if($abc->rowCount() == 0) {
            return false;
        }

        return $abc->fetchAll(PDO::FETCH_OBJ);
    }
}