@extends('layouts.layoutindex')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
    }
    switch($user->nivel) {
        case '1': $nivel = 'Visitante'; break;
        case '2': $nivel = 'Publicador'; break;
        case '3': $nivel = 'Pioneiro Regular'; break;
        case '4': $nivel = 'Ancião'; break;
        case '5': $nivel = 'Administrador'; break;
        case '0': $nivel = 'SEM ACESSOS'; break;
    }

    $d1 = new DateTime($user->criado);
    $d2 = new DateTime($user->expira);
    $hoje = new DateTime();
    $diff = $hoje->diff($d2);
    //var_dump($diff);

    $validade = '';
    if($diff->invert == 1 || ($diff->invert == 0 && $diff->days < 30)) {
        $validade = '<span class="badge badge-danger" style="font-size: .9rem;">';
    } else if($diff->days >= 30 && $diff->days <= 60) {
        $validade = '<span class="badge badge-warning" style="font-size: .9rem;">';
    } else {
        $validade = '<span class="badge badge-primary" style="font-size: .9rem;">';
    }

    if($diff->invert == 1) {
        $validade .= '- '.$diff->days.' dias </span>';
    } else {
        $validade .= ' '.$diff->days.' dias </span>';
    }

    if($user->beta == '1') {
        $beta = '<span class="badge badge-success"><i class="fas fa-check"></i> SIM</span>';
    } else {
        $beta = '<span class="badge badge-secondary"><i class="fas fa-times"></i> NÃO</span>';
    }

    if($user->change_pass == 'n') {
        $trocasenha = '<span class="badge badge-success"><i class="fas fa-check"></i> Tudo certo</span>';
    } else {
        $trocasenha = '<span class="badge badge-warning"><i class="fas fa-check"></i> TROQUE A SENHA!</span>';
    }

    if($user->modo_facil == '0') {
        $facil = '<span class="badge badge-secondary"> DESATIVADO </span>';
    } else {
        $facil = '<span class="badge badge-success"> ATIVADO </span>';
    }

    $mapa = new Mapa();
    $estudos = $mapa->getBE($_SESSION['id']);
    
    if($user->ma === '0') {
        // Assistente desativado
        $ma = '<span class="badge badge-secondary"> DESATIVADO</span>';
    } else {
        // Ativado
        $ma = '<span class="badge badge-success"> ATIVO</span>';
    }
