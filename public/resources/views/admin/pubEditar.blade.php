@extends('layouts.layoutadmin')

@section ('paginaCorrente', 'Administração')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

	// TESTES
	//$u->modo_facil = '1';

	//var_dump($u);

	$n0 = $n1 = $n2 = $n3 = $n4 = $n5 = '';
	switch((int)$u->nivel) {
		case 0: $n0 = 'selected="selected"'; break;
		case 1: $n1 = 'selected="selected"'; break;
		case 2: $n2 = 'selected="selected"'; break;
		case 3: $n3 = 'selected="selected"'; break;
		case 4: $n4 = 'selected="selected"'; break;
		case 5: $n5 = 'selected="selected"'; break;
	}

	$criado = new DateTime($u->criado);
	$atualizado = new DateTime($u->atualizado);
	$hoje = new DateTime();
	$expira = new DateTime($u->expira);
	$diff = $hoje->diff($expira);
	$ultLogin = new DateTime($u->atualizado);

	if((bool)$u->beta === TRUE) {
		$betaStr = '<span class="badge badge-success text-white" style="font-size:1rem;" title="Este é um usuário BETA Tester." data-toggle="tooltip"><i class="fas fa-check"></i></span>';
	} else {
		$betaStr = '<span class="badge badge-secondary text-white" style="font-size:1rem;" title="Não é um usuário BETA Tester." data-toggle="tooltip"><i class="fas fa-times"></i></span>';
	}

	$situacao = 'bg-light';
	$situacaoStr = '<span class="badge badge-success" style="font-size:1rem;"><i class="fas fa-check"></i> OK</span>';
	$bloq = FALSE;
	if((bool)$u->bloqueado == TRUE) {
		$situacao = 'bg-danger  text-white';
		$situacaoStr = '<span class="badge badge-danger" title="Bloqueado manualmente." data-toggle="tooltip"><i class="fas fa-times"></i> BLOQUEADO</span>';
		$bloq = TRUE;
	}

	if((int)$u->tentativas >= 3) {
		$situacao = 'bg-danger  text-white';
		$situacaoStr = '<span class="badge badge-danger" title="Excedeu 3 tentativas de login." data-toggle="tooltip"><i class="fas fa-times"></i> BLOQUEADO</span>';
		$bloq = TRUE;
	}

	if($hoje >= $expira) {
		$situacao = 'bg-danger  text-white';
		$situacaoStr = '<span class="badge badge-danger" title="Validade expirou." data-toggle="tooltip"><i class="fas fa-times"></i> BLOQUEADO</span>';
		$bloq = TRUE;
	}

	if($u->change_pass == 'n') {
		$trocaSenhaStr = '<span class="badge badge-success" style="font-size:1rem;" title="Nenhuma notificação de troca de senha" data-toggle="tooltip"><i class="fas fa-check"></i> OK</span>';
	} else {
		$trocaSenhaStr = '<span class="badge badge-danger" style="font-size:1rem;" title="Há notificação de troca de senha" data-toggle="tooltip"><i class="fas fa-times"></i> PENDENTE</span>';
	}

	if($u->modo_facil == '0') { // DESATIVADO
		$mfacil = '<span class="badge badge-secondary" style="font-size:1rem;" title="Modo fácil desativado" data-toggle="tooltip"> <i class="fas fa-times"></i></span>';
	} else { // ATIVADO
		$mfacil = '<span class="badge badge-success" style="font-size:1rem;" title="Modo fácil ativado" data-toggle="tooltip"> <i class="fas fa-check"></i></span>';
	}

	$diffStr = '';

	if($diff->invert == 0) {
		
		if($diff->days >= 60) {
			$diffStr = '<span class="badge badge-success">';
		} else if($diff->days > 30) {
			$diffStr = '<span class="badge badge-info">';
		} else {
			$diffStr = '<span class="badge badge-warning">';
		}

		$diffStr .= 'EXPIRA EM '.$diff->days.' DIAS</span>';
	} else {
		$diffStr = '<span class="badge badge-danger">EXPIROU HÁ '.$diff->days.' DIA(S)</span>';
	}
	//var_dump($diff);
