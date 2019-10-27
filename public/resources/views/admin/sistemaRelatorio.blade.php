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
    <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
    <li class="breadcrumb-item active">Relatórios</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <div class="border shadow-sm border-secondary px-3 py-2 d-flex mb-2">
                <select class="form-control mr-3" id="selTipo">
                    <option value="">Escolha:</option>
                    <option value="geral">Relatório Geral</option>
                    <option value="visitas">Entre Visitas ({{$periodoIni->format('d/m/Y')}}) a ({{$periodoFim->format('d/m/Y')}})</option>
                </select>
                <button type="button" class="btn btn-primary" onclick="getRelatorio()"> Gerar </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="border shadow-sm p-3 " id="relatorioDiv">
                <small class="text-muted"><i>Seu relatório será exibido aqui.</i></small>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    function getRelatorio()
    {
        let tipo = $('#selTipo').find(':selected').val();

        if(tipo != '') {
            $.post("{{$router->generate('admSisRelatorioPOST')}}",{
                tipo: tipo
            }, function(data){
                $('#relatorioDiv').html(data);
            });
        }
        
    }
</script>
@endsection