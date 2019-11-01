var codigoDeStatus = {
    401: function(){
        console.log('Acesso não autorizado');
        alert('Erro 401. Faça login novamente.');
        window.open('./login.php');
    },
    403: function(){
        console.log('Acesso proibido!');
        alert('Erro 403. Acesso proibido');
    },
    404: function(){
        console.log('Não encontrado!');
        alert('Erro 404. Não encontrado');
    },
    500: function(){
        console.log('Erro interno no servidor.');
        alert('Erro 500. Erro interno no servidor');
    },
    503: function(){
        console.log('Serviço indisponível temporariamente.');
        alert('Erro 503. Temporariamente indisponível');
    },
};
var mapaCentro = {lat: -12.906985,  lng: -38.443157};
var mapProp = {
		center: mapaCentro,
		zoom:12
    };



function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }

    return true;
}

function goAnchor(anchor) {

    if(anchor == 'topo' || anchor == 'top' || anchor == '#topo' || anchor == '#top') {
        $('body, html').scrollTop(0);
    } else {
        var x = $(anchor).offset();
        var novaPos = x.top - $('.navbar.fixed-top').height() - 12;
        $('body, html').scrollTop(novaPos);
    }
    
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

/*
 * ########################## CONSULTA
 */

function consultaPesquisa()
{
    // Captura valores e constroi URL de consulta
    var nome, bairro, turno, idade, be, oculto, encontrado, desativado;
    nome = $('#filtroNome').val();
    bairro = $('#filtroBairro').find(':selected').val();
    turno = $('#filtroTurno').find(':selected').val();
    idade = $('#filtroIdade').find(':selected').val();
    be = $('#filtroBE').find(':selected').val();
    oculto = $('#filtroOculto').find(':selected').val();
    encontrado = $('#filtroEncontrado').find(':selected').val();
    if($('#filtroDesativado').prop('checked') == true) {
        desativado = 'yes';
    } else {
        desativado = 'not';
    }

    // Se todas as variáveis REQUERIDAS estiverem vazias, retorna erro
    if(nome == "" && bairro == "" && turno == "" && idade == "" && be == "" && encontrado == "") {
        alert('Não é possivel pesquisar, se nenhum parâmetro foi fornecido. Tente novamente...');
        $('#filtroNome').focus();
    } else {

        if(nome == "") {
            nome = "~null~";
        }
        if(bairro == "") {
            bairro = "~null~";
        }
        if(turno == "") {
            turno = "~null~";
        }
        if(idade == "") {
            idade = "~null~";
        }
        if(be == "") {
            be = "~null~";
        }
        if(oculto == "") {
            oculto = "~null~";
        }
        if(encontrado == "") {
            encontrado = "~null~";
        }

        var urlPesquisa = nome +'/'+ bairro +'/'+ turno +'/'+ idade +'/'+ be +'/'+ oculto +'/'+ encontrado +'/'+ desativado;
        //alert(encodeURI(urlPesquisa));
        console.log(encodeURI(urlPesquisa));
        
        $.ajax({
            url: "/consulta/pesquisa/"+encodeURI(urlPesquisa),
            method: "GET", // Método pode ser POST, GET ou outro verbo HTTP
            processData: false, // Processar os dados antes do envio
            statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
            cache: false, // Não usar dados em cache
            success: function(data) {
                //console.log(data);
                if(isJson(data)) {
                    var res = JSON.parse(data);
                    console.log(res);

                    if(res.length > 0) { // Resultado encontrado
                        $('#resultado .card-body').html('')
                            .html('<table class="table table-sm table-hover"><thead class="thead-dark"><tr><th>Nome</th><th>Bairro</th><th>Situação</th></tr></thead> <tbody></tbody></table>');
                
                        res.forEach( function(valor) { // Para cada resultado
                            var cAtivo; // Para controle de classes
                            var bAtivo, bBE, bEncontrado; // Para controle de badges

                            if((valor.ativo == "1" || valor.ativo == true) && (valor.oculto == "0" || valor.oculto == false)) { // ATIVO e VISÍVEL
                                cAtivo = '';
                                bAtivo = '<span class="badge badge-success" data-toggle="tooltip" title="ATIVO!"><i class="fas fa-star"></i></span>';
                            } else if((valor.ativo == "1" || valor.ativo == true) && (valor.oculto == "1" || valor.oculto == true)) { // ATIVO e OCULTO
                                cAtivo = "table-info";
                                bAtivo = '<span class="badge badge-info" data-toggle="tooltip" title="Oculto"><i class="far fa-star-half"></i></span>';
                            } else { // DESATIVADO
                                cAtivo = "table-danger";
                                bAtivo = '<span class="badge badge-danger" data-toggle="tooltip" title="Desativado"><i class="far fa-star"></i></span>';
                            }

                            if(valor.be == "1") { // BIBLIA ESTUDA
                                bBE = ' <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda!"><i class="fas fa-heart"></i></span>';
                                bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i></span>';
                            } else if(valor.be == "0" && valor.encontrado == "1") { // ENCONTRADO
                                bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i></span>';
                                bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i></span>';
                            } else { // NÃO ENCONTRADO
                                bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i></span>';
                                bEncontrado = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i></span>';
                            }
                            $('#resultado .card-body tbody').append('<tr class="'+cAtivo+'" onclick="consultaResultadoInfo('+valor.id+')"><td>'+valor.nome+'</td> <td>'+valor.bairro+'</td><td>'+ bAtivo + bEncontrado + bBE +'</td></tr>');
                        });
                        $('#resultado .card-body').append('<hr><h4>TOTAL: <span class="badge badge-info">'+res.length+'</span></h4>');

                    } else { // Nenhum resultado
                        $('#resultado .card-body').html('Nada encontrado. :/');
                    }
                    
                    goAnchor('#resultado');

                    
                } else {
                    $('#resultado .card-body').html(data);
                }
                
            }, // Em caso de sucesso
            error: function() {
                //alert("Ocorreu um erro");
            }, // Em caso de erro
            complete: function(jqXHR){
                //console.log(jqXHR);
            } // Quando a conexão finalizar (com sucesso ou erro)
        });
        
    }

}

function consultaResultadoInfo(id)
{
    if(id > 0) {
        $.ajax({
            url: "/consulta/id/"+id,
            method: "GET", // Método pode ser POST, GET ou outro verbo HTTP
            processData: false, // Processar os dados antes do envio
            statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
            cache: false, // Não usar dados em cache
            success: function(data) {
                if(isJson(data)){ 
                    var surdo = JSON.parse(data);
                    console.log(surdo);
                    var tempVar; // Variável temporária
                    var cBE, cDias; // Variáveis de controle dos campos do resultado
                    if(surdo.be == "1" || surdo.be == true) {
                        cBE = '<span class="badge badge-success" style="font-size: 1rem"><i class="fas fa-heart"></i> &nbsp; '+surdo.resp+'</span>';
                    } else {
                        cBE = '<span class="badge badge-secondary" style="font-size: 1rem"><i class="far fa-heart"></i> &nbsp; NÃO</span>';
                    }

                    
                    var bAtivo, bBE, bEncontrado, motivo = ''; // Para controle de badges

                    if((surdo.ativo == "1" || surdo.ativo == true) && (surdo.ocultar == "0" || surdo.ocultar == false)) { // ATIVO e VISÍVEL
                        bAtivo = '<span class="badge badge-success" data-toggle="tooltip" title="ATIVO!"><i class="fas fa-star"></i> ATIVO</span>';
                    } else if((surdo.ativo == "1" || surdo.ativo == true) && (surdo.ocultar == "1" || surdo.ocultar == true)) { // ATIVO e OCULTO
                        bAtivo = '<span class="badge badge-info" data-toggle="tooltip" title="Oculto"><i class="far fa-star-half"></i> OCULTO</span>';
                        motivo = '<div class="bg-info text-white text-center py-2 px-3"><strong>MOTIVO:</strong> <i>"'+ surdo.motivo +'"</i></div>';
                    } else { // DESATIVADO
                        bAtivo = '<span class="badge badge-danger" data-toggle="tooltip" title="Desativado"><i class="far fa-star"></i> DESATIVADO</span>';
                        motivo = '<div class="bg-danger text-white text-center py-2 px-3"><strong>MOTIVO:</strong> <i>"'+ surdo.motivo +'"</i></div>';
                    }
                    
                    if(surdo.be == "1") { // BIBLIA ESTUDA
                        bBE = ' <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda!"><i class="fas fa-heart"></i> BÍBLIA ESTUDA</span>';
                        bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>';
                    } else if(surdo.be == "0" && surdo.encontrado == "1") { // ENCONTRADO
                        bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i> NÃO ESTUDA</span>';
                        bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>';
                    } else { // NÃO ENCONTRADO
                        bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i> NÃO ESTUDA</span>';
                        bEncontrado = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i> NÃO ENCONTRADO</span>';
                    }
                    


                    if(surdo.dia_melhor != '') {
                        tempVar = surdo.dia_melhor.split("|");
                        cDias = '';
                        tempVar.forEach((valor) => {
                            switch(valor) {
                                case "1":
                                    cDias += 'Domingo; ';
                                    break;
                                
                                case '2':
                                    cDias += 'Segunda; ';
                                    break;
                            
                                case '3':
                                    cDias += 'Terça; ';
                                    break;
                                    
                                case '4':
                                    cDias += 'Quarta; ';
                                    break;
                                    
                                case '5':
                                    cDias += 'Quinta; ';
                                    break;
                                    
                                case '6':
                                    cDias += 'Sexta; ';
                                    break;

                                case '7':
                                    cDias += 'Sábado; ';
                                    break;
                            }
                        });

                        cDias = cDias.substr(0, cDias.length-2);
                        console.log(tempVar);
                        console.log(cDias);
                    } else {
                        cDias = '-';
                    }

                    if(surdo.p_ref == '') { surdo.p_ref = '-'; }
                    if(surdo.familia == '') { surdo.familia = '-'; }
                    if(surdo.facebook == '') { surdo.facebook = '-'; }
                    if(surdo.whats == '') { surdo.whats = '-'; }
                    if(surdo.tel == '') { surdo.tel = '-'; }
                    if(surdo.idade == '') { surdo.idade = '-'; }
                    if(surdo.obs == '') { surdo.obs = '-'; }
                    if(surdo.turno == '') { surdo.turno = '-'; }
                    if(surdo.hora_melhor == '') { surdo.hora_melhor = '-'; }
                    
                    

                    $('#resultado-info .card-body').html('')
                        .append('<h3><strong>'+surdo.nome+'</strong> <small style="font-size: .875rem">[ID: '+surdo.id+']</small><br>'+ bAtivo + bEncontrado + bBE +'</h3>'+motivo+'<hr>')
                        .append('<dl><dt>Endereço:</dt><dd>'+surdo.endereco+'</dd> <dt>Bairro:</dt><dd>'+surdo.bairro+'</dd> <dt>Ponto de Referência:</dt><dd>'+surdo.p_ref+'</dd> <dt>Família:</dt><dd>'+surdo.familia+'</dd>'+
                        '<dt><i class="fab fa-facebook-f"></i> Facebook:</dt><dd>'+surdo.facebook+'</dd> <dt><i class="fab fa-whatsapp"></i> Whatsapp:</dt><dd>'+surdo.whats+'</dd> <dt>Telefone(s):</dt><dd>'+surdo.tel+'</dd> <dt>Faixa Etária:</dt><dd>'+surdo.idade+'</dd> <dt>Observações:</dt><dd>'+surdo.obs+'</dd>' +
                        '<dt>Turno:</dt><dd>'+surdo.turno+'</dd> <dt>Hora Melhor:</dt><dd>'+surdo.hora_melhor+'</dd> <dt>Dia Melhor:</dt><dd>'+cDias+'</dd> <dt>Bíblia Estuda:</dt><dd>'+cBE+'</dd>' +
                        '</dl>')
                        .append('<hr><div class="row"> <div class="col-12 col-sm-6 col-md-6 col-lg-12 col-xl-6 mb-1"><a href="/surdo/'+surdo.id+'" target="_blank" class="btn btn-primary btn-block">Mais informações</a></div> '+
                        '<div class="col-12 col-sm-6 col-md-6 col-lg-12 col-xl-6 mb-1"><a href="/cadastro/editar/'+surdo.id+'" target="_blank" class="btn btn-primary btn-block">Editar cadastro</a></div> '+
                        '<div class="col-12 col-sm-6 col-md-6 col-lg-12 col-xl-6 mb-1"><a href="/registros/novo/'+surdo.id+'" target="_blank" class="btn btn-primary btn-block">Novo registro</a></div> '+
                        '<div class="col-12 col-sm-6 col-md-6 col-lg-12 col-xl-6 mb-1"><a href="/registros/buscar/'+surdo.id+'" target="_blank" class="btn btn-primary btn-block">Ver registros</a></div></div>')
                        .append()
                        .append();
                } else {
                    $('#resultado-info .card-body').html(data);
                }
                goAnchor('#resultado-info');
            }, // Em caso de sucesso
            error: function() {
                //alert("Ocorreu um erro");
            }, // Em caso de erro
            complete: function(jqXHR){
                //console.log(jqXHR);
            } // Quando a conexão finalizar (com sucesso ou erro)
        });
    }
}

/*
 * ########################## REGISTROS - BUSCAR
 */

function getRegistroFormatado(registroId, surdoId, surdoNome, bairroNome, dataVisita, encontrado, pubNome, texto, publicadorID = 0)
{
    var retorno = '', extra = '', encString = '';

    if(texto.indexOf('|EXTRA|') > 0) {
        var x = texto.indexOf('|EXTRA|');
        extra = texto.substr(x);
        texto = texto.substr(0, x);

        extra = '<span class="badge badge-primary">&nbsp; '+ extra.substr(7, extra.length - 15) +' &nbsp;</span>';
        x = undefined;
    }

    if(encontrado == '1') {
        // Encontrado
        encString = '<span class="badge badge-light text-primary" data-toggle="tooltip" title="" data-original-title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>';
        encHeaderClass = 'enc';
    } else {
        // Não encontrado
        encString = '<span class="badge badge-light text-muted" data-toggle="tooltip" title="" data-original-title="Não encontrado"><i class="fas fa-check"></i> NÃO ENCONTRADO</span>';
        encHeaderClass = 'nenc';
    }

    // verifica se é o Publicador que criou o registro ou se é um usuário nível 5
    let nivel = parseInt(getCookie('smoAut'));
    let pubId = parseInt(getCookie('smoCod'));
    let excluir = '';
    if(nivel == 5 || pubId == publicadorID) {
        excluir = '<button class="btn btn-sm btn-danger" onclick="registroDeleta('+registroId+', $(this))"><i class="fas fa-eraser"></i> &nbsp; Excluir</button>';
    } else {
        excluir = '';
    }
    

    retorno = '<div class="registro-card">'+
    '<div class="registro-header '+encHeaderClass+' d-flex flex-md-row flex-column flex-wrap">'+
        '<span class="flex-fill"><strong>'+surdoNome+'</strong> '+ encString +
        ' <br><small><strong>Bairro: '+bairroNome+'</strong></small></span>'+
        '<span class="flex-fill text-md-right"> <strong>Data:</strong> '+dataVisita+' <br> <strong>Publicador(a):</strong> '+pubNome+' </span>'+
    '</div> <div class="registro-body">'+
        '<div class="registro-texto"> '+texto+' </div>'+
        '<div class="registro-badges"> '+extra+' </div>'+
    '</div>'+
    '<div class="registro-footer">'+
        '<a href="/surdo/'+surdoId+'" class="btn btn-sm btn-primary"><i class="fas fa-info"></i> &nbsp; Ver surdo</a> '+
        '<a href="/registros/editar/'+registroId+'" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> &nbsp; Editar</a> '+
        excluir+
    '</div> </div>';

    return retorno;
}

function registroBusca()
{
    var surdo, publicador;
    surdo = $('#surdo').find(':selected').val();
    publicador = $('#publicador').find(':selected').val();

    if( (surdo == '0' || surdo == 0 || surdo == '') &&
        (publicador == '0' || publicador == 0 || publicador == '') ) {
        alert('Escolha um publicador ou surdo.');
        return false;
    }

    $.ajax({
        url: "/registros/buscar/surdo/"+surdo+"/publicador/"+publicador,
        method: "POST", // Método pode ser POST, GET ou outro verbo HTTP
        processData: false, // Processar os dados antes do envio
        statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
        cache: false, // Não usar dados em cache
        success: function(data) {
            console.log(data);
            if(data == '{0}') {
                $('#resultadobusca').html('<strong>Nada encontrado</strong>');
            } else if(isJson(data)) {
                var resultado = JSON.parse(data);
                //console.log(resultado);

                $('#resultadobusca').html('');
                for(i=0; i<resultado.length; i++) {
                    var temp = resultado[i];
                    var x = temp.data_visita.split('-');
                    var data_formatada = x[2]+'/'+x[1]+'/'+x[0];
                    $('#resultadobusca').append(getRegistroFormatado(temp.id, temp.mapa_id, temp.nome, temp.bairro, data_formatada, temp.encontrado, temp.publicador, temp.texto, temp.pub_id));
                }

                // Verifica se há 10 itens no resultado.
                // Se houver menos de 10, não exibe botão "Carregar mais";
                // Se houver 10, exibe botão "Carregar mais";
                if(resultado.length == 10) {
                    $('#resultadobusca').append('<div class="loadmore text-center"><hr><button type="button" class="btn btn-outline-primary" onclick="registroLoadMore(10);">&nbsp; <i class="fas fa-plus"></i> Carregar mais... &nbsp;</button></div>');
                }
            } else {
                $('#resultadobusca').html('<strong>Resultado inesperado</strong><br>'+data);
            }
            
        }, // Em caso de sucesso
        error: function() {
            //alert("Ocorreu um erro");
        }, // Em caso de erro
        complete: function(jqXHR){
            //console.log(jqXHR);
        } // Quando a conexão finalizar (com sucesso ou erro)
    });
    return false;
}

function registroLoadMore(inicio)
{
    // Primeiro, remove o botão de Carregar mais.
    $('#resultadobusca .loadmore').remove();

    var surdo, publicador;
    surdo = $('#surdo').find(':selected').val();
    publicador = $('#publicador').find(':selected').val();

    if( (surdo == '0' || surdo == 0 || surdo == '') &&
        (publicador == '0' || publicador == 0 || publicador == '') ) {
        alert('Escolha um publicador ou surdo.');
        return false;
    }

    $.ajax({
        url: "/registros/buscar/surdo/"+surdo+"/publicador/"+publicador+"/limit="+inicio+"-10",
        method: "POST", // Método pode ser POST, GET ou outro verbo HTTP
        processData: false, // Processar os dados antes do envio
        statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
        cache: false, // Não usar dados em cache
        success: function(data) {
            //console.log(data);
            if(data == '{0}') {
                // Mostra uma mensagem no final do resultado dizendo que já carregou tudo.
                $('#resultadobusca').append('<div class="text-center"><hr> <small class="text-muted">Nada mais para exibir</small></div>');
            } else if(isJson(data)) {
                var resultado = JSON.parse(data);


                for(i=0; i<resultado.length; i++) {
                    var temp = resultado[i];
                    var x = temp.data_visita.split('-');
                    var data_formatada = x[2]+'/'+x[1]+'/'+x[0];
                    $('#resultadobusca').append(getRegistroFormatado(temp.id, temp.mapa_id, temp.nome, temp.bairro, data_formatada, temp.encontrado, temp.publicador, temp.texto, temp.pub_id));
                }

                // Verifica se há 10 itens no resultado.
                // Se houver menos de 10, não exibe botão "Carregar mais";
                // Se houver 10, exibe botão "Carregar mais";
                if(resultado.length == 10) {
                    $('#resultadobusca').append('<div class="loadmore text-center"><hr><button type="button" class="btn btn-outline-primary" onclick="registroLoadMore('+ inicio + 10 +');">&nbsp; <i class="fas fa-plus"></i> Carregar mais... &nbsp;</button></div>');
                }
            }
            
        }, // Em caso de sucesso
        error: function() {
            //alert("Ocorreu um erro");
        }, // Em caso de erro
        complete: function(jqXHR){
            //console.log(jqXHR);
        } // Quando a conexão finalizar (com sucesso ou erro)
    });
    return false;
}

function registroDeleta(regid, objeto = null)
{
    $.ajax({
        url: "/registros/deleta/"+regid,
        method: "POST", // Método pode ser POST, GET ou outro verbo HTTP
        processData: false, // Processar os dados antes do envio
        statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
        cache: false, // Não usar dados em cache
        success: function(data) {
            console.log(data);
            if(data == 1) {
                if(objeto != null) {
                    objeto.parents('.registro-card').slideUp('slow');
                    setTimeout(function(){
                        //objeto.parents('.registro-card').remove();
                    }, 1000);
                }
            } else {
                alert('Ocorreu um erro... Tente mais tarde.');
                console.log(data);
            }
            
        }, // Em caso de sucesso
        error: function() {
            //alert("Ocorreu um erro");
        }, // Em caso de erro
        complete: function(jqXHR){
            //console.log(jqXHR);
        } // Quando a conexão finalizar (com sucesso ou erro)
    });
}

function registroUltimos()
{
    $.ajax({
        url: "/registros/ultimos",
        method: "POST", // Método pode ser POST, GET ou outro verbo HTTP
        processData: false, // Processar os dados antes do envio
        statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
        cache: false, // Não usar dados em cache
        success: function(data) {
            if(data == '{0}') {
                $('#ultimos').append('<strong>Nada encontrado</strong>');
            } else if(isJson(data)) {
                var resultado = JSON.parse(data);
                //console.log(resultado);

                //$('#ultimos').html('');
                for(i=0; i<resultado.length; i++) {
                    var temp = resultado[i];
                    var x = temp.data_visita.split('-');
                    var data_formatada = x[2]+'/'+x[1]+'/'+x[0];
                    $('#ultimos').append(getRegistroFormatado(temp.id, temp.mapa_id, temp.nome, temp.bairro, data_formatada, temp.encontrado, temp.publicador, temp.texto, temp.pub_id));
                }

                $('#ultimos .registro-footer').remove();
                $('#ultimos .registro-card').addClass('mr-3');
                $('#ultimos .registro-card .text-md-right').addClass('text-md-left').removeClass('text-md-right');

            }
            
        }, // Em caso de sucesso
        error: function() {
            //alert("Ocorreu um erro");
        }, // Em caso de erro
        complete: function(jqXHR){
            //console.log(jqXHR);
        } // Quando a conexão finalizar (com sucesso ou erro)
    });
}
    
function initMap(draggable = false, gps_marker = '')
{

	if(gps_marker == '') {
		map = new google.maps.Map(document.getElementById('mapsAPI'), mapProp);
		var marker = new google.maps.Marker({
			position: mapaCentro,
			map: map,
			draggable: draggable,
			animation: google.maps.Animation.DROP
		});
	} else {
		var x = gps_marker.split(",");
		var pos_marker = {lat: parseFloat(x[0]), lng: parseFloat(x[1])};
		
		map = new google.maps.Map(document.getElementById('mapsAPI'), {
			center: pos_marker,
			zoom: 16
		});
		
		var marker = new google.maps.Marker({
			position: pos_marker,
			map: map,
			draggable: draggable,
			animation: google.maps.Animation.DROP
		});
		
	}
	
	google.maps.event.addListener(marker, 'dragend', function(){
		var textGPS = '';
		textGPS = this.getPosition().lat().toFixed(6) + ',' + this.getPosition().lng().toFixed(6);
		$('[name="gps"], [name="gpsval"]').val(textGPS);
		/*document.getElementById('gps').value = textGPS;
		document.getElementById('gps1').value = textGPS;*/
	});
}

function getGPSAtual()
{
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(showPosition, GPSError);
	} else {
		alert('O navegador não possui suporte à esta função.');
	}
}

