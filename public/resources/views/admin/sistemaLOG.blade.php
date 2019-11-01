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
    <li class="breadcrumb-item active">Registro de Eventos</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="modal-body">
                    <form class="form-inline">
                    <label class="mx-1"><strong>Tipo:</strong></label>
                    <select class="form-control mx-1" name="logTipo">
                        <option value="0">Todos</option>
                        <option value="1">1. Cadastro</option>
                        <option value="2">2. Atualização</option>
                        <option value="3">3. Remoção</option>
                        <option value="4">4. Consulta</option>
                        <option value="5">5. Erro(s)</option>
                        <option value="6">6. Sistema</option>
                        <option value="7">7. SMO Mobile</option>
                    </select>
                    <label class="mx-1"><strong>Usuário:</strong></label>
                    <select class="form-control mx-1" name="logUsuario">
                        <option value="0">Todos</option>
                        @foreach($usuarios as $u)
                        <option value="{{$u->id}}">{{$u->nome}} {{$u->sobrenome}}</option>
                        @endforeach
                    </select>
                    <label class="mx-1"><strong>Linhas:</strong></label>
                    <select class="form-control mx-1" name="logQtd">
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                        <option value="60">60</option>
                    </select>
                    <button type="button" class="ml-2 btn btn-primary btn-sm btnFiltro"><i class="fas fa-filter"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-2" id="resultado">
        
        </div>
    </div>
@endsection

@section('script')
<script>
    function adm_getLog(pagina) {
        let ini;
        if(pagina > 0) {
            ini = ($('[name="logQtd"]').find(':selected').val() * pagina) - $('[name="logQtd"]').find(':selected').val();
        } else {
            ini = 0;
        }

        $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'getLOG',
                tipo: $('[name="logTipo"]').find(':selected').val(),
                usuario: $('[name="logUsuario"]').find(':selected').val(),
                inicio: ini,
                qtd: $('[name="logQtd"]').find(':selected').val(),
            },
            function(data){
                $('#resultado').html(data);
            });
    }

    $(document).ready(function(){
        $(document).on('click', '.btnFiltro', function(){
            $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'getLOG',
                tipo: $('[name="logTipo"]').find(':selected').val(),
                usuario: $('[name="logUsuario"]').find(':selected').val(),
                inicio: 0,
                qtd: $('[name="logQtd"]').find(':selected').val(),
            },
            function(data){
                $('#resultado').html(data);
            });
        });

        $('.btnFiltro').trigger('click');
    });
</script>
@endsection