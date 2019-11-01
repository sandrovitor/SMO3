<?php
include_once('Model.php');

class Relatorio extends Model {
    protected $maGeral = '';
    protected $pdo;
    protected $periodoIni;
    protected $periodoFim;


    public function __construct()
    {
        parent::__construct();
        $abc = $this->pdo->query('SELECT valor FROM config WHERE opcao = "data_visita_prox"');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $this->periodoFim = $reg->valor;

        
        $abc = $this->pdo->query('SELECT valor FROM config WHERE opcao = "data_visita_ult"');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $this->periodoIni = $reg->valor;

    }
    
    function getPeriodoIni()
    {
        return new DateTime($this->periodoIni);
    }

    function getPeriodoFim()
    {
        return new DateTime($this->periodoFim);
    }

    public function Geral()
    {
        $html = '';
        $hoje = new DateTime();
        // Total de bairros
        $abc = $this->pdo->query('SELECT id FROM ter WHERE 1');
        $bairrosQtd = $abc->rowCount();

        // Total de Registros
        $abc = $this->pdo->query('SELECT id FROM registro WHERE 1');
        $registrosQtd = $abc->rowCount();
        
        // Total de Endereços no Histórico
        $abc = $this->pdo->query('SELECT id FROM historico_mapa WHERE 1');
        $historicoQtd = $abc->rowCount();
        
        // Total de logins
        $abc = $this->pdo->query('SELECT COUNT(qtd_login) as qtd FROM `login` WHERE 1');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $loginQtd = $reg->qtd;

        // Total de surdos
        $abc = $this->pdo->query('SELECT id FROM mapa WHERE 1');
        $surdosQtd = $abc->rowCount();

        // Total de surdos já encontrados
        $abc = $this->pdo->query('SELECT DISTINCT mapa.id FROM `mapa` LEFT JOIN registro on mapa.id = registro.mapa_id WHERE registro.encontrado = TRUE');
        $surdosEnc = $abc->rowCount();
        $surdosEncP = ($surdosEnc * 100)/ $surdosQtd;
        
        // Categoria dos surdos (ativo, oculto, desativado)
        $abc = $this->pdo->query('SELECT (SELECT COUNT(id) FROM mapa WHERE ativo = 0) as desativado, (SELECT COUNT(id) FROM mapa WHERE ocultar = 1) as oculto, (SELECT COUNT(id) FROM mapa WHERE ativo = 1 AND ocultar = 0) as ativo FROM mapa LIMIT 0,1');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $surdoAtivo = $reg->ativo;              $surdoAtivoP = round(($surdoAtivo * 100) / $surdosQtd, 0);
        $surdoOculto = $reg->oculto;            $surdoOcultoP = round(($surdoOculto * 100) / $surdosQtd, 0);
        $surdoDesativado = $reg->desativado;    $surdoDesativadoP = round(($surdoDesativado * 100) / $surdosQtd, 0);

        // Publicadores
        $abc = $this->pdo->query('SELECT login.*,
        (SELECT COUNT(registro.id) FROM registro WHERE registro.pub_id = login.id) as registros,
        (SELECT COUNT(mapa.id) FROM mapa WHERE mapa.resp_id = login.id) as estudos
        FROM login WHERE 1 ORDER BY login.nome ASC, login.sobrenome ASC');
        $pubTotal = $abc->rowCount();
        $publicadores = $abc->fetchAll(PDO::FETCH_OBJ);

        $abc = $this->pdo->query('SELECT
        (SELECT COUNT(id) FROM login WHERE nivel = 0) as n0,
        (SELECT COUNT(id) FROM login WHERE nivel = 1) as n1,
        (SELECT COUNT(id) FROM login WHERE nivel = 2) as n2,
        (SELECT COUNT(id) FROM login WHERE nivel = 3) as n3,
        (SELECT COUNT(id) FROM login WHERE nivel = 4) as n4,
        (SELECT COUNT(id) FROM login WHERE nivel = 5) as n5
        FROM `login` LIMIT 0,1');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $pubNivel0 = $reg->n0;      $pubNivel0P = round(($pubNivel0 * 100) / $pubTotal);
        $pubNivel1 = $reg->n1;      $pubNivel1P = round(($pubNivel1 * 100) / $pubTotal);
        $pubNivel2 = $reg->n2;      $pubNivel2P = round(($pubNivel2 * 100) / $pubTotal);
        $pubNivel3 = $reg->n3;      $pubNivel3P = round(($pubNivel3 * 100) / $pubTotal);
        $pubNivel4 = $reg->n4;      $pubNivel4P = round(($pubNivel4 * 100) / $pubTotal);
        $pubNivel5 = $reg->n5;      $pubNivel5P = round(($pubNivel5 * 100) / $pubTotal);



        $html .= '
        <h4><strong>Relatório Geral</strong> <small class="text-muted"><i>(gerado em '.$hoje->format('d/m/Y \à\s H:i:s').')</i></small></h4>
        <br>
        <div class="border border-info bg-light rounded-lg p-2">
            <h6 class="mb-3"><strong>DADOS DO SMO</strong></h6>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <strong>Quantidade de Bairros</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:100%;height:20px">'.$bairrosQtd.'</div>
                    </div>
                    <br>


                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <strong>Registros de Visita</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:100%;height:20px">'.$registrosQtd.'</div>
                    </div>
                    <br>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <strong>Endereços no Histórico</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:100%;height:20px">'.$historicoQtd.'</div>
                    </div>
                    <br>

                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <strong>Quantidade de Acessos</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:100%;height:20px">'.$loginQtd.'</div>
                    </div>
                    <br>
                
                </div>
            </div>
        </div>

        <hr>
        <div class="border border-info bg-light rounded-lg p-2">
            <h6 class="mb-3"><strong>SURDOS</strong></h6>
            <div class="row">
                <div class="col-12 col-md-6">
                    <strong>Total de Surdos</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:100%;height:20px">'.$surdosQtd.'</div>
                    </div>
                    <br>

                </div>
                <div class="col-12 col-md-6">
                    <strong>Surdos Já Encontrados</strong>
                    <div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:'.$surdosEncP.'%;height:20px">'.$surdosEnc.'</div>
                    </div>
                    <br>

                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <strong>Total de Surdos</strong>
                    <div class="progress" style="height:40px">
                        <div class="progress-bar bg-success" data-toggle="tooltip" title="'.$surdoAtivo.' ativos" style="width:'.$surdoAtivoP.'%;height:40px">
                            '.$surdoAtivoP.'%
                        </div>
                        <div class="progress-bar bg-warning" data-toggle="tooltip" title="'.$surdoOculto.' ocultos" style="width:'.$surdoOcultoP.'%;height:40px">
                            '.$surdoOcultoP.'%
                        </div>
                        <div class="progress-bar bg-danger" data-toggle="tooltip" title="'.$surdoDesativado.' desativados" style="width:'.$surdoDesativadoP.'%;height:40px">
                            '.$surdoDesativadoP.'%
                        </div>
                    </div>
                    <br>

                </div>
            </div>

        </div>

        <hr>
        <div class="border border-dark bg-light px-2 py-3">
            <h6 class="mb-3"><strong>PUBLICADORES</strong></h6>
            <div class="row">
                <div class="col-12 ">
                    <strong>Total de Publicadores </strong> <span class="badge badge-dark"> '.$pubTotal.' </span>
                    <div class="progress" style="height:30px">
                        <div class="progress-bar" style="width:'.$pubNivel0P.'%;height:30px;background-color:#a6a6a6;" data-toggle="tooltip" title="Nível 0: Sem acesso.">'.$pubNivel0.'</div>
                        <div class="progress-bar" style="width:'.$pubNivel1P.'%;height:30px;background-color:#8c8c8c;" data-toggle="tooltip" title="Nível 1: Visitante.">'.$pubNivel1.'</div>
                        <div class="progress-bar" style="width:'.$pubNivel2P.'%;height:30px;background-color:#737373;" data-toggle="tooltip" title="Nível 2: Publicador.">'.$pubNivel2.'</div>
                        <div class="progress-bar" style="width:'.$pubNivel3P.'%;height:30px;background-color:#595959;" data-toggle="tooltip" title="Nível 3: Pioneiro Regular.">'.$pubNivel3.'</div>
                        <div class="progress-bar" style="width:'.$pubNivel4P.'%;height:30px;background-color:#404040;" data-toggle="tooltip" title="Nível 4: Ancião.">'.$pubNivel4.'</div>
                        <div class="progress-bar" style="width:'.$pubNivel5P.'%;height:30px;background-color:#1a1a1a;" data-toggle="tooltip" title="Nível 5: Administrador.">'.$pubNivel5.'</div>
                    </div>
                    <br>

                </div>
            </div>
            <div class="row">
                <div class="col-12 " style="overflow-x:auto;">
                    <table class="table table-bordered" style="font-size: .8rem;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Publicador</th>
                                <th>Nível</th>
                                <th>Data Criação</th>
                                <th>Acessos</th>
                                <th>Último Login</th>
                                <th>Registros de Visita</th>
                                <th>Estudos</th>
                                <th>Validade</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">';

        foreach($publicadores as $p) {
            $criado = new DateTime($p->criado);
            $atualizado = new DateTime($p->atualizado);
            $expira = new DateTime($p->expira);
            $html.= '
                            <tr>
                                <td>'.$p->nome.' '.$p->sobrenome.'</td>
                                <td class="text-center">'.$p->nivel.'</td>
                                <td class="text-center">'.$criado->format('d/m/Y').'</td>
                                <td class="text-center">'.$p->qtd_login.'</td>
                                <td class="text-center">'.$atualizado->format('d/m/Y \à\s H:i').'</td>
                                <td class="text-center">'.$p->registros.'</td>
                                <td class="text-center">'.$p->estudos.'</td>
                                <td class="text-center">'.$expira->format('d/m/Y \à\s H:i').'</td>
                            </tr>
            ';
        }
                        $html .='

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        ';

        return $html;
    }

    public function EntreVisitas()
    {
        $html = '';
        $hoje = new DateTime();
        $periodoIni = new DateTime($this->periodoIni);
        $periodoFim = new DateTime($this->periodoFim);

        // Total de surdos
        $abc = $this->pdo->query('SELECT mapa.id, mapa.nome, mapa.mapa, mapa.ativo, mapa.ocultar, mapa.be, ter.bairro,
        (SELECT COUNT(registro.id) FROM registro WHERE registro.mapa_id = mapa.id AND registro.data_visita >= "'.$this->periodoIni.'" AND registro.data_visita < "'.$this->periodoFim.'") as registros,
        IF( (SELECT COUNT(registro.id) as registros FROM registro WHERE registro.mapa_id = mapa.id AND registro.data_visita >= "'.$this->periodoIni.'" AND registro.data_visita < "'.$this->periodoFim.'" AND registro.encontrado = 1) > 0, TRUE, FALSE) as encontrado
        FROM mapa LEFT JOIN ter ON mapa.bairro_id = ter.id
        ORDER BY ter.regiao ASC, ter.bairro ASC, mapa.nome ASC');
        $surdosQtd = $abc->rowCount();
        $surdos = $abc->fetchAll(PDO::FETCH_OBJ);

        $surdosEnc = 0; $surdosBE = 0; $listaEnc = ''; $listaNEnc = ''; $b1 = ''; $b2 = '';
        $countEnc = 0; $countNEnc = 0;
        foreach($surdos as $s) {
            if(($s->encontrado == 1 || $s->be == 1) && $s->ativo == 1) {
                $surdosEnc++;

                if($b1 == '' || $b1 != $s->bairro) {
                    $b1 = $s->bairro;
                    $listaEnc .= '<h6 class="mt-3"><strong>'.$b1.'</strong></h6> ';
                }

                if($s->be == 1) {
                    $listaEnc .= $s->nome.' <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda"><i class="fas fa-heart"></i></span><br> ';
                } else {
                    $listaEnc .= $s->nome.'<br> ';
                }
                
                $countEnc++;
            }
            if($s->be == 1 && $s->ativo == 1) {
                $surdosBE++;
            }

            if($s->encontrado == 0 && $s->be == 0 && $s->ativo == 1) {
                if($b2 == '' || $b2 != $s->bairro) {
                    $b2 = $s->bairro;
                    $listaNEnc .= '<h6 class="mt-3"><strong>'.$b2.'</strong></h6> ';
                }

                $listaNEnc .= $s->nome.'<br> ';
                $countNEnc++;
            }
        }

        unset($b1, $b2);
        
        // Categoria dos surdos (ativo, oculto, desativado)
        $abc = $this->pdo->query('SELECT (SELECT COUNT(id) FROM mapa WHERE ocultar = 1) as oculto, (SELECT COUNT(id) FROM mapa WHERE ativo = 1 AND ocultar = 0) as ativo FROM mapa LIMIT 0,1');
        $reg = $abc->fetch(PDO::FETCH_OBJ);
        $surdoTerritorioTotal = $reg->ativo + $reg->oculto;
        $surdoAtivo = $reg->ativo;              $surdoAtivoP = round(($surdoAtivo * 100) / $surdoTerritorioTotal, 0);
        $surdoOculto = $reg->oculto;            $surdoOcultoP = round(($surdoOculto * 100) / $surdoTerritorioTotal, 0);


        // Total de surdos encontrados
        $surdosEncP = ($surdosEnc * 100)/ $surdoTerritorioTotal;

        // Total de surdos BE.
        $surdosBEP = ($surdosBE * 100)/ $surdoTerritorioTotal;

        $html .= '
        <h4><strong>Relatório entre Visitas</strong> [<i>'.$periodoIni->format('d/m/Y').' a '.$periodoFim->format('d/m/Y').'</i>] <br><small class="text-muted"><i>(gerado em '.$hoje->format('d/m/Y \à\s H:i:s').')</i></small></h4>
        <br>

        <div class="row">
            <div class="col-12">
                <strong>Total de SURDOS</strong> <span class="badge badge-dark">'.$surdoTerritorioTotal.'</span>
                <div class="progress" style="height:20px">
                    <div class="progress-bar bg-success" data-toggle="tooltip" title="'.$surdoAtivo.' ativos" style="width:'.$surdoAtivoP.'%;height:20px">
                        '.$surdoAtivoP.'%
                    </div>
                    <div class="progress-bar bg-info" data-toggle="tooltip" title="'.$surdoOculto.' ocultos" style="width:'.$surdoOcultoP.'%;height:20px">
                        '.$surdoOcultoP.'%
                    </div>
                </div>
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-12">
                <strong>SURDOS encontrados</strong> <span class="badge badge-primary px-2">'.($surdosEnc - $surdosBE).'</span>
                <span class="d-none d-sm-inline">&nbsp; | &nbsp;</span>
                <br class="d-block d-sm-none">
                <strong>SURDOS Bíblia Estuda Já</strong> <span class="badge badge-info px-2">'.$surdosBE.'</span>
                <div class="progress" style="">
                    <div class="progress-bar bg-primary" style="width:'.($surdosEncP - $surdosBEP).'%;" title="Surdos Encontrados que não Estudam a Biblia: '.($surdosEnc - $surdosBE).'" data-toggle="tooltip">
                        '.round($surdosEncP - $surdosBEP).'%
                    </div>
                    <div class="progress-bar bg-info" style="width:'.$surdosBEP.'%;" title="Surdos que Estudam a Biblia: '.$surdosBE.'" data-toggle="tooltip">
                        '.round($surdosBEP).'%
                    </div>
                </div>
            </div>
        </div>

        <!--
        <div class="row">
            <div class="col-12 col-md-6">
                <strong>SURDOS encontrados</strong> <span class="badge badge-dark">'.$surdosEnc.'</span>
                <div class="progress" style="">
                    <div class="progress-bar" style="width:'.$surdosEncP.'%;">
                        '.round($surdosEncP).'%
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <strong>SURDOS Bíblia Estuda Já</strong> <span class="badge badge-dark">'.$surdosBE.'</span>
                <div class="progress" style="">
                    <div class="progress-bar" style="width:'.$surdosBEP.'%;">
                        '.round($surdosBEP).'%
                    </div>
                </div>
            </div>
        </div>
        -->
        <br>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div onclick="cardBodyCollapse(this)" style="cursor:pointer">ENCONTRADOS <span class="badge badge-light">'.$countEnc.'</span></div>
                    </div>
                    <div class="card-body collapse pt-1" style="overflow-y: auto; height: calc(100vh - 280px);">
                        '.$listaEnc.'
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <div onclick="cardBodyCollapse(this)" style="cursor:pointer">NÃO ENCONTRADOS <span class="badge badge-light">'.$countNEnc.'</span></div>
                    </div>
                    <div class="card-body collapse pt-1" style="overflow-y: auto; height: calc(100vh - 280px);">
                        '.$listaNEnc.'
                    </div>
                </div>
            </div>
        </div>
        ';
        

        return $html;
    }

}