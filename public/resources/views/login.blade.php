@extends('layouts.login')

@php
	if(isset($_COOKIE['user']) && $_COOKIE['user'] != '') {
		if(!isset($_SESSION['user'])) {
			$_SESSION['user'] = $_COOKIE['user'];
		}
		$saveuserChecked = 'checked="checked"';
	} else {
		$saveuserChecked = '';
	}

	if(isset($_SESSION['user']) && $_SESSION['user'] != '') {
		$afSenha = 'autofocus="autofocus"';
		$afUser = '';
	} else {
		$afUser = 'autofocus="autofocus"';
		$afSenha = '';
	}

	$msgRetorno = '';
	if($smoMSG != false) {
		$msgRetorno = $smoMSG;
	}

	$loginFoto = '';
	switch(rand(0, 5)) {
		case 0:
			$loginFoto = 'images/302016037_univ_lsr_xl.jpg';
			break;

		case 1:
			$loginFoto = 'images/302016044_univ_cnt_2_xl.jpg';
			break;

		case 2:
			$loginFoto = 'images/1102018985_univ_lsr_lg.jpg';
			break;

		case 3:
			$loginFoto = 'images/502016131_univ_lsr_xl.jpg';
			break;

		case 4:
			$loginFoto = 'images/502018510_univ_lsr_xl.jpg';
			break;

		case 5:
			$loginFoto = 'images/202017332_univ_cnt_3_xl.jpg';
			break;
	}

@endphp

@section('conteudo')
	<div class="content-image">
	</div>
	<div class="content">
		<h3>SMO :: Entrar<br>
		<small class="text-muted">Sistema de Mapas Online 3.0</small></h3>
		<form method="post" action="login">
			<div class="form-group textbox">
				<input type="text" name="usuario" placeholder="Nome de usuário ou e-mail" required value="{{$_SESSION['user'] or ''}}" @yield('afUser')>
			</div>
			<div class="form-group textbox">
				<input type="password" name="senha" placeholder="Senha" required @yield('afSenha')>
				<button type="button" class="senha-util" data-target="" title="Mostrar senha" class="mostra_senha"><i class="fas fa-eye"></i></button>
			</div>
			<div class="form-group">
				<input type="checkbox" name="save_user" value="yes" {{$saveuserChecked}}> Lembrar meu nome de usuário
			</div>
			<div class="form-group">
				<button type="submit">Entrar</button>
			</div>

			<a href="/forgot">Esqueci a senha</a>
		</form>
	</div>
@endsection

@section('titulo', 'SMO ::: LOGIN')
@section ('msgRetorno', $msgRetorno)
@section ('loginFoto', $loginFoto)
@section ('afUser', $afUser)
@section ('afSenha', $afSenha)
