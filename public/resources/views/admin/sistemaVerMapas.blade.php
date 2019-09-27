@extends('layouts.layoutadmin')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	//var_dump($bairros, $regiao);

@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
    <li class="breadcrumb-item active">Ver Mapas</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
	<div class="row">
		<div class="col-12">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs">
				@php
					$active = 'active';
				@endphp
				@foreach($regiao as $key => $valor)
					@if($valor != '')
					<li class="nav-item">
						<a class="nav-link {{$active}}" data-toggle="tab" href="#regiao{{$key}}">{{$valor}}</a>
					</li>
					@php
					$active = '';
					@endphp
					@endif
				@endforeach
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				@php
					$active = 'active';
				@endphp
				@foreach($regiao as $key => $valor)
					@if($valor != '')
					<div class="tab-pane container py-2 {{$active}}" id="regiao{{$key}}">
						<div class="d-flex flex-wrap" style="font-size: .875rem;">
							@php
								$surdos = array();
								$mNome = '';
								$mBairro = '';
							@endphp

							@foreach($mapas as $m)
								@if($m->regiao == $key && $m->mapa != '')

									@php
										if($mNome == '') {
											$mNome = $m->mapa;
											$mBairro = $m->bairro;
										}
									@endphp


									@if($mNome != $m->mapa)
										@php
											while(count($surdos) < 4) {
												array_push($surdos, array('id' => '-', 'nome' => '-', 'bairro' => '-'));
											}
										@endphp
									<div class="bloco-mapa">
										<table class="table table-bordered table-sm">
											<thead class="bg-info">
												<tr>
													<th colspan="3" class="text-white">
														<div class="d-flex">
														<div class="flex-fill">{{$mBairro}}</div>
														<div class="flex-fill text-right"><span class="badge badge-light">{{$mNome}}</span></div>
														</div>
													</th>
												</tr>
												<tr>
													<th>ID</th>
													<th>Nome</th>
													<th>Bairro</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>{{$surdos[0]['id']}}</td>
													<td>{{$surdos[0]['nome']}}</td>
													<td>{{$surdos[0]['bairro']}}</td>
												</tr>
												<tr>
													<td>{{$surdos[1]['id']}}</td>
													<td>{{$surdos[1]['nome']}}</td>
													<td>{{$surdos[1]['bairro']}}</td>
												</tr>
												<tr>
													<td>{{$surdos[2]['id']}}</td>
													<td>{{$surdos[2]['nome']}}</td>
													<td>{{$surdos[2]['bairro']}}</td>
												</tr>
												<tr>
													<td>{{$surdos[3]['id']}}</td>
													<td>{{$surdos[3]['nome']}}</td>
													<td>{{$surdos[3]['bairro']}}</td>
												</tr>
											</tbody>
										</table>
									</div>
										@php
											$mNome = $m->mapa;
											$mBairro = $m->bairro;
											$surdos = array();
										@endphp
									@endif

									@php
										array_push($surdos, array('id' => $m->id, 'nome' => $m->nome, 'bairro' => $m->bairro));
									@endphp


								@endif
							@endforeach

							@if(count($surdos) > 0)
								@php
									while(count($surdos) < 4) {
										array_push($surdos, array('id' => '-', 'nome' => '-', 'bairro' => '-'));
									}
								@endphp
								<div class="bloco-mapa">
								<table class="table table-bordered table-sm">
									<thead class="bg-info">
										<tr>
											<th colspan="3" class="text-white">
												<div class="d-flex">
												<div class="flex-fill">{{$mBairro}}</div>
												<div class="flex-fill text-right"><span class="badge badge-light">{{$mNome}}</span></div>
												</div>
											</th>
										</tr>
										<tr>
											<th>ID</th>
											<th>Nome</th>
											<th>Bairro</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>{{$surdos[0]['id']}}</td>
											<td>{{$surdos[0]['nome']}}</td>
											<td>{{$surdos[0]['bairro']}}</td>
										</tr>
										<tr>
											<td>{{$surdos[1]['id']}}</td>
											<td>{{$surdos[1]['nome']}}</td>
											<td>{{$surdos[1]['bairro']}}</td>
										</tr>
										<tr>
											<td>{{$surdos[2]['id']}}</td>
											<td>{{$surdos[2]['nome']}}</td>
											<td>{{$surdos[2]['bairro']}}</td>
										</tr>
										<tr>
											<td>{{$surdos[3]['id']}}</td>
											<td>{{$surdos[3]['nome']}}</td>
											<td>{{$surdos[3]['bairro']}}</td>
										</tr>
									</tbody>
								</table>
								</div>
								@php
									$mNome = '';
									$mBairro = '';
									$surdos = array();
								@endphp
							@endif

							<!-- SURDOS SEM MAPA -->
							
							<div class="w-100 mt-3">
								<table class="table table-bordered table-sm">
									<thead class="thead-dark">
										<tr>
											<th colspan="3">
												SURDOS SEM MAPA
											</th>
										</tr>
										<tr>
											<th>ID</th>
											<th>Nome</th>
											<th>Bairro</th>
										</tr>
									</thead>
									<tbody>
									@php
										$x=0;
									@endphp
									@foreach($mapas as $m)
										@if($m->regiao == $key && $m->mapa == '')
										@php
											$x++;
										@endphp
										<tr>
											<td>{{$m->id}}</td>
											<td>{{$m->nome}}</td>
											<td>{{$m->bairro}}</td>
										</tr>
										@endif
									@endforeach
									
									@if($x == 0)
										<tr>
											<td colspan="3">Nenhum surdo</td>
										</tr>
									@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
						@php
						$active = '';
						@endphp

					@endif
				@endforeach
			</div>
		</div>
	</div>
@endsection

@section('script')
<style>

.bloco-mapa {
	width: 100%;
	padding: 0 .3125rem;
	box-sizing: border-box;
}
.bloco-mapa td:first-of-type {
	width: 40px;
	font-size: .875rem;
}

/* #############   XS   ############# */
@media (max-width: 575px) {
	.bloco-mapa {
		max-width: 100%;
		margin: 0;
	}
}

/* #############   SM   ############# */
@media (min-width: 576px) {
	.bloco-mapa {
		max-width: 100%;
		margin: 0;
	}
}

/* #############   MD   ############# */
@media (min-width: 768px) {
	.bloco-mapa {
		max-width: calc(60%);
		margin: 0 20%;
	}
}

/* #############   LG   ############# */
@media (min-width: 992px) {
	.bloco-mapa {
		max-width: 50%;
		margin: 0;
	}
}

/* #############   XL   ############# */
@media (min-width: 1200px) {
	.bloco-mapa {
		max-width: 33.3333%;
		margin: 0;
	}
}





</style>
@endsection