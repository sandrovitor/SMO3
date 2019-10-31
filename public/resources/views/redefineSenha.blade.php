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
		<h3>SMO :: Redefinir senha<br>
		<small class="text-muted">Sistema de Mapas Online 3.0</small></h3>
		<form method="post" action="#" onsubmit="return false;">
			<div class="mb-4"><i>{{$u->user}}</i>, redefina sua senha.</div>
			<div class="form-group textbox">
				<input type="password" class="senha" name="senha1" required placeholder="Nova senha" autofocus>
			</div>
			<div class="form-group textbox">
				<input type="password" class="senha" name="senha2" required placeholder="Repita nova senha">
			</div>
			<div class="form-group">
				<input type="hidden" name="token" value="{{$token}}">
				<input type="hidden" name="uid" value="{{$u->id}}">
				<button type="button" class="btn-dark" onclick="verSenhas()">Ver/ocultar senhas</button>
				<button type="submit" onclick="return redefinir();">Redefinir</button>
			</div>

		</form>
	</div>
	<script>
		function redefinir(){
			if($('[name="senha1"]').val() != $('[name="senha2"]').val()) {
				alert('Senhas não são iguais!');
				$('[name="senha1"]').focus();
			} else {
				$.post('/redefine',{
					senha: $('[name="senha1"]').val(),
					token: $('[name="token"]').val(),
					uid: $('[name="uid"]').val()
				},function(data){
					if(data == 'OK') {
						location.href='/login';
					} else {
						alert(data);
					}
					return false;
				});
			}

			return false;
		}
	</script>
@endsection

@section('titulo', 'SMO ::: Redefinir de Senha')
@section('msgRetorno')
	{!!$msgRetorno!!}
	@if($u->bloqueado == '1')
		<div class="alert alert-info">
			Redefinir a senha não vai liberar acesso à sua conta que se encontra <strong>bloqueada</strong>. Para desbloquear, contate administrador.
		</div>
	@elseif((int)$u->tentativas >= 3)
		<div class="alert alert-info">
			Redefinir a senha não vai liberar acesso à sua conta que se encontra <strong>bloqueada por muitas tentativas erradas</strong>. Para desbloquear, contate administrador.
		</div>
	@elseif(new DateTime($u->expira) < new DateTime('now'))
		<div class="alert alert-info">
			Redefinir a senha não vai liberar acesso à sua conta que se encontra <strong>expirada</strong>. Para desbloquear, contate administrador.
		</div>
	@endif
@endsection
@section ('loginFoto', $loginFoto)
@section ('saveuserChecked', $saveuserChecked)
@section ('afUser', $afUser)
@section ('afSenha', $afSenha)
