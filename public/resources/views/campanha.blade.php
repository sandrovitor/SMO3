@extends('layouts.layoutindex')

@php
    $config = new Config();
    $mapa = new Mapa();
    $campanha = $config->get('campanha');
    $hoje = new DateTime();
    

    if($hoje > $campanha['FinalDateTime']) {
        $campanhaFinalizada = '<br><span class="badge badge-dark" style="font-size: 1.2rem;">FINALIZADA!</span>';
    }

@endphp

@section ('paginaCorrente', 'Campanha')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Campanha</li>
@endsection

@section('conteudo')
    @if($campanha['ativo'] == 'not')
        <div class="text-center border border-primary rounded-sm mb-2 p-3 shadow-sm">
            <strong>Infelizmente não há campanhas ativas.</strong>
        </div>
        
    
    @elseif($campanha['InicioDateTime'] > $hoje)
        <div class="text-center border border-primary rounded-sm mb-2 p-3 shadow-sm">
            <h3>
                <span class="badge badge-light">CAMPANHA {{$campanha['nome']}}</span>
            </h3><br>
            <strong>Início da Campanha:</strong> <span class="badge badge-secondary">{{$campanha['InicioDateTime']->format('d/m/Y')}}</span>
        </div>

    @else

        <div class="row">
            <div class="col-12 col-sm-12 text-center">
                <h3>
                    <span class="badge badge-light">CAMPANHA {{$campanha['nome']}}</span><br>
                    <small class="text-muted">Período: {{$campanha['InicioDateTime']->format('d/m/Y')}} a {{$campanha['FinalDateTime']->format('d/m/Y')}}</small>
                </h3>
                {!! $campanhaFinalizada or '' !!}
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header py-2">
                        <a href="javascript:void(0)" data-toggle="collapse" data-target="#surdos-body"><strong>SURDOS</strong></a>
                        &nbsp; <span class="glyphicon glyphicon-question-sign" data-toggle="popover" data-trigger="hover" title="O que é isto?" data-content="Uma lista de surdos que receberam ou não o convite/publicação da campanha.<br><br> Lista pode ser filtrada."></span>
                    </div>
                    <div id="surdos-body" class="collapse ">
                        <div class="card-body p-2" style="overflow-x:hidden; overflow-y:auto; max-height: calc(100vh - 250px);">
                            <div class="row">
                                <div class="col-12">
                                    @php
                                    
                                        $listaSurdos = $mapa->campanhaResultado($campanha['inicio'], $campanha['fim']);
                                        $totalSurdos = count($listaSurdos);
                                        $totalCampanhaRecebido = 0;
                                        for($i=0; $i < $totalSurdos; $i++) {
                                            if($listaSurdos[$i]->campanha == '1') {
                                                $totalCampanhaRecebido++;
                                            }
                                        }
                                        
                                        //var_dump($listaSurdos);
                                    @endphp
                                    <input type="text" class="form-control" data-toggle="filter" data-target="#table-campanha" placeholder="Digite um bairro ou nome para pesquisar"><br>
                                    <div class="text-center">
                                        Total de surdos: <span class="badge badge-secondary">{{$totalSurdos}}</span> | Surdos encontrados: <span class="badge badge-success">{{$totalCampanhaRecebido}}</span>
                                    </div>
                                    <div class="text-center" id="filter_conta"></div>

                                    <hr><br>
                                    <table class="table table-sm" style="font-size: .9rem" id="table-campanha">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Bairro</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($listaSurdos as $r)
                                            <tr>
                                                <td>{{$r->nome}}</td>
                                                <td>{{$r->bairro}}</td>
                                                <td>
                                                @if ($r->campanha == '1')
                                                    <span class="badge badge-success p-1">RECEBIDO</span>
                                                @else
                                                    <span class="badge badge-secondary p-1">PENDENTE</span>
                                                @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div id="tabela-msg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-7">
                <div class="card">
                    <div class="card-header py-2">
                        <a href="javascript:void(0)" data-toggle="collapse" data-target="#grafico-body"><strong>GRÁFICO DE COBERTURA DA CAMPANHA</strong></a></div>
                    <div id="grafico-body" class="collapse ">
                        <div class="card-body">
                            <kbd>BREVE! Em desenvolvimento...</kbd>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
    @endif
@endsection