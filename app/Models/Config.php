<?php
include_once('Model.php');

class Config extends Model {
    protected $tabela = 'config';
    protected $pdo;
    protected $periodoIni;
    protected $periodoFim;
    protected $social;
    protected $campanha;
    protected $versao;
    protected $versaoData;
    protected $regiao = array();
    protected $printestilo;


    function __construct()
    {
        parent::__construct();

        $abc = $this->pdo->query('SELECT * FROM config WHERE 1');
        $resultado = $abc->fetchAll(PDO::FETCH_OBJ);

        foreach ($resultado as $r) {
            switch($r->opcao) {
                case 'data_visita_prox':
                    $this->periodoFim = $r->valor;
                    break;

                case 'data_visita_ult':
                    $this->periodoIni = $r->valor;
                    break;

                case 'versao':
                    $this->versao = $r->valor;
                    break;

                case 'versao_data':
                    $this->versaoData = $r->valor;
                    break;

                case 'social_ativa':
                    $this->social['ativo'] = $r->valor;
                    break;

                case 'social_data':
                    $this->social['data'] = $r->valor;
                    break;

                case 'social_duracao':
                    $this->social['duracao'] = $r->valor;
                    break;

                case 'campanha_ativa':
                    $this->campanha['ativo'] = $r->valor;
                    break;

                case 'campanha_inicio':
                    $this->campanha['inicio'] = $r->valor;
                    break;

                case 'campanha_fim':
                    $this->campanha['fim'] = $r->valor;
                    break;

                case 'campanha_nome':
                    $this->campanha['nome'] = $r->valor;
                    break;

                case strpos($r->opcao, 'regiao_') !== false:
                    $x = explode('_', $r->opcao);
                    $key = $x[1];
                    $this->regiao[(int)$x[1]] = $r->valor;
                    break;

                case 'print_estilo':
                    $this->printestilo = $r->valor;
            }
        }

        // Ultimos calculos
        // Social
        $this->social['InicioDateTime'] = new DateTime($this->social['data']);
        $this->social['FinalDateTime'] = new DateTime($this->social['data']);
        $x = explode(':', $this->social['duracao']);
        $this->social['FinalDateTime'] = $this->social['FinalDateTime']->add(new DateInterval('PT'.$x[0].'H'.$x[1].'M'));

        // Campanha
        $this->campanha['InicioDateTime'] = new DateTime($this->campanha['inicio']);
        $this->campanha['FinalDateTime'] = new DateTime($this->campanha['fim']);
        $this->campanha['FinalDateTime']->setTime(23,59,59);
    }

    function get(string $nomeVariavel)
    {
        return $this->$nomeVariavel;
    }

    function set(string $nomeVariavel, $valor)
    {
        $abc = $this->pdo->prepare('SELECT * FROM config WHERE opcao = :op');
        $abc->bindValue(':op', $nomeVariavel, PDO::PARAM_STR);
        $abc->execute();
        if($abc->rowCount() > 0) {
            $abc = $this->pdo->prepare('UPDATE config SET valor = :val WHERE opcao = :op');
            $abc->bindValue(':val', $valor, PDO::PARAM_STR);
            $abc->bindValue(':op', $nomeVariavel, PDO::PARAM_STR);
            $abc->execute();
            
            $this->__construct();
            
            $log = new LOG();
            $log->novo(LOG::TIPO_SISTEMA, 'alterou valor da configuração <i>'.$nomeVariavel.'</i> para: <kbd>'.$valor.'</kbd>.');
            return true;
        } else {
            return false;
        }
    }

    function setConfigGeral(string $uVisita, string $pVisita, string $versao, string $versaoData) {
        $abc = $this->pdo->prepare('UPDATE config SET `valor` = :uVisita WHERE `opcao` = "data_visita_ult"; '.
        'UPDATE config SET `valor` = :pVisita WHERE `opcao` = "data_visita_prox"; '.
        'UPDATE config SET `valor` = :versao WHERE `opcao` = "versao"; '.
        'UPDATE config SET `valor` = :versaoData WHERE `opcao` = "versao_data"; ');
        try {
            $abc->bindValue(':uVisita', $uVisita, PDO::PARAM_STR);
            $abc->bindValue(':pVisita', $pVisita, PDO::PARAM_STR);
            $abc->bindValue(':versao', $versao, PDO::PARAM_STR);
            $abc->bindValue(':versaoData', $versaoData, PDO::PARAM_STR);

            $abc->execute();
            
            $log = new LOG();
            $log->novo(LOG::TIPO_SISTEMA, 'atualizou as configurações gerais do sistema. Última Visita: <pre>'.$uVisita.'</pre>; Próxima Visita: <pre>'.$pVisita.'</pre>; Versão: <pre>'.$versao.'</pre>.');
            return true;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }


}