@endphp
@section ('paginaCorrente', 'Meu Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Meu Perfil</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4">
            <div class="shadow-sm p-3 mb-3 border border-1">
                <div class="imagem-usuario">
                    <img src="/images/user2.png">
                </div>
                <h5 class="text-center mt-2 mb-4">{{$user->nome}} {{$user->sobrenome}}</h5>
                <hr>
                <strong>Usuário:</strong> <i>{{$user->user}}</i><br>
                <span class="badge badge-success" style="font-size: .9rem;"><i class="fas fa-lock"></i> &nbsp;<strong>Nível {{$user->nivel}}</strong></span> - {{$nivel}}
                <hr>
                <strong>Criado em: </strong> &nbsp;&nbsp; {{$d1->format('d/m/Y H:i')}} <br>
                <strong>Validade: </strong>  &nbsp;&nbsp; {{$d2->format('d/m/Y H:i')}} &nbsp; {!!$validade!!}
            </div>
            <div class="shadow-sm p-3 mb-3 border border-1">
                <h5 class="mb-3">Suas configurações</h5>
                <strong>Usuário BETA:</strong> &nbsp;&nbsp; {!!$beta!!} <br>
                <strong>Status da senha:</strong> &nbsp;&nbsp; {!!$trocasenha!!} <br>
                <strong>Acessos:</strong> &nbsp;&nbsp; <span class="badge badge-info">{!!$user->qtd_login!!}</span> <br>
                <strong><i class="fas fa-ribbon"></i> Modo fácil:</strong> &nbsp;&nbsp; {!!$facil!!} <br>
                <hr>
                <h5><strong>Assistente de Ministério &nbsp;</strong></h5>
                @if(isset($_SESSION['ma']) && $_SESSION['ma'] === TRUE)
                <div class="border border-danger p-2">
                    <button type="button" class="btn btn-sm btn-warning mb-2" onclick="desMA()">Desativar Assistente</button> <br>
                    <button type="button" class="btn btn-sm btn-danger mb-2" onclick="desApagaMA()">Desativar e apagar tudo</button>
                </div>
                @else
                <button type="button" class="btn btn-sm btn-primary mb-2" onclick="ativaMA()">Ativar Assistente</button>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-7 col-xl-8">
            <div class="shadow-sm p-3 mb-3 border border-1">
                <h5 class="mb-3">Estudos</h5>
                @if($estudos !== false)
                    <div class="d-flex flex-wrap">
                    @foreach($estudos as $x)
                        <div class="border bg-light rounded-lg mr-2 mb-2 py-2 px-3" title="{{$x->nome}} [{{$x->bairro}}]" data-toggle="tooltip">
                            <strong>{{$x->nome}}</strong> &nbsp; &nbsp; <button type="button" class="btn btn-sm btn-danger" onclick="removeEstudo({{$x->id}})"><i class="fas fa-times"></i></button>
                        </div>
                    @endforeach
                    </div>
                @else
                    <div class="text-center" style="width: 100%;">Nenhum estudante ainda</div>
                @endif
            </div>
            
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="shadow-sm p-3 mb-3 border border-1">
                        <h5 class="mb-3">Meus dados</h5>
                        <form action="{{$router->generate('perfilMeusDadosPOST')}}" method="post">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" name="nome" class="form-control form-control-sm" value="{{$user->nome}}" maxlength="25" required>
                            </div>
                            <div class="form-group">
                                <label>Sobrenome</label>
                                <input type="text" name="sobrenome" class="form-control form-control-sm" value="{{$user->sobrenome}}" maxlength="25" required>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="shadow-sm p-3 mb-3 border border-1">
                        <h5 class="mb-3">Senha</h5>
                        <form action="{{$router->generate('perfilTrocaSenhaPOST')}}" method="post">
                            <div class="form-group">
                                <label>Senha atual</label>
                                <input type="password" name="senha_atual" class="form-control form-control-sm" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <label>Nova senha</label>
                                <input type="password" name="senha_nova" class="form-control form-control-sm" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <label>Confirme a senha</label>
                                <input type="password" name="senha_confirma" class="form-control form-control-sm" maxlength="32" required>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <button type="button" class="btn btn-light btn-sm mb-2" onclick="showSenhas()"><i class="fas fa-eye"></i> Ver/ocultar senhas</button><br>
                                <button type="reset" class="btn btn-warning btn-sm">Reset</button> <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection

@section ('script')
<style>

</style>
<script>
function showSenhas() {
    if($('[name="senha_atual"]').attr('type') == 'text') {
        $('[name="senha_atual"], [name="senha_nova"], [name="senha_confirma"]').attr('type','password');
    } else {
        $('[name="senha_atual"], [name="senha_nova"], [name="senha_confirma"]').attr('type','text');
    }
}
function removeEstudo(id) {
    let item = $(event.target).parents('div').eq(0);
    $.post('{{$router->generate("functionsGeral")}}',{
        funcao: 'removeEstudo',
        id: id
    },function(data){
        if(data == 'OK') {
            item.fadeOut('fast',function(){
                $('[data-toggle="tooltip"]').tooltip('disable');
                item.remove();
                $('[data-toggle="tooltip"]').tooltip();});
        } else {
            alert(data);
        }
    });
}
function desMA() {
    let x = confirm('Você deseja desativar o Assistente de Ministério?'+"\n"+ 'Suas informações continuarão salvas, mas inacessíveis.');
    let id = {{$_SESSION['id']}};
    if(x == true) {
        $.post('{{$router->generate("functionsGeral")}}',{
            funcao: 'desMA',
            id: id
        },function(data){
            if(data == 'OK') {
                location.reload();
            } else {
                alert(data);
            }
        });
    }
}
function desApagaMA() {
    let x = confirm('Você deseja desativar o Assistente de Ministério e APAGAR TUDO?'+"\n\nATENÇÃO:\n"+ 'Suas informações SERÃO APAGADAS e não poderá ser desfeito.');
    let id = {{$_SESSION['id']}};
    if(x == true) {
        $.post('{{$router->generate("functionsGeral")}}',{
            funcao: 'desApagaMA',
            id: id
        },function(data){
            if(data == 'OK') {
                location.reload();
            } else {
                alert(data);
            }
        });
    }
}
function ativaMA() {
    let id = {{$_SESSION['id']}};
    $.post('{{$router->generate("functionsGeral")}}',{
        funcao: 'ativaMA',
        id: id
    },function(data){
        if(data == 'OK') {
            location.reload();
        } else {
            console.log(data);
            alert(data);
        }
    });
}
$(document).ready(function(){

    $( document ).ajaxStart(function() {
        $('[data-toggle="tooltip"]').tooltip('disable');
    });
    $( document ).ajaxStop(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
</script>
@endsection