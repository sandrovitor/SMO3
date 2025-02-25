@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    
    //var_dump($bairros);
    

    //var_dump($regiao);
    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
    <li class="breadcrumb-item active">Bairros</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 d-flex flex-wrap align-items-start">
            <div class="bloco-surdo border-0">
                <div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">NOVA REGIÃO</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse ">
                        <form action="#" method="post" onsubmit="jss11(); return false;">
                            <div class="form-group">
                                <label>Escolha a região:</label>
                                <select class="form-control form-control-sm" name="regiao_id_todos" onchange="jss01(this)">
                                    <option value="0"></option>
                                    @foreach($regiao as $key => $valor)
                                    
                                    @if($valor == '')
                                    <option data-nome="{{$valor}}" value="{{$key}}">Região {{$key}}</option>
                                    @else
                                    <option data-nome="{{$valor}}" value="{{$key}}">Região {{$key}} - {{$valor}}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Definir nome da região:</label>
                                <input type="text" class="form-control form-control-sm" name="regiao_nome">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Criar/Alterar região</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bloco-surdo border-0">
                <div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">NOVO BAIRRO</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse ">
                        <form action="#" method="post" onsubmit="jss12(); return false;">
                            <div class="form-group">
                                <label>Nome do bairro:</label>
                                <input type="text" class="form-control form-control-sm" name="bairro_nome">
                            </div>
                            <div class="form-group">
                                <label>Escolha a região:</label>
                                <select class="form-control form-control-sm" name="regiao_id">
                                    @foreach($regiao as $key => $valor)
                                    
                                    @if($valor != '')
                                    <option data-nome="{{$valor}}" value="{{$key}}">Região {{$key}} - {{$valor}}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Criar bairro</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bloco-surdo border-0">
                <div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">EDITAR BAIRRO</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse ">
                        <form action="#" method="post" onsubmit="jss13(); return false;">
                            <div class="form-group">
                                <label>Escolha o bairro:</label>
                                <select class="form-control form-control-sm" name="bairro_id" onchange="jss02(this)">
                                    <option value="0"></option>
                                    @php
                                        $x = '';
                                    @endphp
                                    @foreach($bairros as $b)
                                        @if($x == '')
                                        <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                        @php
                                            $x = $b->regiao_nome;
                                        @endphp
                                        @elseif($x != $b->regiao_nome)
                                        </optgroup>
                                        <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                        @php
                                            $x = $b->regiao_nome;
                                        @endphp
                                        @endif

                                        <option value="{{$b->id}}" data-regiao="{{$b->regiao_numero}}">{{$b->bairro}}</option>
                                    @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Novo nome:</label>
                                <input type="text" class="form-control form-control-sm" name="bairro_nome">
                            </div>
                            <div class="form-group">
                                <label>Alterar região:</label>
                                <select class="form-control form-control-sm" name="regiao_id">
                                    @foreach($regiao as $key => $valor)
                                    
                                    @if($valor != '')
                                    <option data-nome="{{$valor}}" value="{{$key}}">Região {{$key}} - {{$valor}}</option>
                                    @endif

                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Criar bairro</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bloco-surdo border-0">
                <div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">APAGAR BAIRRO</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse ">
                        <form action="#" method="post" onsubmit="jss14(); return false;">
                            <div class="form-group">
                                <label>Escolha o bairro:</label>
                                <select class="form-control form-control-sm" name="bairro_id">
                                    <option value="0"></option>
                                    @php
                                        $x = '';
                                    @endphp
                                    @foreach($bairros as $b)
                                        @if($x == '')
                                        <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                        @php
                                            $x = $b->regiao_nome;
                                        @endphp
                                        @elseif($x != $b->regiao_nome)
                                        </optgroup>
                                        <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                        @php
                                            $x = $b->regiao_nome;
                                        @endphp
                                        @endif
                                        
                                        <option value="{{$b->id}}" data-regiao="{{$b->regiao_numero}}">{{$b->bairro}}</option>
                                    @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm">APAGAR bairro?</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bloco-surdo border-0">
                <div class="card">
                    <div class="card-header px-3 py-2">
                        <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">LISTA DE BAIRROS</a>
                    </div>
                    <div class="card-body px-3 py-2 collapse " style="font-size: .875rem;">
                        @php
                            $x = '';
                        @endphp
                        @foreach($bairros as $b)
                            @if($x == '')
                            <h6 class="font-weight-bold">Região {{$b->regiao_numero}} - {{$b->regiao_nome}}</h6>
                            <div class="d-flex flex-wrap">
                            @php
                                $x = $b->regiao_nome;
                            @endphp
                            @elseif($x != $b->regiao_nome)
                            </div>
                            <hr>
                            <h6 class="font-weight-bold">Região {{$b->regiao_numero}} - {{$b->regiao_nome}}</h6>
                            <div class="d-flex flex-wrap">
                            @php
                                $x = $b->regiao_nome;
                            @endphp
                            @endif
                            <div class="px-1 py-1 flex-fill w-50">{{$b->bairro}}</div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
