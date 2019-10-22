@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	//var_dump($tp);
@endphp

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPublicador')}}">Publicadores</a></li>
	<li class="breadcrumb-item active">Território Pessoal</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
	<div class="row">
		<div class="col-12 col-md-6">
				<div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">Publicadores</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse show" style="overflow-y: auto; max-height: 300px;">
                        <table class="table table-sm table-hover">
							<thead>
								<tr>
									<th>Usuário</th>
									<th>Terr. Pessoal</th>
									<th>Opções</th>
								</tr>
							</thead>
							<tbody>
								@foreach($tp as $u)
								<tr smo-pubid="{{$u->id}}">
									<td>{{$u->nome}} {{$u->sobrenome}}</td>
									
									@if($u->surdosTP == 0)
									<td>
										<span class="badge badge-light"><i class="fas fa-times"></i> NÃO TEM</span>
									</td>
									<td>
										<button typ="button" class="btn btn-light btn-sm" onclick="aptp01()"><i class="fas fa-plus"></i></button>
									</td>
									@else
									<td>
										<span class="badge badge-success">{{$u->surdosTP}} surdo(s)</span>
									</td>
									<td>
										<button typ="button" class="btn btn-primary btn-sm" onclick="aptp01()"><i class="fas fa-search"></i></button>
									</td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>
                    </div>
                </div>
		</div>
		<div class="col-12 col-md-6">
				<div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">Surdos sem TP</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse show" style="overflow-y: auto; max-height: 300px;">
						<table class="table table-sm table-hover">
							<thead>
								<tr>
									<th>Surdo</th>
									<th>Bairro</th>
									<th>Mapa</th>
								</tr>
							</thead>
							<tbody>
								@foreach($surdos as $s)
									<tr>
										<td>{{$s->nome}}</td>
										<td>{{$s->bairro}}</td>
										<td>{{$s->mapa}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
                    </div>
                </div>
		</div>
	</div>

	<div class="modal fade" id="modalAddSurdo">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title"><strong>Adicionar surdo ao TP</strong></h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<h5><strong>Publicador(a): </strong><span class="pubnome"></span> <small>[ID: <span class="pubid"></span>]</small></h5>
						</div>
					</div><hr>
					
					<div class="row">
						<div class="col-12">
							<div class="alert alert-info"><strong>INSTRUÇÕES:</strong> Para adicionar ou remover um surdo, dê dois cliques.</div>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="border p-2 shadow-sm">
								<h5><strong>Território Pessoal</strong></h5>
								<div class="listaTP" style="overflow-y:auto; max-height: 300px;"></div>
							</div>
						</div>
						<div class="col-6">
							<div class="border p-2 shadow-sm">
								<h5><strong>Surdos disponíveis</strong></h5>
								<div class="listaSSTP d-flex flex-wrap" style="overflow-y:auto; max-height: 300px;"></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12" id="mensagem">
							
						</div>
					</div>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-success" disabled id="btnSalvar">Salvar</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Sair sem salvar</button>
				</div>

			</div>
		</div>
	</div>

	
@endsection

@section('script')
<style>
.listaTP {
	display:flex; flex-wrap: wrap;
}
.listaTP .surdobloco {
	width: calc(50% - .5rem);
	color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.surdobloco {
	border: 1px solid #dee2e6;
	margin: 0 .5rem .5rem 0;
	padding: .5rem;
	border-radius: .5rem;
	text-align: center;
	cursor:pointer;
}
</style>
<script>
var surdosJSONRAW = {!!$surdosJSON!!};
var surdosJSON = {!!$surdosJSON!!};
function aptp01()
{
	let pubid = $(event.target).parents('tr').eq(0).attr('smo-pubid');
	let pubnome = $(event.target).parents('tr').eq(0).find('td').eq(0).text();
	//console.log($(event.target));
	//console.log(pubid);

	// Reseta
	$('#modalAddSurdo').find('.pubid').text(pubid);
	$('#modalAddSurdo').find('.pubnome').text(pubnome);
	$('#modalAddSurdo .listaSSTP, #modalAddSurdo .listaTP').html('');
	$('#btnSalvar').attr('disabled', true);

	//console.log(surdosJSON[200]);
	surdosJSON.forEach(function(s, index){
		if(s.tp_pub == '0') {
			$('#modalAddSurdo').find('.listaSSTP').append('<div class="surdobloco" smo-sid="'+s.id+'"><strong>'+s.nome + '</strong><br>['+s.bairro+']'+'</div>');
		} else if(s.tp_pub == pubid) {
			$('#modalAddSurdo').find('.listaTP').append('<div class="surdobloco" smo-sid="'+s.id+'"><strong>'+s.nome + '</strong><br>['+s.bairro+']'+'</div>');
		}
	});


	$('#modalAddSurdo').modal();
}



function salvar()
{
	let conteudo = $(event.target).parents('.modal-content').eq(0);

	let surdoTPNovo = [];
	let surdoTPVelho = [];
	let atualiza = {sub: '', add: ''}; // Sub = remove do TP; Add = adiciona ao TP.
	//console.log(atualiza);

	let pubid = conteudo.find('.pubid').text();
	let pubnome = conteudo.find('.pubnome').text();
	//console.log(conteudo, pubid, pubnome);

	// Varre div TP e preenche variável "surdoTPNovo"
	let arr = conteudo.find('.listaTP').children();
	for(i = 0; i < arr.length; i++) {
		surdoTPNovo.push(arr.eq(i).attr('smo-sid'));
	}

	console.log("SURDO TP NOVO: "+surdoTPNovo);

	// Preenche variável "surdoTPVelho" com o TP antigo.
	surdosJSON.forEach(function(s){
		if(s.tp_pub == pubid) {
			surdoTPVelho.push(s.id);
		}
	});

	console.log("SURDO TP VELHO: "+surdoTPVelho);

	// Busca as diferenças entre os arrays.
	// Busca os surdos novos para adicionar ao TP
	let v = [];
	surdoTPNovo.forEach(function(s){
		//console.log(s);
		if(surdoTPVelho.findIndex(function(velho){return velho == s;}) == -1){
			// Entrada "s" não encontrada. Adiciona ao array V;
			v.push(s);
		}
	});

	atualiza.add = v.join();

	// Busca os surdos velhos para remover do TP
	v = [];
	surdoTPVelho.forEach(function(s){
		//console.log(s);
		if(surdoTPNovo.findIndex(function(novo){return novo == s;}) == -1){
			// Entrada "s" não encontrada. Adiciona ao array V;
			v.push(s);
		}
	});

	atualiza.sub = v.join();

	console.log(atualiza);
	$.post('{{$router->generate("admFunctions")}}',{
		funcao: 'setTP',
		atualiza: atualiza,
		pubid: pubid
	},function(data){
		if(data == 'OK') {
			// Confirma mudanças e fecha modal.
			v = atualiza.sub.split(',');
			// REMOVE TP
			if(v.length > 0) {
				v.forEach(function(s){
					surdosJSON.forEach(function(surdo, chave){
						if(surdo.id == s) {
							surdosJSON[chave].tp_pub = 0;
						}
					});
				});
			}
			

			v = atualiza.add.split(',');
			// ADICIONA TP
			if(v.length > 0) {
				v.forEach(function(s){
					surdosJSON.forEach(function(surdo, chave){
						if(surdo.id == s) {
							surdosJSON[chave].tp_pub = pubid;
						}
					});
				});
			}

			// Localiza usuário na lista e atualiza info
			let linha = $('.card table').eq(0).find('tr[smo-pubid="'+pubid+'"]');
			if(surdoTPNovo.length > 0) {
				linha.find('td:eq(1)').html('<span class="badge badge-success">'+surdoTPNovo.length+' surdo(s)</span>');
				linha.find('td:eq(2)').html('<button typ="button" class="btn btn-primary btn-sm" onclick="aptp01()"><i class="fas fa-search"></i></button>');
			} else {
				linha.find('td:eq(1)').html('<span class="badge badge-light"><i class="fas fa-times"></i> NÃO TEM</span>');
				linha.find('td:eq(2)').html('<button typ="button" class="btn btn-light btn-sm" onclick="aptp01()"><i class="fas fa-plus"></i></button>');
			}

			// Rescreve lista de surdos sem TP.
			let tabela = $('.card table').eq(1).find('tbody');
			tabela.html('');
			surdosJSON.forEach(function(s){
				if(s.tp_pub == 0) {
					tabela.append('<tr><td>'+s.nome+'</td> <td>'+s.bairro+'</td> <td>'+s.mapa+'</td></tr>');
				}
			});

			// Fecha o modal
			$('#modalAddSurdo').modal('hide');
			
		} else {
			$('#modalAddSurdo #mensagem').html('<hr><h5>Mensagem do SMO:</h5> '+data);
		}
	});

}

$(document).ready(function(){
	$(document).on('dblclick', '#modalAddSurdo .listaSSTP > div', function(){
		$(this).clone().appendTo('#modalAddSurdo .listaTP');
		$(this).remove();
		$('#btnSalvar').attr('disabled', false);

	});
	$(document).on('dblclick', '#modalAddSurdo .listaTP > div', function(){
		$(this).clone().appendTo('#modalAddSurdo .listaSSTP');
		$(this).remove();
		$('#btnSalvar').attr('disabled', false);
	});

	$(document).on('click', '#btnSalvar', function(){
		salvar();
	});
});
</script>
@endsection