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
                    $this->periodoIni = $r->valor;
                    break;

                case 'data_visita_ult':
                    $this->periodoFim = $r->valor;
                    break;

                case 'versao':
                    $this->versao = $r->valor;
                    break;

                case 'versao_data':
                    $this->versaoData = $r->valor;
                    break;

                case 'conferencia_ativa':
                    $this->social['ativo'] = $r->valor;
                    break;

                case 'conferencia_data':
                    $this->social['data'] = $r->valor;
                    break;

                case 'conferencia_duracao':
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
            
            return true;
        } else {
            return false;
        }
    }


}