@endsection

@section('script')
<script>
    function cardBodyCollapse(x) {
        $(x).parents('.card').children('.card-body.collapse').collapse('toggle');
    }
    function jss01(x) {
        if($(x).find(':selected').val() == 0) {
            $(x).parents('form').find('[name="regiao_nome"]').val('');
        } else {
            $(x).parents('form').find('[name="regiao_nome"]').val( $(x).find(':selected').attr('data-nome') );
        }
    }
    function jss02(x) {
        if($(x).find(':selected').val() == 0) {
            $(x).parents('form').find('[name="bairro_nome"]').val('');
        } else {
            $(x).parents('form').find('[name="bairro_nome"]').val( $(x).find(':selected').text() );
        }
    }

    function jss11() {
        let x = $(event.target);
        if($(x).find('[name="regiao_id_todos"]').find(':selected').val() != 0) {
            $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'setRegiao',
                regiao_id: $(x).find('[name="regiao_id_todos"]').find(':selected').val(),
                regiao_nome: $(x).find('[name="regiao_nome"]').val()
            },function(data){
                if(isJson(data)) {
                    let res = JSON.parse(data);
                    $('[name="regiao_id"]').find('option').remove();

                    for(key = 1; key <= Object.keys(res).length; key++) {
                        let valor = res[key];
                        let item = $(x).find('[name="regiao_id_todos"]');
                        item.find('[value="'+key+'"]').attr('data-nome', valor);
                        if(valor != "") {
                            item.find('[value="'+key+'"]').text('Região '+key+ ' - '+valor);
                            $('[name="regiao_id"]').append('<option value="'+key+'" data-nome="'+valor+'">Região '+key+ ' - '+valor+'</option>')
                        } else {
                            item.find('[value="'+key+'"]').text('Região '+key);
                        }
                    }
                    
                    
                } else {
                    alert('O servidor enviou informações inesperadas.');
                }
            });
        } else {
            alert('Escolha uma região para continuar...');
        }
        return false;
    }

    function jss12() {
        let x = $(event.target);
        let bNome = $(x).find('[name="bairro_nome"]').val();
        if(bNome.trim() != '') {
            $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'setBairroNovo',
                bairro_nome: bNome.trim(),
                regiao_id: $(x).find('[name="regiao_id"]').find(':selected').val()
            },function(data){
                if(data == 'OK') {
                    location.reload();
                } else {
                    alert(data);
                }
            });
        } else {
            alert('Digite o nome do bairro a ser adicionado.');
        }

        return false;
    }

    function jss13() {
        let x = $(event.target);
        let bItem = $(x).find('[name="bairro_id"]').find(':selected');
        let bNome = $(x).find('[name="bairro_nome"]').val().trim();
        let regiao = $(x).find('[name="regiao_id"]').val();

        if(bNome != '') {
            if(bItem.text() == bNome && bItem.attr('data-regiao') == regiao) {
                alert('Nenhuma alteração foi realizada.');
            } else {
                $.post('{{$router->generate("admFunctions")}}',
                {
                    funcao: 'setBairroNome',
                    bairro_id: bItem.val(),
                    bairro_nome: bNome.trim(),
                    regiao_id: $(x).find('[name="regiao_id"]').find(':selected').val()
                },function(data){
                    if(data == 'OK') {
                        location.reload();
                    } else {
                        alert(data);
                    }
                });
            }
        } else {
            alert('Digite um novo nome para o bairro (não pode ficar em branco).');
        }

        return false;
    }

    function jss14() {
        let x = $(event.target);
        let bItem = $(x).find('[name="bairro_id"]').find(':selected');
        if(bItem.val() != 0) {
            let resp = confirm('Você tem certeza de que deseja excluir esse bairro?');
            
            if(resp == true) {
                $.post('{{$router->generate("admFunctions")}}',
                {
                    funcao: 'setBairroDelete',
                    bairro_id: bItem.val(),
                },function(data){
                    if(data == 'OK') {
                        location.reload();
                    } else {
                        alert(data);
                    }
                });
            }
        } else {
            alert('Escolha um bairro para excluir.');
        }

    }


    $(document).ready(function(){
        
    });
</script>
@endsection