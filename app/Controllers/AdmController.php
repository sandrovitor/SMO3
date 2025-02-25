<?php
// Autoload do Composer. Busca autoload
$composerAutoloadFile = 'vendor/autoload.php';
if(file_exists($composerAutoloadFile)) {
    require_once('/vendor/autoload.php');
} else if(file_exists('../'.$composerAutoloadFile)) {
    require_once('../'.$composerAutoloadFile);
} else if(file_exists('../../'.$composerAutoloadFile)) {
    require_once('../../'.$composerAutoloadFile);
}
Use eftec\bladeone\BladeOne;
use Spipu\Html2Pdf\Html2Pdf;
//$views = '../public/resources/views';
//$cache = '../cache';

// Libera restante da página, somente se estiver autenticado
include_once('Auth.php');
include_once('../app/SessionMessage.php');

class AdmController
{
    
    const VIEWS = '../public/resources/views';
    const CACHE = '../cache';

    static function authorized(int $nivel)
    {
        // Verifica se está autenticado
        $auth = new Auth();
        if($auth->guard($nivel) == true) {
            return true;
        }

        return false;
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
            case 'salvaEditarMapas':
                $mapa = new Mapa();
                return $mapa->salvaEditarMapas($_POST['dados']);
                break;

            case 'getLOG':
                $log = new LOG();
                return $log->getHTML($_POST['tipo'], $_POST['usuario'], $_POST['inicio'], $_POST['qtd']);
                break;

            case 'setConfigEstilo':
                $config = new Config();
                $config->set('print_estilo', $_POST['valor']);
                return true;
                break;

            case 'setRegiao':
                $config = new Config();
                $config->set('regiao_'.(int)$_POST['regiao_id'], $_POST['regiao_nome']);
                return json_encode($config->get('regiao'));
                break;

            case 'setBairroNovo':
                $bairro = new Bairro();
                $res = $bairro->novo($_POST['bairro_nome'], $_POST['regiao_id']);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Bairro criado com sucesso.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha! '.$res;
                }
                break;

