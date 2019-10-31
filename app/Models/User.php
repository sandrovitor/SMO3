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
                $abc = $this->pdo->prepare('UPDATE login SET pass = :senha WHERE id = :id');
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

    function redefineSenha(int $usuarioId, $senha)
    {
        // Atualiza
        $abc = $this->pdo->prepare('UPDATE login SET pass = :senha, token = "", change_pass = "n" WHERE id = :id');
        $abc->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $abc->bindValue(':senha', hash('sha256', $senha), PDO::PARAM_INT);
        try {
            $abc->execute();
            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'redefiniu a senha.', $usuarioId);
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
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
            $abc = $this->pdo->prepare('UPDATE '.$this->tabela.' SET tentativas = tentativas + 1 WHERE `user` = :username');
            $abc->bindValue(':username', $username, PDO::PARAM_STR);
            $abc->execute();

            return false;
        }
    }

    public function confirmAuth(int $id)
    {
        // Atualiza quantidade de tentativas para 0 e incrementar quantidade de logins
        $abc = $this->pdo->prepare('UPDATE '.$this->tabela.' SET `tentativas` = 0, `token`="", `qtd_login` = `qtd_login`+1 WHERE `id` = :id');
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

    public function checaUsernameLivre(string $user)
    {
        $abc = $this->pdo->prepare('SELECT * FROM login WHERE user = :user');
        $abc->bindValue(':user', $user, PDO::PARAM_STR);
        $abc->execute();

        if($abc->rowCount() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function notificaSenha(bool $notifica, int $id)
    {
        $abc = $this->pdo->prepare('UPDATE login SET change_pass = :notifica WHERE id = :id');
        if($notifica == TRUE) {
            $t = 'y';
        } else {
            $t = 'n';
        }
        try {
            $abc->bindValue(':notifica', $t, PDO::PARAM_BOOL);
            $abc->bindValue(':id', $id, PDO::PARAM_INT);
            $abc->execute();
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
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
            $abc = $this->pdo->query('SELECT `id`, `nome`, `sobrenome`, `nivel`, `user`, `email`, `expira`, `bloqueado`, `tentativas` FROM `login` WHERE `bloqueado` = 0 AND `expira` > "'.date('Y-m-d').'" ORDER BY nome ASC, sobrenome ASC');
        } else { // Puxa todos os logins
            $abc = $this->pdo->query('SELECT `id`, `nome`, `sobrenome`, `nivel`, `user`, `email`, `expira`, `bloqueado`, `tentativas` FROM `login` WHERE 1 ORDER BY nome ASC, sobrenome ASC');
        }
        
        
        if($abc->rowCount() === 0) {
            return false;
        }

        return $abc->fetchAll(PDO::FETCH_OBJ);
    }

    public function novo(array $u)
    {
        $sql = 'INSERT INTO login (id, nome, sobrenome, user, email, pass, nivel, criado, atualizado, expira, change_pass, token, beta, tentativas, bloqueado, qtd_login, modo_facil, copyright_accept, ma) VALUES
        ("", :nome, :sobrenome, :user, :email, :pass, :nivel, :criado, :atualizado, :expira, :change_pass, "", 0, 0, 0, 0, 0, 0, 0)';

        if($u['senha1'] === $u['senha2']) {
            $pass = hash('sha256',$u['senha1']);
        } else {
            return 'Senhas diferentes.';
        }

        // Converte array em objeto
        $u = (object)$u;

        $hoje = date('Y-m-d H:i:s');
        $expira = new DateTime($hoje);
        $expira->add(new DateInterval('P1Y'));
        try {
            $abc = $this->pdo->prepare($sql);

            $abc->bindValue(':nome', $u->nome, PDO::PARAM_STR);
            $abc->bindValue(':sobrenome', $u->sobrenome, PDO::PARAM_STR);
            $abc->bindValue(':user', $u->usuario, PDO::PARAM_STR);
            $abc->bindValue(':email', $u->email, PDO::PARAM_STR);
            $abc->bindValue(':pass', $pass, PDO::PARAM_STR);
            $abc->bindValue(':nivel', (int)$u->nivel, PDO::PARAM_INT);
            $abc->bindValue(':criado', $hoje, PDO::PARAM_STR);
            $abc->bindValue(':atualizado', $hoje, PDO::PARAM_STR);
            $abc->bindValue(':expira', $expira->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $abc->bindValue(':change_pass', 'n', PDO::PARAM_BOOL);

            $abc->execute();
            // Verifica se o usuário foi criado

            $abc = $this->pdo->prepare('SELECT * FROM login WHERE user = :user');
            $abc->bindValue(':user', $u->usuario, PDO::PARAM_STR);
            $abc->execute();

            if($abc->rowCount() == 1){
                return true;
            } else {
                return 'Usuário não foi criado. Consulte desenvolvedor.';
            }
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    public function salva($id, $nome, $sobrenome, $usuario, $email, $expira, $nivel)
    {
        $reg = $this->getInfo($id);
        $log = new LOG();
        if($reg === FALSE) {
            $log->novo(LOG::TIPO_ERRO, 'não foi possível salvar usuário [ID: '.$id.'], pois ele não existe.');
            SessionMessage::novo(array('tipo' => 'warning', 'titulo' => 'Falha!', 'texto' => 'Não foi possível salvar usuário [ID: '.$id.'], pois ele não existe.'));
            return false;
        }

        $d = new DateTime($expira);
        $sql = 'UPDATE login SET nome = :nome, sobrenome = :sobrenome, user = :usuario, email = :email, expira = :expira, nivel = :nivel WHERE id = :id';
        $abc = $this->pdo->prepare($sql);

        try {
            $abc->bindValue(':nome', $nome, PDO::PARAM_STR);
            $abc->bindValue(':sobrenome', $sobrenome, PDO::PARAM_STR);
            $abc->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $abc->bindValue(':email', $email, PDO::PARAM_STR);
            $abc->bindValue(':expira', $d->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $abc->bindValue(':nivel', $nivel, PDO::PARAM_INT);
            $abc->bindValue(':id', $id, PDO::PARAM_INT);

            $abc->execute();
            $log->novo(LOG::TIPO_ATUALIZA, 'editou usuário '.$nome.' [ID: '.$id.'].');
            SessionMessage::novo(array('tipo' => 'success', 'titulo' => 'Sucesso!', 'texto' => 'Dados do usuário foram salvos.'));
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    public function bloquear($id)
    {
        // Bloquear usuário
        $reg = $this->getInfo($id);

        $abc = $this->pdo->query('UPDATE login SET bloqueado = TRUE WHERE id = '.$reg->id);
        return true;
    }

    public function desbloquear($id)
    {
        // Identifica a causa do bloqueio e desfaz.
        $reg = $this->getInfo($id);
        $sql = '';

        if((bool)$reg->bloqueado === TRUE) {
            // Remove bloqueio manual
            $sql.= 'UPDATE login SET bloqueado = FALSE WHERE id = '.$reg->id.';';
        }

        if($reg->tentativas >= 3) {
            // Remove excesso de tentativas
            $sql.= 'UPDATE login SET tentativas = 0 WHERE id = '.$reg->id.';';
        }

        $hoje = new DateTime();
        $expira = new DateTime($reg->expira);
        if($hoje >= $expira) {
            // Adiciona um ano à validade do perfil
            $expira = new DateTime();
            $expira->add(new DateInterval('P1Y'));
            $sql.= 'UPDATE login SET expira = "'.$expira->format('Y-m-d H:i:s').'" WHERE id = '.$reg->id.';';
            
        }

        // Executa a atualização
        $abc = $this->pdo->query($sql);

        return true;

    }

    public function ativaBETA($id)
    {
        $u = $this->getInfo($id);

        if((bool)$u->beta == TRUE) {
            return true;
        } else {
            $abc = $this->pdo->query('UPDATE login SET beta = TRUE WHERE id = '.$u->id);
            return true;
        }
    }

    public function desativaBETA($id)
    {
        $u = $this->getInfo($id);

        if((bool)$u->beta == FALSE) {
            return true;
        } else {
            $abc = $this->pdo->query('UPDATE login SET beta = FALSE WHERE id = '.$u->id);
            return true;
        }
    }

    public function resetaSenha($id)
    {
        $u = $this->getInfo($id);

        $abc = $this->pdo->query('UPDATE login SET pass = "'.hash('sha256', '12345678').'" WHERE id = '.$u->id);
        return true;
    }

    public function ativaMFacil($id)
    {
        $u = $this->getInfo($id);

        if((bool)$u->modo_facil == FALSE) {
            $abc = $this->pdo->query('UPDATE login SET modo_facil = TRUE WHERE id = '.$u->id);
        }

        return true;
    }

    public function desativaMFacil($id)
    {
        $u = $this->getInfo($id);

        if((bool)$u->modo_facil == TRUE) {
            $abc = $this->pdo->query('UPDATE login SET modo_facil = FALSE WHERE id = '.$u->id);
        }

        return true;
    }

    public function delete($id)
    {
        $u = $this->getInfo($id);

        $abc = $this->pdo->query('DELETE FROM login WHERE id = '.$u->id);

        SessionMessage::novo(array('titulo' => 'Sucesso!', 'tipo' => 'success', 'texto' => 'Usuário '.$u->nome.' '.$u->sobrenome.' [ID: '.$u->id.'] foi removido com sucesso.'));
        return true;
    }

    public function esqueciSenha(int $usuarioId)
    {
        //var_dump($usuarioId);

        // Recupera dados do usuário
        $u = $this->getInfo($usuarioId);
        if($u === false) {
            SessionMessage::novo(array('titulo' => 'Não encontrado!', 'texto' => 'Não localizamos sua conta. Contate o administrador.', 'tipo' => 'warning'));
            header('Location: /login');
            exit();
        } else {
            //var_dump($u);
            // Verifica se o usuário possui um e-mail de recuperação.
            if($u->email == '') {
                SessionMessage::novo(array('titulo' => 'Não consegui!', 'texto' => 'Você não possui um e-mail para continuar o procedimento. Contate o administrador.', 'tipo' => 'warning'));
                header('Location: /login');
                exit();
            } else {
                // Envia e-mail de recuperação.
                /**
                 * A URL de definição será composta por nome de usuario, token de redefinição (randChar) e um hash das duas informações (md5).
                 * Exemplo: USER=teste&TOKEN=sadvavjuqUBPIU&VERIFY=SABVAbbkbbB5163
                 */

                $t = $this->randChar(24);
                $token = substr($t, 0,4).'-'.substr($t, 4,4).'-'.substr($t, 8,4).'-'.substr($t, 12,4).'-'.substr($t, 16,4).'-'.substr($t, 20,4);

                

                $verify = md5($u->user.$token);
                $url = 'https://'.$_SERVER['HTTP_HOST'].'/redefine?user='.$u->user.'&token='.$token.'&verify='.$verify;
                // Salva token no banco de dados
                $abc = $this->pdo->query('UPDATE '.$this->tabela.' SET token = "'.$token.'" WHERE id = '.$u->id);

                // Envia email.

                $html = '
                <html>
                    <head>
                        <title>Redefinir senha - Sistema de Hospedagem LS-03</title>
                        <style>
                        @import url(\'https://fonts.googleapis.com/css?family=Lato\');
                        .bg-smo {
                            background-color: rgba(0,121,107,1);
                            color: rgb(236, 236, 236);
                        }
                        body {
                            font-size:16px;
                            font-family: \'Lato\', sans-serif;
                        }
                        </style>
                    </head>
                    <body>
                        <div style="background-color: rgba(0,121,107,1); font-size: 1.3rem; padding: .5rem 1rem;color:white; font-weight:bold;">SMO :: Sistema de Mapas Online</div>
                        <div style="padding: 2rem 1rem; font-size: 1.2rem; line-height: 1.8rem;">
                            <h3>Olá '.$u->nome.' '.$u->sobrenome.' <small style="color: #6c757d; font-size:.75rem;"><i>'.$u->user.'</i></small>.</h3>

                            Você solicitou redefinição de senha pelo site. Após a redefinição, você poderá entrar no sistema com seu Usuário e a nova senha.<br>
                            <a href="'.$url.'" target="_blank">Clique aqui para ser direcionado à tela de redefinição de senha</a>.<br><small>Esse link expira no seu próximo login ou se uma nova redefinição de senha for solicitada.</small><br><br>
                            
                            Caso você não tenha solicitado essa redefinição, ignore este e-mail. Suas credenciais de acesso não foram afetadas.<br><br>
                            
                            Att,<br>
                            Sistema de Mapas Online.
                        </div>
                        <div style="background-color: #00796b34; font-size: .8rem; padding: .2rem 1rem;color:black; border-top: 2px solid #00796b;">
                            * Não responder este e-mail pois ele foi gerado automaticamente.<br>
                            ** Em caso de dúvidas, consulte um dos administradores.<br>
                            *** Esse sistema não tem vínculo com o site JW.ORG.
                        </div>
                    </body>
                </html>
                ';
                

                $headers = array('MIME-Version: 1.0', 'Content-type: text/html; charset=utf-8');
                $headers[] = 'To: '.$u->email;
                $headers[] = 'From: naoresponda@lscb.dssmart.com.br';
                $headers[] = 'Reply-To: '.$u->email;


                $send = mail($u->email, 'Redefina sua senha', $html, implode("\r\n", $headers));
                $mailStr = explode('@',$u->email);
                $mailStr[0] = substr($mailStr[0], 0, 1).'***'.substr($mailStr[0],strlen($mailStr[0])-2, 1);
                $mailStr = implode('@', $mailStr);

                if($send == true) {
                    SessionMessage::novo(array('titulo' => 'Enviado!', 'texto' => 'E-mail de redefinição foi enviado para <strong>'.$mailStr.'</strong>.', 'tipo' => 'success'));
                } else {
                    SessionMessage::novo(array('titulo' => 'Falha!', 'texto' => 'Não conseguimos enviar e-mail de redefinição.', 'tipo' => 'warning'));
                }
                
                header('Location: /login');
                exit();
            }
        }
        
        
    }

    public function buscaEmail(string $email)
    {
        $abc = $this->pdo->prepare('SELECT id FROM login WHERE email = :email');
        try {
            $abc->bindValue(':email', $email, PDO::PARAM_STR);
            $abc->execute();

            if($abc->rowCount() == 0) {
                return false;
            } else {
                $reg = $abc->fetch(PDO::FETCH_OBJ);
                return $reg->id;
            }
        } catch(PDOException $e) {
            return 'erro';
        }
    }

    public function buscaUsuario(string $user)
    {
        $abc = $this->pdo->prepare('SELECT id FROM login WHERE user = :user');
        try {
            $abc->bindValue(':user', $user, PDO::PARAM_STR);
            $abc->execute();

            if($abc->rowCount() == 0) {
                return false;
            } else {
                $reg = $abc->fetch(PDO::FETCH_OBJ);
                return $reg->id;
            }
        } catch(PDOException $e) {
            return 'erro';
        }
    }
}