function showPosition(position)
{
	console.log(position);
	$('[name="gps"], [name="gpsval"]').val(position.coords.latitude.toFixed(6)+', '+position.coords.longitude.toFixed(6));
	// Atualiza mapa
	initMap(true, $('[name="gps"]').val());
}

function GPSError(error) {
	switch(error.code) {
		case error.PERMISSION_DENIED:
			alert("Usuário negou o acesso à localização.");
			break;
		case error.POSITION_UNAVAILABLE:
			alert("Informações da localização está indisponível.");
			break;
		case error.TIMEOUT:
			alert("O pedido para localização expirou.");
			break;
		case error.UNKNOWN_ERROR:
			alert("Erro desconhecido.");
			break;
	}
}

function checaPendencias()
{
    $.post('/pendencias',{}, function(data){
        if(isJson(data)) {
            var res = JSON.parse(data);
            console.log(data);
            if(res.qtd == 0) {
                $('#pend-card .card-header').html('<strong>PENDÊNCIAS &nbsp; <span class="badge badge-success"><i class="fas fa-check"></i></span></strong>');
                $('#pend-body').html('Tudo tranquilo no SMO.');
            } else {
                let pend = res.dados;
                $('#pend-body').html('');
                $('#pend-card .card-header').html('<strong>PENDÊNCIAS &nbsp; <span class="badge badge-dark">'+res.qtd+'</span></strong>');
                
                pend.forEach(function(valor){
                    $('#pend-body').append('<a href="'+valor.link+'"><div class="alert alert-'+valor.tipo+' py-2 px-3 mb-1"><strong>'+valor.titulo+'</strong> '+valor.texto+' </div></a>');
                });
            }

            /*
            $('#pend-body').html('');
            $('#pend-card .card-header').html('<strong>PENDÊNCIAS &nbsp; <span class="badge badge-dark">'+res.length+'</span></strong>');
            res.forEach(function(valor){
                $('#pend-body').append('<a href="'+valor.link+'"><div class="alert alert-'+valor.tipo+' py-2 px-3 mb-1"><strong>'+valor.titulo+'</strong> '+valor.texto+' </div></a>');
            });
            */
        }
    });
}





