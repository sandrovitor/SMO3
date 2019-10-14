@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    $lista = $mapa->listaSurdos(TRUE, TRUE, FALSE);
    //var_dump($lista);
    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSurdo')}}">Surdos</a></li>
    <li class="breadcrumb-item active">Histórico</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header px-3 py-2">
                    Surdos
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <select class="form-control" name="surdo_sel">
                            @if(count($lista) > 0)
                                <option value="0">- Escolha: </option>
                                @php
                                    $x = '';
                                @endphp
                                @foreach($lista as $s)
                                @if($x == '')
                                <optgroup label="{{$s->bairro}}">
                                @php
                                    $x = $s->bairro;
                                @endphp
                                @elseif($x != $s->bairro)
                                </optgroup>
                                <optgroup label="{{$s->bairro}}">
                                @php
                                    $x = $s->bairro;
                                @endphp
                                @endif

                                @php
                                    $y = array();
                                    if($s->ativo == '0') {
                                        array_push($y, 'DESATIVADO');
                                    }
                                    if($s->ocultar == '1') {
                                        array_push($y, 'OCULTO');
                                    }
                                    if(count($y) > 0) {
                                        $y = '&nbsp; &nbsp; ['.implode(';', $y).']';
                                    } else {
                                        $y = '';
                                    }

                                @endphp
                                <option value="{{$s->id}}">{{$s->nome}} {{$y}}</option>
                                @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-body px-3 py-3" id="listaHistorico">
                    Escolha um surdo
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="verInfo">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title font-weight-bold">Visualizar histórico</h4>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div>

                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="compInfo">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title font-weight-bold">Comparar histórico</h4>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div>

                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<style>
.div-difyes:hover {
    background-color: #ccffcc;
    transition: all .3s ease-in-out;
}
.div-difnot:hover {
    background-color: #c5c5c5;
    transition: all .3s ease-in-out;
}
</style>
<script>

    function verInfo(id)
    {
        $.post('/admin/surdo/historico/ver/'+id,{},function(data){
            $('#verInfo .modal-body').html(data);
            $('#compInfo').modal('hide');
            $('#verInfo').modal();
        });
    }

    function comparar(id)
    {
        $.post('/admin/surdo/historico/compara/'+id,{},function(data){
            $('#compInfo .modal-body').html(data);
            $('#verInfo').modal('hide');
            setTimeout(function(){$('#compInfo').modal();}, 400);
        });
    }

    function apagar(id)
    {
        $.post('{{$router->generate("admFunctions")}}',{
            funcao: 'deleteHistorico',
            id: id
        },function(data){
            if(data == 'OK') {
                $('#verInfo, #compInfo').modal('hide');
                setTimeout(function(){$('[name="surdo_sel"]').trigger('change');}, 400);
            } else {
                alert(data);
            }
            
        });
    }

    function recuperar(id)
    {
        $.post('{{$router->generate("admFunctions")}}',{
            funcao: 'setHistoricoRecupera',
            id: id
        },function(data){
            if(data == 'OK') {
                location.reload();
            } else {
                alert(data);
            }
            
        });
    }

    $(document).on('change','[name="surdo_sel"]', function(){
        if($(this).find(':selected').val() == 0) {
            $('#listaHistorico').html('Escolha um surdo');
        } else {
            $.post('{{$router->generate("admFunctions")}}',{
                funcao: 'getHistoricoLista',
                id: $(this).find(':selected').val()
            },function(data){
                $('#listaHistorico').html(data);
            });
        }
    });
</script>
@endsection