@endphp

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPublicador')}}">Publicadores</a></li>
	<li class="breadcrumb-item"><a href="{{$router->generate('admPubVer')}}">Listar</a></li>
	<li class="breadcrumb-item active">Editar: {{$u->nome}} {{$u->sobrenome}}</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
<form action="" method="post">
	<div class="row">
		<div class="col-12 col-lg-4">
			<div class="form-group">
				<label>Nome:</label>
				<input type="text" name="nome" class="form-control form-control-sm" maxlength="30" value="{{$u->nome}}" required>
			</div>
			<div class="form-group">
				<label>Sobrenome:</label>
				<input type="text" name="sobrenome" class="form-control form-control-sm" maxlength="30" value="{{$u->sobrenome}}" required>
			</div>
			<div class="form-group">
				<label>Nome de usuário:</label>
				<input type="text" name="usuario" class="form-control form-control-sm" maxlength="16" value="{{$u->user}}" required>
				<small class="text-muted">Será usado para logar no sistema. Não use caracteres especiais. Somente letras e números.</small>
			</div>
			<div class="form-group">
				<label>Endereço de e-mail:</label>
				<input type="text" name="email" class="form-control form-control-sm" value="{{$u->email}}" maxlength="40">
				<small class="text-muted">Alguns contatos do SMO serão realizados por e-mail.</small>
			</div>
			<div class="form-group">
				<label>Criado em:</label>
				<input type="date" class="form-control form-control-sm" value="{{$criado->format('Y-m-d')}}" disabled>
			</div>
			<div class="form-group">
				<label>Nível de acesso</label>
				<select class="form-control form-control-sm" name="nivel">
					<option value="0" {{$n0}}>Nível 0 - Sem acesso</option>
					<option value="1" {{$n1}}>Nível 1 - Visitante</option>
					<option value="2" {{$n2}}>Nível 2 - Publicador</option>
					<option value="3" {{$n3}}>Nível 3 - Pioneiro Regular</option>
					<option value="4" {{$n4}}>Nível 4 - Ancião</option>
					<option value="5" {{$n5}}>Nível 5 - Administrador</option>
				</select>
			</div>
		</div>
		<div class="col-12 col-lg-4">
			<div class="border shadow-sm p-3 mb-2">
				<h5><strong>Validade do perfil</strong><br><small>{!!$diffStr!!}</small></h5>
				<br>
				<div class="form-group">
					<input type="date" name="expira" class="form-control form-control-sm" value="{{$expira->format('Y-m-d')}}">
				</div>
			</div>

			<div class="border shadow-sm p-3 mb-2">
				<h5><strong>Mais informações</strong></h5>
				<br>
				<div class="row mb-2">
					<div class="col-6">
						<div class="bg-primary text-white text-center p-2 rounded-sm">Acessos <span class="badge badge-light">{{$u->qtd_login}}</span></div>
					</div>
					<div class="col-6">
						<div class="bg-primary text-white text-center p-2 rounded-sm">Tentativas <span class="badge badge-light">{{$u->tentativas}}</span></div>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-6">
						<div class="bg-light text-center p-2 rounded-sm">Usuário BETA<br> {!!$betaStr!!}</div>
					</div>
					<div class="col-6">
						<div class="{{$situacao}} text-center p-2 rounded-sm">Situação <br>{!!$situacaoStr!!}</div>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-12">
						<div class="bg-primary text-white text-center p-2 rounded-sm"><strong>Último login:</strong>
						<br> <span class="badge badge-light" style="font-size: 1rem;">{{$ultLogin->format('d/m/Y H:i')}}</span></div>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-6">
						<div class="bg-light text-center p-2 rounded-sm">Troca de senha<br> {!!$trocaSenhaStr!!}</div>
					</div>
					<div class="col-6">
						<div class="bg-light text-center p-2 rounded-sm"> Modo Fácil <br> {!!$mfacil!!}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-4">
			<div class="border shadow-sm p-3">
				<h5><strong>OPÇÕES RÁPIDAS</strong></h5>
				<br>
				<div class="row mb-2">
					<div class="col-6">
						@if($bloq == TRUE)
						<button type="button" class="btn btn-block btn-primary" onclick="desbloquear()">Desbloquear</button>
						@else
						<button type="button" class="btn btn-block btn-warning" onclick="bloquear()">Bloquear</button>
						@endif
					</div>
					<div class="col-6">
						<button type="button" class="btn btn-block btn-primary" onclick="resetasenha()">Resetar senha</button>
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-6">
						@if((bool)$u->beta === TRUE)
						<button type="button" class="btn btn-block btn-danger text-white" style="font-size: .8rem;" onclick="desativabeta()">Remover BETA</button>
						@else
						<button type="button" class="btn btn-block btn-success" onclick="ativabeta()">Tornar BETA</button>
						@endif
					</div>
					<div class="col-6">
						@if($u->change_pass == 'n')
						<button type="button" class="btn btn-block btn-warning" style="font-size: .8rem;" onclick="notificaSenha()">Notificar senha</button>
						@else
						<button type="button" class="btn btn-block btn-info" style="font-size: .8rem;" onclick="desnotificaSenha()">Não notificar</button>
						@endif
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-12">
						@if($u->modo_facil == '0')
						<button type="button" class="btn btn-block btn-info" onclick="ativaMFacil()">Ativar modo fácil</button>
						@else
						<button type="button" class="btn btn-block btn-warning" onclick="desativaMFacil()">Desativar modo fácil</button>
						@endif
					</div>
				</div>
				<hr>
				<div class="row mb-2">
					<div class="col-12">
						<button type="button" class="btn btn-block btn-danger" onclick="deletePerfil()"><strong><i class="fas fa-eraser"></i> APAGAR PERFIL</strong></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="form-group">
				<input type="hidden" name="id" value="{{$u->id}}">
				<button type="submit" class="btn btn-success">Salvar</button>
				<button type="reset" class="btn btn-warning">Resetar</button>
			</div>
		</div>
	</div>
