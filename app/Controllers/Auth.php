<?php
include_once('../app/Models/User.php');
include_once('../app/Models/Mapa.php');
include_once('../app/SessionMessage.php');


class Auth
{
    private $session;
    private $isLogged;


    function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        
        if(!isset($_SESSION['logado']) || $_SESSION['logado'] == false) {
            $this->isLogged = false;
        } else {
            // Verifica se a sessão expirou
            $timeout = 3600; // (1 hora) em segundos.
            $time = $_SERVER['REQUEST_TIME'];
            if($time - $_SESSION['ultima_atividade'] > $timeout) { // A diferença é maior que o tempo limite. Volta ao login, para confirmar credenciais.
                $_SESSION['logado'] = false;
                $this->isLogged = false;
                SessionMessage::novo(array('titulo' => 'Confirme sua identidade!', 'texto' => 'Você ficou muito tempo ocioso.', 'tipo' => 'info'));
                header('Location: /login');
                return false;
            } else {
                $this->isLogged = true;
                $_SESSION['ultima_atividade'] = $_SERVER['REQUEST_TIME'];
                $this->session = $_SESSION;
                return true;
            }
        }
    }

    function getIsLogged()
    {
        return $this->isLogged;
    }

    function authorized()
    {
        if($this->isLogged == false) {
            // Captura URL local, armazena em SESSION e depois redireciona.
            @session_start();
            $_SESSION['url'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            return false;
        }
        return true;
    }

    function authenticate($username, $password)
    {
        //var_dump($username, $password);
        $user = new User();
        $aut = $user->auth($username, $password);
        if($aut == false) {
            SessionMessage::novo(array('titulo' => 'NEGADO!', 'texto' => 'Usuário e/ou senha está incorreto.', 'tipo' => 'danger'));
            return 'Não encontrado!';
            return false;
        } else {
            // Verifica se existem restrições no login
            $expira = new DateTime($aut->expira);
            if($aut->bloqueado == true) {
                // Bloqueado
                SessionMessage::novo(array('titulo' => 'NEGADO!', 'texto' => 'Usuário bloqueado.', 'tipo' => 'warning'));
                return 'Bloqueado';
                return false;
            }
            if($aut->tentativas >= 3) {
                // Excedeu número de tentativas
                SessionMessage::novo(array('titulo' => 'Falha!', 'texto' => 'Usuário bloqueado por número de tentativas.', 'tipo' => 'warning'));
                return 'Tentativas';
                return false;
            }
            if($expira < new DateTime()) {
                // Perfil expirado.
                SessionMessage::novo(array('titulo' => 'Falha!', 'texto' => 'Validade do perfil expirou.', 'tipo' => 'warning'));
                return 'Perfil expirado';
                return false;
            }
            if((int)$aut->nivel === 0) {
                // Nivel 0 = Sem acesso
                SessionMessage::novo(array('titulo' => 'Falha!', 'texto' => 'Esse perfil está sem acesso. Nível 0..', 'tipo' => 'warning'));
                return 'Nível 0';
                return false;
            }

            // Esgotadas todas as verificações acima....
            // Cria sessão e autoriza o acesso
            
            $_SESSION['logado'] = true;
            $_SESSION['nome'] = $aut->nome;
            $_SESSION['sobrenome'] = $aut->sobrenome;
            $_SESSION['email'] = $aut->email;
			$_SESSION['id'] = (int)$aut->id;
			$_SESSION['user'] = $aut->user;
			$_SESSION['nivel'] = (int)$aut->nivel;
			$_SESSION['criado'] = $aut->criado;
			$_SESSION['atualizado'] = $aut->atualizado;
			$_SESSION['expira'] = $aut->expira;
			$_SESSION['logado'] = true;
			$_SESSION['sessiontime'] = time() + ((60*60) * (2)); // Tempo de sessão.
			$_SESSION['login_time'] = date('Y-m-d H:i:s');
			$_SESSION['change_pass'] = $aut->change_pass;
			$_SESSION['acessos'] = $aut->qtd_login+1;
            $_SESSION['modo_facil'] = (bool)$aut->modo_facil;
            $_SESSION['ma'] = (bool)$aut->ma;
			if($aut->beta == TRUE) {
				$_SESSION['beta'] = TRUE;
			} else {
				$_SESSION['beta'] = FALSE;
			}
			$_SESSION['tema'] = $aut->tema;
			$_SESSION['ultima_atividade'] = $_SERVER['REQUEST_TIME'];

            // Confirma login
            $user->confirmAuth($aut->id);

            return true;
        }
        
    }

    function unauthenticate()
    {
        // Armazena session localmente
        session_unset();
        

        $_SESSION['logado'] = false;
        $_SESSION['user'] = $this->session['user'];
        $this->isLogged = false;

        return true;
    }

    /**
     * Recebe o nível de acesso para essa página. A função irá conferir com o nível de acesso armazenado na SESSION.
     */
    function guard(int $nivel = 1)
    {
        if(!$this->authorized()) {
            return false;
        } else if((int)$_SESSION['nivel'] < $nivel) {
            // Caso o nível de acesso da SESSION seja inferior ao nível exigido, recebe mensagem de erro na página inicial.
            SessionMessage::novo(array('titulo' => 'Acesso negado!', 'texto' => 'Você não pode acessar a página <i>'.$_SERVER['REQUEST_URI'].'</i>, porque não possui o nível de acesso permitido. [Acesso nível: '.$nivel.'].', 'tipo'=> 'warning'));
            header('Location: /');

            //exit('<div class="alert alert-danger"><strong><i class="fas fa-ban"></i> Acesso negado!</strong> Você não pode acessar a página <i>'.$_SERVER['REQUEST_URI'].'</i>, porque não possui o nível de acesso permitido. [Acesso nível: '.$nivel.'].</div>');
            exit();
            return false;
        }

        return true;
    }
}