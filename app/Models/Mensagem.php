<?php
include_once('Model.php');

class Mensagem extends Model
{
    protected $nome;
    private $msg;

    function __construct()
    {
        parent::__construct();
        @session_start();
        if($_SESSION['logado'] !== true) {
            header('/');
            exit();
        }

        $this->nome = $_SESSION['nome'];
    }

    private function msg1()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = 'Bem-vindo, '.$this->nome.'!! Temos algo para te falar...';
        $this->msg['body'] = 'É um prazer ter você aqui conosco, compartilhando informações sobre surdos do nosso território. 
        Toda informação que você souber é válida e bastante preciosa.
        <br><strong>Não guarde só pra si, compartilhe no <abbr title="Sistema de Mapas Online">SMO</abbr>.</strong>

        <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "\'<i>... mudarei a língua dos povos para uma língua pura, para que todos eles possam (...) <strong>servi-lo ombro a ombro</strong>.</i>\'"
        - Sof. 3:9</h2>

        <br><br>
        <ul>
            <li>Em <code>Início</code> você verá algumas informações rápidas sobre suas atividade e outras informações úteis do sistema.</li>
            <li>Em <code>Surdos > Consulta</code> você poderá buscar surdos no SMO, usando diversos filtros (como Nome, Turno e Idade).
            Para tudo funcionar corretamente, todas as informações do surdo precisam estar preenchidas.</li>
            <li>Em <code>Surdos > Registros</code> você pode escrever novos Registros de Visita (não importa se você encontrou ou não) e consultar o que outros escreveram.</li>
            <li>Em <code>Surdos > Cadastro</code> você atualiza informações de surdos existente ou cadastra novos surdos encontrados.
            (PS.: Essas informações passam por análise dos Administradores, por isso pode demorar um pouco até ela estar disponível para consulta.)</li>
            <li>Em <code>Surdos > Redes Sociais</code> você terá acesso rápido a todos os surdos com telefone, WhatsApp e Facebook para contato, durante os dias de pregação em Redes Sociais.</li>
            <li>Em <code>Surdos > Território Pessoal</code> você verá uma lista com os surdos que estão no seu território pessoal. Você poderá visitá-los quando quiser. Antes peça um território ao seu Superintendente de Serviço.</li>
        </ul>
        <br>
        <h5 class="text-center font-weight-bold">Qualquer dúvida que surgir, não hesite em contatar um ancião ou administrador para te ajudar.</h5>
        
