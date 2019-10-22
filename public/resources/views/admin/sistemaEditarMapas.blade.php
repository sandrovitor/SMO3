@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    $surdoJSON = array();
    if(!empty($mapas)){
        foreach($mapas as $m) {
            array_push($surdoJSON, array(
                'id' => $m->id,
                'nome' => $m->nome,
                'mapa' => $m->mapa,
                'mapa_indice' => $m->mapa_indice,
                'bairro' => $m->bairro,
                'bairro_id' => $m->bairro_id,
                'gps' => $m->gps,
                'regiao' => $m->regiao,
            ));
        }
    

        $surdoJSON = json_encode($surdoJSON);
    }
    
    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
    <li class="breadcrumb-item active">Editar Mapas</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <div class="card border border-primary">
                <div class="card-body p-2">
                    <button type="button" class="btn btn-primary btn-sm" onclick="modalNovoMapa()"><i class="fas fa-plus"></i> Novo mapa</button>
                    <button type="button" class="btn btn-success btn-sm" id="botaoSalvar" disabled><i class="fas fa-save"></i> Salvar edição</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-5 col-lg-4 col-xl-4">
            <div class="card">
                <div class="card-header font-weight-bold py-2 px-3">
                    Surdos sem mapa
                </div>
                <div class="card-body px-2 py-3" id="div_sem_mapas"></div>
            </div>
        </div>
        <div class="col-12 col-md-7 col-lg-8 col-xl-8">
            <div class="card">
                <div class="card-header font-weight-bold py-2 px-3">
                    Mapas &nbsp; 
                    <select id="sel_regiao_id">
                        @foreach($regiao as $key => $valor)
                            @if($valor != '')
                            <option value="{{$key}}">Região {{$key}} - {{$valor}}</option>
                            @endif
                        @endforeach
                    </select> &nbsp; 
                    <button type="button" class="btn btn-sm btn-primary" onclick="reloadSurdos()"><i class="fas fa-redo"></i></button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="mostrarTodosGPS()"><i class="fas fa-map-marker-alt"></i></button>
                </div>
                <div class="card-body px-2 py-3" id="div_mapas">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr style="background: #ffffb3;">
                                        <th colspan="3">BAIRRO MAPA</th>
                                    </tr>
                                    <tr style="font-size: 10px; background: #ffffdd;">
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Ordenar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ID</td>
                                        <td>ID</td>
                                        <td>ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAIS -->
    <div class="modal" id="modalNovoMapa">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Criar novo mapa</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><strong>Nome do mapa</strong></label>
                                <input type="text" class="form-control" maxlength="7" name="mapaNome">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-success" onclick="novoMapa()">Criar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalAddSurdo">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Adicionar surdo ao mapa</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Escolha o surdo:
                    <input type="hidden" name="mapaAlvo" value="">
                    <div class="surdosLista d-flex flex-wrap"></div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalGENERICO">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">RETORNO DO SERVIDOR</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Modal body..
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<style>
    #div_mapas,
    #div_sem_mapas {
        font-size: .875rem;
    }
    #div_mapas tbody td:first-of-type,
    #div_sem_mapas tbody td:first-of-type {
        width: 40px;
    }

    #modalAddSurdo .surdosLista div {
        margin-right: .5rem;
        padding:.3rem 1rem;
        border:1px solid #c3c3c3;
        transition: all .2s linear;
        cursor:pointer;
    }
    #modalAddSurdo .surdosLista div:hover {
        background-color: #f7ffaa;
    }
    #modalAddSurdo .surdosLista div.active,
    #modalAddSurdo .surdosLista div.active:hover {
        background-color: #82f178;
    }
