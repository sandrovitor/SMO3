@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	//var_dump($surdos);
	//var_dump($config);
	$estudantes = array();
	$contEstudantes = 0;

	foreach($surdos as $s){
		if((bool)$s->be == TRUE) {
			if(!isset($estudantes[$s->regiao]) || !is_array($estudantes[$s->regiao])) {
				$estudantes[$s->regiao] = array();
			}

			array_push($estudantes[$s->regiao], $s);
			$contEstudantes++;
		}
	}
	$contEstudantesP = round(($contEstudantes * 100) / count($surdos), 0);

	//var_dump($estudantes);
	$regiao = $config->get('regiao');

@endphp

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPublicador')}}">Publicadores</a></li>
	<li class="breadcrumb-item active">Estudos</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
	<div class="row">
		<div class="col-12 col-lg-6">
			<div class="card">
				<div class="card-header py-2">
					<div onclick="cardBodyCollapse(this)" style="cursor:pointer;"><strong>Estudantes da Bíblia</strong> <small class="text-muted">(gerado às {{date('d/m/Y \à\s H:i:s')}})</small></div>
				</div>
				<div class="card-body py-2 collapse show">
					<strong>BÍBLIA ESTUDA JÁ</strong> <span class="badge badge-dark">{{$contEstudantes}}</span>
					<div class="progress" style="">
						<div class="progress-bar" style="width:{{$contEstudantesP}}%">{{$contEstudantesP}}%</div>
					</div>
					<br>
					@foreach($estudantes as $chave => $e)
					<div class="card mb-1">
						<div class="card-header bg-info text-white p-2 d-flex">
							<div onclick="cardBodyCollapse(this)" style="cursor:pointer;" class="mr-auto"><strong>Região {{$chave}} - {{$regiao[$chave]}}</strong></div>
							<span class="badge badge-light align-self-center ml-auto">{{@count($e)}}</span>
						</div>
						<div class="card-body p-2 collapse">
							<table class="table table-sm">
								<thead>
									<tr>
										<th>Nome</th>
										<th>Bairro</th>
										<th>Responsável</th>
									</tr>
								</thead>
								<tbody>
								@foreach($e as $s)
									<tr>
										<td>{{$s->nome}}</td>
										<td>{{$s->bairro}}</td>
										<td>{{$s->resp}}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div>
					@endforeach
					<small class="text-muted">* Atualize a página para atualizar estas listas.</small>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-6">
			<div class="card">
				<div class="card-header py-2">
					<div onclick="cardBodyCollapse(this)" style="cursor:pointer;"><strong>Vincular estudantes/publicadores</strong></div>
				</div>
				<div class="card-body py-2 collapse show">
					<div class="form-group">
						<label>Região:</label>
						<select id="regiao" class="form-control form-control-sm">
							<option value="0">Escolha:</option>
							@foreach($regiao as $chave => $r)
							@if($r !== '')
							<option value="{{$chave}}">REGIÃO {{$chave}} - {{strtoupper($r)}}</option>
							@endif
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Bairro:</label>
						<select id="bairro" class="form-control form-control-sm" disabled>
						</select>
					</div>
					<div class="form-group">
						<label>Surdo:</label>
						<select id="surdo" class="form-control form-control-sm" disabled>
						</select>
					</div>
					<div class="form-group row">
						<div class="col-6">
							<label>Situação:</label>
							<select id="be" class="form-control form-control-sm" disabled>
								<option value="0">Não estuda</option>
								<option value="1">Estuda já</option>
							</select>
						</div>
						<div class="col-6">
							<label>Responsável:</label>
							<select id="resp" class="form-control form-control-sm" disabled>
								<option value="0">Escolha:</option>
								@foreach($publicadores as $p)
								<option value="{{$p->id}}">{{$p->nome}} {{$p->sobrenome}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<button type="button" id="salvar" class="btn btn-success btn-sm" disabled><i class="fas fa-check"></i> Alterar</button>
						<button type="button" id="reset" class="btn btn-warning btn-sm" ><i class="fas fa-redo"></i> Reset</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
<script>
var surdosJSON = {!!json_encode($surdos)!!};
var bairros = {!!json_encode($bairros)!!};
function loadBairro()
{
	let regiao = $('#regiao').find(':selected').val();
	let conta = 0;
	$('#bairro').html('');
	bairros.forEach(function(valor){
		if(valor.regiao_numero == regiao){
			$('#bairro').append('<option value="'+valor.id+'">'+valor.bairro+'</option>');
			conta++;
		}
	});

	if(conta > 0) {
		$('#bairro').prepend('<option value="0" selected>Escolha:</option>');
	}
}

function loadSurdo()
{
	let bairro = $('#bairro').find(':selected').text();
	let conta = 0;
	$('#surdo').html('');
	surdosJSON.forEach(function(valor){
		if(valor.bairro == bairro) {
			$('#surdo').append('<option value="'+valor.id+'" smo-be="'+valor.be+'" smo-respid="'+valor.resp_id+'">'+valor.nome+'</option>');
			conta++;
		}
	});

	if(conta > 0) {
		$('#surdo').prepend('<option value="0" selected>Escolha:</option>');
	}
}

function infoBE()
{
	let surdo = $('#surdo').find(':selected');
	$('#be').val(surdo.attr('smo-be'));
	$('#resp').val(surdo.attr('smo-respid'));
	
	$('#be').trigger('change');
}

function btnReset()
{
	$('#bairro, #surdo').html('').prop('disabled', true);
	$('#be, #resp').val(0).prop('disabled', true);
	$('#regiao').val(0);
	$('#salvar').prop('disabled', true);
}
	$(document).ready(function(){
		$(document).on('change', '#regiao', function(){
			loadBairro();
			if($(this).val() != '0') {
				$('#bairro').prop('disabled', false);
			} else {
				$('#bairro').prop('disabled', true);
			}
		});
		
		$(document).on('change', '#bairro', function(){
			loadSurdo();
			if($(this).val() != '0') {
				$('#surdo').prop('disabled', false);
			} else {
				$('#surdo').prop('disabled', true);
			}
		});
		
		$(document).on('change', '#surdo', function(){
			infoBE();
			if($(this).val() != '0') {
				$('#be, #resp').prop('disabled', false);
			} else {
				$('#be, #resp').prop('disabled', true);
			}
		});

		$(document).on('change', '#be, #resp', function(){
			if($('#be').find(':selected').val() == '0') {
				$('#salvar').prop('disabled', false);
			} else {
				if($('#resp').find(':selected').val() !== '0') {
					$('#salvar').prop('disabled', false);
				} else {
					$('#salvar').prop('disabled', true);
				}
			}
		});

		$(document).on('click', '#salvar',function(){
			if( ($('#surdo').find(':selected').val() != '0' && $('#be').find(':selected').val() == '0') ||
				($('#surdo').find(':selected').val() != '0' && $('#be').find(':selected').val() == '1' && $('#resp').find(':selected').val() != '0') ) {
				$.post('{{$router->generate("admFunctions")}}',{
					funcao: 'setBE',
					surdo: $('#surdo').find(':selected').val(),
					be: $('#be').find(':selected').val(),
					resp: $('#resp').find(':selected').val()
				}, function(data){
					if(data == 'OK') {
						surdosJSON.forEach(function(valor, index){
							if(valor.id == $('#surdo').find(':selected').val()) {
								if($('#be').find(':selected').val() == '0') { // Não estuda
									surdosJSON[index].be = '0';
									surdosJSON[index].resp = null;
									surdosJSON[index].resp_id = '0';
								} else { // Estuda já
									surdosJSON[index].be = '1';
									surdosJSON[index].resp = $('#resp').find(':selected').text();
									surdosJSON[index].resp_id = $('#resp').find(':selected').val();
								}
							}
						});
						btnReset();
					} else {
						alert(data);
						console.log(data);
					}
				});
			} else {
				alert('Não é possível salvar esse valores.');
			}
		});

		$(document).on('click', '#reset', function(){
			btnReset();
		});
	});
</script>
@endsection