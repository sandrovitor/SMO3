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
        
        //var_dump($abc->rowCount(), $abc->fetchAll(PDO::FETCH_OBJ));return false;

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

    function getBE(int $publicadorId)
    {
        $abc= $this->pdo->prepare('SELECT mapa.id, mapa.nome, mapa.mapa, mapa.ocultar, ter.bairro FROM `mapa` LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE mapa.ativo = TRUE AND mapa.be = TRUE AND mapa.resp_id = :resp');
        $abc->bindValue(':resp', $publicadorId, PDO::PARAM_INT);
        $abc->execute();
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }

    function removeBE(int $surdoId)
    {
        $abc = $this->pdo->prepare('UPDATE mapa SET resp_id = "0", be = FALSE WHERE id = :id');
        $abc->bindValue(':id', $surdoId, PDO::PARAM_INT);
        try {
            $abc->execute();
            $s = json_decode($this->surdoId((int) $id));
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'definiu o surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a> como Bíblia Não Estuda.');
            return true;
        }catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function ativar(int $surdoId)
    {
        $s = json_decode($this->surdoId((int) $surdoId));
        $abc = $this->pdo->prepare('UPDATE mapa SET ativo = TRUE, motivo = "" WHERE id = :id');
        try{
            $abc->bindValue(':id', $surdoId, PDO::PARAM_INT);

            $abc->execute();
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'ativou o surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
            return TRUE;
        } catch(PDOException $e){
            return $e->getMessage();
        }
    }

    function desativar(int $surdoId, $motivo)
    {
        $s = json_decode($this->surdoId((int) $surdoId));
        $abc = $this->pdo->prepare('UPDATE mapa SET ativo = FALSE, motivo = :motivo WHERE id = :id');
        try{
            $abc->bindValue(':motivo', $motivo, PDO::PARAM_STR);
            $abc->bindValue(':id', $surdoId, PDO::PARAM_INT);

            $abc->execute();
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'desativou o surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
            return TRUE;
        } catch(PDOException $e){
            return $e->getMessage();
        }

    }

    function ocultar(int $surdoId, $motivo)
    {
        $s = json_decode($this->surdoId((int) $surdoId));
        $abc = $this->pdo->prepare('UPDATE mapa SET ocultar = TRUE, motivo = :motivo WHERE id = :id');
        try{
            $abc->bindValue(':motivo', $motivo, PDO::PARAM_STR);
            $abc->bindValue(':id', $surdoId, PDO::PARAM_INT);

            $abc->execute();
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'ocultou o surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
            return TRUE;
        } catch(PDOException $e){
            return $e->getMessage();
        }
    }

    function desocultar(int $surdoId)
    {
        $s = json_decode($this->surdoId((int) $surdoId));
        $abc = $this->pdo->prepare('UPDATE mapa SET ocultar = FALSE, motivo = "" WHERE id = :id');
        try{
            $abc->bindValue(':id', $surdoId, PDO::PARAM_INT);

            $abc->execute();
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 're-exibiu o surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
            return TRUE;
        } catch(PDOException $e){
            return $e->getMessage();
        }
    }

    function excluir(int $surdoId)
    {
        $s = json_decode($this->surdoId((int) $surdoId));
        $abc = $this->pdo->prepare('DELETE FROM mapa WHERE id = :id1; DELETE FROM registro WHERE mapa_id = :id2; DELETE FROM historico_mapa WHERE mapa_id = :id3;');
        try{
            $abc->bindValue(':id1', $surdoId, PDO::PARAM_INT);
            $abc->bindValue(':id2', $surdoId, PDO::PARAM_INT);
            $abc->bindValue(':id3', $surdoId, PDO::PARAM_INT);

            $abc->execute();
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'excluiu o surdo <i>'.$s->nome.' ['.$s->bairro.']</i> do sistema.');
            SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Surdo <i>'.$s->nome.' ['.$s->bairro.']</i> excluído.', 'tipo' => 'success'));
            return TRUE;
        } catch(PDOException $e){
            return $e->getMessage();
        }
    }

    function salvaEditarMapas($m)
    {
        $mOBJ = json_decode($m);
        $sql = '';
        
        $sql = '';
        $alt = array(0 => array(), 1 => array()); // 0 -> Surdos com mapa; 1 -> Surdos sem mapa
        
        try {
            foreach($mOBJ as $surdo) {
                if($surdo != null) {
                    $sql .= 'UPDATE `mapa` SET `mapa` = "'.$surdo->mapa.'", `mapa_indice` = "'.$surdo->mapa_indice.'" WHERE `mapa`.`id` = '.$surdo->id.'; ';

                    // Log de alterações.
                    if($surdo->mapa == '') {
                        
                        $s = json_decode($this->surdoId((int) $surdo->id));
                        array_push($alt[1], $s->nome);
                    } else {
                        $s = json_decode($this->surdoId((int) $surdo->id));
                        array_push($alt[0], '<a href="/surdo/'.$s->id.'" target="_blank">'.$s->nome.'</a> (<strong>'.$surdo->mapa.'</strong>, posição '.$surdo->mapa_indice.')');

                        // Marcos (CBO001, posição 1)
                    }
                }
            }

            $xyz = $this->pdo->query($sql);

            // LOG
            $log = new LOG();
            if(count($alt[0]) > 0) { $log->novo(LOG::TIPO_ATUALIZA, 'realocou os surdos para os mapas: '.implode(', ', $alt[0])); }
            if(count($alt[1]) > 0) { $log->novo(LOG::TIPO_ATUALIZA, 'removeu estes surdos de seus mapas: '.implode(', ', $alt[1])); }
            
        } catch(PDOException $e) {
            $resultado['sucesso'] = false;
            $resultado['mensagem'] = 'Ocorreu um erro ao salvar: '. $e->getMessage();
            $resultado['dados'] = null;

            return json_encode($resultado);
        }

        
        $resultado['sucesso'] = true;
        $resultado['mensagem'] = null;
        $resultado['dados'] = null;

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
            $sql = 'SELECT `mapa`.`id`, `mapa`.`nome`, `ter`.`bairro`, `mapa`.`ativo`, `mapa`.`mapa`, `mapa`.`ocultar`, `mapa`.`be`, `mapa`.`resp_id`, `mapa`.`tp_pub`, (SELECT `login`.`nome` FROM `login` WHERE `login`.`id` = `mapa`.`resp_id`) as `resp` FROM `mapa` LEFT JOIN `ter` ON `mapa`.`bairro_id` = `ter`.`id` WHERE '.$parametro.' ORDER BY `ter`.`bairro` ASC, `mapa`.`nome` ASC';
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

    function listaTP() // Lista todos os territórios pessoais no SMO
    {
        $abc = $this->pdo->query('SELECT login.id, login.nome, login.sobrenome, login.user, (SELECT COUNT(*) FROM mapa WHERE mapa.tp_pub = login.id) as surdosTP FROM `login` WHERE 1 ORDER BY login.nome ASC, login.sobrenome ASC');

        if($abc->rowCount() == 0) {
            return false;
        } else {
            $reg = $abc->fetchAll(PDO::FETCH_OBJ);
            return $reg;
        }
    }

    function listaSurdosSemTP()
    {
        $abc = $this->pdo->query('SELECT mapa.id, mapa.nome, mapa.mapa, ter.bairro FROM `mapa` LEFT JOIN ter ON mapa.bairro_id = ter.id WHERE tp_pub = 0 AND mapa.ativo = TRUE AND mapa.ocultar = FALSE ORDER BY ter.regiao ASC, ter.bairro ASC, mapa.nome ASC');

        if($abc->rowCount() == 0) {
            return false;
        } else {
            $reg = $abc->fetchAll(PDO::FETCH_OBJ);
            return $reg;
        }
    }

    function setTP(array $remover = array(), array $adicionar = array(), int $publicador) // Define surdos em Território Pessoal
    {
        //var_dump($remover, $adicionar, $publicador);

        if(count($remover) > 0) {
            // Remove os surdos do TP
            $sql = '';
            foreach($remover as $s) {
                $sql .= 'UPDATE mapa SET tp_pub = 0 WHERE id = '.(int)$s.';';
            }

            try {
                $abc = $this->pdo->query($sql);
            } catch(PDOException $e) {
                return 'Erro ao remover: '.$e->getMessage();
            }
        }

        if(count($adicionar) > 0) {
            // Adiciona os surdos ao TP
            $sql = '';
            foreach($adicionar as $s) {
                $sql .= 'UPDATE mapa SET tp_pub = '.(int)$publicador.' WHERE id = '.(int)$s.';';
            }

            try {
                $abc = $this->pdo->query($sql);
            } catch(PDOException $e) {
                return 'Erro ao adicionar surdos ao TP: '.$e->getMessage();
            }
        }

        return true;
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
                
                $s = json_decode($this->surdoId((int) $id));
                $log = new LOG();
                $log->novo(LOG::TIPO_SISTEMA, 'fez uma cópia dos dados do surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a> para o histórico.');
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

    function historicoVer(int $id)
    {
        $abc = $this->pdo->query('SELECT historico_mapa.*, CONCAT(login.nome, " ", login.sobrenome) as autor, ter.bairro
        FROM historico_mapa
        LEFT JOIN login ON historico_mapa.cad_autor = login.id
        LEFT JOIN ter ON historico_mapa.bairro_id = ter.id
        WHERE historico_mapa.id = '.$id);

        if($abc->rowCount() == 0) {
            return '<div class="alert alert-danger">Informações sobre o histórico não encontradas. Tente novamnte mais tarde.</div>';
        }

        $s = $abc->fetch(PDO::FETCH_OBJ);
        $data = new DateTime($s->data);
        if($s->cad_autor == 0) {
            $s->autor = 'SISTEMA';
        } else {
            if($s->autor == ' ') {
                $s->autor == 'Desconhecido';
            }
        }
        //var_dump($s);
        $diam = explode('|', $s->dia_melhor);
        //var_dump($diam);
        $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = '';

        foreach($diam as $d) {
            switch($d) {
                case '1': $d1 = 'checked="checked"'; break;
                case '2': $d2 = 'checked="checked"'; break;
                case '3': $d3 = 'checked="checked"'; break;
                case '4': $d4 = 'checked="checked"'; break;
                case '5': $d5 = 'checked="checked"'; break;
                case '6': $d6 = 'checked="checked"'; break;
                case '7': $d7 = 'checked="checked"'; break;
            }
        }

        $html = '
		<small><i>(Histórico ID: <strong>'.$s->id.'</strong> | Surdo ID.: <strong>'.$s->mapa_id.'</strong>)</i></small>
        <br><button type="button" class="btn btn-primary btn-sm" onclick="comparar('.$s->id.')"><i class="fas fa-exchange-alt"></i> Comparar com endereço atual</button>
        <br><br>
        
        <div class="border shadow-sm bg-info text-white p-2">
            <div class="row">
                <div class="col-6">
                    <strong>Data da Alteração:</strong> <i>'.$data->format('d/m/Y H:i:s').'</i>
                </div>
                <div class="col-6">
                    <strong>Autor da Alteração:</strong> <i>'.$s->autor.'</i>
                </div>
            </div>
        </div>
        <br>


		<table class="table table-sm table-hover table-bordered shadow-sm">
			<tbody>		
				<tr>
					<th>Nome:</th>
					<td>'.$s->nome.'</td>
				</tr>
				<tr>
					<th>Bairro:</th>
					<td>'.$s->bairro.'</td>
				</tr>
				<tr>
					<th>Endereço:</th>
					<td>'.$s->endereco.'</td>
				</tr>
				<tr>
					<th>Ponto de Referência:</th>
					<td>'.$s->p_ref.'</td>
				</tr>
				<tr>
					<th>Localização GPS:</th>
					<td>'.$s->gps.'</td>
				</tr>
				<tr>
					<th>Referência Familiar:</th>
					<td>'.$s->familia.'</td>
				</tr>
				<tr>
					<th>Facebook:</th>
					<td>'.$s->facebook.'</td>
				</tr>
				<tr>
					<th>Whatsapp:</th>
					<td>'.$s->whats.'</td>
				</tr>
				<tr>
					<th>Telefone(s):</th>
					<td>'.$s->tel.'</td>
				</tr>
				<tr>
					<th>Idade:</th>
					<td>'.$s->idade.'</td>
				</tr>
				<tr>
					<th>Turno:</th>
					<td>'.$s->turno.'</td>
				</tr>
				<tr>
					<th>Dia Melhor:</th>
					<td>
						<div class="checkbox"><label><input type="checkbox" value="1" '.$d1.' disabled="">Domingo</label></div>
						<div class="checkbox"><label><input type="checkbox" value="2" '.$d2.' disabled="">Segunda</label></div>
						<div class="checkbox"><label><input type="checkbox" value="3" '.$d3.' disabled="">Terça</label></div>
						<div class="checkbox"><label><input type="checkbox" value="4" '.$d4.' disabled="">Quarta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="5" '.$d5.' disabled="">Quinta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="6" '.$d6.' disabled="">Sexta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="7" '.$d7.' disabled="">Sábado</label></div>
					</td>
				</tr>
				<tr>
					<th>Observações:</th>
					<td>'.$s->obs.'</td>
				</tr>		
			</tbody>
        </table>';
        
        return $html;
    }

    function historicoCompara(int $id)
    {
        // Tabela HISTORICO_MAPA
        $abc = $this->pdo->query('SELECT historico_mapa.*, CONCAT(login.nome, " ", login.sobrenome) as autor, ter.bairro
        FROM historico_mapa
        LEFT JOIN login ON historico_mapa.cad_autor = login.id
        LEFT JOIN ter ON historico_mapa.bairro_id = ter.id
        WHERE historico_mapa.id = '.$id);

        if($abc->rowCount() == 0) {
            return '<div class="alert alert-danger">Informações sobre o histórico não encontradas. Tente novamente mais tarde.</div>';
        }

        $s = $abc->fetch(PDO::FETCH_OBJ);
        $data = new DateTime($s->data);
        if($s->cad_autor == 0) {
            $s->autor = 'SISTEMA';
        } else {
            if($s->autor == ' ') {
                $s->autor == 'Desconhecido';
            }
        }
        //var_dump($s);
        $diam = explode('|', $s->dia_melhor);
        //var_dump($diam);
        $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = '';

        foreach($diam as $d) {
            switch($d) {
                case '1': $d1 = 'checked="checked"'; break;
                case '2': $d2 = 'checked="checked"'; break;
                case '3': $d3 = 'checked="checked"'; break;
                case '4': $d4 = 'checked="checked"'; break;
                case '5': $d5 = 'checked="checked"'; break;
                case '6': $d6 = 'checked="checked"'; break;
                case '7': $d7 = 'checked="checked"'; break;
            }
        }

        // Tabela MAPA
        $abc = $this->pdo->query('SELECT mapa.*, ter.bairro
        FROM mapa
        LEFT JOIN ter ON mapa.bairro_id = ter.id
        WHERE mapa.id = '.$s->mapa_id);

        if($abc->rowCount() == 0) {
            return '<div class="alert alert-danger">Informações atuais do surdo não foram encontradas. Tente novamente mais tarde.</div>';
        }

        $a = $abc->fetch(PDO::FETCH_OBJ);

        $diam = explode('|', $a->dia_melhor);
        $f1 = $f2 = $f3 = $f4 = $f5 = $f6 = $f7 = '';
        foreach($diam as $f) {
            switch($f) {
                case '1': $f1 = 'checked="checked"'; break;
                case '2': $f2 = 'checked="checked"'; break;
                case '3': $f3 = 'checked="checked"'; break;
                case '4': $f4 = 'checked="checked"'; break;
                case '5': $f5 = 'checked="checked"'; break;
                case '6': $f6 = 'checked="checked"'; break;
                case '7': $f7 = 'checked="checked"'; break;
            }
        }

        // RESSALTA AS DIFERENÇAS NA COMPARAÇÃO
        $dif = 0;
        if($s->nome != $a->nome) {
            $cNome = 'bg-danger text-white';
            $dif++;
        } else {
            $cNome = '';
        }

        if($s->bairro != $a->bairro) {
            $cBai = 'bg-danger text-white';
            $dif++;
        } else {
            $cBai = '';
        }

        if($s->endereco != $a->endereco) {
            $cEnd = 'bg-danger text-white';
            $dif++;
        } else {
            $cEnd = '';
        }

        if($s->p_ref != $a->p_ref) {
            $cPref = 'bg-danger text-white';
            $dif++;
        } else {
            $cPref = '';
        }

        if($s->gps != $a->gps) {
            $cGps = 'bg-danger text-white';
            $dif++;
        } else {
            $cGps = '';
        }

        if($s->familia != $a->familia) {
            $cFamilia = 'bg-danger text-white';
            $dif++;
        } else {
            $cFamilia = '';
        }

        if($s->facebook != $a->facebook) {
            $cFacebook = 'bg-danger text-white';
            $dif++;
        } else {
            $cFacebook = '';
        }

        if($s->whats != $a->whats) {
            $cWhats = 'bg-danger text-white';
            $dif++;
        } else {
            $cWhats = '';
        }

        if($s->tel != $a->tel) {
            $cTel = 'bg-danger text-white';
            $dif++;
        } else {
            $cTel = '';
        }

        if($s->idade != $a->idade) {
            $cIdade = 'bg-danger text-white';
            $dif++;
        } else {
            $cIdade = '';
        }

        if($s->turno != $a->turno) {
            $cTurno = 'bg-danger text-white';
            $dif++;
        } else {
            $cTurno = '';
        }

        if($s->dia_melhor != $a->dia_melhor) {
            $cDia = 'bg-danger text-white';
            $dif++;
        } else {
            $cDia = '';
        }

        if($s->obs != $a->obs) {
            $cObs = 'bg-danger text-white';
            $dif++;
        } else {
            $cObs = '';
        }

        $html = '
        
        <div class="border shadow-sm bg-info text-white p-2">
            <div class="row">
                <div class="col-6">
                    <strong>Data da Alteração:</strong> <i>'.$data->format('d/m/Y H:i:s').'</i>
                </div>
                <div class="col-6">
                    <strong>Autor da Alteração:</strong> <i>'.$s->autor.'</i>
                </div>
            </div>
        </div>
        <br>


        <table class="table table-sm table-hover table-bordered shadow-sm">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th>ATUAL</th>
                    <th>HISTÓRICO</th>
                </tr>
            </thead>
			<tbody>		
				<tr class="'.$cNome.'">
					<th>Nome:</th>
					<td>'.$a->nome.'</td>
					<td>'.$s->nome.'</td>
				</tr>
				<tr class="'.$cBai.'">
					<th>Bairro:</th>
					<td>'.$a->bairro.'</td>
					<td>'.$s->bairro.'</td>
				</tr>
				<tr class="'.$cEnd.'">
					<th>Endereço:</th>
					<td>'.$a->endereco.'</td>
					<td>'.$s->endereco.'</td>
				</tr>
				<tr class="'.$cPref.'">
					<th>Ponto de Referência:</th>
					<td>'.$a->p_ref.'</td>
					<td>'.$s->p_ref.'</td>
				</tr>
				<tr class="'.$cGps.'">
					<th>Localização GPS:</th>
					<td>'.$a->gps.'</td>
					<td>'.$s->gps.'</td>
				</tr>
				<tr class="'.$cFamilia.'">
					<th>Referência Familiar:</th>
					<td>'.$a->familia.'</td>
					<td>'.$s->familia.'</td>
				</tr>
				<tr class="'.$cFacebook.'">
					<th>Facebook:</th>
					<td>'.$a->facebook.'</td>
					<td>'.$s->facebook.'</td>
				</tr>
				<tr class="'.$cWhats.'">
					<th>Whatsapp:</th>
					<td>'.$a->whats.'</td>
					<td>'.$s->whats.'</td>
				</tr>
				<tr class="'.$cTel.'">
					<th>Telefone(s):</th>
					<td>'.$a->tel.'</td>
					<td>'.$s->tel.'</td>
				</tr>
				<tr class="'.$cIdade.'">
					<th>Idade:</th>
					<td>'.$a->idade.'</td>
					<td>'.$s->idade.'</td>
				</tr>
				<tr class="'.$cTurno.'">
					<th>Turno:</th>
					<td>'.$a->turno.'</td>
					<td>'.$s->turno.'</td>
				</tr>
				<tr class="'.$cDia.'">
                    <th>Dia Melhor:</th>
                    <td>
						<div class="checkbox"><label><input type="checkbox" value="1" '.$f1.' disabled="">Domingo</label></div>
						<div class="checkbox"><label><input type="checkbox" value="2" '.$f2.' disabled="">Segunda</label></div>
						<div class="checkbox"><label><input type="checkbox" value="3" '.$f3.' disabled="">Terça</label></div>
						<div class="checkbox"><label><input type="checkbox" value="4" '.$f4.' disabled="">Quarta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="5" '.$f5.' disabled="">Quinta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="6" '.$f6.' disabled="">Sexta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="7" '.$f7.' disabled="">Sábado</label></div>
					</td>
					<td>
						<div class="checkbox"><label><input type="checkbox" value="1" '.$d1.' disabled="">Domingo</label></div>
						<div class="checkbox"><label><input type="checkbox" value="2" '.$d2.' disabled="">Segunda</label></div>
						<div class="checkbox"><label><input type="checkbox" value="3" '.$d3.' disabled="">Terça</label></div>
						<div class="checkbox"><label><input type="checkbox" value="4" '.$d4.' disabled="">Quarta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="5" '.$d5.' disabled="">Quinta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="6" '.$d6.' disabled="">Sexta</label></div>
						<div class="checkbox"><label><input type="checkbox" value="7" '.$d7.' disabled="">Sábado</label></div>
					</td>
				</tr>
				<tr class="'.$cObs.'">
					<th>Observações:</th>
					<td>'.$a->obs.'</td>
					<td>'.$s->obs.'</td>
				</tr>		
			</tbody>
        </table>';

        // Verifica se houve diferenças
        if($dif > 0) {
            $html .= '
            
            <div class="mt-2 py-3 px-2 shadow-sm border text-center div-difyes">
                Houve <strong>'.$dif.'</strong> diferença(s). O que deseja fazer?<br><br>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="recuperar('.$s->id.')">Recuperar</button>
                    <button type="button" class="btn btn-danger" onclick="apagar('.$s->id.')">Excluir</button>
                </div>
            </div>';
        } else {
            $html .= '
            
            <div class="mt-2 py-3 px-2 shadow-sm border text-center div-difnot">
                Não houve diferença(s).
            </div>';
        }
        
        return $html;
    }

    function historicoRecupera(int $id)
    {
        // Procura registro no historico
        $abc = $this->pdo->prepare('SELECT * FROM historico_mapa WHERE id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() == 0) {
            return 'Histórico não encontrado.';
        }

        $s = $abc->fetch(PDO::FETCH_OBJ);

        // Faz backup antes de recuperar o histórico.
        $this->historicoNovo($s->mapa_id);

        // Faz UPDATE
        $sql = 'UPDATE mapa SET
        nome = :nome, bairro_id = :bairro, gps = :gps, be = :be,
        resp_id = :respid, endereco = :endereco, p_ref = :pref, familia = :familia,
        whats = :whats, tel = :tel, facebook = :facebook, obs = :obs, idade = :idade,
        turno = :turno, hora_melhor = :horamelhor, dia_melhor = :diamelhor
        WHERE id = '.$s->mapa_id;

        try {
            $abc = $this->pdo->prepare($sql);


            $abc->bindValue(":nome", $s->nome, PDO::PARAM_STR);
            $abc->bindValue(":bairro", $s->bairro_id, PDO::PARAM_INT);
            $abc->bindValue(":gps", $s->gps, PDO::PARAM_STR);
            $abc->bindValue(":be", $s->be, PDO::PARAM_BOOL);
            $abc->bindValue(":respid", $s->resp_id, PDO::PARAM_INT);
            $abc->bindValue(":endereco", $s->endereco, PDO::PARAM_STR);
            $abc->bindValue(":pref", $s->p_ref, PDO::PARAM_STR);
            $abc->bindValue(":familia", $s->familia, PDO::PARAM_STR);
            $abc->bindValue(":tel", $s->tel, PDO::PARAM_STR);
            $abc->bindValue(":whats", $s->whats, PDO::PARAM_STR);
            $abc->bindValue(":facebook", $s->facebook, PDO::PARAM_STR);
            $abc->bindValue(":obs", $s->obs, PDO::PARAM_STR);
            $abc->bindValue(":idade", $s->idade, PDO::PARAM_STR);
            $abc->bindValue(":turno", $s->turno, PDO::PARAM_STR);
            $abc->bindValue(":horamelhor", $s->hora_melhor, PDO::PARAM_STR);
            $abc->bindValue(":diamelhor", $s->dia_melhor, PDO::PARAM_STR);

            $abc->execute();

            $s = json_decode($this->surdoId((int) $id));
            $log = new LOG();
            $log->novo(LOG::TIPO_ATUALIZA, 'recuperou dados do surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a> do histórico.');
            SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Histórico de <i>'.$s->nome.'<i> recuperado com sucesso.', 'tipo' => 'success'));
            return 'OK';
        } catch(PDOException $e) {
            return $e->getMessage();
        }

        
    }

    function historicoApaga(int $id)
    {
        $abc = $this->pdo->prepare('DELETE FROM historico_mapa WHERE id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        try {
            $abc->execute();
            $s = json_decode($this->surdoId((int) $id));
            $log = new LOG();
            $log->novo(LOG::TIPO_REMOVE, 'removeu uma entrada no histórico de dados do surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
            return 'OK';
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    function historicoLista(int $id)
    {
        //var_dump($id);
        $abc = $this->pdo->prepare('SELECT historico_mapa.*, CONCAT(login.nome, " ", login.sobrenome) as autor FROM `historico_mapa` LEFT JOIN login ON historico_mapa.cad_autor = login.id WHERE historico_mapa.mapa_id = :id');
        $abc->bindValue(':id', $id, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() > 0) {
            $html = '';
            $registros = $abc->fetchAll(PDO::FETCH_OBJ);
            $html .= '
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Autor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';
            foreach($registros as $s) {
                $data = new DateTime($s->data);
                if($s->cad_autor == 0) {
                    $s->autor = 'SISTEMA';
                } else {
                    if($s->autor == ' ') {
                        $s->autor == 'Desconhecido';
                    }
                }
                $html .= '
                <tr>
                    <td>'.$data->format('d/m/Y H:i').'</td>
                    <td>'.$s->autor.'</td>
                    <td>
                        <button class="btn btn-sm btn-primary" title="Ver info" onclick="verInfo('.$s->id.')"><i class="fas fa-search"></i></button>
                        <button class="btn btn-sm btn-primary" title="Comparar" onclick="comparar('.$s->id.')"><i class="fas fa-exchange-alt"></i></button>
                        <button class="btn btn-sm btn-danger" title="Apagar" onclick="apagar('.$s->id.')"><i class="fas fa-eraser"></i></button>
                    </td>
                </tr>';
            }
            $html .= '
                </tbody>
            </table>';

            return $html;
        } else {
            return 'Nada encontrado no histórico.';
        }
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

            
            $log = new LOG();
            $log->novo(LOG::TIPO_REMOVE, 'recusou a pendência ID: '.$pre->id.'.');
            return true;
        }

        // Aprova a pendência.
        // Verifica se é cadastro NOVO ou EDITADO
        if($pre->mapa_id == 0) {
            // NOVO
            // Lança cadastro no mapa.
            $sql = "INSERT INTO `mapa` (id, nome, mapa, mapa_indice, bairro_id, gps, ativo, ocultar, be, resp_id, motivo, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, tp_pub)".
                    "VALUES (NULL, :nome, '', '0', :bairro, :gps, TRUE, FALSE, 0, 0, '', :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', '', :cadautor, :caddata, 0)";
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

                $log = new LOG();
                $log->novo(LOG::TIPO_CADASTRO, 'aprovou a pendência ID: '.$pre->id.' de cadastro de novo surdo.');
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
                    
                    $s = json_decode($this->surdoId((int) $pre->id));
                    $log = new LOG();
                    $log->novo(LOG::TIPO_ATUALIZA, 'recuperou o histórico de dados do surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
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
            if(!isset($obj['dia_melhor']) || $obj['dia_melhor'] == '') { $dia_melhor = ''; } else { $dia_melhor = implode('|', $obj['dia_melhor']); }


            if($ativo == TRUE && $ocultar == FALSE) {
                $motivo = '';
            }
            
            
			
			// Insere registro no DB via PDO.
			try {
                // Escreve a query com 'pseudo-nomes'.
                $sql = "INSERT INTO `mapa` (id, nome, mapa, mapa_indice, bairro_id, gps, ativo, ocultar, be, resp_id, motivo, endereco, p_ref, familia, whats, tel, facebook, obs, idade, turno, hora_melhor, dia_melhor, cad_autor, cad_data, tp_pub) ".
                    "VALUES (null, :nome, '', '0', :bairro, :gps, :ativo, :ocultar, 0, 0, :motivo, :endereco, :pref, :familia, :whats, :tel, :facebook, :obs, :idade, :turno, '', :diam, :cadautor, :caddata, 0)";
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
                $abc->bindValue(":diam", $dia_melhor, PDO::PARAM_STR);
				
				
				// Executa a query.
                $executa = $abc->execute();
                
                
                $log = new LOG();
                $log->novo(LOG::TIPO_CADASTRO, 'adicionou o surdo <i>'.$nome.'</i> ao sistema.');
				
				return true;
			} catch(PDOException $e) {
				// Caso aconteça alguma falha, exibe mensagem de erro na tela.
				return $e->getMessage();
			}
		} else {
			return 'Nome e bairro estão em branco.';
		}
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
            if(!isset($obj['dia_melhor']) || $obj['dia_melhor'] == '') { $dia_melhor = ''; } else { $dia_melhor = implode('|', $obj['dia_melhor']); }


            // Faz backup para o histórico
            $res = $this->historicoNovo($id);
            if(!$res) {
                return 'Falha na cópia de segurança para o Histórico de Endereços.';
            }
            
            
			// Insere registro no DB via PDO.
			try {
                // Escreve a query com 'pseudo-nomes'.
                $sql = "UPDATE `mapa` SET `nome` = :nome, `bairro_id` = :bairro, `gps` = :gps, `endereco` = :endereco, `p_ref` = :pref, `familia` = :familia, `whats` = :whats, `tel` = :tel, `facebook` = :facebook, `obs` = :obs,  `idade` = :idade, `turno` = :turno, `dia_melhor` = :diam WHERE `mapa`.`id` = :id";
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
                $abc->bindValue(":diam", $dia_melhor, PDO::PARAM_STR);
				
				
				// Executa a query.
                $executa = $abc->execute();
                if($executa) {
                    $log = new LOG();
                    $log->novo(LOG::TIPO_ATUALIZA, 'atualizou dados do surdo <a href="/surdo/'.$id.'" target="_blank"><i>'.$nome.'</i></a>.');
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
                    
                    
                    $log = new LOG();
                    $log->novo(LOG::TIPO_CADASTRO, 'adicionou o surdo '.$nome.' ao pré-cadastro.');
                    
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

                    
                    $s = json_decode($this->surdoId((int) $id));
                    $log = new LOG();
                    $log->novo(LOG::TIPO_CADASTRO, 'pediu alteração do surdo <a href="/surdo/'.$s->id.'" target="_blank"><i>'.$s->nome.' ['.$s->bairro.']</i></a>.');
                    
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