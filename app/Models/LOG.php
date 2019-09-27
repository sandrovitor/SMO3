<?php
include_once('Model.php');

class LOG extends Model {
    protected $tabela = 'log';
    protected $pdo;


    function __construct()
    {
        parent::__construct();
    }
    
    public function novo(int $tipo, $evento, $usuid)
    {

        //11021994 (CONCLUIR E TESTAR)
        $abc=$this->pdo->prepare('INSERT INTO `log` (evento, tipo, pub_id) VALUES (:evento, :tipo, :usuario)');
        $abc->bindValue(':tipo', $tipo, PDO::PARAM_INT);
        $abc->bindValue(':evento', addslashes($evento), PDO::PARAM_STR);
        $abc->bindValue(':usuario', $usuid, PDO::PARAM_INT);

        $abc->execute();
        return true;
    }

    public function get($tipo = 0, $usuario = 0, $inicio = 0, $qtd = 25)
    {
        // Cria filtro de pesquisa
        switch($tipo) {
            case 0:
            default:
                $sqlTipo = '';
                break;

            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $sqlTipo = 'log.tipo = '.$tipo;
        }

        if($usuario > 0) {
            $sqlPUB = 'log.pub_id = '.$usuario;
        } else {
            $sqlPUB = '';
        }

        if($sqlTipo == '' && $sqlPUB == '') {
            $sqlFiltro = '1';
        } else if($sqlTipo != '' && $sqlPUB != '') {
            $sqlFiltro = $sqlTipo.' AND '.$sqlPUB;
        } else if($sqlTipo != '') {
            $sqlFiltro = $sqlTipo;
        } else if($sqlPUB != '') {
            $sqlFiltro = $sqlPUB;
        }

        $sql = 'SELECT log.*, CONCAT(login.nome, " ", login.sobrenome) as nome FROM log LEFT JOIN login ON log.pub_id = login.id WHERE '.$sqlFiltro.' ORDER BY log.data DESC LIMIT '.$inicio.', '.$qtd;
        $abc = $this->pdo->query($sql);
        if($abc->rowCount() > 0) {
            $res = $abc->fetchAll(PDO::FETCH_OBJ);
        } else {
            $res = array();
        }

        $def = $this->pdo->query('SELECT log.id FROM log WHERE '.$sqlFiltro);


        $consulta = array('parcial' => count($res), 'resultado' => $res, 'total' => $def->rowCount());
        return json_encode($consulta);
    }