        <hr>
        <span class="text-primary"><strong>OBS.:</strong> Se você já usou o SMO na versão 2.0, não se preocupe que nada foi mudado!</span>';
    }

    private function msg25()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = $this->nome.', esse é o '.$_SESSION['acessos'].'º acesso!';
        $this->msg['body'] = 'Olha só esse número! Você já conseguiu chegar até aqui, então me diz: foi muito difícil?
        <br>
            <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "<i><strong>Não desistamos de fazer aquilo que é excelente</strong>, pois ceifaremos na época devida, se não desfalecermos.</i>"
            <br>- Gál. 6:9</h2>
        <br>
        <h5>Não importa a dificuldade que você encontre nsta modalidade, <strong>não desista</strong> de dar seu melhor... Jeová te recompensará abertamente!</h5>';
    }

    private function msg50()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = $this->nome.', esse é o '.$_SESSION['acessos'].'º acesso!';
        $this->msg['body'] = 'Obrigado por seu zelo e esforço em contribuir para o serviço de pregação. Seus esforços não são em vão.
        <br>
            <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "<i>Pois Deus não é injusto para se esquecer da <strong>sua obra e do amor que vocês mostraram</strong> ao nome dele, <strong>por servirem</strong> os santos <strong>e continuarem a servi-los</strong>.</i>"
            <br>- Heb. 6:10</h2>
        <br>
        <h5>Continue assim...</h5>';
    }

    private function msg75()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = $this->nome.', esse é o '.$_SESSION['acessos'].'º acesso!';
        $this->msg['body'] = 'Olha só esse número! Você já conseguiu chegar até aqui, então me diz: foi muito difícil?
        <br>
            <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "<i>Que <strong>Jeová a recompense pelo que você tem feito</strong> e que haja para você <strong>um salário perfeito da parte de Jeová</strong>, o Deus de Israel, debaixo das asas de quem você veio buscar refúgio.</i>"
            <br>- Rute 2:12</h2>
        <br>
        <h5>Sim, você fez um trabalho excelente e sua recompensa não virá de homens. Cada pequeno trabalho que você faz no serviço de pregação é visto e valorizado por Jeová. Ele promete lhe dar "<strong>um salário perfeito</strong>" por seu esforço.<br>
        Continue assim. Todos estamos muito felizes.</h5>';
    }

    private function msg100()
    {
        
        $sql = 'SELECT (SELECT COUNT(id) FROM mapa WHERE mapa.resp_id = login.id) as estudos, (SELECT COUNT(id) FROM registro WHERE registro.pub_id = login.id) as registros, (SELECT COUNT(id) FROM pre_cadastro WHERE pre_cadastro.cad_autor = login.id) as cadastros FROM login WHERE login.id = '.$_SESSION['id'];
        
        $abc = $this->pdo->query($sql);
        $usuario = $abc->fetch(PDO::FETCH_OBJ);
        
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = $this->nome.', esse é o '.$_SESSION['acessos'].'º acesso!';
        $this->msg['body'] = 'Estamos feliz com essa sua marca... Quer ver o que mais você fez junto com a gente?<br><br>

        <ul style="font-weight: bold">
            <li>Surdos que estudam com você: <span class="badge badge-dark">'.$usuario->estudos.'</span></li>
            <li>Total de registros de visita: <span class="badge badge-dark">'.$usuario->registros.'</span></li>
            <li>Surdos novos e editados: <span class="badge badge-dark">'.$usuario->cadastros.'</span></li>
        </ul>
        <br>
        Você conseguiu isso sozinho? Não. <strong>Jeová abençoou seus esforços</strong> e continuará abençoando se continuar dando o melhor de si.
        <hr>
        
        <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
        <i>Jeová... disse: “<strong>Vá com a força que você tem...
        Não sou eu que estou enviando você?</strong>”</i>
        <br>- Juí. 6:14</h2>';
    }

    private function msg125()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = 'Uaau, '.$this->nome.'! Esse é o '.$_SESSION['acessos'].'º acesso!';
        $this->msg['body'] = 'Impressionante esse seu compromisso. Ficamos verdadeiramente felizes com sua atividade. Sabe de quem você nos lembra? <strong>Paulo</strong>. Veja:
        <br>
            <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "<i>Assim, tendo <strong>terno amor por vocês, estávamos decididos não só a lhes transmitir as boas novas de Deus, mas também a lhes dar tudo de nós</strong>, porque vocês vieram a ser muito amados por nós.</i>"
            <br>- 1 Tess. 2:8</h2>
        <br>
        Paulo se gastou em prol do Reino. Você está fazendo o mesmo, <i><strong>dando tudo de si</strong></i> para pregar as Boas Novas porque seu <strong>amor</strong> por Jeová e às pessoas é fortemente evidente.
        <hr>
        <h5>Continue e incentive outros a fazer o mesmo através do seu exemplo!</h5>';
    }

    private function msg150()
    {
        $this->msg = array('header' => '', 'body' => '', 'mostrar' => true);
        $this->msg['header'] = $this->nome.', precisamos ser francos com você!';
        $this->msg['body'] = 'Já tem <strong>'.$_SESSION['acessos'].'</strong> acessos ao SMO?<br><br>
        Com certeza, todo o corpo de anciãos e servos ministeriais, pioneiros (regulares e auxiliares) e publicadores estão compartilhando deste sentimento:
        <br>
            <h2 id="modal01_biblia" class="text-center" style="margin: 30px 25px; font-family: \'Times New Roman\', Times, serif; display:none;">
            "<i>Temos a obrigação de <strong>sempre agradecer a Deus por vocês</strong>, irmãos. Isso é apropriado, porque a <strong>sua fé está crescendo extraordinariamente</strong>, e <strong>o amor</strong> de todos vocês uns pelos outros <strong>está aumentando</strong>.</i>"
            <br>- 2 Tess. 1:3</h2>
        <br>
        Sim, seu ministério e empenho tem sido notado por todos na congregação e por Jeová. Muito obrigado por nos ajudar nessa obra que cumpre profecias. Seu zelo reflete o quanto sua fé nas promessas divinas é grande.<br><br>
        Logo em breve seu amor a Jeová e às pessoas será recompensado. Continue aumentando seu amor por Ele. Breve estaremos juntos no Novo Mundo, contando nossas experiências de serviço durante os Últimos Dias. <strong>Não Desista!</strong>
        <hr>
        <h5 class="text-center"><i>"Temos a obrigação de sempre agradecer a Deus por vocês..."</i></h5>
        <div class="text-right">Forte abraço dos seus irmãos,<br>Congregação LS Castelo Branco.</div>';
    }

    public function getMsg()
    {
        $acessos = $_SESSION['acessos'];

        if($acessos > 151 && $acessos <= 301) {
            $acessos -= 150;
        } else if($acessos > 301 && $acessos <= 451) {
            $acessos -= 300;
        } else if($acessos > 451 && $acessos <= 601) {
            $acessos -= 450;
        } else if($acessos > 601 && $acessos <= 751) {
            $acessos -= 600;
        }

        if($this->msg == '') {
            switch($acessos) {
                case 1: $this->msg1(); break;
                case 25: $this->msg25(); break;
                case 50: $this->msg50(); break;
                case 75: $this->msg75(); break;
                case 100: $this->msg100(); break;
                case 125: $this->msg125(); break;
                case 150: $this->msg150(); break;

                default: return FALSE; break;
            }
        }

        return $this->msg;
    }
}