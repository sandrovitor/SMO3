<?php
// Autoload do Composer
require_once('../vendor/autoload.php');
Use eftec\bladeone\BladeOne;
//$views = '../public/resources/views';
//$cache = '../cache';

// Libera restante da página, somente se estiver autenticado
include_once('Auth.php');
include_once('../app/SessionMessage.php');

class PageController
{
    
    const VIEWS = '../public/resources/views';
    const CACHE = '../cache';

    function authorized()
    {
        // Verifica se está autenticado
        $auth = new Auth();
        if($auth->authorized() == false) {
            return false;
        }

        return true;
    }

    private function router()
    {
        $router = new AltoRouter();
        include('../routes/web.php');
        return $router;
    }

    function pendencias()
    {
        // Dá um retorno das pendências
        /**
         * 'titulo' => Titulo a pendência
         * 'texto' => Conteúdo explicativo da pendência.
         * 'tipo' => Grau da pendência... Pode ser 'info', 'warning', 'danger', 'dark', igual as classes do ALERT.
         * 'link' => Endereço da pendência.
         */
        $x = array();

        $mapa = new Mapa();
        $abc = $mapa->contaPendencias();
        if($abc > 0) {
            array_push($x, array('titulo' => 'Pré-cadastro:', 'texto' => 'Há '. $abc .' pendências aguardando análise.', 'tipo' => 'warning', 'link' => '/admin/surdo/pendencias'));
        }



        if(count($x) == 0) {
            return '{0}';
        } else {
            return json_encode($x);
        }
    }

    function login()
    {
        // Verifica se já está autenticado
        $auth = new Auth();
        if($auth->getIsLogged()) {
            header('Location: /');
            return false;
        }

        // Exibe tela de login
        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("login", array(
            'smoMSG' => SessionMessage::lerFormatado()
        ));
    }

    function loga($params)
    {
        // Chama autenticação de usuário
        $auth = new Auth();
        $auth->authenticate($_POST['usuario'], $_POST['senha']);
        // Escreve cookie
        if(isset($_POST['save_user']) && $_POST['save_user'] == 'yes') {
            $x = setcookie('user', $_SESSION['user'], time()+(60*60*24*30), '/','',TRUE);
        } else {
            $x = setcookie('user', '', time()-3600, '/','',TRUE);
        }
        // Seta cookie de ID e nível.
        setcookie('smoCod', $_SESSION['id'], time()+(60*60*24*30), '/','',TRUE);
        setcookie('smoAut', $_SESSION['nivel'], time()+(60*60*24*30), '/','',TRUE);

        
        // Verifica se há um URL para redirecionar
        if($_SESSION['url'] != '') {
            $url = $_SESSION['url'];
            unset($_SESSION['url']);
            header('Location: '.$url);
        } else {
            header('Location: /');
        }
        
    }

    function logout()
    {
        // Verifica se está logado
        $auth = new Auth();
        if($auth->getIsLogged() == true) {
            // Desloga
            $auth->unauthenticate();
            
            SessionMessage::novo(array('titulo' => 'Até mais!', 'texto' => 'Você saiu do SMO...', 'tipo' => 'info'));
        }

        
        
        header('Location: /login');
    }

