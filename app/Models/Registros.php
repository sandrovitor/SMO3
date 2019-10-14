<?php
include_once('Model.php');

/*
 * Os retornos das funções são enviadas para a próxima página via SESSION.
 * O controlador vai ler o retorno, exibir a mensagem e apagar esse retorno da SESSION.
 * 
 */

class Registros extends Model {
    protected $tabela = 'mapa';
    protected $pdo;

    function __construct()
    {
        parent::__construct();
    }

    function novo(array $params)
    {
        //var_dump($params);
        // Analisa variável por variável e cria QUERY de INSERT
        $sql = 'INSERT INTO `registro` (`id`, `mapa_id`, `data_visita`, `texto`, `pub_id`, `encontrado`, `conferencia`, `campanha`) VALUES (NULL, :surdo, :data, :texto, :publicador, :enc, :conf, :camp)';
        if(!isset($params['surdo']) || $params['surdo'] == '') {
            echo 'Surdo inválido';
            SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro: SURDO INVÁLIDO. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
            return false;
        } else {
            $surdo = $params['surdo'];
        }

        if(!isset($params['data']) || $params['data'] == '') {
            echo 'Data inválida';
            SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro: DATA INVÁLIDA. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
            return false;
        } else {
            $data = $params['data'];
        }

        if(!isset($params['publicador']) || $params['publicador'] == '') {
            echo 'Publicador inválido';
            SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro: PUBLICADOR INVÁLIDO. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
            return false;
        } else {
            $publicador = $params['publicador'];
        }

        if(!isset($params['texto']) || $params['texto'] == '') {
            echo 'Texto inválido';
            SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro: TEXTO DO REGISTRO INVÁLIDO. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
            return false;
        } else {
            $texto = addslashes(utf8_encode($params['texto']));
        }

        if(isset($params['encontrado'])) {
            $enc = TRUE;
        } else {
            $enc = FALSE;
        }

        if(isset($params['social'])) {
            $conf = TRUE;
        } else {
            $conf = FALSE;
        }

        if(isset($params['campanha'])) {
            $camp = TRUE;
        } else {
            $camp = FALSE;
        }
        
        
        // Valida variáveis no PDO
        //$abc = $this->pdo->prepare($sql);
        $mod = new Model();
        $abc = $mod->pdo->prepare($sql);
        $abc->bindValue(':surdo', $surdo, PDO::PARAM_INT);
        $abc->bindValue(':data', $data, PDO::PARAM_STR);
        $abc->bindValue(':texto', $texto, PDO::PARAM_STR);
        $abc->bindValue(':publicador', $publicador, PDO::PARAM_INT);
        $abc->bindValue(':enc', $enc, PDO::PARAM_BOOL);
        $abc->bindValue(':conf', $conf, PDO::PARAM_BOOL);
        $abc->bindValue(':camp', $camp, PDO::PARAM_BOOL);

        try {
            $abc->execute();
            SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Registro adicionado.', 'tipo' => 'success'));
        } catch(PDOException $e) {
            SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro no BD. '.$e->getMessage().'. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
            return false;

        }

        if(isset($params['be'])) {
            // Define surdo como BE
            $abc = $mod->pdo->prepare('UPDATE `mapa` SET `be` = TRUE, `resp_id` = :resp WHERE `id` = :id');
            $abc->bindValue(':resp', $publicador, PDO::PARAM_INT);
            $abc->bindValue(':id', $surdo, PDO::PARAM_INT);
            try {
                $abc->execute();
                SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Surdo adicionado à sua lista de estudantes.', 'tipo' => 'success'));
            } catch(PDOException $e) {
                SessionMessage::novo(array('titulo' => 'Erro!', 'texto' => 'Ocorreu um erro no BD ao definir o surdo como estudante. '.$e->getMessage().'. <strong>Tente novamente mais tarde.</strong>', 'tipo' => 'danger'));
                return false;
    
            }

        }
        return true;
    }

