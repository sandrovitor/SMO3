@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    
    //var_dump($bairros);
    $regiao = '';

    $social = $config->get('social');
    $camp = $config->get('campanha');


    if($social['ativo'] == 'yes') {
        $socialAtivaYES = 'selected="selected"';
        $socialSHOW = '';
    } else {
        $socialAtivaNOT = 'selected="selected"';
        $socialSHOW = 'display:none;';
    }

    $socialData = str_replace(' ', 'T', $social['data']);

    if($camp['ativo'] == 'yes') {
        $campAtivaYES = 'selected="selected"';
        $campSHOW = '';
    } else {
        $campAtivaNOT = 'selected="selected"';
        $campSHOW = 'display:none;';
    }

    if($camp['nome'] == 'Congresso Regional') {
        $cNome1 = 'selected="selected"';
    } else {
        $cNome2 = 'selected="selected"';
    }
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
    <li class="breadcrumb-item active">Configurações</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <h4><span class="glyphicon glyphicon-cog"></span> Configurações Gerais</h4>
            <form action="#" method="post">
                <div class="form-group">
                    <label>Data da ÚLTIMA visita (terça-feira)</label>
                    <input type="date" class="form-control form-control-sm" name="ult_visita" value="{{$config->get('periodoIni')}}">
                </div>
                <div class="form-group">
                    <label>Data da PRÓXIMA visita (terça-feira)</label>
                    <input type="date" class="form-control form-control-sm" name="prox_visita" value="{{$config->get('periodoFim')}}">
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label>Versão do SMO (3.xx.mmdd)</label>
                        <input type="text" class="form-control form-control-sm" name="versao" value="{{$config->get('versao')}}">
                    </div>
                    <div class="form-group col-6">
                        <label>Data da versão</label>
                        <input type="date" class="form-control form-control-sm" name="versao_data" value="{{$config->get('versaoData')}}">
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </div>
            </form>
            <hr>
            <kbd>Fazer as funções de cada botão</kbd>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <h4><i class="far fa-comment"></i> Redes Sociais</h4>
            <form action="#" method="post">
                <div class="form-group">
                    <label>Agendar?</label>
                    <select class="form-control" name="social_ativa">
                        <option value="yes" {{$socialAtivaYES or ''}}>Sim</option>
                        <option value="not" {{$socialAtivaNOT or ''}}>Não</option>
                    </select>
                </div>
                <div id="socialBloco" style="{{$socialSHOW}}">
                    <div class="row">
                        <div class="form-group col-7">
                            <label>Data e hora</label>
                            <input type="datetime-local" class="form-control form-control-sm" name="social_data" value="{{$socialData}}">
                        </div>
                        <div class="form-group col-5">
                            <label>Duração do evento</label>
                            <input type="time" class="form-control form-control-sm" name="social_duracao" value="{{$social['duracao']}}">
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </div>
            </form>
            <hr>

            <h4><span class="glyphicon glyphicon-briefcase"></span> Campanha de Pregação</h4>
            <form action="#" method="post">
                <div class="form-group">
                    <label>Ativar campanha de pregação?</label>
                    <select class="form-control" name="campanha_ativa">
                        <option value="yes" {{$campAtivaYES or ''}}>Sim</option>
                        <option value="not" {{$campAtivaNOT or ''}}>Não</option>
                    </select>
                </div>
                <div id="campanhaBloco" style="{{$campSHOW}}">
                    <div class="form-group">
                        <label>Nome da Campanha</label>
                        <select class="form-control" name="campanha_nome">
                            <option value="Congresso Regional" {{$cNome1 or ''}}>Congresso Regional</option>
                            <option value="Celebração" {{$cNome2 or ''}}>Celebração</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label>Início</label>
                            <input type="date" class="form-control form-control-sm" name="campanha_inicio" value="{{$camp['inicio']}}">
                        </div>
                        <div class="form-group col-6">
                            <label>Fim</label>
                            <input type="date" class="form-control form-control-sm" name="campanha_fim" value="{{$camp['fim']}}">
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </div>
            </form>
            
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $(document).on('change', '[name="social_ativa"]', function(){
            if($(this).find(':selected').val() == 'yes') {
                $('#socialBloco').slideDown();
            } else {
                $('#socialBloco').slideUp();
            }
        });

        $(document).on('change', '[name="campanha_ativa"]', function(){
            if($(this).find(':selected').val() == 'yes') {
                $('#campanhaBloco').slideDown();
            } else {
                $('#campanhaBloco').slideUp();
            }
        });
    });
</script>
@endsection