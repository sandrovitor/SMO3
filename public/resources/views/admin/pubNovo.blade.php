@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	
@endphp

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPublicador')}}">Publicadores</a></li>
	<li class="breadcrumb-item active">Novo</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)
	
@section('conteudo')
	<form action="" method="post" onsubmit="return valida();">
		<div class="row">
			<div class="col-12 col-md-6 col-lg-4">
				<div class="form-group">
					<label>Primeiro nome</label>
					<input type="text" name="nome" class="form-control form-control-sm" maxlength="30" required>
				</div>
				<div class="form-group">
					<label>Último nome</label>
					<input type="text" name="sobrenome" class="form-control form-control-sm" maxlength="30" required>
				</div>
				<div class="form-group">
					<label>Nome de usuário</label>
					<input type="text" name="usuario" class="form-control form-control-sm" maxlength="16" required>
					<small class="text-muted">Será usado para logar no sistema. Não use caracteres especiais. Somente letras e números.</small>
				</div>
				<div class="form-group">
					<label>Endereço de e-mail</label>
					<input type="text" name="email" class="form-control form-control-sm" maxlength="16">
					<small class="text-muted">Alguns contatos do SMO serão realizados por e-mail.</small>
				</div>
				<div class="form-group">
					<div class="form-check">
						<label class="form-check-label">
							<input name="senhapadrao" type="checkbox" class="form-check-input" value="yes" checked="checked"> Usar senha padrão do sistema.
							<br><small class="text-muted">A senha padrão 12345678 será usada.</small>
						</label>
					</div>
				</div>
				<div id="senhas" style="display:none">
					<div class="form-group">
						<label>Senha</label>
						<input type="password" name="senha1" class="form-control form-control-sm" maxlength="12">
						<small class="text-muted">A senha deve conter de 8 a 12 caracteres.</small>
					</div>
					<div class="form-group">
						<label>Repita senha</label>
						<input type="password" name="senha2" class="form-control form-control-sm" maxlength="12">
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-4">
				<div class="form-group">
					<label>Nível de acesso</label>
					<select class="form-control form-control-sm" name="nivel">
						<option value="0">Nível 0 - Sem acesso</option>
						<option value="1">Nível 1 - Visitante</option>
						<option value="2">Nível 2 - Publicador</option>
						<option value="3">Nível 3 - Pioneiro Regular</option>
						<option value="4">Nível 4 - Ancião</option>
						<option value="5">Nível 5 - Administrador</option>
					</select>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success btn-sm">Salvar</button>
					<button type="reset" class="btn btn-warning btn-sm">Apagar</button>
				</div>
			</div>
			<div class="col-12 col-md-6 col-lg-4">
				<div class="border border-info shadow-sm p-2">
					<h6><strong>NÍVEIS DE ACESSO</strong></h6>
					<ul>
						<li><strong>Nível 0:</strong> Usuário sem acesso. Não consegue logar.</li>
						<li><strong>Nível 1:</strong> Visitante. Perfil temporário que expira em poucos dias.</li>
						<li><strong>Nível 2:</strong> Publicador.</li>
						<li><strong>Nível 3:</strong> Pioneiro Regular.</li>
						<li><strong>Nível 4:</strong> Ancião. Possui acesso a algumas funções administrativas.</li>
						<li><strong>Nível 5:</strong> Administrador. Tem acesso a todas as funções do sistema. MUITO CUIDADO, pois um usuário com muitas permissões pode fazer uma configuração errada acidentalmente.</li>
					</ul>
				</div>
				<kbd>Terminar página</kbd>
			</div>
		</div>
	</form>
@endsection

@section('script')
	<script>
	function valida()
	{
		if($('[name="senhapadrao"]').prop('checked') == true) {
			$('[name="senha1"], [name="senha2"]').val('12345678');
		} else {
			if($('[name="senha1"]').val() == '' || $('[name="senha2"]').val() == '') {
				alert('Forneça uma senha e confirme a senha.')
				return false;
			}
		}

		return true;
	}
	$(document).ready(function(){
		$(document).on('click', '[name="senhapadrao"]', function(){
			if($(this).prop('checked') == true) {
				$('#senhas').slideUp();
			} else {
				$('#senhas').slideDown();
			}
		});
	});
	</script>
@endsection