    function busca($params)
    {
        /*
         * TIPOS DE URL QUE COMBINA COM ESSE CONTROLLER
         * 
         * registros/buscar/100                                     => Somente um único registro. Variáveis: $regid
         * registros/buscar/surdo/100/publicador/100/               => Vários registros (LIMIT 10) por SURDOS E PUBLICADORES. Variáveis: $surdoid, $pubid
         * registros/buscar/surdo/100/publicador/100/limit=0-100&order=new      => Vários registros por SURDOS E PUBLICADORES + QUERY STRING. Variáveis: $surdoid, $pubid, $querystr
         * 
         */ 


        // Verifica tipo de busca
        if(isset($params['regid'])) { 
            // ##################################################################### UNICO
            $abc = $this->pdo->prepare('SELECT mapa.nome, registro.*, (SELECT ter.bairro FROM ter WHERE ter.id = mapa.bairro_id) as bairro, (SELECT login.nome FROM login WHERE login.id = registro.pub_id) as publicador FROM registro LEFT JOIN mapa ON registro.mapa_id = mapa.id WHERE registro.id = :regid');
            $abc->bindValue(':regid', $params['regid'], PDO::PARAM_INT);
            $abc->execute();

            if($abc->rowCount() > 0)  {
                $x = $abc->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($x);
            } else {
                echo '{0}';
            }

            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_CONSULTA, 'buscou o registro '.$params['regid'].'.');
        } else if(isset($params['querystr'])) { 
            // ##################################################################### VARIOS + QUERY STR
            // Captura o QUERY STRING
            $querystr = explode('&', $params['querystr']);
            $q = array();
            //var_dump($querystr);
            foreach($querystr as $a) {
                $b = explode('=', $a);
                switch($b[0]) {
                    case 'limit':
                        $q[$b[0]] = 'LIMIT '.str_replace('-',', ',$b[1]);
                        break;

                    default:
                        $q[$b[0]] = $b[1];
                        break;
                }
                
            }

            $querystr = $q;
            $logStr = array();
            $mapa = new Mapa();
            $user = new User();

            // Verifica se há SURDOID e PUBID
            $where = array();
            if($params['surdoid'] != 0) { // SURDOID
                array_push($where, 'registro.mapa_id = :surdoid');
                $s = $mapa->surdoId($params['surdoid']);
                array_push($logStr, 'do(a) surdo(a) '.$s->nome.' ['.$s->bairro.']');
            }

            if($params['pubid'] != 0) { // PUBID
                array_push($where, 'registro.pub_id = :pubid');
                $u = $user->getInfo($params['pubid']);
                array_push($logStr, 'do(a) publicador(a) '. $u->nome);
            }


            $logStr = implode(', ', $logStr);
            $where = implode(' AND ', $where);
            if($where == '') {
                $where = '1';
                $logStr = 'de qualquer surdo(a) e publicador(a)';

            }

            $abc = $this->pdo->prepare('SELECT mapa.nome, registro.*, (SELECT ter.bairro FROM ter WHERE ter.id = mapa.bairro_id) as bairro, (SELECT login.nome FROM login WHERE login.id = registro.pub_id) as publicador FROM registro LEFT JOIN mapa ON registro.mapa_id = mapa.id WHERE '.$where.' ORDER BY registro.data_visita DESC '.$querystr['limit']);
            if($params['surdoid'] != 0) {   $abc->bindValue(':surdoid', $params['surdoid'], PDO::PARAM_INT); }
            if($params['pubid'] != 0) {     $abc->bindValue(':pubid', $params['pubid'], PDO::PARAM_INT); }
            $abc->execute();

            if($abc->rowCount() > 0)  {
                $x = $abc->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($x);
            } else {
                echo '{0}';
            }

            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_CONSULTA, 'buscou registros '.$logStr.'.');
            
        } else { 
            // ##################################################################### VÁRIOS S/ QUERY STR
            $logStr = array();
            $mapa = new Mapa();
            $user = new User();

            // Verifica se há SURDOID e PUBID
            $where = array();
            if($params['surdoid'] != 0) { // SURDOID
                array_push($where, 'registro.mapa_id = :surdoid');
                $s = $mapa->surdoId($params['surdoid']);
                array_push($logStr, 'do(a) surdo(a) '.$s->nome.' ['.$s->bairro.']');
            }

            if($params['pubid'] != 0) { // PUBID
                array_push($where, 'registro.pub_id = :pubid');
                $u = $user->getInfo($params['pubid']);
                array_push($logStr, 'do(a) publicador(a) '. $u->nome);
            }


            $logStr = implode(', ', $logStr);
            $where = implode(' AND ', $where);
            if($where == '') {
                $where = '1';
                $logStr = 'de qualquer surdo(a) e publicador(a)';

            }

            $abc = $this->pdo->prepare('SELECT mapa.nome, registro.*, (SELECT ter.bairro FROM ter WHERE ter.id = mapa.bairro_id) as bairro, (SELECT login.nome FROM login WHERE login.id = registro.pub_id) as publicador FROM registro LEFT JOIN mapa ON registro.mapa_id = mapa.id WHERE '.$where.' ORDER BY registro.data_visita DESC LIMIT 0, 10 ');
            if($params['surdoid'] != 0) {   $abc->bindValue(':surdoid', $params['surdoid'], PDO::PARAM_INT); }
            if($params['pubid'] != 0) {     $abc->bindValue(':pubid', $params['pubid'], PDO::PARAM_INT); }
            $abc->execute();

            if($abc->rowCount() > 0)  {
                $x = $abc->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($x);
            } else {
                echo '{0}';
            }

            /**
             * LOG DE ATIVIDADES
             */
            $log = new LOG();
            $log->novo(LOG::TIPO_CONSULTA, 'buscou registros '.$logStr.'.');
        }
    }

    function ultimos()
    {
        $abc = $this->pdo->query('SELECT mapa.nome, registro.*, (SELECT ter.bairro FROM ter WHERE ter.id = mapa.bairro_id) as bairro, (SELECT login.nome FROM login WHERE login.id = registro.pub_id) as publicador FROM registro LEFT JOIN mapa ON registro.mapa_id = mapa.id WHERE 1 ORDER BY registro.data_visita DESC LIMIT 0, 5 ');
        if($abc->rowCount() > 0) {
            $x = $abc->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($x);
        } else {
            echo '{0}';
        }
    }

    function deleta(int $regid)
    {
        
        $abc = $this->pdo->prepare('SELECT id FROM registro WHERE id = :regid');
        $abc->bindValue(':regid', $regid, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() > 0) {
            $reg = $abc->fetch(PDO::FETCH_OBJ);
            // Existe!
            $abc = $this->pdo->prepare('DELETE FROM registro WHERE id = :regid');
            $abc->bindValue(':regid', $regid, PDO::PARAM_INT);
            try {
                $abc->execute();

                /**
                 * LOG DE ATIVIDADES
                 */
                $log = new LOG();
                $mapa = new Mapa();
                $s = $mapa->surdoId($reg->mapa_id);
                $log->novo(LOG::TIPO_REMOVE, 'apagou registro [ID: '.$regid.'] do surdo <i>'.$s->nome.' ['.$s->bairro.']</i>.');
                echo true;
            } catch(PDOException $e) {
                echo 'Falha no BD: '.$e->getMessage();
            }
            
        } else {
            // Não existe
            echo true;

        }
        
    }

    function salva() { // EDITAR REGISTRO

    }
    
}