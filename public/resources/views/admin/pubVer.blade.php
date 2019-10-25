@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	//var_dump($usuarios);
@endphp

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPublicador')}}">Publicadores</a></li>
	<li class="breadcrumb-item active">Listar</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
	<div class="row">
		<div class="col-12 d-flex flex-wrap">
			@if($usuarios === false)
				Sem usuários
			@else
			@foreach($usuarios as $u)
			@php
				$bloq = FALSE;
				$difStr = '';
				

				$expira = new DateTime($u->expira);
				$hoje = new DateTime();
				if($hoje >= $expira) { // Expirado
					$bloq = TRUE;
				}

				if($u->tentativas >= 3) { // Bloqueado por tentativas
					$bloq = TRUE;
				}


				if((bool)$u->bloqueado == TRUE) {
					$bloq = TRUE;
				}


				if($bloq === TRUE) {
					$bloq = 'text-danger';
				} else {
					$bloq = '';
				}

				$diff = $hoje->diff($expira);
				//var_dump($diff);
				if($diff->invert == 1) {
					$difStr = '<span class="badge badge-danger">- ';
				} else {
					if($diff->days > 60) {
						$difStr = '<span class="badge badge-primary">';
					} else if($diff->days >= 30) {
						$difStr = '<span class="badge badge-warning">';
					} else {
						$difStr = '<span class="badge badge-danger">';
					}
				}
				$difStr .= $diff->days.' dia(s)</span>'
			@endphp
			<div class="carduser shadow-sm p-2 mb-2 mr-2" smo-pubid="{{$u->id}}">
				<strong class="{{$bloq}}">{{$u->nome}} {{$u->sobrenome}}</strong>  <small><i>{{$u->user}}</i></small>
				<div class="d-flex justify-content-between">
					<div class="mr-auto mr-1"><span class="badge badge-secondary">Nível {{$u->nivel}}</span></div>
					<div class="ml-auto ml-1">{!!$difStr!!}</div>
				</div>
			</div>
			@endforeach
			@endif
		</div>
	</div>
@endsection

@section('script')
<style>
.carduser {
	border: 1px solid #dee2e6;
	transition: all .2s ease-in-out;
	cursor:pointer;
}
.carduser:hover {
	border-color: blue;
}
@media (max-width: 767px) {
	.carduser {
		width: 100%;
	}
}
</style>
<script>
	$(document).ready(function(){
		$(document).on('click', '.carduser', function(){
			let item = $(event.target);
			if(item.hasClass('carduser') == false) {
				item = item.parents('.carduser').eq(0);
			}
			let pubid = item.attr('smo-pubid');

			console.log($(event.target));
			console.log(item);
			console.log(pubid);
			
			if(pubid == '' || pubid == 0 || pubid == undefined) {
				alert('Ação inválida...');
			} else {
				location.href = '/admin/publicadores/editar/'+item.attr('smo-pubid');
			}
			
		});
	});
</script>
@endsection