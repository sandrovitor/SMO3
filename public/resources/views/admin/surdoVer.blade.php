@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    //var_dump($surdos);
    $bairro = '';
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSurdo')}}">Surdos</a></li>
    <li class="breadcrumb-item active">Ver</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <h4>Lista de surdos no SMO<br>
            <small class="text-muted">TOTAL: <span class="badge badge-info">{{count($surdos)}}</span></small></h4>
            <input type="text" class="form-control" placeholder="Filtre a lista por nome" id="meuInput">
            <hr>
        </div>
    </div>

    <div class="row" id="lista-surdos">
        <div class="col-12">
            @foreach($surdos as $reg)
                @php
                // Faz alguns pré-processamentos
                $blocoHeaderBackground = '';
                if($reg->ativo == '1' && $reg->ocultar == '0') {
                    $blocoHeaderBackground = '';
                } else if($reg->ativo == '1' && $reg->ocultar == '1') {
                    $blocoHeaderBackground = 'bg-oculto';
                } else {
                    $blocoHeaderBackground = 'bg-desativado';
                }

                @endphp
                @if($bairro == '')
                <h5 class="mt-4"><strong>{{$reg->bairro}}</strong></h5>
                <div class="row"><div class="col-12 d-flex flex-wrap align-items-start">
                    @php($bairro = $reg->bairro)
                @elseif($bairro != $reg->bairro)
                </div></div>
                <h5 class="mt-4"><strong>{{$reg->bairro}}</strong></h5>
                <div class="row"><div class="col-12 d-flex flex-wrap align-items-start">
                    @php($bairro = $reg->bairro)
                @endif

                <div class="bloco-surdo">
                    <div class="bloco-header {{$blocoHeaderBackground}}">
                        <button type="button" class="bloco-more" data-toggle="collapse" data-target="#bloco-div-{{$reg->id}}"><i class="fas fa-plus"></i></button>
                        <strong>{{$reg->nome}}</strong>
                        
                        @if($reg->ativo == '1' && $reg->ocultar == '0')
                        <span class="badge badge-success" data-toggle="tooltip" title="ATIVO!"><i class="fas fa-star"></i></span>
                        @elseif($reg->ativo == '1' && $reg->ocultar == '1')
                        <span class="badge badge-info" data-toggle="tooltip" title="OCULTO!"><i class="far fa-star-half"></i></span>
                        @else
                        <span class="badge badge-danger" data-toggle="tooltip" title="DESATIVADO!"><i class="far fa-star"></i></span>
                        @endif

                        @if($reg->be == '1' && $reg->resp_id != '0')
                        <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia estuda!"><i class="fas fa-heart"></i></span>
                        @else
                        <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia!"><i class="far fa-heart"></i></span>
                        @endif
                    </div>
                    <div id="bloco-div-{{$reg->id}}" class="collapse">
                        <div class="bloco-body">
                            <dl>
                                <dt>Endereço:</dt>
                                <dd>{{$reg->endereco}}</dd>
                                
                                <dt>Ponto de Referência:</dt>
                                <dd>
                                    @if($reg->p_ref == '')
                                    -
                                    @else
                                    {{$reg->p_ref}}
                                    @endif
                                </dd>
                                
                                <dt>Idade:</dt>
                                <dd>
                                    @if($reg->idade == '')
                                    -
                                    @else
                                    {{$reg->idade}}
                                    @endif
                                </dd>
                                
                                <dt>Família:</dt>
                                <dd>
                                    @if($reg->familia == '')
                                    -
                                    @else
                                    {{$reg->familia}}
                                    @endif
                                </dd>
                                
                                <dt></dt>
                                <dd></dd>
                            </dl>
                            <hr>
                            @if($reg->ativo == '0')
                            <div class="alert alert-danger">
                                <strong>MOTIVO DO DESATIVAMENTO:</strong><br>
                                "{{$reg->motivo}}"
                            </div>
                            @elseif($reg->ocultar == '1')
                            <div class="alert alert-info">
                                <strong>MOTIVO DO OCULTAMENTO:</strong><br>
                                "{{$reg->motivo}}"
                            </div>
                            @endif

                            @if($reg->be == '1' && $reg->resp_id != '0')
                            <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia estuda!"><i class="fas fa-heart"></i> {{$reg->resp}}</span>
                            @else
                            <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia!"><i class="far fa-heart"></i> Não estuda</span>
                            @endif
                        </div>
                        <div class="bloco-footer d-flex flex-wrap">
                            <a href="/surdo/{{$reg->id}}" target="_blank" class="btn btn-info btn-sm mx-1">Mais info</a>
                            <a href="/registros/buscar/{{$reg->id}}" target="_blank" class="btn btn-info btn-sm mx-1">Registros</a>
                            <a href="/admin/surdo/editar/{{$reg->id}}" target="_blank" class="btn btn-info btn-sm mx-1">Editar</a>
                        </div>
                    </div>
                    
                </div>

            @endforeach
                </div></div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function(){
    $("#meuInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#lista-surdos .bloco-header").filter(function() {
            $(this).parents('.bloco-surdo').toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
@endsection