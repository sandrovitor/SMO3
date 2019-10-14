@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSurdo')}}">Surdos</a></li>
    <li class="breadcrumb-item active">Pendências</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header py-2"><a href="#" data-toggle="collapse" data-target="#pendencias-lista">Pendências</a></div>
                <div class="card-body collapse show p-2" id="pendencias-lista" style="overflow-y:auto; max-height: 60vh; height: 100%;">
                    <div class="list-group">
                        @if($pendencias == false)
                        <strong>Não há pendências</strong>
                        @else
                            @foreach($pendencias as $p)
                            @php
                                if($p->mapa_id == 0) {
                                    // NOVO
                                    $status = '<span class="badge badge-success">NOVO</span>';
                                } else {
                                    // EDITADO
                                    $status = '<span class="badge badge-info">EDITADO</span>';
                                }
                            @endphp
                            <a href="#" onclick="showPendencia({{$p->id}});" class="py-1 px-2 list-group-item list-group-item-action">{!!$status!!} <strong>{{$p->nome}}</strong> - {{$p->bairro}}</i></a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
        <div class="col-12 col-lg-8" id="pendencias-dados">
        @if($pendencias !== false)
            @foreach($pendencias as $p)
            @php
                $cad_data = new DateTime($p->cad_data);
                $gps = $p->gps;

                if($p->endereco == '') { $p->endereco= '-'; }
                if($p->p_ref == '') { $p->p_ref = '-'; }
                if($p->familia == '') { $p->familia = '-'; }
                if($p->facebook == '') { $p->facebook = '-'; }
                if($p->whats == '') { $p->whats = '-'; }
                if($p->tel == '') { $p->tel = '-'; }
                if($p->idade == '') { $p->idade = '-'; }
                if($p->obs == '') { $p->obs = '-'; }
                if($p->gps == '') { $p->gps = '-'; }
                if($p->turno == '') { $p->turno = '-'; }
            @endphp
            @if($p->mapa_id == 0)
            <div class="card" style="display:none" id="pendencia-{{$p->id}}">
                <input type="hidden" name="gps" value="{{$gps}}" data-gpsdisabled="true">
                <div class="card-header py-1 px-3 bg-light ">
                    <div class="row">
                        <div class="col-5">
                            <span class="badge badge-success">NOVO</span> <strong>{{$p->nome}}</strong> <br> <span class="badge badge-dark text-white">{{$p->bairro}}</span>
                        </div>
                        <div class="offset-2 col-5 text-right">
                        <strong>AUTOR(A):</strong> {{$p->autor}}<br>
                            <kbd>{{$cad_data->format('d/m/Y H:i:s')}}</kbd>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2 px-3">
                    <div class="row">
                        <div class="col-7">
                            <div class="row">
                                <div class="col-6">
                                    <dl>
                                        <dt>Nome:</dt>
                                        <dd>{{$p->nome}}</dd>

                                        <dt>Endereço:</dt>
                                        <dd>{{$p->endereco}}</dd>

                                        <dt>Ponto de Referência:</dt>
                                        <dd>{{$p->p_ref}}</dd>

                                        <dt>GPS:</dt>
                                        <dd>{{$p->gps}}</dd>

                                        <dt>Familia:</dt>
                                        <dd>{{$p->familia}}</dd>
                                    </dl>
                                </div>
                                <div class="col-6">
                                    <dl>
                                        <dt>WhatsApp:</dt>
                                        <dd>{{$p->whats}}</dd>

                                        <dt>Telefone:</dt>
                                        <dd>{{$p->tel}}</dd>

                                        <dt>Faixa Etária:</dt>
                                        <dd>{{$p->idade}}</dd>

                                        <dt>Turno:</dt>
                                        <dd>{{$p->turno}}</dd>

                                        <dt>Observação:</dt>
                                        <dd>{{$p->obs}}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="col-5 col-mapa">
                            
                        </div>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-sm btn-primary" onclick="pendAction( {{$p->id}}, true )"><i class="fas fa-check"></i> Confirmar</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="pendAction( {{$p->id}}, false )"><i class="fas fa-times"></i> Recusar</button>
                </div>
            </div>
            @else
            @php
                $surdo = $surdos[$p->mapa_id];
                if($surdo->p_ref == '') { $surdo->p_ref = '-'; }
                if($surdo->endereco == '') { $surdo->endereco = '-'; }
                if($surdo->gps == '') { $surdo->gps = '-'; }
                if($surdo->familia == '') { $surdo->familia = '-'; }
                if($surdo->facebook == '') { $surdo->facebook = '-'; }
                if($surdo->whats == '') { $surdo->whats = '-'; }
                if($surdo->tel == '') { $surdo->tel = '-'; }
                if($surdo->idade == '') { $surdo->idade = '-'; }
                if($surdo->obs == '') { $surdo->obs = '-'; }
                if($surdo->turno == '') { $surdo->turno = '-'; }

                if($surdo->nome != $p->nome) { $trbackNome = 'bg-danger text-white';}
                if($surdo->endereco != $p->endereco) { $trbackEndereco = 'bg-danger text-white';}
                if($surdo->p_ref != $p->p_ref) { $trbackPref = 'bg-danger text-white';}
                if($surdo->gps != $p->gps) { $trbackGps = 'bg-danger text-white';}
                if($surdo->familia != $p->familia) { $trbackFamilia = 'bg-danger text-white';}
                if($surdo->facebook != $p->facebook) { $trbackFacebook = 'bg-danger text-white';}
                if($surdo->whats != $p->whats) { $trbackWhats = 'bg-danger text-white';}
                if($surdo->tel != $p->tel) { $trbackTel = 'bg-danger text-white';}
                if($surdo->idade != $p->idade) { $trbackIdade = 'bg-danger text-white';}
                if($surdo->turno != $p->turno) { $trbackTurno = 'bg-danger text-white';}
                if($surdo->obs != $p->obs) { $trbackObs = 'bg-danger text-white';}
                if($surdo->bairro != $p->bairro) { $trbackBairro = 'bg-danger text-white';}
                
            @endphp
            <div class="card" style="display:none" id="pendencia-{{$p->id}}">
                <div class="card-header py-1 px-3 bg-light ">
                    <div class="row">
                        <div class="col-5">
                        <span class="badge badge-info">EDITADO</span> <strong>{{$p->nome}}</strong> <br> <span class="badge badge-dark text-white">{{$p->bairro}}</span>
                        </div>
                        <div class="offset-2 col-5 text-right">
                            <strong>AUTOR(A):</strong> {{$p->autor}}<br>
                            <kbd>{{$cad_data->format('d/m/Y H:i:s')}}</kbd>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2 px-3">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ANTIGO</th>
                                <th>NOVO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="{{$trbackNome or ''}}">
                                <th>Nome:</th>
                                <td>{{$surdo->nome}}</td>
                                <td>{{$p->nome}}</td>
                            </tr>
                            <tr class="{{$trbackBairro or ''}}">
                                <th>Bairro:</th>
                                <td>{{$surdo->bairro}}</td>
                                <td>{{$p->bairro}}</td>
                            </tr>
                            <tr class="{{$trbackEndereco or ''}}">
                                <th>Endereço:</th>
                                <td>{{$surdo->endereco}}</td>
                                <td>{{$p->endereco}}</td>
                            </tr>
                            <tr class="{{$trbackPref or ''}}">
                                <th>Ponto de Referência:</th>
                                <td>{{$surdo->p_ref}}</td>
                                <td>{{$p->p_ref}}</td>
                            </tr>
                            <tr class="{{$trbackGps or ''}}">
                                <th>GPS:</th>
                                <td>{{$surdo->gps}}</td>
                                <td>{{$p->gps}}</td>
                            </tr>
                            <tr class="{{$trbackFamilia or ''}}">
                                <th>Família:</th>
                                <td>{{$surdo->familia}}</td>
                                <td>{{$p->familia}}</td>
                            </tr>
                            <tr class="{{$trbackFacebook or ''}}">
                                <th>Facebook:</th>
                                <td>{{$surdo->facebook}}</td>
                                <td>{{$p->facebook}}</td>
                            </tr>
                            <tr class="{{$trbackWhats or ''}}">
                                <th>WhatsApp:</th>
                                <td>{{$surdo->whats}}</td>
                                <td>{{$p->whats}}</td>
                            </tr>
                            <tr class="{{$trbackTel or ''}}">
                                <th>Telefone(s):</th>
                                <td>{{$surdo->tel}}</td>
                                <td>{{$p->tel}}</td>
                            </tr>
                            <tr class="{{$trbackIdade or ''}}">
                                <th>Faixa Etária:</th>
                                <td>{{$surdo->idade}}</td>
                                <td>{{$p->idade}}</td>
                            </tr>
                            <tr class="{{$trbackTurno or ''}}">
                                <th>Turno:</th>
                                <td>{{$surdo->turno}}</td>
                                <td>{{$p->turno}}</td>
                            </tr>
                            <tr class="{{$trbackObs or ''}}">
                                <th>Observações:</th>
                                <td>{{$surdo->obs}}</td>
                                <td>{{$p->obs}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-primary" onclick="pendAction( {{$p->id}}, true )"><i class="fas fa-check"></i> Confirmar</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="pendAction( {{$p->id}}, false )"><i class="fas fa-times"></i> Recusar</button>
                </div>
            </div>
            @endif
            
            @endforeach
        @endif
            
        </div>
    </div>
@endsection

@section('script')
<script>
    function showPendencia(id) {
        $('#pendencias-dados .card').hide(100);
        setTimeout(function(){$('#pendencia-'+id).show();}, 200);

        if($('#mapsAPI').length > 0) {
            $('#mapsAPI').remove();
        }

        if($('#pendencia-'+id+' input[name="gps"]').val() != '' && $('#pendencia-'+id+' input[name="gps"]').length > 0) {
            $('#pendencia-'+id+' .col-mapa').html('<div id="mapsAPI"></div>');
            initMapa(false, $('#pendencia-'+id+' input[name="gps"]').val() );
        } else {
            $('#pendencia-'+id+' .col-mapa').html('<strong>Sem informação do GPS</strong>');
        }
        
    }
</script>
@endsection