    function index()
    {
        // Checa se está autenticado
        PageController::authorized();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("homepage",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    function consulta()
    {
        // Checa se está autenticado
        PageController::authorized();

        $mapa = new Mapa();
        $bairros = $mapa->listaBairro();
        //var_dump($bairros);
        $x = ''; $group = '';
        foreach($bairros as $b) {
            if($group == '') {
                $x .= '<optgroup label="Região '.$b->regiao_nome.'"> ';
                $group = $b->regiao_nome;
            } else if($group != $b->regiao_nome) {
                $x .= '</optgroup> <optgroup label="Região '.$b->regiao_nome.'"> ';
                $group = $b->regiao_nome;
            }
            $x .= '<option value="'.$b->id.'">'.$b->bairro.'</option> ';
        }
        $x.= '</optgroup>';

        $bairros = $x;

        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("consulta",array(
            'bairros' => $bairros,
            'containertipo' => 'container-fluid',
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    function surdo(array $p)
    {
        // Checa se está autenticado
        PageController::authorized();

        $mapa = new Mapa();

        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("surdo",array(
            'containertipo' => 'container',
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'surdo' => json_decode($mapa->surdoId($p['surdoid'])),
        ));
    }

    function consultaPesquisa()
    {
        // Checa se está autenticado
        PageController::authorized();

        // Extrai conteudo da URL
        $uri = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'], 'pesquisa/')+9);
        //var_dump($uri);

        // Explode a URL para separar as variáveis
        $variaveis = explode('/', urldecode($uri));
        //var_dump($variaveis);
        foreach($variaveis as $key => $value) {
            if($value == '~null~') {
                $variaveis[$key] = '';
            }
        }
        //var_dump($variaveis);

        // Consulta o banco de dados com as variaveis
        $mapa = new Mapa();

        //var_dump($mapa->pesquisa($variaveis));

        return $mapa->pesquisa($variaveis);
    }

    function consultaId()
    {
        // Checa se está autenticado
        PageController::authorized();

        // Extrai conteudo da URL
        $id = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'], 'id/')+3);

        $mapa = new Mapa();
        return $mapa->surdoId((int)$id);
        
    }

    function registros()
    {
        // Checa se está autenticado
        PageController::authorized();

        
        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("registros",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y')
        ));
    }

    function registrosNovo(array $params)
    {
        // Checa se está autenticado
        PageController::authorized();

        // Verifica se há parametros
        $surdoId = '';
        $surdoUnico = false;
        if($params != '' && count($params) > 0) {
            if(isset($params['id']) && $params['id'] != '') {
                $surdoId = $params['id'];
                $surdoUnico = true;
            }
        }

        $user = new User();
        $listaUsuarios = $user->listaUsuarios(TRUE);

        $publicadores = '';
        foreach($listaUsuarios as $b) {
            if($b->id == $_SESSION['id']) {
                $publicadores .= '<option value="'.$b->id.'" selected>'.$b->nome.' '.$b->sobrenome.'</option> ';
            } else {
                $publicadores .= '<option value="'.$b->id.'">'.$b->nome.' '.$b->sobrenome.'</option> ';
            }
        }

        $mapa = new Mapa();
        $listaSurdos = $mapa->listaSurdos();
        $x = ''; $group = '';
        foreach($listaSurdos as $b) {
            if($group == '') {
                $x .= '<optgroup label="'.$b->bairro.'"> ';
                $group = $b->bairro;
            } else if($group != $b->bairro) {
                $x .= '</optgroup> <optgroup label="'.$b->bairro.'"> ';
                $group = $b->bairro;
            }

            // Verifica se um surdo especifico foi enviado!
            if($surdoId != '' && $b->id == $surdoId) {
                $x .= '<option value="'.$b->id.'" data-be="'.$b->be.'" data-resp-id="'.$b->resp_id.'" data-resp="'.$b->resp.'" selected>'.$b->nome.'</option> ';
            } else {
                $x .= '<option value="'.$b->id.'" data-be="'.$b->be.'" data-resp-id="'.$b->resp_id.'" data-resp="'.$b->resp.'">'.$b->nome.'</option> ';
            }
        }
        $x.= '</optgroup>';

        $surdos = $x;

        
        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("registrosNovo",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'publicadores' => $publicadores,
            'surdos' => $surdos,
            'surdoId' => $surdoId,
            'surdoUnico' => $surdoUnico
        ));
    }

    function registrosBuscar(array $params)
    {
        // Checa se está autenticado
        PageController::authorized();


        $user = new User();
        $listaUsuarios = $user->listaUsuarios(TRUE);

        $publicadores = '';
        foreach($listaUsuarios as $b) {
            $publicadores .= '<option value="'.$b->id.'">'.$b->nome.' '.$b->sobrenome.'</option> ';
            
        }

        $mapa = new Mapa();
        $listaSurdos = $mapa->listaSurdos();
        $x = ''; $group = '';
        foreach($listaSurdos as $b) {
            if($group == '') {
                $x .= '<optgroup label="'.$b->bairro.'"> ';
                $group = $b->bairro;
            } else if($group != $b->bairro) {
                $x .= '</optgroup> <optgroup label="'.$b->bairro.'"> ';
                $group = $b->bairro;
            }

            if(isset($params['surdoid']) && $params['surdoid'] == $b->id) {
                $x .= '<option value="'.$b->id.'" data-be="'.$b->be.'" data-resp-id="'.$b->resp_id.'" data-resp="'.$b->resp.'" selected>'.$b->nome.'</option> ';
            } else {
                $x .= '<option value="'.$b->id.'" data-be="'.$b->be.'" data-resp-id="'.$b->resp_id.'" data-resp="'.$b->resp.'">'.$b->nome.'</option> ';
            }
            
        }
        $x.= '</optgroup>';

        $surdos = $x;
        if(!isset($params['surdoid'])) { $surdoid = '';} else {$surdoid = $params['surdoid'];}

        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("registrosBuscar",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'publicadores' => $publicadores,
            'surdos' => $surdos,
            'surdoid' => $surdoid
        ));
    }

    function registrosConsulta(array $params)
    {

        /*
         * TIPOS DE URL QUE COMBINA COM ESSE CONTROLLER
         * 
         * registros/buscar/100                                     => Somente um único registro. Variáveis: $regid
         * registros/buscar/surdo/100/publicador/100/               => Vários registros (LIMIT 10) por SURDOS E PUBLICADORES. Variáveis: $surdoid, $pubid
         * registros/buscar/surdo/100/publicador/100/limit=0-100&order=new      => Vários registros por SURDOS E PUBLICADORES + QUERY STRING. Variáveis: $surdoid, $pubid, $querystr
         * 
         */ 

        
        $registros = new Registros();
        echo $registros->busca($params);

    }

    function registrosUltimos()
    {
        $registros = new Registros();
        echo $registros->ultimos();
    }

    function registroDeleta(array $params)
    {
        $auth = new Auth();
        if($auth->authorized() == false) {
            // Redireciona para página de login
            header('Location: /login');
            exit();
        }

        $r = new Registros();
        $r->deleta($params['regid']);
    }

    function cadastro()
    {
        // Checa se está autenticado
        PageController::authorized();

        $mapa = new Mapa();

        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("cadastro",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bairros' => $mapa->listaBairro(),
        ));
    }

    function cadastroEditar(array $obj)
    {
        // Checa se está autenticado
        PageController::authorized();

        $mapa = new Mapa();

        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("cadastroEditar",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bairros' => $mapa->listaBairro(),
            'surdo' => json_decode($mapa->surdoId($obj['surdoid'])),
        ));
    }

    function tpessoal()
    {
        // Checa se está autenticado
        PageController::authorized();

        $mapa = new Mapa();
        $surdos = $mapa->meuTP();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("tpessoal",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'surdos' => $surdos
        ));
    }

    function social()
    {
        // Checa se está autenticado
        PageController::authorized();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("social",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    function campanha()
    {
        // Checa se está autenticado
        PageController::authorized();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("campanha",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    function perfil()
    {
        // Checa se está autenticado
        PageController::authorized();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("perfil",array(
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    function teste()
    {
        return 'Uma página de teste';
    }
}