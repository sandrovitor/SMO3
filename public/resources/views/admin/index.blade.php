@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	if(!isset($pillDefault)) {
		$surdoPill = 'active';
	} else {
		switch($pillDefault) {
			default:
			case 'surdo':
				$surdoPill = 'active';
				break;

			case 'publicador':
				$publicadorPill = 'active';
				break;

			case 'sistema':
				$sistemaPill = 'active';
				break;

			case 'bd':
				$bdPill = 'active';
				break;
		}
	}
@endphp

@section('breadcrumb')
	<li class="breadcrumb-item active">Administração</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
	<div class="row">
		<div class="col-12 mb-2">
			<ul class="nav nav-pills nav-justified">
				<li class="nav-item">
					<a class="nav-link {{$surdoPill or ''}}" data-toggle="pill" href="#pill-surdos">Surdos</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{$publicadorPill or ''}}" data-toggle="pill" href="#pill-publicadores">Publicadores</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{$sistemaPill or ''}}" data-toggle="pill" href="#pill-sistema">Sistema</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{$bdPill or ''}}" data-toggle="pill" href="#pill-bd">Banco de Dados</a>
				</li>
			</ul>
		</div>
		<div class="col-12">
			<div class="tab-content">
				<div class="tab-pane container {{$surdoPill or 'fade'}}" id="pill-surdos">
					<ul class="nav nav-pills">
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSurdoNovo')}}">Novo</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSurdoVer')}}">Listar</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSurdoPendencias')}}">Pendências</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSurdoHistorico')}}">Histórico</a></li>
					</ul>
				</div>
				<div class="tab-pane container {{$publicadorPill or 'fade'}}" id="pill-publicadores">
					<ul class="nav nav-pills">
						<li class="nav-item"><a class="nav-link" href="#">Novo</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Editar</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Território Pessoal</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Estudos</a></li>
					</ul>
				</div>
				<div class="tab-pane container {{$sistemaPill or 'fade'}}" id="pill-sistema">
					<ul class="nav nav-pills">
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisConfig')}}">Configurações</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisBairros')}}">Bairros</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisVerMapas')}}">Ver Mapas</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisEditarMapas')}}">Edição de Mapas</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisImpressao')}}">Impressão</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admSisLOG')}}">Registro de Eventos</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
					</ul>
				</div>
				<div class="tab-pane container {{$bdPill or 'fade'}}" id="pill-bd">
					<ul class="nav nav-pills">
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admBdBackup')}}">Backup e Restauração</a></li>
						<li class="nav-item"><a class="nav-link" href="{{$router->generate('admBdSQL')}}">SQL</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection