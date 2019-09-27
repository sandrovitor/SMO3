<?php
include_once('Model.php');

class Mapa extends Model {
    protected $tabela = 'mapa';
    protected $pdo;
    protected $periodoIni;
    protected $periodoFim;


    function __construct()
    {
        parent::__construct();
    }
    
    function visitaPeriodo()
    {
        $abc = $this->pdo->query('SELECT opcao, valor FROM config WHERE opcao LIKE "data_visita%"');
        $x = 0;
        while ($x < $abc->rowCount()) {
            $reg = $abc->fetch(PDO::FETCH_OBJ);
            if($reg->opcao == 'data_visita_ult') {
                $this->periodoIni = $reg->valor;
            } else if($reg->opcao == 'data_visita_prox') {
                $this->periodoFim = $reg->valor;
            }
            $x++;
        }
        return true;
    }

    function listaBairro()
    {
        $abc = $this->pdo->query('SELECT ter.id, ter.bairro, ter.regiao as regiao_numero, (SELECT config.valor FROM config WHERE config.opcao = CONCAT("regiao_", regiao_numero)) as regiao_nome FROM ter WHERE 1 ORDER BY ter.regiao ASC, ter.bairro ASC');
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    function listaMapas()
    {
        $abc = $this->pdo->query('SELECT DISTINCT mapa.mapa, ter.bairro FROM mapa LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.mapa <> "" ORDER BY mapa.mapa ASC');
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    function getMapas()
    {
        $abc = $this->pdo->query('SELECT mapa.*, ter.bairro, ter.regiao FROM mapa LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.ocultar = 0 ORDER BY ter.regiao ASC, mapa.mapa ASC, mapa.mapa_indice ASC, mapa.nome ASC');
        if($abc->rowCount() == 0) {
            return false;
        }
        return $abc->fetchAll(PDO::FETCH_OBJ);
    }

    function getMapasForPrint($filtro = 'todos')
    {
        /**
         * Tipos de filtro:
         * TODOS        = (string) "todos" os mapas (exceto os surdos sem mapa).
         * BAIRRO       = (int) Número do bairro. Exemplo: 1.
         * INTERVALO    = (string) separado por hífen "-". Exemplo: CAJ001-CAJ012.
         * INDIVIDUAL   = (string) separado por vírgula ",". Exemplo: CAJ001,CAJ002,CAJ003.
         */

        if($filtro == '') {
            // NULO
            return 'Nulo... Não pode ser vazio.';
        } else if($filtro == 'todos') {
            // TODOS
            $abc = $this->pdo->query('SELECT mapa.*, login.nome as publicador, ter.bairro, ter.regiao FROM mapa LEFT JOIN login ON mapa.resp_id = login.id LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.ocultar = 0 AND mapa.mapa <> "" ORDER BY ter.regiao ASC, mapa.mapa ASC, mapa.mapa_indice ASC, mapa.nome ASC');
            if($abc->rowCount() > 0) {
                return $abc->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } else if(is_int((int)$filtro) && (int)$filtro != 0) {
            // BAIRRO
            $abc = $this->pdo->query('SELECT mapa.*, login.nome as publicador, ter.bairro, ter.regiao FROM mapa LEFT JOIN login ON mapa.resp_id = login.id LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.ocultar = 0 AND mapa.mapa <> "" AND mapa.bairro_id = '.(int)$filtro.' ORDER BY ter.regiao ASC, mapa.mapa ASC, mapa.mapa_indice ASC, mapa.nome ASC');
            if($abc->rowCount() > 0) {
                return $abc->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } else if(strrpos($filtro,'-') !== false) {
            // INTERVALO
            $x = explode('-',$filtro);
            $abc = $this->pdo->query('SELECT mapa.*, login.nome as publicador, ter.bairro, ter.regiao FROM mapa LEFT JOIN login ON mapa.resp_id = login.id LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.ocultar = 0 AND mapa.mapa >= "'.$x[0].'" AND mapa.mapa <= "'.$x[1].'" ORDER BY ter.regiao ASC, mapa.mapa ASC, mapa.mapa_indice ASC, mapa.nome ASC');
            if($abc->rowCount() > 0) {
                return $abc->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }

        } else if(strrpos($filtro, ',') !== false) {
            // INDIVIDUAL
            $x = explode(',', $filtro);
            $sqlFiltro = array();
            foreach($x as $a) {
                array_push($sqlFiltro, 'mapa.mapa = "'.$a.'"');
            }

            $sqlFiltro = implode(' OR ',$sqlFiltro);
            $abc = $this->pdo->query('SELECT mapa.*, login.nome as publicador, ter.bairro, ter.regiao FROM mapa LEFT JOIN login ON mapa.resp_id = login.id LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.ocultar = 0 AND ('.$sqlFiltro.') ORDER BY ter.regiao ASC, mapa.mapa ASC, mapa.mapa_indice ASC, mapa.nome ASC');
            if($abc->rowCount() > 0) {
                return $abc->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
            
        } else {
            //NULO
            return 'Opção inválida!';
        }
    }

    function salvaEditarMapas($m)
    {
        $mOBJ = json_decode($m);
        $sql = '';
        
        $sql = '';
        
        try {
            foreach($mOBJ as $surdo) {
                if($surdo != null) {
                    $sql .= 'UPDATE `mapa` SET `mapa` = "'.$surdo->mapa.'", `mapa_indice` = "'.$surdo->mapa_indice.'" WHERE `mapa`.`id` = '.$surdo->id.'; ';
                }
            }

            $xyz = $this->pdo->query($sql);
            
        } catch(PDOException $e) {
            $resultado['sucesso'] = false;
            $resultado['mensagem'] = 'Ocorreu um erro ao salvar: '. $e->getMessage();
            $resultado['dados'] = null;

            return json_encode($resultado);
        }

        // Retorna lista de surdos atualizada (formato JSON)
        $mapas = $this->getMapas();
        $surdoJSON = array();
        foreach($mapas as $m) {
            array_push($surdoJSON, array(
                'id' => $m->id,
                'nome' => $m->nome,
                'mapa' => $m->mapa,
                'mapa_indice' => $m->mapa_indice,
                'bairro' => $m->bairro,
                'bairro_id' => $m->bairro_id,
                'gps' => $m->gps,
                'regiao' => $m->regiao,
            ));
        }

        $surdoJSON = json_encode($surdoJSON);

        $resultado['sucesso'] = true;
        $resultado['mensagem'] = null;
        $resultado['dados'] = $surdoJSON;

        return json_encode($resultado);
    }

    function listaSurdos(bool $ocultos = TRUE, bool $desativados = FALSE, bool $infoCompleta = FALSE)
    {
        if($ocultos == TRUE && $desativados == TRUE) { // Inclui ocultos e desativados
            $parametro = '1';
        } else if($ocultos == FALSE && $desativados == TRUE) { // Exclui ocultos e inclui os desativados
            $parametro = '`mapa`.`ocultar` = 0';
        } else if($ocultos == TRUE && $desativados == FALSE) { // Inclui os ocultos e exclui os desativados
            $parametro = '`mapa`.`ativo` = 1';
        } else if($ocultos == FALSE && $desativados == FALSE) { // Exclui os ocultos e desativados
            $parametro = '`mapa`.`ocultar` = 0 AND `mapa`.`ativo` = 1';
        } else {
            $parametro = ' 1 ';
        }

        // Retorna informações completas ou não.
        if($infoCompleta == FALSE) {
            $sql = 'SELECT `mapa`.`id`, `mapa`.`nome`, `ter`.`bairro`, `mapa`.`be`, `mapa`.`resp_id`, (SELECT `login`.`nome` FROM `login` WHERE `login`.`id` = `mapa`.`resp_id`) as `resp` FROM `mapa` LEFT JOIN `ter` ON `mapa`.`bairro_id` = `ter`.`id` WHERE '.$parametro.' ORDER BY `ter`.`bairro` ASC, `mapa`.`nome` ASC';
        } else {
            $sql = 'SELECT `mapa`.*, `ter`.`bairro`, (SELECT `login`.`nome` FROM `login` WHERE `login`.`id` = `mapa`.`resp_id`) as `resp` FROM `mapa` LEFT JOIN `ter` ON `mapa`.`bairro_id` = `ter`.`id` WHERE '.$parametro.' ORDER BY `ter`.`bairro` ASC, `mapa`.`nome` ASC';
        }
        
        
        $abc = $this->pdo->query($sql);

        if($abc->rowCount() == 0) {
            return false;
        }

        return $abc->fetchAll(PDO::FETCH_OBJ);
    }

    function pesquisa(array $variaveis)
    {
        foreach($variaveis as $key => $value) {
            if($value == '~null~') {
                $variaveis[$key] = '';
            }
        }

        // Usa variáveis individuais
        $nome = $variaveis[0];
        $bairro = $variaveis[1];
        $turno = $variaveis[2];
        $idade = $variaveis[3];
        $be = $variaveis[4];
        $oculto = $variaveis[5];
        $encontrado = $variaveis[6];
        $desativado = substr($variaveis[7],0,3);


        $this->visitaPeriodo();

        $sql = 'SELECT `mapa`.`id`, `mapa`.`nome`, `mapa`.`ativo`, `mapa`.`ocultar` as oculto, `mapa`.`be`,  `ter`.`bairro`, '.
            'if((SELECT `registro`.`id` FROM `registro` WHERE `registro`.`mapa_id` = `mapa`.`id` AND (`registro`.`data_visita` >= "'.$this->periodoIni.'" AND `registro`.`data_visita` <= "'.$this->periodoFim.'") LIMIT 0, 1)>0, TRUE, FALSE) as encontrado '.
            'FROM `mapa` LEFT JOIN `ter` ON `mapa`.`bairro_id` = `ter`.`id` WHERE ';
        



        
        $controle = array();

        if($nome != '') {
            $sql .= '`mapa`.`nome` LIKE :nome AND ';
            array_push($controle, ':nome');
        }
        if($bairro != '') {
            $sql .= '`mapa`.`bairro_id` = :bairro AND ';
            array_push($controle, ':bairro');
        }
        if($turno != '') {
            $sql .= '`mapa`.`turno` LIKE :turno AND ';
            array_push($controle, ':turno');
        }
        if($idade != '') {
            $sql .= '`mapa`.`idade` LIKE :idade AND ';
            array_push($controle, ':idade');
        }
        if($be != '') {
            $sql .= '`mapa`.`be` = :be AND ';
            array_push($controle, ':be');
        }
        if($oculto != '' && $oculto != 'AMBOS') {
            $sql .= '`mapa`.`ocultar` = :oculto AND ';
            array_push($controle, ':oculto');
        }
        if($desativado != 'yes') {
            $sql .= '`mapa`.`ativo` = TRUE AND ';
        }

        // Remove últimos 4 caracteres
        if(count($controle) > 0) {
            $sql = substr($sql,0,-4);
        }

        $sql .= 'ORDER BY `ter`.`regiao` ASC, `ter`.`bairro` ASC, `mapa`.`nome` ASC';



        $abc = $this->pdo->prepare($sql);
        foreach($controle as $x) {
            switch($x) {
                case ':nome':
                    $abc->bindValue(':nome', '%'.$nome.'%', PDO::PARAM_STR);
                    break;

                case ':bairro':
                    $abc->bindValue(':bairro', $bairro, PDO::PARAM_INT);
                    break;

                case ':turno':
                    if($turno == 'IND') {
                        $abc->bindValue(':turno', "", PDO::PARAM_STR);
                    } else {
                        $abc->bindValue(':turno', $turno.'%', PDO::PARAM_STR);
                    }
                    break;

                case ':idade':
                    if($idade == 'IND') {
                        $abc->bindValue(':idade', "", PDO::PARAM_STR);
                    } else {
                        $abc->bindValue(':idade', $idade.'%', PDO::PARAM_STR);
                    }
                    break;

                case ':be':
                    if($be == 'YES') {
                        $abc->bindValue(':be', TRUE, PDO::PARAM_BOOL);
                    } else {
                        $abc->bindValue(':be', FALSE, PDO::PARAM_BOOL);
                    }
                    break;

                case ':oculto':
                    if($oculto == 'YES') {
                        $abc->bindValue(':oculto', TRUE, PDO::PARAM_BOOL);
                    } else {
                        $abc->bindValue(':oculto', FALSE, PDO::PARAM_BOOL);
                    }
                    break;

                case ':encontrado':
                    if($encontrado == 'YES') {
                        $abc->bindValue(':encontrado', TRUE, PDO::PARAM_BOOL);
                    } else {
                        $abc->bindValue(':encontrado', FALSE, PDO::PARAM_BOOL);
                    }
                    break;

                
            }
        }
        try {
            $abc->execute();
        } catch(PDOException $e) {
            return 'Ocorreu um erro no Banco de Dados!';
        }

        $surdos = $abc->fetchAll(PDO::FETCH_OBJ);
        //var_dump($encontrado);
        //echo '<strong>SQL:</strong> '.$sql.'<br>';
        //echo '<strong>Periodo:</strong> '.$this->periodoIni . ' a '.$this->periodoFim .'<br>';
        //var_dump($surdos);
        //echo '--------------------------------';

        /*
         * ----------------------------
         * Se a pesquisa inclui surdos encontrados ou não encontrados
         * ----------------------------
         */
        if($encontrado != '') {
            $x = array();
            // Realiza um novo filtro no resultado
            if($encontrado == 'YES') {
                foreach($surdos as $key => $value) {
                    if($value->encontrado == '1') {
                        // Adiciona no novo resultado
                        array_push($x,$value);
                    }
                }
            } else if($encontrado == 'NOT') {
                foreach($surdos as $key => $value) {
                    if($value->encontrado == '0') {
                        // Adiciona n novo resultado
                        array_push($x,$value);
                    }
                }
            }

            $surdos = $x;
            unset($x);
        }

        //var_dump($surdos);
        
        return json_encode($surdos);
    }

    function meuTP() // Meu Território Pessoal
    {
        $this->visitaPeriodo();
        // Checa se usuário tem território pessoal.
        $abc = $this->pdo->query('SELECT mapa.*, (SELECT COUNT(registro.id) FROM registro WHERE registro.mapa_id = mapa.id AND registro.data_visita > "'.$this->periodoIni.'" AND registro.data_visita <= "'.$this->periodoFim.'" AND registro.encontrado = TRUE) as encontrado, ter.bairro FROM mapa LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = 1 AND mapa.tp_pub = '.$_SESSION['id']);
        if($abc->rowCount() > 0) {
            $surdos = $abc->fetchAll(PDO::FETCH_OBJ);
        } else {
            $surdos = '';
        }

        return $surdos;
    }

    function surdoId(int $id)
    {
        if($id <= 0) {
            return 'Inválido!';
        } else {
            $this->visitaPeriodo();
            $abc = $this->pdo->prepare('SELECT `mapa`.*, `ter`.`bairro`, `login`.`nome` as resp, '.
            'if((SELECT COUNT(`registro`.`id`) FROM `registro` WHERE `registro`.`mapa_id` = `mapa`.`id` AND (`registro`.`data_visita` >= "'.$this->periodoIni.'" AND `registro`.`data_visita` <= "'.$this->periodoFim.'") LIMIT 0, 1)>0, TRUE, FALSE) as encontrado '.
            'FROM `mapa` LEFT JOIN `ter` ON `mapa`.`bairro_id` = `ter`.`id` LEFT JOIN `login` ON `mapa`.`resp_id` = `login`.`id` WHERE `mapa`.`id` = :id');
            $abc->bindValue(':id', $id, PDO::PARAM_INT);
            try {
                $abc->execute();
            } catch(PDOException $e) {
                return 'Ocorreu um erro no Banco de Dados!';
            }

            if($abc->rowCount() > 0) {
                return json_encode($abc->fetch(PDO::FETCH_OBJ));
            } else {
                return 'Nada encontrado';
            }
        }
    }

    function campanhaResultado(string $campanhaInicio, string $campanhaFinal)
    {
        $abc = $this->pdo->query('SELECT mapa.id, mapa.nome, ter.bairro, IF((SELECT registro.data_visita FROM registro WHERE registro.mapa_id = mapa.id AND registro.campanha = TRUE AND registro.data_visita >= "'.$campanhaInicio.'" AND registro.data_visita <= "'.$campanhaFinal.'") <> "", TRUE, FALSE) as campanha FROM mapa LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = TRUE ORDER BY ter.bairro ASC, mapa.nome ASC');

        $reg = $abc->fetchAll(PDO::FETCH_OBJ);

        return $reg;
    }

    function historicoNovo(int $id)
    {
        try{
            $abc = $this->pdo->prepare('SELECT * FROM `mapa` WHERE id = :id');
            $abc->bindValue(':id', $id, PDO::PARAM_INT);
            $abc->execute();
        } catch(PDOException $e) {
            // Caso aconteça alguma falha, exibe mensagem de erro na tela.
            return $e->getMessage();
        }
        if($abc->rowCount() > 0) {
            $obj = $abc->fetch(PDO::FETCH_ASSOC);
        } else {
            return 'A cópia de segurança para o Histórico de Endereços falhou, pois o registro original não foi encontrado.';
        }



        $sql = "INSERT INTO `historico_mapa` (`id`, `data`, `mapa_id`, `nome`, `bairro_id`, `gps`, `be`, `resp_id`, `endereco`, `p_ref`, `familia`, `whats`, `tel`, `facebook`, `obs`, `idade`, `turno`, `hora_melhor`, `dia_melhor`, `cad_autor`, `cad_data`) ".
            "VALUES (NULL, CURRENT_TIMESTAMP, :id, :nome, :bairro, :gps, :be, :respid, :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, :horamelhor, :diamelhor, :cadautor, :caddata)";
        


        try {
            $abc = $this->pdo->prepare($sql);
            // Parametriza as 'pseudo-nomes' e passa para a query.
            // PDO::PARAM_STR é o parametro para strings.
            // PDO::PARAM_INT é o parametro para inteiros.
            $abc->bindValue(":nome", $obj['nome'], PDO::PARAM_STR);
            $abc->bindValue(":id", $obj['id'], PDO::PARAM_INT);
            $abc->bindValue(":bairro", $obj['bairro_id'], PDO::PARAM_INT);
            $abc->bindValue(":gps", $obj['gps'], PDO::PARAM_STR);
            $abc->bindValue(":be", $obj['be'], PDO::PARAM_BOOL);
            $abc->bindValue(":respid", $obj['resp_id'], PDO::PARAM_INT);
            $abc->bindValue(":endereco", $obj['endereco'], PDO::PARAM_STR);
            $abc->bindValue(":pref", $obj['p_ref'], PDO::PARAM_STR);
            $abc->bindValue(":familia", $obj['familia'], PDO::PARAM_STR);
            $abc->bindValue(":tel", $obj['tel'], PDO::PARAM_STR);
            $abc->bindValue(":whats", $obj['whats'], PDO::PARAM_STR);
            $abc->bindValue(":facebook", $obj['facebook'], PDO::PARAM_STR);
            $abc->bindValue(":obs", $obj['obs'], PDO::PARAM_STR);
            $abc->bindValue(":idade", $obj['idade'], PDO::PARAM_STR);
            $abc->bindValue(":turno", $obj['turno'], PDO::PARAM_STR);
            $abc->bindValue(":horamelhor", $obj['hora_melhor'], PDO::PARAM_STR);
            $abc->bindValue(":diamelhor", $obj['dia_melhor'], PDO::PARAM_STR);
            $abc->bindValue(":cadautor", $obj['cad_autor'], PDO::PARAM_INT);
            $abc->bindValue(":caddata", $obj['cad_data'], PDO::PARAM_STR);
            
            
            // Executa a query.
            $executa = $abc->execute();
            
            if($executa) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            // Caso aconteça alguma falha, exibe mensagem de erro na tela.
            echo $e->getMessage();
            return $e->getMessage();
        }
    }

    function historicoVer()
    {
        
    }

    function historicoRecupera()
    {
        
    }

    function historicoApaga()
    {
        
    }

    function contaPendencias()
    {
        $abc = $this->pdo->query('SELECT id FROM `pre_cadastro` WHERE pendente = TRUE');
        return $abc->rowCount();
    }

    function pendLista()
    {
        $abc = $this->pdo->query('SELECT pre_cadastro.*,(SELECT ter.bairro FROM ter WHERE ter.id = pre_cadastro.bairro_id) as bairro, (SELECT login.nome FROM login WHERE login.id = pre_cadastro.cad_autor) as autor FROM `pre_cadastro` WHERE pendente = TRUE');
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }

    function pendAction(int $id, bool $confirma)
    {
        // Captura informação do pré-cadastro
        $abc = $this->pdo->prepare('SELECT * FROM `pre_cadastro` WHERE `id` = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        $abc->execute();
        if($abc->rowCount() == 0) {
            return 'Esse registro ('.$id.') não foi encontrado no sistema.';
        }

        $pre = $abc->fetch(PDO::FETCH_OBJ);

        // Verifica se o pré-cadastro já foi atendido
        if((bool)$pre->pendente == FALSE) {
            return true;
        }


        // Recusa pendência
        if($confirma == false) {
            // Recusa a pendência.
            $abc = $this->pdo->query('UPDATE pre_cadastro SET pendente = FALSE, aprovado = FALSE, data_aprovacao = "'.date('Y-m-d H:i:s').'" WHERE id = '.$pre->id);
            return true;
        }

        // Aprova a pendência.
        // Verifica se é cadastro NOVO ou EDITADO
        if($pre->mapa_id == 0) {
            // NOVO
            // Lança cadastro no mapa.
            $sql = "INSERT INTO `mapa` (id, nome, surdo_id, mapa, mapa_indice, bairro_id, gps, ativo, ocultar, be, resp_id, encontrado, motivo, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, tp_pub)".
                    "VALUES (NULL, :nome, '0', '', '0', :bairro, :gps, TRUE, FALSE, 0, 0, 0, '', :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', '', :cadautor, :caddata, 0)";
            $abc = $this->pdo->prepare($sql);

            try {
                $abc->bindValue(":nome", $pre->nome, PDO::PARAM_STR);
                $abc->bindValue(":bairro", $pre->bairro_id, PDO::PARAM_INT);
                $abc->bindValue(":gps", $pre->gps, PDO::PARAM_STR);
                $abc->bindValue(":endereco", $pre->endereco, PDO::PARAM_STR);
                $abc->bindValue(":pref", $pre->p_ref, PDO::PARAM_STR);
                $abc->bindValue(":familia", $pre->familia, PDO::PARAM_STR);
                $abc->bindValue(":tel", $pre->tel, PDO::PARAM_STR);
                $abc->bindValue(":whats", $pre->whats, PDO::PARAM_STR);
                $abc->bindValue(":facebook", $pre->facebook, PDO::PARAM_STR);
                $abc->bindValue(":obs", $pre->obs, PDO::PARAM_STR);
                $abc->bindValue(":idade", $pre->idade, PDO::PARAM_STR);
                $abc->bindValue(":turno", $pre->turno, PDO::PARAM_STR);
                $abc->bindValue(":cadautor", $pre->cad_autor, PDO::PARAM_INT);
                $abc->bindValue(":caddata", $pre->cad_data, PDO::PARAM_STR);

                $abc->execute();
            } catch(PDOException $e) {
                return 'Erro na base de dados: '.$e->getMessage();
            }

            // Confirma operação de atualização no pré cadastro.
            $abc = $this->pdo->query('UPDATE pre_cadastro SET pendente = FALSE, aprovado = TRUE, data_aprovacao = "'.date('Y-m-d H:i:s').'" WHERE id = '.$pre->id);

            return true;

        } else {
            // EDITADO
            // Faz backup do endereço atual para o histórico.
            $hist = $this->historicoNovo($pre->mapa_id);
            if($hist == true) {
                // Altera as informações no MAPA.
                $sql = 'UPDATE mapa SET nome = :nome, bairro = :bairro, endereco = :end, p_ref = :pref, gps = :gps, familia = :familia, facebook = :face, whats = :whats, tel = :tel, idade = :idade, turno = :turno, obs = :obs WHERE id = :id';

                try {
                    $abc = $this->pdo->prepare($sql);
                    $abc->bindValue(":nome", $pre->nome, PDO::PARAM_STR);
                    $abc->bindValue(":bairro", $pre->bairro_id, PDO::PARAM_INT);
                    $abc->bindValue(":gps", $pre->gps, PDO::PARAM_STR);
                    $abc->bindValue(":endereco", $pre->endereco, PDO::PARAM_STR);
                    $abc->bindValue(":pref", $pre->p_ref, PDO::PARAM_STR);
                    $abc->bindValue(":familia", $pre->familia, PDO::PARAM_STR);
                    $abc->bindValue(":tel", $pre->tel, PDO::PARAM_STR);
                    $abc->bindValue(":whats", $pre->whats, PDO::PARAM_STR);
                    $abc->bindValue(":facebook", $pre->facebook, PDO::PARAM_STR);
                    $abc->bindValue(":obs", $pre->obs, PDO::PARAM_STR);
                    $abc->bindValue(":idade", $pre->idade, PDO::PARAM_STR);
                    $abc->bindValue(":turno", $pre->turno, PDO::PARAM_STR);
                    $abc->bindValue(":cadautor", $pre->cad_autor, PDO::PARAM_INT);
                    $abc->bindValue(":caddata", $pre->cad_data, PDO::PARAM_STR);

                    
                    $abc->bindValue(":id", $pre->id, PDO::PARAM_INT);

                    $abc->execute();
                } catch(PDOException $e) {
                    return 'Erro na base de dados: '.$e->getMessage();
                }

                // Confirma operação de atualização no pré cadastro.
                $abc = $this->pdo->query('UPDATE pre_cadastro SET pendente = FALSE, aprovado = TRUE, data_aprovacao = "'.date('Y-m-d H:i:s').'" WHERE id = '.$pre->id);

                return true;
            } else {
                // Falha no backup para o histórico.
                return $hist;
            }

        }

        //$abc = $this->pdo->prepare('UPDATE `pre_cadastro` SET `pendente` = FALSE WHERE `id` = :id');

        //return 'Tivemos um probleminha do lado de cá';
        return true;
    }

    function surdoNovo(array $obj)
    {
        if($obj['nome'] != '' && $obj['bairro'] != 0 && $obj['bairro'] != '') {
			$nome = trim($obj['nome']);
			//$mapa = strtoupper(trim($obj['mapa']));
			$mapa = '';
			$bairro = (int)($obj['bairro']);
			$endereco = strtoupper(trim($obj['endereco']));
			$p_ref = strtoupper(trim($obj['pref']));
			$gps = trim($obj['gpsval']);
			$familia = strtoupper(trim($obj['familia']));
			$tel = strtoupper(trim($obj['tel']));
			$whats = strtoupper(trim($obj['whatsapp']));
			$facebook = trim($obj['facebook']);
			if(!isset($obj['idade'])) { $idade = ''; } else { $idade = $obj['idade'];}
			if(!isset($obj['turno'])) { $turno = ''; } else if($obj['turno'] == 'NOT' || ($obj['turno'] != 'MANHÃ' && $obj['turno'] != 'TARDE' && $obj['turno'] != 'NOITE')) { $turno = '';} else { $turno = $obj['turno'];}
			if(!isset($obj['motivo'])) {$motivo = ''; } else { $motivo = strtoupper($obj['motivo']);}
			$obs = strtoupper($obj['obs']);
            $caddata = date('Y-m-d H:i:s');
            if($obj['ativo'] == 'yes') { $ativo = TRUE; } else { $ativo = FALSE; }
            if($obj['ocultar'] == 'yes') { $ocultar = TRUE; } else { $ocultar = FALSE; }

            if($ativo == TRUE && $ocultar == FALSE) {
                $motivo = '';
            }
			
			
			// Insere registro no DB via PDO.
			try {
                // Escreve a query com 'pseudo-nomes'.
                $sql = "INSERT INTO `mapa` (id, nome, surdo_id, mapa, mapa_indice, bairro_id, gps, ativo, ocultar, be, resp_id, encontrado, motivo, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, tp_pub)".
                    "VALUES (NULL, :nome, '0', '', '0', :bairro, :gps, :ativo, :ocultar, 0, 0, 0, :motivo, :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', '', :cadautor, :caddata, 0)";
                $abc = $this->pdo->prepare($sql);
			    
				
				// Parametriza as 'pseudo-nomes' e passa para a query.
				// PDO::PARAM_STR é o parametro para strings.
				// PDO::PARAM_INT é o parametro para inteiros.
                $abc->bindValue(":nome", $nome, PDO::PARAM_STR);
                
				$abc->bindValue(":bairro", $bairro, PDO::PARAM_INT);
				$abc->bindValue(":gps", $gps, PDO::PARAM_STR);
				$abc->bindValue(":ativo", $ativo, PDO::PARAM_BOOL);
				$abc->bindValue(":ocultar", $ocultar, PDO::PARAM_BOOL);
				$abc->bindValue(":endereco", $endereco, PDO::PARAM_STR);
				$abc->bindValue(":motivo", $motivo, PDO::PARAM_STR);
				$abc->bindValue(":pref", $p_ref, PDO::PARAM_STR);
				$abc->bindValue(":familia", $familia, PDO::PARAM_STR);
				$abc->bindValue(":tel", $tel, PDO::PARAM_STR);
				$abc->bindValue(":whats", $whats, PDO::PARAM_STR);
				$abc->bindValue(":facebook", $facebook, PDO::PARAM_STR);
				$abc->bindValue(":obs", $obs, PDO::PARAM_STR);
				$abc->bindValue(":idade", $idade, PDO::PARAM_STR);
				$abc->bindValue(":turno", $turno, PDO::PARAM_STR);
				$abc->bindValue(":cadautor", $_SESSION['id'], PDO::PARAM_INT);
				$abc->bindValue(":caddata", $caddata, PDO::PARAM_STR);
				
				
				// Executa a query.
				$executa = $abc->execute();
				
				return true;
			} catch(PDOException $e) {
				// Caso aconteça alguma falha, exibe mensagem de erro na tela.
				return $e->getMessage();
			}
		} else {
			return 'Nome e bairro estão em branco.';
		}

        return true;
    }

    function surdoEditar(array $obj)
    {
        if($obj['nome'] != '' && $obj['bairro'] != 0 && $obj['bairro'] != '') {
			$nome = trim($obj['nome']);
			//$mapa = strtoupper(trim($obj['mapa']));
            $mapa = '';
            $id = $obj['id'];
			$bairro = (int)($obj['bairro']);
			$endereco = strtoupper(trim($obj['endereco']));
			$p_ref = strtoupper(trim($obj['pref']));
			$gps = trim($obj['gpsval']);
			$familia = strtoupper(trim($obj['familia']));
			$tel = strtoupper(trim($obj['tel']));
			$whats = strtoupper(trim($obj['whatsapp']));
			$facebook = trim($obj['facebook']);
			if(!isset($obj['idade'])) { $idade = ''; } else { $idade = $obj['idade'];}
			if(!isset($obj['turno'])) { $turno = ''; } else if($obj['turno'] == 'NOT' || ($obj['turno'] != 'MANHÃ' && $obj['turno'] != 'TARDE' && $obj['turno'] != 'NOITE')) { $turno = '';} else { $turno = $obj['turno'];}
			$obs = strtoupper($obj['obs']);


            // Faz backup para o histórico
            $res = $this->historicoNovo($id);
            if(!$res) {
                return 'Falha na cópia de segurança para o Histórico de Endereços.';
            }
            
            
			// Insere registro no DB via PDO.
			try {
                // Escreve a query com 'pseudo-nomes'.
                $sql = "UPDATE `mapa` SET `nome` = :nome, `bairro_id` = :bairro, `gps` = :gps, `endereco` = :endereco, `p_ref` = :pref, `familia` = :familia, `whats` = :whats, `tel` = :tel, `facebook` = :facebook, `obs` = :obs,  `idade` = :idade, `turno` = :turno WHERE `mapa`.`id` = :id";
                $abc = $this->pdo->prepare($sql);
			    
				
				// Parametriza as 'pseudo-nomes' e passa para a query.
				// PDO::PARAM_STR é o parametro para strings.
				// PDO::PARAM_INT é o parametro para inteiros.
                $abc->bindValue(":nome", $nome, PDO::PARAM_STR);
                $abc->bindValue(":id", $id, PDO::PARAM_INT);
				$abc->bindValue(":bairro", $bairro, PDO::PARAM_INT);
				$abc->bindValue(":gps", $gps, PDO::PARAM_STR);
				$abc->bindValue(":endereco", $endereco, PDO::PARAM_STR);
				$abc->bindValue(":pref", $p_ref, PDO::PARAM_STR);
				$abc->bindValue(":familia", $familia, PDO::PARAM_STR);
				$abc->bindValue(":tel", $tel, PDO::PARAM_STR);
				$abc->bindValue(":whats", $whats, PDO::PARAM_STR);
				$abc->bindValue(":facebook", $facebook, PDO::PARAM_STR);
				$abc->bindValue(":obs", $obs, PDO::PARAM_STR);
				$abc->bindValue(":idade", $idade, PDO::PARAM_STR);
				$abc->bindValue(":turno", $turno, PDO::PARAM_STR);
				
				
				// Executa a query.
                $executa = $abc->execute();
                if($executa) {
                    return true;
                } else {
                    return 'Aconteceu algum erro ao salvar.';
                }
				
			} catch(PDOException $e) {
				// Caso aconteça alguma falha, exibe mensagem de erro na tela.
				return $e->getMessage();
			}
		} else {
			return 'Nome e bairro estão em branco.';
		}

        return true;
    }

    function preCadastroNovo(array $obj)
    {
        //var_dump($obj);
        session_start();
        if((int)$_SESSION['nivel'] == 5) {
            // Administrador não passa pelo pré-cadastro.
            $obj['ativo'] = 'yes';
            $obj['ocultar'] = 'not';
            $obj['motivo'] = '';
            return $this->surdoNovo($obj);
        } else {

            // Salva em pré-cadastro
            if($obj['nome'] != '' && $obj['bairro'] != 0 && $obj['bairro'] != '') {
                $nome = trim($obj['nome']);
                //$mapa = strtoupper(trim($obj['mapa']));
                $mapa = '';
                $bairro = (int)($obj['bairro']);
                $endereco = strtoupper(trim($obj['endereco']));
                $p_ref = strtoupper(trim($obj['pref']));
                $gps = trim($obj['gpsval']);
                $familia = strtoupper(trim($obj['familia']));
                $tel = strtoupper(trim($obj['tel']));
                $whats = strtoupper(trim($obj['whatsapp']));
                $facebook = trim($obj['facebook']);
                if(!isset($obj['idade'])) { $idade = ''; } else { $idade = $obj['idade'];}
                if(!isset($obj['turno'])) { $turno = ''; } else if($obj['turno'] == 'NOT' || ($obj['turno'] != 'MANHÃ' && $obj['turno'] != 'TARDE' && $obj['turno'] != 'NOITE')) { $turno = '';} else { $turno = $obj['turno'];}
                if(!isset($obj['motivo'])) {$motivo = ''; } else { $motivo = strtoupper($obj['motivo']);}
                $obs = strtoupper($obj['obs']);
                $caddata = date('Y-m-d H:i:s');
                if($obj['ativo'] == 'yes') { $ativo = TRUE; } else { $ativo = FALSE; }
                if($obj['ocultar'] == 'yes') { $ocultar = TRUE; } else { $ocultar = FALSE; }
    
                if($ativo == TRUE && $ocultar == FALSE) {
                    $motivo = '';
                }
                
                
                // Insere registro no DB via PDO.
                try {
                    // Escreve a query com 'pseudo-nomes'.
                    $sql = "INSERT INTO `pre_cadastro` (id, nome, mapa_id, bairro_id, gps, pendente, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, aprovado, data_aprovacao)".
                        "VALUES (NULL, :nome, '0', :bairro, :gps, TRUE, :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', '', :cadautor, :caddata, FALSE, '0000-00-00')";
                    $abc = $this->pdo->prepare($sql);
                    
                    
                    // Parametriza as 'pseudo-nomes' e passa para a query.
                    // PDO::PARAM_STR é o parametro para strings.
                    // PDO::PARAM_INT é o parametro para inteiros.
                    $abc->bindValue(":nome", $nome, PDO::PARAM_STR);
                    $abc->bindValue(":bairro", $bairro, PDO::PARAM_INT);
                    $abc->bindValue(":gps", $gps, PDO::PARAM_STR);
                    $abc->bindValue(":endereco", $endereco, PDO::PARAM_STR);
                    $abc->bindValue(":pref", $p_ref, PDO::PARAM_STR);
                    $abc->bindValue(":familia", $familia, PDO::PARAM_STR);
                    $abc->bindValue(":tel", $tel, PDO::PARAM_STR);
                    $abc->bindValue(":whats", $whats, PDO::PARAM_STR);
                    $abc->bindValue(":facebook", $facebook, PDO::PARAM_STR);
                    $abc->bindValue(":obs", $obs, PDO::PARAM_STR);
                    $abc->bindValue(":idade", $idade, PDO::PARAM_STR);
                    $abc->bindValue(":turno", $turno, PDO::PARAM_STR);
                    $abc->bindValue(":cadautor", $_SESSION['id'], PDO::PARAM_INT);
                    $abc->bindValue(":caddata", $caddata, PDO::PARAM_STR);
                    
                    
                    // Executa a query.
                    $executa = $abc->execute();
                    
                    return true;
                } catch(PDOException $e) {
                    // Caso aconteça alguma falha, exibe mensagem de erro na tela.
                    return $e->getMessage();
                }
            } else {
                return 'Nome e bairro estão em branco.';
            }
    
            return true;

        }
    }

    function preCadastroEditar(array $obj)
    {
        //var_dump($obj);
        session_start();
        if((int)$_SESSION['nivel'] == 5) {
            // Administrador não passa pelo pré-cadastro.

            return $this->surdoEditar($obj);
        } else {
            // Salva em pré-cadastro.
            if($obj['nome'] != '' && $obj['bairro'] != 0 && $obj['bairro'] != '') {
                $nome = trim($obj['nome']);
                //$mapa = strtoupper(trim($obj['mapa']));
                $mapa = '';
                $id = $obj['id'];
                $bairro = (int)($obj['bairro']);
                $endereco = strtoupper(trim($obj['endereco']));
                $p_ref = strtoupper(trim($obj['pref']));
                $gps = trim($obj['gpsval']);
                $familia = strtoupper(trim($obj['familia']));
                $tel = strtoupper(trim($obj['tel']));
                $whats = strtoupper(trim($obj['whatsapp']));
                $facebook = trim($obj['facebook']);
                if(!isset($obj['idade'])) { $idade = ''; } else { $idade = $obj['idade'];}
                if(!isset($obj['turno'])) { $turno = ''; } else if($obj['turno'] == 'NOT' || ($obj['turno'] != 'MANHÃ' && $obj['turno'] != 'TARDE' && $obj['turno'] != 'NOITE')) { $turno = '';} else { $turno = $obj['turno'];}
                if(!isset($obj['motivo'])) {$motivo = ''; } else { $motivo = strtoupper($obj['motivo']);}
                $obs = strtoupper($obj['obs']);
                $caddata = date('Y-m-d H:i:s');
                if($obj['ativo'] == 'yes') { $ativo = TRUE; } else { $ativo = FALSE; }
                if($obj['ocultar'] == 'yes') { $ocultar = TRUE; } else { $ocultar = FALSE; }
    
                if($ativo == TRUE && $ocultar == FALSE) {
                    $motivo = '';
                }
                
                
                // Insere registro no DB via PDO.
                try {
                    // Escreve a query com 'pseudo-nomes'.
                    $sql = "INSERT INTO `pre_cadastro` (id, nome, mapa_id, bairro_id, gps, pendente, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, aprovado, data_aprovacao)".
                        "VALUES (NULL, :nome, :id, :bairro, :gps, TRUE, :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', '', :cadautor, :caddata, FALSE, '0000-00-00')";
                    $abc = $this->pdo->prepare($sql);
                    
                    
                    // Parametriza as 'pseudo-nomes' e passa para a query.
                    // PDO::PARAM_STR é o parametro para strings.
                    // PDO::PARAM_INT é o parametro para inteiros.
                    $abc->bindValue(":nome", $nome, PDO::PARAM_STR);
                    $abc->bindValue(":id", $id, PDO::PARAM_INT);
                    $abc->bindValue(":bairro", $bairro, PDO::PARAM_INT);
                    $abc->bindValue(":gps", $gps, PDO::PARAM_STR);
                    $abc->bindValue(":endereco", $endereco, PDO::PARAM_STR);
                    $abc->bindValue(":pref", $p_ref, PDO::PARAM_STR);
                    $abc->bindValue(":familia", $familia, PDO::PARAM_STR);
                    $abc->bindValue(":tel", $tel, PDO::PARAM_STR);
                    $abc->bindValue(":whats", $whats, PDO::PARAM_STR);
                    $abc->bindValue(":facebook", $facebook, PDO::PARAM_STR);
                    $abc->bindValue(":obs", $obs, PDO::PARAM_STR);
                    $abc->bindValue(":idade", $idade, PDO::PARAM_STR);
                    $abc->bindValue(":turno", $turno, PDO::PARAM_STR);
                    $abc->bindValue(":cadautor", $_SESSION['id'], PDO::PARAM_INT);
                    $abc->bindValue(":caddata", $caddata, PDO::PARAM_STR);
                    
                    
                    // Executa a query.
                    $executa = $abc->execute();
                    
                    return true;
                } catch(PDOException $e) {
                    // Caso aconteça alguma falha, exibe mensagem de erro na tela.
                    return $e->getMessage();
                }
            } else {
                return 'Nome e bairro estão em branco.';
            }
    
            return true;
        }
    }
}