            case 'setBairroNome':
                $bairro = new Bairro();
                $res = $bairro->edita($_POST['bairro_id'], $_POST['bairro_nome'], $_POST['regiao_id']);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Bairro alterado com sucesso.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha! '.$res;
                }
                break;

            case 'setBairroDelete':
                $bairro = new Bairro();
                $res = $bairro->remove($_POST['bairro_id']);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Bairro foi removido.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha! '.$res;
                }
                break;

            case 'setConfigGeral':
                $config = new Config();
                $res = $config->setConfigGeral($_POST['ult_visita'], $_POST['prox_visita'], $_POST['versao'], $_POST['versao_data']);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Configurações salvas.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha! '.$res;
                }
                break;

            case 'setSocial':
                $config = new Config();
                $sAtiva = $_POST['social_ativa'];
                $sData = $_POST['social_data'];
                $sDuracao = $_POST['social_duracao'];
                if($sAtiva !== 'yes') {
                    $sAtiva = 'not';
                }
                $sData = str_replace('T', ' ', $sData);

                $res = $config->set('social_ativa', $sAtiva);
                if($res !== true) { return 'Houve uma falha ao ativar o evento! '; }
                $res = $config->set('social_data', $sData);
                if($res !== true) { return 'Houve uma falha ao configurar data e hora do evento! '; }
                $res = $config->set('social_duracao', $sDuracao);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Configurações do evento <i>Redes Sociais</i> salvas.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha ao definir a duração do evento! ';
                }
                break;

            case 'setCampanha':
                $config = new Config();
                $cAtiva = $_POST['ativa'];
                $cNome = $_POST['nome'];
                $cInicio = $_POST['inicio'];
                $cFim = $_POST['fim'];
                if($cAtiva !== 'yes') {
                    $cAtiva = 'not';
                }

                $d = new DateTime($cInicio);
                $cNome .= ' '.$d->format('Y');

                $res = $config->set('campanha_ativa', $cAtiva);
                if($res !== true) { return 'Houve uma falha ao ativar a campanha!'; }
                $res = $config->set('campanha_inicio', $cInicio);
                if($res !== true) { return 'Houve uma falha ao configurar data de inicio do evento! '; }
                $res = $config->set('campanha_fim', $cFim);
                if($res !== true) { return 'Houve uma falha ao configurar data final do evento!'; }
                $res = $config->set('campanha_nome', $cNome);
                if($res === true) {
                    SessionMessage::novo(array('titulo' => 'Sucesso', 'texto' => 'Configurações da campanha <i>'.$cNome.'</i> foram salvas.', 'tipo' => 'success'));
                    return 'OK';
                } else {
                    return 'Houve uma falha ao definir a duração do evento! ';
                }
                break;

            case 'setBackupManual':
                $bd = new BD();
                $bd->backup();
                return $bd->getUltimoBackup();
                break;

            case 'setBackupDelete':
                $bd = new BD();
                return $bd->deleteFile($_POST['nome']);
                break;

            case 'setRestauraBackup':
                $bd = new BD();
                return $bd->restaura($_POST['nome']);
                break;

            case 'getHistoricoLista':
                $mapa = new Mapa();
                return $mapa->historicoLista($_POST['id']);
                break;

            case 'deleteHistorico':
                $mapa = new Mapa();
                return $mapa->historicoApaga($_POST['id']);
                break;
                
            case 'setHistoricoRecupera':
                $mapa = new Mapa();
                return $mapa->historicoRecupera($_POST['id']);
                break;

            case 'setPubBloquear':
                $user = new User();
                $res = $user->bloquear($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setPubDesbloquear':
                $user = new User();
                $res = $user->desbloquear($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setBETAAtiva':
                $user = new User();
                $res = $user->ativaBETA($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setBETADesativa':
                $user = new User();
                $res = $user->desativaBETA($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setPubSenhaReset':
                $user = new User();
                $res = $user->resetaSenha($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setModoFacilAtiva':
                $user = new User();
                $res = $user->ativaMFacil($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

                
            case 'setModoFacilDesativa':
                $user = new User();
                $res = $user->desativaMFacil($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setNotificaSenha':
                $u = new User();
                $res = $u->notificaSenha(TRUE, $_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;
    
            case 'setDesnotificaSenha':
                $u = new User();
                $res = $u->notificaSenha(FALSE, $_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;
                break;

            case 'deleteUsuario':
                $user = new User();
                $res = $user->delete($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return 'Falha desconhecida';
                }
                break;

            case 'setTP':
                $mapa = new Mapa();
                $res = $mapa->setTP(explode(',', $_POST['atualiza']['sub']), explode(',', $_POST['atualiza']['add']), (int)$_POST['pubid']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'setSurdoAtivar':
                $mapa = new Mapa();
                $res = $mapa->ativar($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'setSurdoDesativar':
                $mapa = new Mapa();
                $res = $mapa->desativar($_POST['id'], $_POST['motivo']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'setSurdoOcultar':
                $mapa = new Mapa();
                $res = $mapa->ocultar($_POST['id'], $_POST['motivo']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'setSurdoDesocultar':
                $mapa = new Mapa();
                $res = $mapa->desocultar($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'setSurdoExcluir':
                $mapa = new Mapa();
                $res = $mapa->excluir($_POST['id']);
                if($res === TRUE) {
                    return 'OK';
                } else {
                    return $res;
                }
                break;

            case 'checaUsername':
                $u = new User();
                if($u->checaUsernameLivre($_POST['user']) === true) {
                    return 'OK';
                } else {
                    return 'Nome de usuário já está em uso.';
                }
                break;

            case 'setBE':
                $mapa = new Mapa();
                if($_POST['be'] == '0') {
                    // remove estudo
                    $res = $mapa->removeBE((int)$_POST['surdo']);
                    if($res === TRUE) {
                        return 'OK';
                    } else {
                        return 'Ocorreu um erro aqui. Atualize a página e tente de novo.';
                    }
                } else if($_POST['be'] == '1') {
                    // Adiciona ou altera estudo
                    $res = $mapa->setBE((int)$_POST['surdo'], (int)$_POST['resp'], FALSE);
                    if($res === TRUE) {
                        return 'OK';
                    } else {
                        return 'Ocorreu um erro aqui. Atualize a página e tente de novo.';
                    }
                } else {
                    return 'Parâmetos inválidos. Estudo não pode ser definido.';
                }
                
                break;
        }
        
    }

    static function index()
    {
        AdmController::authorized(4);

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.index",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }
    
    static function impressao()
    {
        AdmController::authorized(4);
        $config = new Config();
        $m = new Mapa();
        $mapas = '';
        $html = '';

        //var_dump($_POST);

        // Verifica MODO de operação
        switch($_POST['fModo']) {
            case 'PRINTMAPAS':
                // Tipo de impressão
                if($_POST['fTipo'] == 'todos') {
                    $mapas = $m->getMapasForPrint('todos');
                } else if($_POST['fTipo'] == 'bairro') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else if($_POST['fTipo'] == 'intervalo') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1'].'-'.$_POST['fCampo2']);
                } else if($_POST['fTipo'] == 'individual') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else {
                    return 'Acesso negado';
                }
                $html = self::impressaoMapasGeraHTML($mapas);
                break;

            case 'PRINTREG':
                if($_POST['fTipo'] == 'todos') {
                    $mapas = $m->getMapasForPrint('todos');
                } else if($_POST['fTipo'] == 'bairro') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else if($_POST['fTipo'] == 'intervalo') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1'].'-'.$_POST['fCampo2']);
                } else if($_POST['fTipo'] == 'individual') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else {
                    return 'Acesso negado';
                }
                $html = self::impressaoRegGeraHTML($mapas);
                break;
            
            case 'PRINTQR':

                break;

            case 'PDFMAPAS':
                // Tipo de impressão
                if($_POST['fTipo'] == 'todos') {
                    $mapas = $m->getMapasForPrint('todos');
                } else if($_POST['fTipo'] == 'bairro') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else if($_POST['fTipo'] == 'intervalo') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1'].'-'.$_POST['fCampo2']);
                } else if($_POST['fTipo'] == 'individual') {
                    $mapas = $m->getMapasForPrint($_POST['fCampo1']);
                } else {
                    return 'Acesso negado';
                }
                $html = self::impressaoMapasGeraHTML($mapas);
                break;

            case 'PDFREG':

                break;

            case 'PDFQR':

                break;

            default:
                return 'Acesso Negado!';
                break;
        }

        

        // Saída diferenciada
        switch($_POST['fModo']) {
            case 'PRINTMAPAS':
            case 'PRINTREG':
            case 'PRINTQR':
                $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
                return $blade->run("admin.impressao",array(
                    'smoMSG' => SessionMessage::ler(),
                    'router' => AdmController::router(),
                    'uNome'=> $_SESSION['nome'],
                    'anoCorrente' => date('Y'),
                    'html' => $html,
                    'estilo' => $config->get('printestilo'),
                ));
                break;

            case 'PDFMAPAS':
            case 'PDFREG':
            case 'PDFQR':
                $fCSS = array(
                    //'css/glyphicon.css',
                    'css/impressao_temafixoPDF.css',
                    'css/'.$config->get('printestilo'),
                );
                $css = '';
                foreach($fCSS as $x) {
                    $handle = fopen($x, 'r');
                    $c = fread($handle, filesize($x));
                    $css .= $c;
                }

                //echo $css;

                $teste = '
                
                <page>
                <br>
                <table class="table-head" style="border: 1px solid #000000; width: 100%;">
                        <tr>
                            <td class="col1">Jaguaripe</td>
                            <td class="col2 text-center">Mapa: CAJ043</td>
                            <td class="col3 text-center">Set/19</td>
                        </tr>
                    
                </table>

                <table class="table-body">
            
            <tr class="lin1">
                        <td class="col1 nome_surdo">Andreia</td>
                        <td class="col2 faixa_et">CRIANÇA</td>
                        <td class="col3"><table class="dia_melhor">
                                <tr>
                                    <td class="">DOM</td>
                                    <td class="">SEG</td>
                                    <td class="">TER</td>
                                    <td class="">QUA</td>
                                    <td class="">QUI</td>
                                    <td class="">SEX</td>
                                    <td class="">SAB</td>
                                </tr>
                            </table>
                        </td>
                        <td class="col4 text-center tar">TARDE</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">CONJUNTO JAGUARIPE I, RUA MACUMBA, CAMINHO 6,<br> CASA 02, Jaguaripe</td>
                        <td colspan="2"> -</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">TEM DUAS IRMÃS GEMEAS. </td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">FIM DA RUA DA MACUMBA, VIRA A DIREITA.<br>k</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">PAI: ANTONIO</td>
                    </tr>
            
            <tr class="lin1">
                        <td class="col1 nome_surdo">Daniel</td>
                        <td class="col2 faixa_et">JOVEM</td>
                        <td class="col3"><table class="dia_melhor">
                                <tbody>
                                    <tr>
                                        <td class="">DOM</td>
                                        <td class="">SEG</td>
                                        <td class="">TER</td>
                                        <td class="">QUA</td>
                                        <td class="">QUI</td>
                                        <td class="">SEX</td>
                                        <td class="">SAB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="col4 text-center def">-</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">CONJUNTO JAGUARIPE, RUA A, CAM. 11,<br> Nº 04, Jaguaripe</td>
                        <td colspan="2"> -</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">- </td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">EM FRENTE AO BAR DO MINEIRO</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">MÃE: MARIA APARECIDA</td>
                    </tr>
            
            <tr class="lin1">
                        <td class="col1 nome_surdo">Arthur</td>
                        <td class="col2 faixa_et">ADULTO</td>
                        <td class="col3"><table class="dia_melhor">
                                <tbody>
                                    <tr>
                                        <td class="">DOM</td>
                                        <td class="">SEG</td>
                                        <td class="">TER</td>
                                        <td class="">QUA</td>
                                        <td class="">QUI</td>
                                        <td class="">SEX</td>
                                        <td class="">SAB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="col4 text-center def">-</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">CONJUNTO JAGUARIPE, SETOR D, <br>QUADRA 20, CASA 2, Jaguaripe</td>
                        <td colspan="2"> -</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">- </td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">-</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">IRMÃ: ANA</td>
                    </tr>
            
            <tr class="lin1">
                        <td class="col1 nome_surdo">Gildásio</td>
                        <td class="col2 faixa_et">ADULTO</td>
                        <td class="col3"><table class="dia_melhor">
                                <tbody>
                                    <tr>
                                        <td class="ok">DOM</td>
                                        <td class="">SEG</td>
                                        <td class="">TER</td>
                                        <td class="">QUA</td>
                                        <td class="">QUI</td>
                                        <td class="">SEX</td>
                                        <td class="">SAB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="col4 text-center def">-</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">CONJUNTO JAGUARIPE, SETOR D, QUADRA 17,<br> CASA 12, Jaguaripe</td>
                        <td colspan="2"> 3238-2291</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">TRABALHA MERCADO EXTRA, LUGAR ROTULA ABACAXI. </td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">PRÓXIMO DO COLÉGIO BRILHO SOLAR</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">MÃE: EDNA| IRMÃ: MICHELE</td>
                    </tr>
                    </table>
                </page>';
                $h2p = new Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(10,7,10,7));
                $h2p->setDefaultFont('Arial');
                $h2p->writeHTML('<style>'.$css.'</style>'.$teste);
                //$h2p->writeHTML($teste);
                $h2p->output('SMO_IMPRESSAO.pdf', 'I');
                
                break;

        }

        
    }

    protected static function impressaoMapasGeraHTML($mapas) {
        
        //var_dump($mapas);
        // Variáveis de controle
        $html = '';
        $cMapa = '';
        $cBairro = '';
        $cPage = 0;
        $x = new DateTime();
        switch($x->format('n')) {
            case 1: $mes = 'Jan'; break;
            case 2: $mes = 'Fev'; break;
            case 3: $mes = 'Mar'; break;
            case 4: $mes = 'Abr'; break;
            case 5: $mes = 'Mai'; break;
            case 6: $mes = 'Jun'; break;
            case 7: $mes = 'Jul'; break;
            case 8: $mes = 'Ago'; break;
            case 9: $mes = 'Set'; break;
            case 10: $mes = 'Out'; break;
            case 11: $mes = 'Nov'; break;
            case 12: $mes = 'Dez'; break;
        }
        $mes .='/'.$x->format('y');
        unset($x);
        $cLinhas = 0;

        $tab = '<table class="table-body">
			<tbody>';

        foreach($mapas as $m) {
            if($cMapa == '') {
                $html .= '
                <div class="page">
                    <table class="table-head">
                        <tbody>
                            <tr>
                                <td class="col1">'.$m->bairro.'</td>
                                <td class="col2 text-center">Mapa: '.$m->mapa.'</td>
                                <td class="col3 text-center">'.$mes.'</td>
                            </tr>
                        </tbody>
                    </table>';
                
                $cMapa = $m->mapa;
                $cPage++;

            } else if($cMapa != $m->mapa) {
                // Fecha tabela "$tab", escreve e abre nova tabela "$tab";
                while($cLinhas < 4) {
                    $tab .='
            
                    <tr class="lin1">
                        <td class="col1 nome_surdo">-</td>
                        <td class="col2 faixa_et">-</td>
                        <td class="col3">DIA MELHOR: -
                        </td>
                        <td class="col4 text-center def">-</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">-</td>
                        <td colspan="2">-</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">-</td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">-</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">-</td>
                    </tr>';

                    $cLinhas++;
                }

                $html .= $tab . '</tbody></table>';
                
                $cLinhas = 0;
                $tab = '<table class="table-body">
                <tbody>';
                


                if($cPage == 3) {
                    $html .= '
                    </div>
                    <div class="page">';
                    
                    $cPage = 0;
                    
                }

                    $html .= '<table class="table-head">
                        <tbody>
                            <tr>
                                <td class="col1">'.$m->bairro.'</td>
                                <td class="col2 text-center">Mapa: '.$m->mapa.'</td>
                                <td class="col3 text-center">'.$mes.'</td>
                            </tr>
                        </tbody>
                    </table>';
                
                    $cMapa = $m->mapa;
                    $cPage++;
            }

            
            // Processa dados
            if($m->endereco == '') {$endereco = '-';} else {$endereco = $m->endereco;}
            if($m->p_ref == '') {$p_ref = '-';} else {$p_ref = $m->p_ref;}
            if($m->familia == '') {$familia = '-';} else {$familia = $m->familia;}
            if($m->tel == '') {$tel = '-';} else {$tel = $m->tel;}
            if($m->whats == '') {$whats = '';} else {$whats = '<i class="fab fa-whatsapp" style="color: #077600;"></i> <span style="color: #077600; font-weight: bold;">'.$m->whats.'</span>; ';}
            if($m->obs == '') { if($m->be == 1) {$obs = '';} else {$obs = '-';} } else {$obs = $m->obs;}
            if($m->idade == '') {$idade = '-';} else {$idade = $m->idade;}
            if($m->turno == '') {$turno = '-'; $turno_cor = 'def';} else {$turno = $m->turno;
            switch($turno) {
                case 'MANHÃ':
                    $turno_cor = 'man';
                    break;
                    
                case 'TARDE':
                    $turno_cor = 'tar';
                    break;
                    
                case 'NOITE':
                    $turno_cor = 'noi';
                    break;
                    
                default:
                    $turno_cor = 'def';
                    break;
            }
            }
            if($m->hora_melhor == '') {$hora_melhor = '-';} else {$hora_melhor = $m->hora_melhor;}
            //if($m->be == 1) { $be = '<strong>BÍBLIA ESTUDA JÁ. RESP.: '. $m->publicador.'.</strong>'; } else { $be = '';}
            if($m->be == 1) { $be = '<span class="badge-be">BÍBLIA ESTUDA: &nbsp;'. $m->publicador.'.</span>'; } else { $be = '';}
            $dia_melhor_arr = explode(';', $m->dia_melhor);
            $dom = ''; $seg = ''; $ter = ''; $qua = ''; $qui = ''; $sex = ''; $sab = '';
            foreach($dia_melhor_arr as $key) {
                switch($key) {
                    case 1: $dom = 'ok'; break;
                    case 2: $seg = 'ok'; break;
                    case 3: $ter = 'ok'; break;
                    case 4: $qua = 'ok'; break;
                    case 5: $qui = 'ok'; break;
                    case 6: $sex = 'ok'; break;
                    case 7: $sab = 'ok'; break;
                }
            }
            if($m->turno == '') {$turno = '-'; $turno_cor = 'def';} else {$turno = $m->turno;
            switch($turno) {
                case 'MANHÃ':
                    $turno_cor = 'man';
                    break;
                    
                case 'TARDE':
                    $turno_cor = 'tar';
                    break;
                    
                case 'NOITE':
                    $turno_cor = 'noi';
                    break;
                    
                default:
                    $turno_cor = 'def';
                    break;
            }
            }
            

            $tab .= '
            
            <tr class="lin1">
                        <td class="col1 nome_surdo">'.$m->nome.'</td>
                        <td class="col2 faixa_et">'.$idade.'</td>
                        <td class="col3">DIA MELHOR: 
                            <table class="dia_melhor">
                                <tbody>
                                    <tr>
                                        <td class="'.$dom.'">DOM</td>
                                        <td class="'.$seg.'">SEG</td>
                                        <td class="'.$ter.'">TER</td>
                                        <td class="'.$qua.'">QUA</td>
                                        <td class="'.$qui.'">QUI</td>
                                        <td class="'.$sex.'">SEX</td>
                                        <td class="'.$sab.'">SAB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="col4 text-center '.$turno_cor.'">'.$turno.'</td>
                    </tr>
                    <tr class="lin2">
                        <td rowspan="2" colspan="2">'.$endereco.', '.$m->bairro.'</td>
                        <td colspan="2">'.$whats.' '.$tel.'</td>
                    </tr>
                    <tr class="lin3">
                        <td rowspan="2" colspan="2">'.$obs.' '.$be.'</td>
                    </tr>
                    <tr class="lin4">
                        <td rowspan="2" colspan="2">'.$p_ref.'</td>
                    </tr>
                    <tr class="lin5">
                        <td colspan="2">'.$familia.'</td>
                    </tr>';

                $cLinhas++;
            
        }

        
        // Fecha tabela "$tab", escreve e abre nova tabela "$tab";
        while($cLinhas < 4) {
            $tab .= '

            <tr class="lin1">
                <td class="col1 nome_surdo">-</td>
                <td class="col2 faixa_et">-</td>
                <td class="col3">DIA MELHOR: -
                </td>
                <td class="col4 text-center def">-</td>
            </tr>
            <tr class="lin2">
                <td rowspan="2" colspan="2">-</td>
                <td colspan="2">-</td>
            </tr>
            <tr class="lin3">
                <td rowspan="2" colspan="2">-</td>
            </tr>
            <tr class="lin4">
                <td rowspan="2" colspan="2">-</td>
            </tr>
            <tr class="lin5">
                <td colspan="2">-</td>
            </tr>';

            $cLinhas++;
        }

        $html .= $tab . '</tbody></table></div>';

        // Saída HTML
        return $html;
    }

    protected static function impressaoRegGeraHTML($mapas) {

        // Variáveis de controle
        $html = '';
        $cMapa = '';
        $cBairro = '';
        $cPage = 0;
        $cBloco = 0;

        foreach($mapas as $m) {
            if($cMapa != $m->mapa && $cMapa != '') {
                // Completa quantidade de bloco escritos
                while($cBloco < 4) {
                    if($cBloco == 0) {
                        // Primeira célula da linha
                        $html .= '<tr>';
                        
                    } else if($cBloco == 2) {
                        // Primeira célula da última linha
                        $html .= '</tr><tr>';
                    }

                    $html .='<td>
                        <div class="bloco-top">
                            <div class="linha" style="text-align: center; padding: 2px; min-height:2mm;">REGISTRO DE CASA EM CASA</div>
                            <div class="linha nome_surdo" style="text-align: center; font-size: 12px; padding: 1px; text-transform: uppercase;"> </div>
                            <div class="linha linha_amar" style="font-weight: bold">
                                <div class="col1"> </div>
                                <div class="col2"><span style="text-transform:none;">Mapa:</span> </div>
                            </div>
                        </div>
                        <div class="bloco-main">
                            <div class="linha">
                                <div class="col1">Publicador(a):</div>
                                <div class="col2">Data:</div>
                            </div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                        </div>
                        <div class="bloco-main">
                            <div class="linha">
                                <div class="col1">Publicador(a):</div>
                                <div class="col2">Data:</div>
                            </div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                        </div>
                        <div class="bloco-main">
                            <div class="linha">
                                <div class="col1">Publicador(a):</div>
                                <div class="col2">Data:</div>
                            </div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                        </div>
                        <div class="bloco-main">
                            <div class="linha">
                                <div class="col1">Publicador(a):</div>
                                <div class="col2">Data:</div>
                            </div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                            <div class="linha"></div>
                        </div>
                    </td>
                    <!-- COLUNA VAZIA -->';

                    $cBloco++;
                }

                // Fecha linha e tabela.
                $html .= '</tr></tbody></table></div>';
                $cBloco = 0;
            }


            // Inicia escrita da página
            $cMapa = $m->mapa;
            if($cBloco == 0) {
                $html .= '<div class="page">
                <table class="registro">
                    <tbody>';

            } else if($cBloco == 4) {
                $html .= '</tbody></table>
                </div>
                <div class="page">
                <table class="registro">
                    <tbody>';
                    $cBloco = 0;
            }

            if($cBloco == 0) {
                // Primeira célula da linha
                $html .= '<tr>';
                
            } else if($cBloco == 2) {
                // Primeira célula da última linha
                $html .= '</tr><tr>';
            } else {
                // Última célula da última linha
            }

            $html .='<td>
            <div class="bloco-top">
                <div class="linha" style="text-align: center; padding: 2px; min-height:2mm;">REGISTRO DE CASA EM CASA</div>
                <div class="linha nome_surdo" style="text-align: center; font-size: 12px; padding: 1px; text-transform: uppercase;">'.$m->nome.'</div>
                <div class="linha linha_amar" style="font-weight: bold">
                    <div class="col1">'.$m->bairro.'</div>
                    <div class="col2"><span style="text-transform:none;">Mapa:</span> '.$m->mapa.'</div>
                </div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
        </td>';

            $cBloco++;
        }

        // Completa quantidade de bloco escritos
        while($cBloco < 4) {
            if($cBloco == 0) {
                // Primeira célula da linha
                $html .= '<tr>';
                
            } else if($cBloco == 2) {
                // Primeira célula da última linha
                $html .= '</tr><tr>';
            }

            $html .='<td>
            <div class="bloco-top">
                <div class="linha" style="text-align: center; padding: 2px; min-height:2mm;">REGISTRO DE CASA EM CASA</div>
                <div class="linha nome_surdo" style="text-align: center; font-size: 12px; padding: 1px; text-transform: uppercase;"> </div>
                <div class="linha linha_amar" style="font-weight: bold">
                    <div class="col1"> </div>
                    <div class="col2"><span style="text-transform:none;">Mapa:</span> </div>
                </div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
            <div class="bloco-main">
                <div class="linha">
                    <div class="col1">Publicador(a):</div>
                    <div class="col2">Data:</div>
                </div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
                <div class="linha"></div>
            </div>
        </td>';

            $cBloco++;
        }

        // Fecha linha e tabela.
        $html .= '</tr></tbody></table></div>';

        return $html;
    }

    static function surdo()
    {
        AdmController::authorized(4);

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.index",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'pillDefault' => 'surdo'
        ));
    }

    static function publicador()
    {
        AdmController::authorized(4);

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.index",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'pillDefault' => 'publicador'
        ));
    }

    static function sistema()
    {
        AdmController::authorized(4);

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.index",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'pillDefault' => 'sistema'
        ));
    }

    static function bd()
    {
        AdmController::authorized(4);

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.index",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'pillDefault' => 'bd'
        ));
    }

    static function surdoNovo()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.surdoNovo",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bairros' => $mapa->listaBairro(),
        ));
    }
    
    static function surdoVer()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.surdoVer",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'surdos' => $mapa->listaSurdos(TRUE, TRUE, TRUE),
        ));
    }

    static function surdoEditar(array $p)
    {
        AdmController::authorized(4);

        if(!$p['surdoid'] || $p['surdoid'] == '' || $p['surdoid'] < 1) {
            // Exibe erro 404
            header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            exit();
        }

        $mapa = new Mapa();
        $x = $mapa->surdoId($p['surdoid']);
        $surdo = json_decode($x);
        $x = null;



        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.surdoEditar",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'surdo' => $surdo,
            'bairros' => $mapa->listaBairro(),
        ));
    }

    static function surdoPendencias()
    {
        AdmController::authorized(5);
        $mapa = new Mapa();
        $pend = $mapa->pendLista();
        $surdos = array();
        if($pend !== false) {
            foreach($pend as $p) {
                if($p->mapa_id != 0) {
                    $s = $mapa->surdoId($p->mapa_id);
                    $surdos[$p->mapa_id] = json_decode($s);
                }
            }
        }
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.surdoPendencias",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'pendencias' => $pend,
            'surdos' => $surdos,
        ));
    }

    static function surdoPendAction(array $p)
    {
        $mapa = new Mapa();
        $pendId = (int)$p['pendId'];

        if($p['confPend'] == 'true') {
            // Aprovado

            $x = $mapa->pendAction($pendId, true);
            return $x;
        } else {
            // Recusado

            $x = $mapa->pendAction($pendId, false);
            return $x;
        }
    }

    static function surdoHistorico()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.surdoHistorico",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'mapa' => new Mapa(),
        ));
    }

    static function surdoHistoricoVer(array $p)
    {

        $mapa = new Mapa();
        return $mapa->historicoVer($p['id']);
    }

    static function surdoHistoricoCompara(array $p)
    {

        $mapa = new Mapa();
        return $mapa->historicoCompara($p['id']);
    }

    static function pubNovo()
    {
        AdmController::authorized(4);
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.pubNovo",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
        ));
    }

    static function pubNovoPOST()
    {
        $user = new User();
        $res = $user->novo($_POST);
        if($res === true) {
            SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Usuário '.$_POST['nome'].' '.$_POST['sobrenome'].' cadastrado com sucesso.', 'tipo' => 'success'));
        } else {
            SessionMessage::novo(array('titulo' => 'Falha!', 'texto' => $res, 'tipo' => 'warning'));
        }
        header('Location: /admin/publicadores/novo');
    }

    static function pubVer()
    {
        AdmController::authorized(4);
        $user = new User();
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.pubVer",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
            'usuarios' => $user->listaUsuarios(FALSE),
        ));
    }

    static function pubEditar($p)
    {
        AdmController::authorized(4);
        
        $user = new User();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.pubEditar",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
            'u' => $user->getInfo($p['pubid']),
        ));
    }

    static function pubEditarPOST($p)
    {
        //var_dump($_POST, $p);
        if($p['pubid'] != $_POST['id']) {
            SessionMessage::novo(array('tipo' => 'warning', 'titulo' => 'Ops!', 'texto' => 'houve uma divergência na transmissão dos dados. A página foi atualizada. Tente novamente.'));
            header('Location: /admin/publicadores/editar/'.$p['pubid']);
            return false;
        }

        $user = new User();
        $res = $user->salva($_POST['id'], $_POST['nome'], $_POST['sobrenome'], $_POST['usuario'], $_POST['email'], $_POST['expira'], $_POST['nivel']);
        if($res !== TRUE && $res !== FALSE) {
            SessionMessage::novo(array('tipo' => 'warning', 'titulo' => 'Mensagem do servidor:', 'texto' => $res));
        }
        
        header('Location: /admin/publicadores/editar/'.$p['pubid']);
    }

    static function pubTpessoal()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();
        

        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.pubTpessoal",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
            'tp' => $mapa->listaTP(),
            'surdos' => $mapa->listaSurdosSemTP(),
            'surdosJSON' => json_encode($mapa->listaSurdos(FALSE)),
        ));
    }

    static function pubEstudos()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();
        $surdos = $mapa->listaSurdos(TRUE);
        $user = new User();
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.pubEstudos",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
            'surdos' => $surdos,
            'bairros' => $mapa->listaBairro(),
            'publicadores' => $user->listaUsuarios(),
        ));
    }

    static function sisConfig()
    {
        AdmController::authorized(5);
        $mapa = new Mapa();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaConfig",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'config' => new Config(),
        ));
    }

    static function sisBairros()
    {
        AdmController::authorized(5);
        $mapa = new Mapa();
        $config = new Config();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaBairros",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bairros' => $mapa->listaBairro(),
            'regiao' => $config->get('regiao'),
        ));
    }

    static function sisVerMapas()
    {
        AdmController::authorized(4);
        $mapa = new Mapa();
        $config = new Config();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaVerMapas",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'mapas' => $mapa->getMapas(),
            'regiao' => $config->get('regiao'),
        ));
    }

    static function sisEditarMapas()
    {
        AdmController::authorized(5);
        $mapa = new Mapa();
        $config = new Config();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaEditarMapas",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'mapas' => $mapa->getMapas(),
            'regiao' => $config->get('regiao'),
        ));
    }

    static function sisLOG()
    {
        AdmController::authorized(4);
        $user = new User();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaLOG",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'usuarios' => $user->listaUsuarios()
        ));
    }

    static function sisImpressao()
    {
        AdmController::authorized(4);
        $bairro = new Bairro();
        $mapa = new Mapa();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaImpressao",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bairros' => $bairro->listaBairro(),
            'mapas' => $mapa->listaMapas()
        ));
    }

    static function sisRelatorio()
    {
        AdmController::authorized(4);
        $rel = new Relatorio();

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.sistemaRelatorio",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'periodoIni' => $rel->getPeriodoIni(),
            'periodoFim' => $rel->getPeriodoFim(),

        ));
    }

    static function sisGetRelatorio()
    {
        //var_dump($_POST);
        $rel = new Relatorio;

        if($_POST['tipo'] === 'geral') {
            return $rel->Geral();
        } else if($_POST['tipo'] === 'visitas') {
            return $rel->EntreVisitas();
        } else {
            return 'Tipo de relatório inválido.';
        }
    }

    static function bdDownload($obj)
    {
        // Procurar arquivo
        $bd = new BD();
        $folder = $bd->get('backupFolder');
        $arquivo = $folder.'/'.$obj['fname'].'.sql';
        if(file_exists($arquivo)) {
            // Arquivo encontrado
            // Força download do arquivo.
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($arquivo) . "\""); 
            readfile($arquivo);
        } else {
            http_response_code(404);
        }
    }

    static function bdBackup()
    {
        AdmController::authorized(5);
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.bdBackup",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
            'bd' => new BD(),
        ));
    }

    static function bdSQL()
    {
        AdmController::authorized(5);
        

        $blade = new BladeOne(AdmController::VIEWS,AdmController::CACHE,BladeOne::MODE_AUTO);
        return $blade->run("admin.bdSQL",array(
            'smoMSG' => SessionMessage::ler(),
            'router' => AdmController::router(),
            'uNome'=> $_SESSION['nome'],
            'anoCorrente' => date('Y'),
        ));
    }


}