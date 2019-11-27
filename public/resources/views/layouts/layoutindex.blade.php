<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Latest compiled and minified CSS -->
<!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link href="/css/layout.css" rel="stylesheet">
<link href="/css/ocult.css" rel="stylesheet">
<link href="/css/glyphicon.css" rel="stylesheet">
@yield('estiloPersonalizado')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> <!-- Font Awesome 5 -->
<link rel="icon" href="/resources/images/favicon_64.png" />
<title>SMO :: @yield('paginaCorrente')</title>
</head>

<body>
<!-- NAVBAR 4 -->
<nav class="navbar navbar-expand-md bg-smo navbar-dark fixed-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href="/"><img src="/resources/images/smo.png" alt="Logo do SMO"></a>

	<!-- Toggler/collapsibe Button -->
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
		<span class="navbar-toggler-icon"></span>
	</button>


	<!-- Links -->
	<div class="collapse navbar-collapse" id="collapsibleNavbar">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/"><span class="glyphicon glyphicon-home"></span> Início</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="nav-drop1" data-toggle="dropdown"><i class="fa fa-deaf"></i> Surdos</a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="{{$router->generate('consulta')}}"><span class="glyphicon glyphicon-search"></span> Consulta</a>
					<a class="dropdown-item" href="{{$router->generate('registros')}}"><span class="glyphicon glyphicon-list-alt"></span> Registros</a>
					<a class="dropdown-item" href="{{$router->generate('cadastro')}}"><span class="glyphicon glyphicon-edit"></span> Cadastro</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{$router->generate('tpessoal')}}"><span class="glyphicon glyphicon-pushpin"></span> Território Pessoal</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{$router->generate('social')}}"><i class="far fa-comment"></i> Redes Sociais</a>
					<a class="dropdown-item" href="{{$router->generate('campanha')}}"><span class="glyphicon glyphicon-briefcase"></span> Campanha de Pregação</a>
				</div>
			</li>
			@if ($_SESSION['nivel'] >= 4)
			<li class="nav-item">
				<a class="nav-link" href="{{$router->generate('admIndex')}}"><span class="glyphicon glyphicon-cog"></span> Administração</a>
			</li>
			@endif
		</ul>

		<ul class="navbar-nav ml-auto">
		<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="nav-drop2" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> {{$uNome}}</a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="{{$router->generate('perfil')}}"><i class="fas fa-address-book"></i> Meu Perfil</a>
					@if(isset($_SESSION['ma']) && $_SESSION['ma'] === TRUE)
					<a class="dropdown-item" href="{{$router->generate('ma')}}"><i class="fas fa-chalkboard-teacher"></i> Assistente</a>
					@endif
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{$router->generate('logout')}}"><span class="glyphicon glyphicon-log-out"></span> Sair</a>
				</div>
			</li>
		</ul>
	</div>
</nav>
<!-- ./ FIM NAVBAR 4 -->


<!-- CONTEUDO -->
<div class="{{$containertipo or 'container'}}" id="body_page">
	<!-- BREADCRUMB -->
	<ul class="breadcrumb">
		@yield('breadcrumb')
		@if($_SESSION['modo_facil'] == TRUE)
		<span class="ml-auto badge {{$mfIcon or 'badge-light'}}" style="font-size: 1rem;" title="Modo Fácil ativado" data-toggle="tooltip"><i class="fas fa-ribbon"></i></span>
		@endif
	</ul>
	<!-- ./FIM DO BREADCRUMB -->
	@yield('mensagemDeRetorno')


    @yield('conteudo')
</div>
<!-- ./FIM DO CONTEUDO -->

<!-- FOOTER -->
<footer class="bg-smo">
	<div class="container">
		<div class="row">
			<div class="col-12 col-sm-12 text-center">
				&copy;2016 - {{$anoCorrente or 'hoje'}} SMO LS Castelo Branco
			</div>
		</div>
	</div>
</footer>
<!-- ./FIM DO FOOTER -->

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script><!-- jQuery -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script><!-- Popper JS -->
<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script><!-- Bootstrap 4.1.3 -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script><!-- Popper JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script><!-- Bootstrap 4.3.1 -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.js"></script> <!-- ChartJS -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1UXAc9GN5QQt7lQ2O0Kf2aCdj35oPsYc"></script> <!-- Maps KEY -->
<script src="/js/smoGeral.js"></script>
@yield('script')
</body>
</html>