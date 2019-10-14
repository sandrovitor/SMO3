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

    function setNome(int $usuarioId, $valor)
    {
        $u = $this->getInfo($usuarioId);

        $abc = $this->pdo->prepare('UPDATE login SET nome = :nome WHERE id = :id');
        $abc->bindValue(':nome', $valor, PDO::PARAM_STR);
        $abc->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        try {
            $abc->execute();
            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'alterou nome de <i>'.$u->nome.'</i> para <i>'.$valor.'</i>.');
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function setSobrenome(int $usuarioId, $valor)
    {
        $u = $this->getInfo($usuarioId);

        $abc = $this->pdo->prepare('UPDATE login SET sobrenome = :snome WHERE id = :id');
        $abc->bindValue(':snome', $valor, PDO::PARAM_STR);
        $abc->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        try {
            $abc->execute();
            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'alterou sobrenome de <i>'.$u->sobrenome.'</i> para <i>'.$valor.'</i>.');
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function setSenha(int $usuarioId, $senhaAntiga, $senha1, $senha2)
    {
        if($senha1 !== $senha2) {
            return 'Confirmação de senha é diferente da nova senha.';
        }

        $abc = $this->pdo->prepare('SELECT pass FROM login WHERE id = :id');
        $abc->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        try {
            $abc->execute();
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        if($abc->rowCount() > 0) {
            $reg = $abc->fetch(PDO::FETCH_OBJ);

            if($reg->pass === hash('sha256', $senhaAntiga)) {
                // Atualiza
                $abc = $this->pdo->prepare('UPDATE login SET pass = :senha WHERE :id = :id');
                $abc->bindValue(':id', $usuarioId, PDO::PARAM_INT);
                $abc->bindValue(':senha', hash('sha256', $senha1), PDO::PARAM_INT);
                try {
                    $abc->execute();
                    /**
                     * LOG DE ATIVIDADES
                     */
                    $log = new LOG();
                    $log->novo(LOG::TIPO_ATUALIZA, 'alterou senha.');
                    return true;
                } catch(PDOException $e) {
                    return $e->getMessage();
                }
            } else {
                return 'Senha atual incorreta.';
            }
        } else {
            return 'Usuário não encontrado. Nnehuma alteração realizada.';
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

    public function desMA(int $id, bool $apagaTudo = FALSE)
    {
        $abc = $this->pdo->prepare('SELECT * FROM login WHERE id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        try {
            $abc->execute();
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        if($abc->rowCount() == 0) {
            return 'Usuário não encontrado.';
        }


        $abc = $this->pdo->prepare('UPDATE login SET ma = FALSE WHERE id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        try {
            $abc->execute();
        } catch(PDOException $e) {
            return $e->getMessage();
        }
        
        $logStr = '';

        if($apagaTudo == TRUE) {
            // Remove tabela do banco
            try{
                $abc = $this->pdo->query('DROP TABLE `ma_'.(int)$id.'`');
            } catch(PDOException $e) {
                return $e->getMessage();
            }

            // Remove todas as entradas do usuário da tabela "ma_geral"
            $abc = $this->pdo->query('DELETE FROM `ma_geral` WHERE usuario = '.(int)$id);
            $logStr = ' Todos os dados foram removidos.';
        }

        // Atualiza SESSION
        @session_start();
        $_SESSION['ma'] = FALSE;
        
        /**
         * LOG DE ATIVIDADES
         */
        $log = new LOG();
        $log->novo(LOG::TIPO_SISTEMA, 'desativou o Assistente de Ministério.'.$logStr);

        return true;
    }

    public function ativaMA(int $id)
    {
        $abc = $this->pdo->prepare('SELECT * FROM login WHERE id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        try {
            $abc->execute();
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        $reg = $abc->fetch(PDO::FETCH_OBJ);
        if($reg->ma != TRUE) {
            // Define MA como ativado
            $abc = $this->pdo->query('UPDATE login SET ma = TRUE WHERE id = '.$reg->id);
        }

        // Verifica a existência da tabela
        $abc = $this->pdo->query('SHOW TABLES');
        $tabelas = $abc->fetchAll(PDO::FETCH_NUM);
        $encontrado = FALSE;
        foreach($tabelas as $t) {
            if($t[0] === 'ma_'.$reg->id) {
                $encontrado = TRUE;
            }
        }

        if($encontrado === FALSE) {
            // Cria a tabela
            $abc = $this->pdo->query('CREATE TABLE ma_'.$reg->id.' (
                `data` date NOT NULL,
                `hora` smallint(6) NOT NULL,
                `horaldc` smallint(6) NOT NULL,
                `publicacao` smallint(6) NOT NULL,
                `videos` smallint(6) NOT NULL,
                `revisitas` int(11) NOT NULL,
                `comentario` varchar(200) NOT NULL
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        }

        // Atualiza SESSION
        @session_start();
        $_SESSION['ma'] = TRUE;

        /**
         * LOG DE ATIVIDADES
         */
        $log = new LOG();
        $log->novo(LOG::TIPO_SISTEMA, 'ativou o Assistente de Ministério.');
        return true;
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