// ################## JQUERY
$(document).ready(function(){
    // Ativa os tooltips
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover({html: true, sanitize: false});
    
    $(document).ajaxStart(function(){
		//$('[data-toggle="tooltip"]').tooltip('disable');
        //$('[data-toggle="popover"]').popover('disable');
	});
	$(document).ajaxStop(function(){
		setTimeout(function(){
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({html:true, sanitize: false});
        }, 500);
    });
    $(document).on('click', '.backTop', function(){
        goAnchor('#topo');
    });
    
    $('[data-toggle="filter"]').keyup(function(){
        var input, tabela, tr, td1, td2, conta;
        var alvo = $(this).data('target');
        
        input = $(this).val().toUpperCase();
        tabela = $(alvo).children('tbody')[0];
        tr = tabela.getElementsByTagName('tr');
		conta = 0;
		
		for (i = 0; i < tr.length; i++) {
			td1 = tr[i].getElementsByTagName('td')[0];
			td2 = tr[i].getElementsByTagName('td')[1];
			
			if(td1 != '' || td2 != '') {
				// Verifica se dentro dessas colunas há o valor pesquisado
				if (td1.innerHTML.toUpperCase().indexOf(input) >= 0 || td2.innerHTML.toUpperCase().indexOf(input) >= 0) {
					tr[i].style.display = '';
					conta++;
				} else {
					tr[i].style.display = 'none';
				}
			}
			
		}
		if(conta == 0 && input != '') {
			$('#tabela-msg').html('Nada encontrado');
			$('#filter_conta').html('');
		} else if(conta > 0 && input != '') {
			$('#tabela-msg').html('');
			$('#filter_conta').html('Surdos filtrados: <span class="badge badge-primary">'+conta+'</span>');
		} else {
			$('#tabela-msg').html('');
			$('#filter_conta').html('');
		}

    });


    if($('#mapsAPI').length > 0 && $('[name="gps"]').data('gpsdisabled') !== true) {
        initMap(true, '');
    }

    if($('#pend-card').length > 0) {
        checaPendencias();
    }

    $(document).on('click', '.form-check-div .form-check', function(){
		$(event.target).find('.form-check-input').trigger('click');
	});
	$(document).on('click', '.form-check-div .form-check-input', function(){
		for(a = 0; a < $('.form-check-div .form-check-input').length; a++) {
			let i = $('.form-check-div .form-check-input').eq(a);
			if($(i).prop('checked') == true) {
				$(i).parents('.form-check').addClass('active');
			} else {
				$(i).parents('.form-check').removeClass('active');
			}
		}
		
	});
});