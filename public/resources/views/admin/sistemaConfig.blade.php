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
            <form action="javascript:void(0)" method="post" onsubmit="jss01();return false;">
                <div class="form-group">
                    <label>Data da ÚLTIMA visita (terça-feira)</label>
                    <input type="date" class="form-control form-control-sm" name="ult_visita" value="{{$config->get('periodoIni')}}" required>
                </div>
                <div class="form-group">
                    <label>Data da PRÓXIMA visita (terça-feira)</label>
                    <input type="date" class="form-control form-control-sm" name="prox_visita" value="{{$config->get('periodoFim')}}" required>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label>Versão do SMO (3.xx.mmdd)</label>
                        <input type="text" class="form-control form-control-sm" name="versao" value="{{$config->get('versao')}}" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Data da versão</label>
                        <input type="date" class="form-control form-control-sm" name="versao_data" value="{{$config->get('versaoData')}}" required>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </div>
            </form>
            <hr>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <h4><i class="far fa-comment"></i> Redes Sociais</h4>
            <form action="javascript:void(0)" method="post" onsubmit="jss02(); return false;">
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
            <form action="javascript:void(0)" method="post" onsubmit="jss03(); return false;">
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
    function jss01() {
        let x = $(event.target);
        if($(x).find('[name="ult_visita"]').val() == '0000-00-00') {
            alert('A data da última visita é obrigatória.')
            return false;
        }
        if($(x).find('[name="prox_visita"]').val() == '0000-00-00') {
            alert('A data da próxima visita é obrigatória (se não souber, estabeleça uma estimativa).')
            return false;
        }

        $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'setConfigGeral',
                ult_visita: $(x).find('[name="ult_visita"]').val(),
                prox_visita: $(x).find('[name="prox_visita"]').val(),
                versao: $(x).find('[name="versao"]').val(),
                versao_data: $(x).find('[name="versao_data"]').val()
            },function(data){
                if(data == 'OK') {
                    location.reload();
                } else {
                    alert(data);
                }
            });

        return false;
    }

    function jss02() {
        let x = $(event.target);
        let sAtiva = $(x).find('[name="social_ativa"]');
        let sData = $(x).find('[name="social_data"]');
        let sDuracao = $(x).find('[name="social_duracao"]');

        if(sAtiva.find(':selected').val() == 'yes') {
            if(sData.val() == '' || sData.val() == '0000-00-00T00:00' || sDuracao.val() == '' || sDuracao.val() == '00:00') {
                alert('Defina uma data e horário para início do evento, e um tempo de duração.');
                return false;
            }
        }

        $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'setSocial',
                social_ativa: sAtiva.find(':selected').val(),
                social_data: sData.val(),
                social_duracao: sDuracao.val(),
            },function(data){
                if(data == 'OK') {
                    location.reload();
                } else {
                    alert(data);
                }
            });

        return false;
    }

    function jss03() {
        let x = $(event.target);
        let cAtiva = $(x).find('[name="campanha_ativa"]');
        let cNome = $(x).find('[name="campanha_nome"]');
        let cInicio = $(x).find('[name="campanha_inicio"]');
        let cFim = $(x).find('[name="campanha_fim"]');

        if(cAtiva.find(':selected').val() == 'yes') {
            if(cInicio.val() == '' || cInicio.val() == '0000-00-00' || cFim.val() == '' || cFim.val() == '0000-00-00') {
                alert('Defina uma data de início e fim da campanha.');
                return false;
            }
        }

        $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'setCampanha',
                ativa: cAtiva.find(':selected').val(),
                nome: cNome.find(':selected').val(),
                inicio: cInicio.val(),
                fim: cFim.val(),
            },function(data){
                if(data == 'OK') {
                    location.reload();
                } else {
                    alert(data);
                }
            });

        return false;

    }

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