    public function getHTML($tipo = 0, $usuario = 0, $inicio = 0, $qtd = 25)
    {
        $consulta = $this->get($tipo, $usuario, $inicio, $qtd);
        $consulta = json_decode($consulta);


        $res = $consulta->resultado;
        if(count($res) < 1) {
            return 'Nada encontrado';
        } else {
            switch($tipo) {
                case 0:
                default:
                    $sqlTipo = '1';
                    break;
    
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    $sqlTipo = 'tipo = '.$tipo;
            }

            $html = '<div class="row"><div class="col-12">'.
            '<table class="table table-sm table-hover table-striped"><thead style="font-size:.75rem;"><tr> <th>TIPO</th> <th>Usuário</th> <th>Detalhe do Evento</th> <th>Data</th> </tr></thead><tbody style="font-family:Courier, serif;">';

            // Varre resultado
            foreach($res as $r) {
                $data = new DateTime($r->data);
                $data = $data->format('d/m/Y H:i:s');
                $bg_row = '';
                switch((int)$r->tipo) {
                    case 1:
                        $op = '<span class="badge badge-success" title="CADASTRO" data-toggle="tooltip">CAD</span>';
                        $bg_row = 'bg-success text-white';
                        break;

                    case 2:
                        $op = '<span class="badge badge-primary" title="ATUALIZAÇÃO" data-toggle="tooltip">ATU</span>';
                        break;

                    case 3:
                        $op = '<span class="badge badge-warning" title="REMOÇÃO" data-toggle="tooltip">REM</span>';
                        $bg_row = 'bg-warning';
                        break;

                    case 4:
                        $op = '<span class="badge badge-info" title="CONSULTA" data-toggle="tooltip">CON</span>';
                        break;

                    case 5:
                        $op = '<span class="badge badge-danger" title="ERRO" data-toggle="tooltip">ERR</span>';
                        $bg_row = 'bg-danger';
                        break;

                    case 6:
                        $op = '<span class="badge badge-dark" title="SISTEMA" data-toggle="tooltip">SIS</span>';
                        $bg_row = 'bg-dark text-white';
                        break;

                    case 7:
                        $op = '<span class="badge badge-primary" title="SMO Mobile" data-toggle="tooltip">MOB</span>';
                        $bg_row = 'bg-primary';
                        break;

                    default:
                        $op = '<span class="badge badge-light" title="DESCONHECIDO" data-toggle="tooltip">###</span>';
                        break;
                }
                
                if($r->pub_id == 0) {
                    $r->nome = 'SISTEMA';
                }
                $html.= '<tr> <td>'.$op.'</td> <td><strong>'.$r->nome.'<strong></td> <td>'.$r->evento.'</td> <td>'.$data.'</td> </tr>';
            }


            $html .= '</tbody></table>'
            .'</div></div>';

            /**
             * 
             * PAGINAÇÃO
             * 
             */

            $pagination = '';
            // Resultado total da pesquisa

            $total = $consulta->total;
            $parcial = $consulta->parcial;

            //  Página atual
            if($inicio > 0) {
                $pAtual = (int) ($inicio / $qtd) + 1;
            } else {
                $pAtual = 1;
            }

            // Total de páginas
            if($total / $qtd <= 1) { // Há somente uma página
                $pTotal = 1;
            } else { // Há mais de uma página
				$pTotal = (int) ($total / $qtd);
				
                if($total % $qtd > 0) { // No caso de numero quebrados, verifica se há modulo e acrescenta uma página
                    $pTotal++;
                }

            }





            // Variáveis de paginação
            $pagAnterior = $pagProximo = ''; // Desativado ou não
            $pagAnteriorLink = $pagProximoLink = ''; // Link do botão Anterior e Proximo
            $pagNumeros = ''; // Números da página

            if($pTotal == 1) { // Total de páginas igual a 1
                $pagAnterior = $pagProximo = 'disabled';
                $pagNumeros = 
                '<li class="page-item active"><a class="page-link" href="#">1</a></li>';

            } else if($pTotal > 1 && $pTotal <= 4) { // Total de páginas entre 2 e 4
                // Primeiro verifica se é a primeira ou última página
                if($pAtual == 1) { // Primeira página
                    $pagAnterior = 'disabled';
                    $pagProximoLink = 'onclick="adm_getLog(2)"';
                } else if($pAtual == $pTotal) { // Última página
                    $pagProximo = 'disabled';
                    $pagAnteriorLink = 'onclick="adm_getLog('. ($pAtual - 1) .')"';
                } else {
                    $pagAnteriorLink = 'onclick="adm_getLog('. ($pAtual - 1) .')"';
                    $pagProximoLink = 'onclick="adm_getLog('. ($pAtual + 1) .')"';
                }

                $x = 1;
                while($x <= $pTotal) {
                    if($x == $pAtual) {
                        $pagNumeros .= 
                            '<li class="page-item active"><a class="page-link" href="javascript:void(0)">'.$x.'</a></li>';
                    } else {
                        $pagNumeros .= 
                            '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog('.$x.')">'.$x.'</a></li>';
                    }
                    $x++;
                }
            } else { // Total de páginas superior a quatro
                // Primeiro verifica se é a primeira ou última página
                if($pAtual == 1) { // Primeira página
                    $pagAnterior = 'disabled';
                    $pagProximoLink = 'onclick="adm_getLog(2)"';
                    $pagNumeros .= 
                            '<li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog('.$pTotal.')">'.$pTotal.'</a></li>';
                } else if($pAtual == $pTotal) { // Última página
                    $pagProximo = 'disabled';
                    $pagAnteriorLink = 'onclick="adm_getLog('. ($pAtual - 1) .')"';
                    $pagNumeros .= 
                            '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog(1)">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item active"><a class="page-link" href="#">'.$pAtual.'</a></li>';
                } else if($pAtual == 2) { // Segunda página
                    $pagAnteriorLink = 'onclick="adm_getLog(1)"';
                    $pagProximoLink = 'onclick="adm_getLog(3)"';
                    $pagNumeros .= 
                            '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog(1)">1</a></li>
                            <li class="page-item active"><a class="page-link" href="#">2</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog('.$pTotal.')">'.$pTotal.'</a></li>';
                } else if($pAtual == $pTotal - 1) { // Penúltima página
                    $penultima = $pTotal - 1;
                    $pagAnteriorLink = 'onclick="adm_getLog('. ($pAtual - 2) .')"';
                    $pagProximoLink = 'onclick="adm_getLog('.$pTotal.')"';
                    $pagNumeros .= 
                            '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog(1)">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item active"><a class="page-link" href="#">'.$pAtual.'</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog('.$pTotal.')">'.$pTotal.'</a></li>';
                } else {
                    $pagAnteriorLink = 'onclick="adm_getLog('. ($pAtual - 1) .')"';
                    $pagProximoLink = 'onclick="adm_getLog('. ($pAtual + 1) .')"';
                    $pagNumeros .= 
                            '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog(1)">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item active"><a class="page-link" href="#">'.$pAtual.'</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="adm_getLog('.$pTotal.')">'.$pTotal.'</a></li>';
                }
            }


            // html da paginação.
            $pagination =
                '<div class="row">
                    <div class="col-12 text-center">
                        <small>Mostrando '.$parcial.' de '.$total.' resultado(s).</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <ul class="pagination justify-content-start">
                            <li class="page-item '.$pagAnterior.'"><a class="page-link" href="javascript:void(0)" '.$pagAnteriorLink.'>Anterior</a></li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <ul class="pagination justify-content-center">
                            '.$pagNumeros.'
                        </ul>
                    </div>
                    <div class="col-4">
                        <ul class="pagination justify-content-end">
                            <li class="page-item '.$pagProximo.'"><a class="page-link" href="javascript:void(0)" '.$pagProximoLink.'>Próximo</a></li>
                        </ul>
                    </div>
                </div>
                ';

            //var_dump($pAtual, $pTotal, $total, $parcial);
            $html .= $pagination;
            return $html;
        }
    }
}