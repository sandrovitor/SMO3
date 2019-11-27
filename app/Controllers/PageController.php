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

    static function authorized()
    {
        // Verifica se está autenticado
        $auth = new Auth();
        if($auth->authorized() == false) {
            return false;
        }

        return true;
    }

    private static function router()
    {
        $router = new AltoRouter();
        include('../routes/web.php');
        return $router;
    }

    static function functions()
    {
        if(!isset($_POST['funcao']) || $_POST['funcao'] == '') {
            http_response_code(403);
            return false;
        }

        switch($_POST['funcao']) {
            case 'removeEstudo':
                $mapa = new Mapa();
                $res = $mapa->removeBE((int)$_POST['id']);
                if($res === true) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'desMA':
                $user = new User();
                $res = $user->desMA($_POST['id'], FALSE);
                if($res === true) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'desApagaMA':
                $user = new User();
                $res = $user->desMA($_POST['id'], TRUE);
                if($res === true) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'ativaMA':
                $user = new User();
                $res = $user->ativaMA($_POST['id']);
                if($res === true) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;
        }
    }

    static function maFunc()
    {
        if(!isset($_POST['funcao']) || $_POST['funcao'] == '') {
            http_response_code(403);
            return false;
        }

        @session_start();
        $ma = new MA((int)$_SESSION['id']);

        switch($_POST['funcao']) {
            case 'setHora':
                $res = $ma->setHora($_POST);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;
            
            case 'getHoras':
                $res = $ma->getHoras((int)$_POST['quantidade']);
                return $res;
                break;

            case 'getRelatorioAno':
                $res = $ma->getRelatorioAno((int)$_POST['ano']);
                return $res;
                break;


        }
    }

    static function maRelatorio($p)
    {
        if((int)$p['mes'] < 10) {
            $p['mes'] = '0'.(int)$p['mes'];
        }

        $ano = (int)$p['ano'];
        $mes = (int)$p['mes'];
        $mesesArr = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
        $data = new DateTime($p['ano'].'-'.$p['mes'].'-01');

        @session_start();
        $ma = new MA((int)$_SESSION['id']);
        $res = $ma->getHoraMes($data);

        $hora = 0;
        $horaldc = 0;
        $pub = 0;
        $videos = 0;
        $revisitas = 0;

        if($res === false) {
            $hora = '00:00';
            $horaldc = '00:00';
        } else {
            foreach($res as $r) {
                $hora += $r->hora;
                $horaldc += $r->horaldc;
                $pub += $r->publicacao;
                $videos += $r->videos;
                $revisitas += $r->revisitas;
            }

            $x = $hora%60;
            $y = ($hora - $x)/60;
            if($y < 10) {
                $hora = '0'.$y.':';
            } else {
                $hora = $y.':';
            }
            
            if($x < 10) {
                $hora .= '0'.$x;
            } else {
                $hora .= $x;
            }

            $x = $horaldc%60;
            $y = ($horaldc - $x)/60;
            if($y < 10) {
                $horaldc = '0'.$y.':';
            } else {
                $horaldc = $y.':';
            }
            
            if($x < 10) {
                $horaldc .= '0'.$x;
            } else {
                $horaldc .= $x;
            }
        }

        $html = '
                    <table id="tabRelatorio'.$ano.$mes.'" class="table table-sm" smo-mes="'.$mes.'" smo-ano="'.$ano.'">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">'.$mesesArr[$mes-1].'/'.$ano.'</th>
                            </tr>
                        </thead>
                        <tr>
                            <th>Horas</th>
                            <td>'.$hora.'</td>
                        </tr>
                        <tr>
                            <th>Horas na LDC</th>
                            <td>'.$horaldc.'</td>
                        </tr>
                        <tr>
                            <th>Publicações</th>
                            <td>'.$pub.'</td>
                        </tr>
                        <tr>
                            <th>Vídeos Mostrados</th>
                            <td>'.$videos.'</td>
                        </tr>
                        <tr>
                            <th>Revisitas</th>
                            <td>'.$revisitas.'</td>
                        </tr>
                    </table>
        ';

        return $html;
    }

    static function maRelatorioRange($p)
    {
        if((int)$p['mes1'] < 10) {
            $p['mes1'] = '0'.(int)$p['mes1'];
        }

        if((int)$p['mes2'] < 10) {
            $p['mes2'] = '0'.(int)$p['mes2'];
        }

        $ano1 = (int)$p['ano1'];
        $mes1 = (int)$p['mes1'];
        $ano2 = (int)$p['ano2'];
        $mes2 = (int)$p['mes2'];

        $mesesArr = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

        $crescente = false;
        // Define se o intervalo é do antigo pro novo ou vice-versa
        if($ano1 > $ano2) { // Ex.: 2019 > 2018. Ordem decrescente.
            $crescente = false;
        } else if($ano1 < $ano2) { // Ex.: 2018 < 2019. Ordem crescente.
            $crescente = true;
        } else { // Anos iguais. Define ordem pelos meses
            if($mes1 > $mes2) { // Ex.: Dezembro > Maio. Ordem decrescente.
                $crescente = false;
            } else { // Exc.: Maio < Dezembro. Ordem Crescente.
                $crescente = true;
            }
        }

        $lista = array();
        if($crescente === false) {
            // DECRESCENTE
            while($ano1 != $ano2 || $mes1 != $mes2) {
                // Decrementa as variáveis 1 até chegar em 2
                $data = new DateTime();
                $data->setDate($ano1, $mes1, 1);
                array_push($lista, $data->format('Y-m-d'));
                
                $mes1--;
                if($mes1 == 0) {
                    $mes1 = 12;
                    $ano1--;
                }

            }
            
        } else {
            // CRESCENTE
            while($ano1 != $ano2 || $mes1 != $mes2) {
                // Incrementa as variáveis 1 até chegar em 2
                $data = new DateTime();
                $data->setDate($ano1, $mes1, 1);
                array_push($lista, $data->format('Y-m-d'));
                
                $mes1++;
                if($mes1 == 13) {
                    $mes1 = 1;
                    $ano1++;
                }

            }
        }
        

        //return var_dump($lista);

        @session_start();
        $ma = new MA((int)$_SESSION['id']);
        $html = '';

        

        for($i = 0; $i < count($lista); $i++) {
            $data = new DateTime($lista[$i]);
            //$res = $ma->getHoraMes($data);

            $res = $ma->getHoraMes($data);
            $ano = $data->format('Y');
            $mes = $data->format('n');
            //return var_dump($lista[$i]);
            

            $hora = 0;
            $horaldc = 0;
            $pub = 0;
            $videos = 0;
            $revisitas = 0;

            if($res === false) {
                $hora = '00:00';
                $horaldc = '00:00';
            } else {
                foreach($res as $r) {
                    $hora += $r->hora;
                    $horaldc += $r->horaldc;
                    $pub += $r->publicacao;
                    $videos += $r->videos;
                    $revisitas += $r->revisitas;
                }

                $x = $hora%60;
                $y = ($hora - $x)/60;
                if($y < 10) {
                    $hora = '0'.$y.':';
                } else {
                    $hora = $y.':';
                }
                
                if($x < 10) {
                    $hora .= '0'.$x;
                } else {
                    $hora .= $x;
                }

                $x = $horaldc%60;
                $y = ($horaldc - $x)/60;
                if($y < 10) {
                    $horaldc = '0'.$y.':';
                } else {
                    $horaldc = $y.':';
                }
                
                if($x < 10) {
                    $horaldc .= '0'.$x;
                } else {
                    $horaldc .= $x;
                }
            }

            $html .= '
                        <table id="tabRelatorio'.$ano.$mes.'" class="table table-sm" smo-mes="'.$mes.'" smo-ano="'.$ano.'" smo-horas=\''.json_encode($res).'\' onclick="listaHoraMesAnterior()" style="cursor:pointer">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">'.$mesesArr[$mes-1].'/'.$ano.'</th>
                                </tr>
                            </thead>
                            <tr>
                                <th>Horas</th>
                                <td>'.$hora.'</td>
                            </tr>
                            <tr>
                                <th>Horas na LDC</th>
                                <td>'.$horaldc.'</td>
                            </tr>
                            <tr>
                                <th>Publicações</th>
                                <td>'.$pub.'</td>
                            </tr>
                            <tr>
                                <th>Vídeos Mostrados</th>
                                <td>'.$videos.'</td>
                            </tr>
                            <tr>
                                <th>Revisitas</th>
                                <td>'.$revisitas.'</td>
                            </tr>
                        </table>
            ';
            
            
        }
        

        return $html;
    }

    static function pendencias()
    {
        // Dá um retorno das pendências
        /**
         * 'titulo' => Titulo a pendência
         * 'texto' => Conteúdo explicativo da pendência.
         * 'tipo' => Grau da pendência... Pode ser 'info', 'warning', 'danger', 'dark', igual as classes do ALERT.
         * 'link' => Endereço da pendência.
         */
        $x = array();

        $mod = new Model();
        $abc = $mod->contaPendencias();

        return json_encode($abc);
    }

    static function login()
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

    static function loga($params)
    {
        // Chama autenticação de usuário
        $auth = new Auth();
        $retorno = $auth->authenticate($_POST['usuario'], $_POST['senha']);
        
        // Escreve cookie
        if($retorno === true) {
            if(isset($_POST['save_user']) && $_POST['save_user'] == 'yes') {
                $x = setcookie('user', $_SESSION['user'], time()+(60*60*24*30), '/','',TRUE);
            } else {
                $x = setcookie('user', '', time()-3600, '/','',TRUE);
            }
            // Seta cookie de ID e nível.
            setcookie('smoCod', $_SESSION['id'], time()+(60*60*24*30), '/','',TRUE);
            setcookie('smoAut', $_SESSION['nivel'], time()+(60*60*24*30), '/','',TRUE);
    
            
            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_SISTEMA, 'ficou online.');
    
            
            // Verifica se há um URL para redirecionar
            if($_SESSION['url'] != '') {
                $url = $_SESSION['url'];
                unset($_SESSION['url']);
                header('Location: '.$url);
            } else {
                header('Location: /');
            }
        } else {
            header('Location: /login');
        }
        
        
    }

    static function logout()
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

    static function esqueciSenha()
    {
        // Verifica se já está autenticado
        $auth = new Auth();
        if($auth->getIsLogged()) {
            header('Location: /');
            return false;
        }

        // Exibe tela de Esqueci a senha
        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("esqueciSenha", array(
            'smoMSG' => SessionMessage::lerFormatado()
        ));
    }

    static function esqueciSenhaPOST()
    {
        // Verifica se já está autenticado
        $auth = new Auth();
        if($auth->getIsLogged()) {
            header('Location: /');
            return false;
        }

        if(strrpos($_POST['usuario'], '@') !== false) {
            // E-mail
            print('E-mail');
            $user = new User();
            $u = $user->buscaEmail($_POST['usuario']);
            if($u === false) {
                SessionMessage::novo(array('titulo' => '', 'texto' => 'E-mail não localizado. Contate um dos administradores.', 'tipo' => 'danger'));
                header('Location: /forgot');
                exit();
            } else if( $u == 'erro') {
                SessionMessage::novo(array('titulo' => '', 'texto' => 'Ocorreu um erro interno. Contate um dos administradores.', 'tipo' => 'danger'));
                header('Location: /forgot');
                exit();
            } else {
                $user->esqueciSenha((int) $u);
            }
        } else {
            // Nome de usuário
            print('Nome de usuário');
            $user = new User();
            $u = $user->buscaUsuario($_POST['usuario']);
            if($u === false) {
                SessionMessage::novo(array('titulo' => '', 'texto' => 'Nome de usuário não localizado. Contate um dos administradores.', 'tipo' => 'danger'));
                header('Location: /forgot');
                exit();
            } else if( $u == 'erro') {
                SessionMessage::novo(array('titulo' => '', 'texto' => 'Ocorreu um erro interno. Contate um dos administradores.', 'tipo' => 'danger'));
                header('Location: /forgot');
                exit();
            } else {
                $user->esqueciSenha((int) $u);
            }
        }
        //var_dump($_POST);
    }

    static function redefineSenha()
    {
        // Verifica se já está autenticado
        $auth = new Auth();
        if($auth->getIsLogged()) {
            header('Location: /');
            return false;
        }


        // Verifica se os dados enviados estão íntegros (sem distorção)
        $usuario = $_GET['user'];
        $token = $_GET['token'];
        $verify = md5($usuario.$token);
        if($verify !== $_GET['verify']) {
            SessionMessage::novo(array('titulo' => 'Inválido!', 'texto' => 'Sinto muito! Esse link está com a integridade corrompida.', 'tipo' => 'danger'));
            header('Location: /login');
            exit();
        } else {
            // verifica se o token é válido
            $user = new User();
            $uid = $user->buscaUsuario($usuario);
            if($uid == 'erro' || $uid === false) {
                SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Há algo de errado com os dados do usuário. Contate administrador.', 'tipo' => 'danger'));
                header('Location: /login');
                exit();
            } else {
                $u = $user->getInfo($uid);
                if($u->token === $token) {
                    // Exibe tela de Esqueci a senha
                    $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
                    return $blade->run("redefineSenha", array(
                        'smoMSG' => SessionMessage::lerFormatado(),
                        'u' => $u,
                        'usuario' => $u->user,
                        'token' => $token,
                        'uid' => $uid,
                    ));
                } else {
                    SessionMessage::novo(array('titulo' => 'Token inválido!', 'texto' => 'Esse token de redefinição é inválido. Contate administrador.', 'tipo' => 'danger'));
                    header('Location: /login');
                    exit();
                }
            }

        }
    }

    static function redefineSenhaPOST()
    {
        $user = new User();
        $u = $user->getInfo((int)$_POST['uid']);

        if($u->token === $_POST['token']) {
            $res = $user->redefineSenha($u->id, $_POST['senha'] );
            if($res === true) {
                SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Senha redefinida com sucesso. A nova senha já pode ser usada.', 'tipo' => 'success'));
                return 'OK';
            } else {
                return $res;
            }
        } else {
            return 'TOKEN inválido! Esse link de redefinição não é mais válido.';
        }
        
    }

    static function index()
    {
        // Checa se está autenticado
        PageController::authorized();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("homepage",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }

    static function consulta()
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

    static function surdo(array $p)
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

    static function consultaPesquisa()
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

    static function consultaId()
    {
        // Checa se está autenticado
        PageController::authorized();

        // Extrai conteudo da URL
        $id = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'], 'id/')+3);

        $mapa = new Mapa();
        return $mapa->surdoId((int)$id);
        
    }

    static function registros()
    {
        // Checa se está autenticado
        PageController::authorized();

        
        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("registros",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y')
        ));
    }

    static function registrosNovo(array $params)
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

        if($_SESSION['modo_facil'] === TRUE) {
            $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
            return $blade->run("registrosNovoMF",array(
                'smoMSG' => SessionMessage::ler(),
                'router' => PageController::router(),
                'uNome'=> $_SESSION['nome'],
                'anoCorrente' => date('Y'),
                'publicadores' => $publicadores,
                'surdos' => $surdos,
                'surdoId' => $surdoId,
                'surdoUnico' => $surdoUnico,
                'mfIcon' => 'badge-primary'
            ));
        } else {
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
    }

    static function registrosBuscar(array $params)
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

    static function registrosConsulta(array $params)
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

    static function registroEdita(array $params)
    {
        // Checa se está autenticado
        PageController::authorized();

        $registro = new Registros();
        $reg = json_decode($registro->busca($params));
        $reg = $reg[0];

        // Verifica se há parametros
        $surdoId = (int)$reg->mapa_id;
        $surdoUnico = false;

        $user = new User();
        $listaUsuarios = $user->listaUsuarios(TRUE);

        $publicadores = '';
        foreach($listaUsuarios as $b) {
            if($b->id == $reg->pub_id) {
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
        return $blade->run("registrosEditar",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'publicadores' => $publicadores,
            'surdos' => $surdos,
            'surdoId' => $surdoId,
            'surdoUnico' => $surdoUnico,
            'registro' => $reg,
        ));
    }

    static function registroSalva()
    {

        $reg = new Registros();
        $res = $reg->salva($_POST);
        if($res === TRUE) {
            header('Location: /registros');
            
        } else {
            header('/registros/editar/'.$_POST['regid']);

        }
        return true;
    }

    static function registrosUltimos()
    {
        $registros = new Registros();
        echo $registros->ultimos();
    }

    static function registroDeleta(array $params)
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

    static function cadastro()
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

    static function cadastroEditar(array $obj)
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

    static function tpessoal()
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

    static function social()
    {
        // Checa se está autenticado
        PageController::authorized();
        $config = new Config();
        $mapa = new Mapa();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("social",array(
            'smoMSG' => SessionMessage::ler(),
            'containertipo' => 'container-fluid',
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'social' => $config->get('social'),
            'surdos' => $mapa->listaSocial(),
        ));
    }

    static function campanha()
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

    static function perfil()
    {
        // Checa se está autenticado
        PageController::authorized();
        $user = new User();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("perfil",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'user' => $user->getInfo($_SESSION['id']),
        ));
    }

    static function perfilSalvaDados(array $obj)
    {
        $user = new User($obj['id']);
        $u = $user->info;
        //var_dump($user);

        if($u->nome !== $obj['nome']) {
            $res = $user->setNome((int)$obj['id'], $obj['nome']);
            if($res !== true) {
                return $res;
            }
        }

        if($u->sobrenome !== $obj['sobrenome']) {
            $res = $user->setSobrenome((int)$obj['id'], $obj['sobrenome']);
            if($res !== true) {
                return $res;
            }
        }

        if($u->email !== $obj['email']) {
            $res = $user->setEmail((int)$obj['id'], $obj['email']);
            if($res !== true) {
                return $res;
            }
        }
        return true;

    }

    static function perfilTrocaSenha(array $obj)
    {
        $user = new User();
        return $user->setSenha($obj['id'], $obj['senha_atual'], $obj['senha_nova'], $obj['senha_confirma']);
    }

    static function ma()
    {
        // Checa se está autenticado
        PageController::authorized();

        // Verifica se está autorizado a visualizar esta página
        if($_SESSION['ma'] === FALSE) {
            http_response_code(404);
            exit();
        }

        $user = new User();


        $blade = new BladeOne(PageController::VIEWS,PageController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("ma",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => PageController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'user' => $user->getInfo($_SESSION['id']),
        ));
    }

    static function maExportXLS()
    {
        if($_SESSION['ma'] === FALSE) {
            http_response_code(404);
            exit();
        }
        $ma = new MA($_SESSION['id']);
        $horas = json_decode($ma->getHorasAll());
        //$horas = json_decode('{0}');

        if($horas == null) {
            return 'Não há nada no seu relatório para exportar.';
        }

        //var_dump($horas);
        $html = '<table border="1">
        <tr style="height:24px; vertical-aling:middle; font-weight:bold;"><td>Data</td> <td>Horas</td> <td>Horas LDC</td> <td>Publicações</td> <td>Vídeos</td> <td>Revisitas</td> <td>Comentário</td></tr>';
        $mesAtual = '';

        foreach($horas as $h) {
            $data = new DateTime($h->data);

            if($mesAtual == '') {
                $html .= '<tr><td colspan="7" style="text-align:center;padding: 10px 0;font-weight:bold;">Mês '.$data->format('m/Y').'</td></tr>';
                $mesAtual = $data->format('m');
            } else if($mesAtual != $data->format('m')) {
                $html .= '<tr><td colspan="7" style="text-align:center; padding:10px 0; font-weight:bold;">Mês '.$data->format('m/Y').'</td></tr>';
                $mesAtual = $data->format('m');
            }

            //var_dump($h);
            $horaldc = $h->horaldc;
            $publicacao = $h->publicacao;
            $videos = $h->videos;
            $revisitas = $h->revisitas;
            $comentario = $h->comentario;

            // Minutos para horas: HORAS
            $minutos = (int)$h->hora;
            $m = $minutos%60;
            $h = ($minutos - $m)/60;
            
            if($h < 10) {
                $retorno = '0'.$h.':';
            } else {
                $retorno = $h.':';
            }

            if($m < 10) {
                $retorno .= '0'.$m;
            } else {
                $retorno .= $m;
            }
            $hor = $retorno;

            
            // Minutos para horas: HORAS LDC
            $minutos = (int)$horaldc;
            $m = $minutos%60;
            $h = ($minutos - $m)/60;
            
            if($h < 10) {
                $retorno = '0'.$h.':';
            } else {
                $retorno = $h.':';
            }

            if($m < 10) {
                $retorno .= '0'.$m;
            } else {
                $retorno .= $m;
            }
            $horaldc = 0;

            $html .= '
            <tr> <td>'.$data->format('d/m/Y').'</td> <td>'.$hor.'</td> <td>'.$horaldc.'</td> <td>'.$publicacao.'</td> <td>'.$videos.'</td> <td>'.$revisitas.'</td> <td>'.$comentario.'</td> </tr>';
        }

        $html .='</table>';
        header ('Cache-Control: no-cache, must-revalidate');
        header ('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header ("Content-Disposition: attachment; filename=\"MeuRelatorio.xls\"");
        echo $html;
    }

    static function teste()
    {
        return 'Uma página de teste';
    }
}