</form>
@endsection

@section('script')
<script>
	function bloquear() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setPubBloquear',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function desbloquear() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setPubDesbloquear',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function ativabeta() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setBETAAtiva',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function desativabeta() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setBETADesativa',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function resetasenha() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			let x = confirm('O usuário esqueceu a senha não foi? Previmos que isso aconteceria... Aqui você pode resetar para senha padrão 12345678.'+"\n\nContinuar?");
			if(x == true) {
				$.post('{{$router->generate("admFunctions")}}', {
					funcao: 'setPubSenhaReset',
					id: id
				}, function(data) {
					if(data == 'OK') {
						alert('Senha resetada com sucesso. Valerá no próximo login.');
					} else {
						alert(data);
					}
				});
			}
			
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function ativaMFacil() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setModoFacilAtiva',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function desativaMFacil() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setModoFacilDesativa',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function notificaSenha() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setNotificaSenha',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function desnotificaSenha() {
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			$.post('{{$router->generate("admFunctions")}}', {
				funcao: 'setDesnotificaSenha',
				id: id
			}, function(data) {
				if(data == 'OK') {
					location.reload();
				} else {
					alert(data);
				}
			});
		} else {
			alert('ID local do usuário é inválido.');
		}
	}

	function deletePerfil()
	{
		let id = $('[type="hidden"][name="id"]').val();
		if(id != undefined) {
			let x = confirm("Você tem certeza de que deseja apagar esse perfil??\nUma opção viável, seria bloquear o acesso da conta.\n\n\nQuer continuar com a exclusão?");
			if(x == true) {
				$.post('{{$router->generate("admFunctions")}}', {
					funcao: 'deleteUsuario',
					id: id
				}, function(data) {
					if(data == 'OK') {
						location.href = "{{$router->generate('admPubVer')}}";
					} else {
						alert(data);
					}
				});
			}
			
		} else {
			alert('ID local do usuário é inválido.');
		}
	}
</script>
@endsection