</style>
<script>
    var surdosJSON = {!!$surdoJSON!!};
    var novaOrdem;

    function modalNovoMapa() {
        $('#modalNovoMapa').modal('show');
    }

    function novoMapa() {
        // Verifica a existência de um mapa igual
        let nome = $('#modalNovoMapa [name="mapaNome"]').val();
        if(nome == '') {
            alert('Digite um nome para o mapa');
            $('#modalNovoMapa [name="mapaNome"]').focus();
            return false;
        }
        nome = nome.toUpperCase();
        //console.log(nome);

        let surdos = surdosJSON;
        let nomeValido = true;
        surdos.forEach(function(s){
            if(s.mapa == nome) {
                nomeValido = false;
            }
        });
        
        if(nomeValido == false) {
            alert('Este nome de mapa já existe.');
            return false;
        }

        for(i=0; i < $('.mapaNome').length; i++) {
            if($('.mapaNome').eq(i).text() == nome) {
                nomeValido = false;
            }
        }

        if(nomeValido == false) {
            alert('Este nome de mapa já existe.');
            return false;
        }

        // Cria novo mapa
        let html = '<table class="table table-bordered table-sm">'+
                                    '<thead> <tr style="background: #e4a8d7;"> <th colspan="3"> MAPA NOVO <span class="glyphicon glyphicon-question-sign" title="Nome do bairro será adicionado automaticamente depois de salvar e atualizar."></span> &nbsp; <span class="float-right mapaNome">'+nome+'</span></th> </tr>'+
                                        '<tr style="font-size: 10px; background: #dec3d8;"> <th>ID</th> <th>Nome</th> <th>Ordenar</th> </tr>'+
                                    '</thead> <tbody>'+
                                    '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>'+
                                    '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>'+
                                    '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>'+
                                    '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>'+
                                    '</tbody></table>';

        $('#div_mapas').prepend('<div class="row"> <div class="col-12 offset-lg-2 col-lg-8">'+html+'</div> </div>');

        $('#modalNovoMapa [name="mapaNome"]').val('');
        $('#modalNovoMapa').modal('hide');
        $('#botaoSalvar').prop('disabled', false);

    }

    function modalAddSurdo(mapaNome) {
        // Verifica os surdos na tabela de surdos sem mapa
        if($('#div_sem_mapas table tbody tr').length == 0) {
            alert('Não há surdos sem mapa.');
            return false;
        }
        $('#modalAddSurdo .surdosLista').html('');
        $('#modalAddSurdo input[name="mapaAlvo"]').val(mapaNome);

        for(i=0; i<$('#div_sem_mapas table tbody tr').length; i++) {
            let l = $('#div_sem_mapas table tbody tr').eq(i);
            let surdo;

            
            for(j = 0; j < surdosJSON.length; j++) {
                if(surdosJSON[j].id == l.find('td').eq(0).text()) {
                    surdo = surdosJSON[j];
                    break;
                }
            }

            $('#modalAddSurdo .surdosLista').append('<div data-id="'+surdo.id+'" data-nome="'+surdo.nome+'" data-bairro="'+surdo.bairro+'"><strong>'+surdo.nome+'</strong> ['+surdo.bairro+']</div>');
        }

        $('#modalAddSurdo').modal('show');
    }

    function emGetMapas() {
        var surdos = surdosJSON;
        var mapaNome = ''; // Nome do mapa atual
        var mapaCont = 0; // Contagem de mapas por linha 
        var surdosCont = 0; // Contagem de surdos por mapa (até 4)

        

        // Limpa área
        var area = $('#div_mapas');
        var html = '';
        let surdosTotal = 0;
        area.html('');

        // Varre array
        surdos.forEach(function(s){
            if(s.mapa != '' && s.regiao == $('#sel_regiao_id').find(':selected').val()) {
                if(mapaNome == '') {
                    mapaNome = s.mapa;

                    // Inicia a linha e a coluna
                    html += '<div class="row"><div class="col-12 col-lg-6">'+
                                '<table class="table table-bordered table-sm">'+
                                    '<thead> <tr style="background: #ffffb3;"> <th colspan="3">'+s.bairro+' &nbsp; <span class="float-right mapaNome">'+mapaNome+'</span></th> </tr>'+
                                        '<tr style="font-size: 10px; background: #ffffdd;"> <th>ID</th> <th>Nome</th> <th>Ordenar</th> </tr>'+
                                    '</thead> <tbody>';

                    // Incrementa variaveis
                    mapaCont++;
                } else if(mapaNome != s.mapa) {
                    mapaNome = s.mapa;

                    // Antes de fechar o mapa, preenche com espaços vazios onde não houver surdo.
                    while(surdosCont < 4) {
                        html += '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>';
                        surdosCont++;
                    }

                    // Fecha o mapa
                    html += '</tbody></table>';
                    surdosCont = 0;

                    // Verifica quantidade de mapas na linha.
                    if(mapaCont == 2) {
                        // Fecha coluna e linha. Inicia nova linha e coluna
                        html += '</div></div> <div class="row"><div class="col-12 col-lg-6">';
                        mapaCont = 0;
                    } else {
                        // Fecha coluna. Inicia nova coluna
                        html += '</div> <div class="col-12 col-lg-6">';
                    }

                    // Inicia mapa
                    html += '<table class="table table-bordered table-sm">'+
                                    '<thead> <tr style="background: #ffffb3;"> <th colspan="3">'+s.bairro+' &nbsp; <span class="float-right mapaNome">'+mapaNome+'</span></th> </tr>'+
                                        '<tr style="font-size: 10px; background: #ffffdd;"> <th>ID</th> <th>Nome</th> <th>Ordenar</th> </tr>'+
                                    '</thead> <tbody>';

                                    
                    mapaCont++;
                }


                // Escreve linha do surdo
                html += '<tr data-original-indice="'+s.mapa_indice+'" data-surdoid="'+s.id+'">'+
                    '<td>'+s.id+'</td>'+
                    '<td>'+s.nome+'</td>'+
                    '<td><button type="button" class="btn btn-sm btn-light border btnOrderUp" data-toggle="tooltip" title="Mover para cima"><i class="fas fa-arrow-up"></i></button> '+
                    '<button type="button" class="btn btn-sm btn-light border btnOrderDown" data-toggle="tooltip" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button> '+
                    '<button type="button" class="btn btn-sm btn-danger btnRemove" data-toggle="tooltip" title="Remover surdo do mapa"><i class="fas fa-minus"></i></button></td>'+
                    '</tr>';

                surdosCont++;
                surdosTotal++;
            }

        });

        // Finaliza.
        if(surdosTotal > 0) {
            // Antes de fechar o mapa, preenche com espaços vazios onde não houver surdo.
            while(surdosCont < 4) {
                html += '<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>';
                surdosCont++;
            }

            // Fecha o mapa, coluna e linha
            html += '</tbody></table> </div> </div>';
        }
        

        area.append(html);

        /**
         * Surdos SEM MAPA
         */

        html = '';
        html+= '<table class="table table-bordered table-sm">'+
                '<thead class="thead-dark">'+
                    '<tr > <th>ID</th> <th>Nome</th> <th>Bairro</th> </tr>'+
                '</thead> <tbody>';

        surdosCont = 0;
        surdos.forEach(function(s){
            if(s.mapa == '' && s.regiao == $('#sel_regiao_id').find(':selected').val()) {
                html += '<tr> <td>'+s.id+'</td> <td>'+s.nome+'</td> <td style="font-size: .625rem">'+s.bairro+'</td> </tr>';
                surdosCont++;
            }
        });

        if(surdosCont == 0) {
            html = 'Nada encontrado';
        } else {
            html+='</tbody></table>';
        }

        $('#div_sem_mapas').html(html);

        // Desativas os botões inválidos
        desativaBotoes()
    }

    function desativaBotoes() {
        $('#div_mapas button').prop('disabled', false);
        setTimeout(function(){
            $('#div_mapas tbody tr:first-of-type .btnOrderUp').prop('disabled', true);
            let x = $('#div_mapas tbody').length;
            while(x > 0) {
                $('#div_mapas tbody').eq(x-1).find('.btnOrderDown').last().prop('disabled', true);;
                x--;
            }
        }, 100);

    }

    function reloadSurdos() {
        location.reload();
    }

    function mostrarTodosGPS() {
        alert('Indisponível');
    }


    $(document).ready(function(){
        emGetMapas();

        $(document).on('change', '#sel_regiao_id', function(){
            emGetMapas();
        });

        $(document).on('click', '.btnOrderUp', function(){
            
            let obj = $(event.target);
            let linha = obj.parents('tr');
            linha.clone(true).insertBefore(linha.prev());
            linha.remove();

            desativaBotoes();
            $('#botaoSalvar').prop('disabled', false);
        });

        $(document).on('click', '.btnOrderDown', function(){
            
            let obj = $(event.target);
            let linha = obj.parents('tr');
            linha.clone(true).insertAfter(linha.next());
            linha.remove();

            desativaBotoes();
            $('#botaoSalvar').prop('disabled', false);
        });

        $(document).on('click', '.btnRemove', function(){
            let obj = $(event.target);
            let linha = obj.parents('tr');
            let linhaID = linha.find('td').eq(0).text();
            let sur;
            //console.log(linhaID);

            let surdos = surdosJSON;
            
            surdos.forEach(function(s){
                if(s.id == linhaID) {
                    sur = s;
                }
            });
            

            if(sur == undefined) {
                alert('Houve um erro na aplicação. Atualize a página. Se persistir, contate o desenvolvedor.');
                return false;
            }

            //console.log(sur);
            if($('#div_sem_mapas table tbody').length == 0) {
                $('#div_sem_mapas').html('<table class="table table-bordered table-sm">'+
                '<thead class="thead-dark">'+
                    '<tr > <th>ID</th> <th>Nome</th> <th>Bairro</th> </tr>'+
                '</thead> <tbody></tbody></table>');
            }
            $('#div_sem_mapas table tbody').append('<tr> <td>'+sur.id+'</td> <td>'+sur.nome+'</td> <td style="font-size: .625rem">'+sur.bairro+'</td> </tr>');
            linha.parents('tbody').append('<tr> <td>-</td> <td>-</td> <td><button type="button" class="btn btn-sm btn-success btnAdd" data-toggle="tooltip" title="Adicionar surdo ao mapa"><i class="fas fa-plus"></i></button></td> </tr>');
            linha.remove();

            
            $('#botaoSalvar').prop('disabled', false);
        });

        $(document).on('click', '.btnAdd', function(){
            let mapaNome = $(event.target).parents('table').find('.mapaNome').text();
            modalAddSurdo(mapaNome);
        });

        $(document).on('click', '#modalAddSurdo .surdosLista div', function(){
            // Busca o mapa
            let surdo = $(this);
            //console.log(surdo);
            let mapaAlvo = $('#modalAddSurdo input[name="mapaAlvo"]').val();
            let mapaOBJ;

            for(i = 0; i < $('#div_mapas .mapaNome').length; i++) {
                if($('#div_mapas .mapaNome').eq(i).text() == mapaAlvo) {
                    mapaOBJ = $('#div_mapas .mapaNome').eq(i).parents('table');
                    break;
                }
            }

            if(mapaOBJ == undefined) {
                alert('Mapa não foi encontrado. Atualize a página e tente de novo.');
                return false;
            }

            // Adiciona o surdo ao mapa e remove da tabela sem mapas
            let linha = mapaOBJ.find('.btnAdd').eq(0).parents('tr');
            
            let html = '<tr data-original-indice="0" data-surdoid="'+surdo.data('id')+'">'+
                    '<td>'+surdo.data('id')+'</td>'+
                    '<td>'+surdo.data('nome')+'</td>'+
                    '<td><button type="button" class="btn btn-sm btn-light border btnOrderUp" data-toggle="tooltip" title="Mover para cima"><i class="fas fa-arrow-up"></i></button> '+
                    '<button type="button" class="btn btn-sm btn-light border btnOrderDown" data-toggle="tooltip" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button> '+
                    '<button type="button" class="btn btn-sm btn-danger btnRemove" data-toggle="tooltip" title="Remover surdo do mapa"><i class="fas fa-minus"></i></button></td>'+
                    '</tr>';
                    
            linha.before(html).remove();

            for(i = 0; i < $('#div_sem_mapas tbody tr').length; i++) {
                if($('#div_sem_mapas tbody tr').eq(i).find('td').eq(0).text() == surdo.data('id')) {
                    $('#div_sem_mapas tbody tr').eq(i).remove();
                    break;
                }
            }


            $('#modalAddSurdo').modal('hide');
            $('#botaoSalvar').prop('disabled', false);
        });

        $(document).on('click', '#botaoSalvar', function(){
            // Cria um array original e um array atual
            // Depois faz uma comparação dos arrays em busca de mudanças

            let aOriginal, aAtual, aMudancas;
            aAtual = [];
            aOriginal = [];
            for(i = 0; i < surdosJSON.length; i++) {
                aOriginal[surdosJSON[i].id] = {
                    id: surdosJSON[i].id,
                    nome: surdosJSON[i].nome,
                    mapa: surdosJSON[i].mapa,
                    mapa_indice: surdosJSON[i].mapa_indice
                    }
            }

            //console.log(aOriginal);

            // Array dos mapas atual.
            for(i = 0; i < $('#div_mapas table').length; i++) {
                let tabela = $('#div_mapas table').eq(i);
                let mapaNome = $('#div_mapas table').eq(i).find('.mapaNome').text();

                for(a = 0; a < tabela.find('tbody tr').length; a++) {
                    let linha = tabela.find('tbody tr').eq(a);

                    if(linha.find('td').eq(0).text() != '-') {
                        aAtual[linha.find('td').eq(0).text()] = {
                            id: linha.find('td').eq(0).text(),
                            nome: linha.find('td').eq(1).text(),
                            mapa: mapaNome,
                            mapa_indice: a+1
                        }
                    }
                }
            }

            // Adiciona os surdos sem mapa
            if($('#div_sem_mapas table tbody tr').length > 0) {
                for(i = 0; i < $('#div_sem_mapas table tbody tr').length; i++) {
                    let linha = $('#div_sem_mapas table tbody tr').eq(i);

                    aAtual[linha.find('td').eq(0).text()] = {
                        id: linha.find('td').eq(0).text(),
                        nome: linha.find('td').eq(1).text(),
                        mapa: '',
                        mapa_indice: 0
                    }
                }
            }
            

            //console.log("aAtual:", aAtual);

            // Compara array para capturar as mudanças
            aMudancas = [];

            for(i = 0; i < aOriginal.length; i++) {
                if(aAtual[i] != undefined) {

                    if((aOriginal[i].mapa != aAtual[i].mapa) || (aOriginal[i].mapa_indice != aAtual[i].mapa_indice)) {
                        aMudancas[i] = {
                            id: i,
                            mapa: aAtual[i].mapa,
                            mapa_indice: aAtual[i].mapa_indice
                        }
                    }
                }
            }

            if(aMudancas.length == 0) {
                alert('Nenhuma mudança realizada');
                $('#botaoSalvar').prop('disabled', true);
            }

            //console.log("aMudancas:", aMudancas);
            // Envia dados para o servidor.
            //
            $.post('{{$router->generate("admFunctions")}}',
            {
                funcao: 'salvaEditarMapas',
                dados: JSON.stringify(aMudancas)
            },function(data){
                console.log(data);
                if(isJson(data)) {
                    let res = JSON.parse(data);
                    let r = JSON.parse(res.dados);
                    if(res.sucesso == false)  {
                        alert('Falha: '+res.mensagem);
                        return false;
                    } else {
                        alert('Alterações salvas!');
                        location.reload();
                        //setTimeout(function(){emGetMapas();}, 500);
                        //$('#botaoSalvar').prop('disabled', true);
                        return true;
                    }
                } else {
                    console.log('MENSAGEM DO SERVIDOR:');
                    console.log(data);
                    $('#modalGENERICO .modal-body').html('MENSAGEM DO SERVIDOR<br>(Capture a tela e envie ao administrador):<br><br>'+data);
                    $('#modalGENERICO').modal('show');
                }
                
            });
        });

        $(document).on('dblclick', '.mapaNome', function(){
            let novoNome = prompt('Novo nome para o mapa "'+$(event.target).text()+'":', $(event.target).text());
            if(novoNome == '' || novoNome == null || novoNome == $(event.target).text()) {
                alert('Não alterado');
            } else {
                let nomeValido = true;
                // varre array em busca do nome
                surdosJSON.forEach(function(s){
                    if(s.mapa == novoNome) {
                        nomeValido = false;
                    }
                });

                // Varre a tela em busca do nome
                for(i=0; i < $('.mapaNome').length; i++) {
                    if($('.mapaNome').eq(i).text() == novoNome) {
                        nomeValido = false;
                        break;
                    }
                }

                if(nomeValido == true) {
                    $(event.target).text(novoNome);
                } else {
                    alert('Nome já existe. Tente novamente...');
                    $(event.target).dblclick();
                }
            }
        });
        
    });
</script>
@endsection