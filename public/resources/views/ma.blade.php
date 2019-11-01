@extends('layouts.layoutindex')

@section ('paginaCorrente', 'Assistente de Ministério')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Assistente de Ministério</li>
@endsection

@php
    $hoje = new DateTime();
    if( $hoje->format('n') >= 9 ) {
        $anoServico = $hoje->format('Y');
        $anoServico++;
    } else {
        $anoServico = $hoje->format('Y');
    }
    unset($hoje);
@endphp

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <div class="shadow-sm p-3 mb-3 border border-1">
                <h3><strong>Bem vindo de volta, {{$user->nome}}.</strong></h3>
                <a href="{{$router->generate('maExportXLS')}}" target="_blank" class="btn btn-secondary btn-sm"><i class="far fa-file-excel"></i> Exportar XLS</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="shadow-sm p-3 mb-3 border border-1" id="divRelatMes">
                <h5 class="mb-3">Relatório do mês atual<br>
                <small class="text-muted" style="font-size: .8rem;"> </small></h5>
                <table class="table table-sm" id="tabRelatAtual" smo-mes="{{date('n')}}" smo-ano="{{date('Y')}}" onclick="listaHorasMesAtual($(this).attr('smo-ano'), $(this).attr('smo-mes'))">
                    <tr>
                        <th>Horas</th>
                        <td class="hora">00:00</td>
                    </tr>
                    <tr>
                        <th>Horas na LDC</th>
                        <td class="horaldc">00:00</td>
                    </tr>
                    <tr>
                        <th>Publicações</th>
                        <td class="publicacao">0</td>
                    </tr>
                    <tr>
                        <th>Vídeos Mostrados</th>
                        <td class="videos">0</td>
                    </tr>
                    <tr>
                        <th>Revisitas</th>
                        <td class="revisitas">0</td>
                    </tr>
                    <tr>
                        <th>Estudos</th>
                        <td class="estudos">-</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="shadow-sm p-3 mb-3 border border-1" id="divDesempenho">
                <h5 class="mb-3">Desempenho do pioneiro</h5><br>
                <table class="table table-sm">
                    <tr>
                        <th>Situação</th>
                        <td class="mes-status">-</td>
                    </tr>
                    <tr>
                        <th>Alvo de horas por dia
                            <span class="badge badge-info badge-pill" title="Como é calculado?" data-content="O alvo de horas é calculado com base na quantidade de horas que você precisa fazer neste mês dividido pela quantidade de dias restantes.
                            <br><br><i>Exemplo:</i><br><br> 70h / 20 dias = <strong>3h30min/dia</strong><br> 70h / 5 dias = <strong>14h/dia</strong>" data-toggle="popover" data-trigger="click hover"><i class="fas fa-info"></i></span>
                        </th>
                        <td class="hora-alvo-dia">-</td>
                    </tr>
                </table>
                <br>
                <hr>
                <strong>Estatísticas do pioneiro (ANO)</strong>
                <table class="table table-sm">
                    <tr>
                        <th>Horas restantes</th>
                        <td class="hora-restante">-</td>
                    </tr>
                    <tr>
                        <th>Horas/mês</th>
                        <td class="hora-restante-mes">-</td>
                    </tr>
                    <tr>
                        <th>Meses Restantes</th>
                        <td class="meses-restante">-</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="shadow-sm p-3 mb-3 border border-1" id="divRelatorioAno">
                <h5 class="mb-3">Relatório do ano de serviço <span class="ano"></span></h5><br>
                <table class="table table-sm">
                    <tr>
                        <th>Horas</th>
                        <td class="hora">00:00</td>
                    </tr>
                    <tr>
                        <th>Horas na LDC</th>
                        <td class="horaldc">00:00</td>
                    </tr>
                    <tr>
                        <th>Publicações</th>
                        <td class="publicacao">0</td>
                    </tr>
                    <tr>
                        <th>Vídeos Mostrados</th>
                        <td class="videos">0</td>
                    </tr>
                    <tr>
                        <th>Revisitas</th>
                        <td class="revisitas">0</td>
                    </tr>
                    <tr>
                        <th>Estudos</th>
                        <td class="estudos">-</td>
                    </tr>
                </table>
                <span class="badge badge-light">Atualizado às <i><span class="atualizadoEm"></span></i></span>
                <button class="btn btn-sm btn-link" onclick="relatorioAno()">Atualizar</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="shadow-sm p-3 mb-3 border border-1">
                <h5 class="mb-3">Meses Anteriores &nbsp; <button type="button" class="btn btn-sm btn-info" onclick="relatorioAnterior()"><i class="fas fa-redo"></i></button></h5><br>
                <div class="divMesAnterior">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdAddHora">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <input type="date" name="data" class="form-control form-control-sm" value="{{date('Y-m-d')}}">
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Horas no ministério</label>
                            <input type="time" name="hora" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label>Horas na LDC</label>
                            <input type="time" name="horaldc" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label>Publicações</label>
                            <input type="number" name="publicacao" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label>Vídeos</label>
                            <input type="number" name="videos" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label>Revisitas</label>
                            <input type="number" name="revisitas" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label>Comentários</label>
                            <textarea name="comentario" rows="2" maxlength="190" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="setHora()">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdListaHora">
        <div class="modal-dialog">
            <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div>

                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
                    </div>
            </div>
        </div>
    </div>
    <div id="btAddHora" title="Adicionar hora ao relatório" data-toggle="tooltip"><i class="fas fa-plus"></i></div>
@endsection

@section('script')
<style>
    #btAddHora {
        width: 50px;
        height: 50px;
        background-color: rgba(0,121,107,1);
        position: fixed;
        bottom: 60px;
        right: 1rem;
        border-radius: 50%;
        line-height: 50px;
        font-size: 30px;
        padding: 0 12px;
        color:white;
        box-shadow: 0px 0px 2px 3px #dee2e6;
        cursor:pointer;
    }
    .divMesAnterior {
        overflow-x: auto;
        display:flex;
    }
    .divMesAnterior table {
        max-width:315px;
        min-width:300px;
        width:100%;
        margin-right: 1rem;
    }
</style>
<script>
    var horasRAW, relatorioAnoRAW, mesAtual = {{date('n')}}, anoAtual = {{(int)date('Y')}}, anoServico = {{$anoServico}};
    var horas;
    var hoje = new Date();
    var meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    var exiMesAtual = {{date('n')}};


    function minutosToHora(minutos)
    {
        let h, m, retorno;
        minutos = parseInt(minutos)
        m = minutos%60;
        h = (minutos - m)/60;
        
        if(h < 10) {
            retorno = '0'+h+':';
        } else {
            retorno = h+':';
        }

        if(m < 10) {
            retorno += '0'+m;
        } else {
            retorno += m;
        }
        return retorno;
    }

    function horasToMinutos(horas)
    {
        let h, m, x, retorno;
        if(horas.indexOf(':') == -1) {
            horas = parseInt(horas);
            horas = horas +':00';
        }
        x = horas.split(':');
        h = parseInt(x[0]);
        m = parseInt(x[1]);
        retorno = (h*60) + m;
        return retorno;
    }
    
    function diasNoMes(ano, mes)
    {
        var data = new Date(ano, mes, 0);
        return parseInt(data.getDate());
    }

    function setHora()
    {
        let form = $(event.target).parents('form').eq(0);
        console.log(form);
        let dia, hora, horaldc, publicacao, videos, revisitas, comentario;
        dia = form.find('[name="data"]').val();
        hora = form.find('[name="hora"]').val();
        horaldc = form.find('[name="horaldc"]').val();
        publicacao = form.find('[name="publicacao"]').val();
        videos = form.find('[name="videos"]').val();
        revisitas = form.find('[name="revisitas"]').val();
        comentario = form.find('[name="comentario"]').val();

        $.post('{{$router->generate("maFunc")}}',{
            funcao: 'setHora',
            dia: dia,
            hora: hora,
            horaldc: horaldc,
            publicacao: publicacao,
            videos: videos,
            revisitas: revisitas,
            comentario: comentario
        },function(retorno){
            if(retorno == 'OK') {
                $('#mdAddHora').modal('hide');
                form[0].reset();
                getHoras(120);
                desempenho();
            } else {
                alert(retorno)
            }
        });
    }

    function getHoras(quantidade)
    {
        $.post('{{$router->generate("maFunc")}}',{
            funcao: 'getHoras',
            quantidade: quantidade
        }, function(retorno){
            if(isJson(retorno)) {
                horasRAW = JSON.parse(retorno);
                relatorioAtual();
                restartPlugins();
            } else if(retorno !== '{0}') {
                alert('Mensagem:'+"\n"+retorno);
                restartPlugins();
            }
        });
    }

    function relatorioAnterior()
    {
        let mes1 = mes2 = mesAtual-1;
        let ano1 = ano2 = anoAtual;
        $('.divMesAnterior').html('');

        for(i = 0; i < 6; i++) {
            mes2--;
            if(mes2 == 0) {
                ano2--;
                mes2 = 12;
            }
        }
        $.post('/ma/relatorio/'+ano1+'/'+mes1+'/'+ano2+'/'+mes2+'/', {}, function(data){
            $('.divMesAnterior').append(data);
        });

        
        //console.log(mes1+'/'+ano1);
        //console.log(mes2+'/'+ano2);
        
    }

    function relatorioAtual()
    {
        let horas = 0, horasldc = 0, publicacao = 0, videos = 0, revisitas = 0;


        horasRAW.forEach(function(x){
            let y = x.data.split('-');
            let mes = parseInt(y[1]);
            let ano = parseInt(y[0]);

            if(ano == $('#tabRelatAtual').attr('smo-ano') && mes == $('#tabRelatAtual').attr('smo-mes')) {
                horas += parseInt(x.hora);
                horasldc += parseInt(x.horaldc);
                publicacao += parseInt(x.publicacao);
                videos += parseInt(x.videos);
                revisitas += parseInt(x.revisitas);
                //console.log(x);
            }
            
        });

        horas = minutosToHora(horas);
        horasldc = minutosToHora(horasldc);
        $('#tabRelatAtual').find('.hora').text(horas);
        $('#tabRelatAtual').find('.horaldc').text(horasldc);
        $('#tabRelatAtual').find('.publicacao').text(publicacao);
        $('#tabRelatAtual').find('.videos').text(videos);
        $('#tabRelatAtual').find('.revisitas').text(revisitas);
    }

    function relatorioAno(ano = '')
    {
        if(ano == '' || ano == 0) {
            ano = anoServico;
        }
        $.post('{{$router->generate("maFunc")}}',{
            funcao: 'getRelatorioAno',
            ano: ano
        },function(retorno){
            console.log('Retorno (Relatório ano):');
            console.log(retorno);
            let temp = JSON.parse(retorno);

            if(ano == anoServico) {
                relatorioAnoRAW = temp;
            }


            let div = $('#divRelatorioAno');
            hoje = new Date();
            div.find('.ano').text(ano);
            div.find('.atualizadoEm').text(hoje.toLocaleDateString()+' '+hoje.toLocaleTimeString());
            div.find('.hora').text(minutosToHora(temp.hora));
            div.find('.horaldc').text(minutosToHora(temp.horaldc));
            div.find('.publicacao').text(temp.publicacao);
            div.find('.videos').text(temp.videos);
            div.find('.revisitas').text(temp.revisitas);

            if(ano == anoServico) {
                desempenho();
            }
        });
    }

    function desempenho()
    {
        setTimeout(function() {
            if(relatorioAnoRAW !== undefined) {
                let temp = relatorioAnoRAW;

                let mesesRestante = 0;
                if(mesAtual >= 9) {
                    mesesRestante = (12 - mesAtual+1) + 8;
                } else {
                    mesesRestante = 8 - (mesAtual-1);
                }
                

                div = $('#divDesempenho');
                // Hora restante (incluindo hora já feita este mês)
                let horaRestante = (840*60) - parseInt(temp.hora) - parseInt(temp.horaldc);
                div.find('.hora-restante').text(minutosToHora(horaRestante));

                // Hora restante (excluindo hora já feita este mês)
                horaRestante = (840*60);
                horaRestante -= (parseInt(temp.hora) + parseInt(temp.horaldc) - horasToMinutos($('#divRelatMes').find('.hora').text()) - horasToMinutos($('#divRelatMes').find('.horaldc').text()));
                console.log('Hora Restante (sem o mês atual): ' +minutosToHora(horaRestante));
                let horaRestanteMes = 0;
                if(horaRestante % mesesRestante > 0) {
                    horaRestanteMes = (horaRestante - (horaRestante % mesesRestante)) / mesesRestante;
                    horaRestanteMes +=1;
                } else {
                    horaRestanteMes = horaRestante / mesesRestante;
                }
                console.log('Meses restantes (inclui mês atual): '+mesesRestante);
                //console.log(horaRestante);
                //console.log(temp);

                
                
                div.find('.hora-restante-mes').text(minutosToHora(horaRestanteMes));
                div.find('.meses-restante').text(mesesRestante);

                // Calcula situação do pioneiro
                let horaMes = horasToMinutos($('#divRelatMes').find('.hora').text());
                let horaldcMes = horasToMinutos($('#divRelatMes').find('.horaldc').text());

                let x = horaRestante; // + horaMes + horaldcMes;
                let horaAlvoMes = 0;
                if(x % mesesRestante > 0) {
                    horaAlvoMes = (x - (x % mesesRestante)) / mesesRestante;
                    horaAlvoMes += 1;
                } else {
                    horaAlvoMes = x / mesesRestante;
                }
                // Substrai as horas já feitas
                //horaAlvoMes -= (horaldcMes + horaMes);
                console.log('Hora alvo Mês: '+minutosToHora( horaAlvoMes));

                let horaAlvoDia;
                if(horaAlvoMes % diasNoMes(anoAtual, mesAtual) > 0) {
                    horaAlvoDia = (horaAlvoMes - (horaAlvoMes % diasNoMes(anoAtual, mesAtual))) / diasNoMes(anoAtual, mesAtual);
                    horaAlvoDia += 1;
                } else {
                    horaAlvoDia = horaAlvoMes / diasNoMes(anoAtual, mesAtual);
                }
                console.log('Hora Alvo DIA: '+minutosToHora( horaAlvoDia));
                

                let horaDia;
                let horaAlvoMesAtual = horaAlvoMes - (horaldcMes + horaMes);
                if(horaAlvoMesAtual % (diasNoMes(anoAtual, mesAtual) - hoje.getDate())  > 0) {
                    horaDia = (horaAlvoMesAtual - (horaAlvoMesAtual % (diasNoMes(anoAtual, mesAtual) - hoje.getDate()))) / (diasNoMes(anoAtual, mesAtual) - hoje.getDate());
                    horaDia += 1;
                } else {
                    horaDia = horaAlvoMesAtual / (diasNoMes(anoAtual, mesAtual) - hoje.getDate());
                }
                console.log('Hora por DIA (até fim do mês): '+minutosToHora( horaDia ));
                console.log('Hora restante (até fim do mês): '+minutosToHora( horaAlvoMesAtual ));
                
                if(horaAlvoDia * parseInt(hoje.getDate()) == horaMes+horaldcMes) {
                    // Está certinho
                    div.find('.mes-status').html('<span class="badge badge-light">Sem atrasos</span>');
                    div.find('.hora-alvo-dia').text( minutosToHora(horaDia) );
                } else if(horaAlvoDia * parseInt(hoje.getDate()) < horaMes+horaldcMes) {
                    // Está adiantado
                    div.find('.mes-status').html('<span class="badge badge-success">Adiantado</span>');
                    div.find('.hora-alvo-dia').text( minutosToHora(horaDia) );
                } else {
                    // Está atrasado
                    div.find('.mes-status').html('<span class="badge badge-danger">Atrasado</span><br>');
                    div.find('.hora-alvo-dia').text( minutosToHora(horaDia) );
                    //console.log(horaDia);
                    //console.log(horaAlvoDia);
                    //console.log((horaMes+horaldcMes));
                    
                }
            } else {
                relatorioAno();
            }
        }, 500);
    }

    function listaHorasMesAtual(ano, mes)
    {
        //console.log(ano);
        //console.log(mes);
        $('#mdListaHora .modal-body').html('');

        horasRAW.forEach(function(x){
            let y = x.data.split('-');
            let m = parseInt(y[1]);
            let a = parseInt(y[0]);

            if(a == ano && m == mes) {
                let textoDescricao = new Array();
                if(x.hora > 0) {
                    textoDescricao.push(minutosToHora(x.hora)+' horas');
                }
                if(x.horaldc > 0) {
                    textoDescricao.push(minutosToHora(x.horaldc)+' horas na LDC');
                }
                if(x.publicacao > 0) {
                    textoDescricao.push(x.publicacao+' publicações');
                }
                if(x.video > 0) {
                    textoDescricao.push(x.video+' vídeos');
                }
                if(x.revisitas > 0) {
                    textoDescricao.push(x.revisitas+' revisitas');
                }

                if(textoDescricao.length > 0) {
                    textoDescricao = textoDescricao.join(', ');
                }

                if(x.comentario != '') {
                    textoDescricao += '<br>'+x.comentario;
                }
                

                $('#mdListaHora .modal-body').append(
                    '<div class="mb-2" onclick="editaHora(\''+y[0]+'-'+y[1]+'-'+y[2]+'\')">'+
                        '<strong>'+y[2] +' de '+meses[y[1]-1]+' de '+y[0]+'</strong>'+
                        '<br>'+
                        '<small>'+textoDescricao+'</small>'+
                    '</div>'
                );
            }
            
        });

        $('#mdListaHora .modal-title').html('<strong>Horas do mês de '+ meses[mes-1]+'</strong>');
        $('#mdListaHora').modal();
    }

    function listaHoraMesAnterior()
    {
        let tabela = $(event.target).parents('table:eq(0)');
        let horas = tabela.attr('smo-horas');
        
        if(horas !== false && horas !== 'false') {
            $('#mdListaHora .modal-body').html('');

            horas = JSON.parse(horas);
            horas.forEach(function(x){
                let y = x.data.split('-');
                let m = parseInt(y[1]);
                let a = parseInt(y[0]);

                let textoDescricao = new Array();
                if(x.hora > 0) {
                    textoDescricao.push(minutosToHora(x.hora)+' horas');
                }
                if(x.horaldc > 0) {
                    textoDescricao.push(minutosToHora(x.horaldc)+' horas na LDC');
                }
                if(x.publicacao > 0) {
                    textoDescricao.push(x.publicacao+' publicações');
                }
                if(x.video > 0) {
                    textoDescricao.push(x.video+' vídeos');
                }
                if(x.revisitas > 0) {
                    textoDescricao.push(x.revisitas+' revisitas');
                }

                if(textoDescricao.length > 0) {
                    textoDescricao = textoDescricao.join(', ');
                }

                if(x.comentario != '') {
                    textoDescricao += '<br>'+x.comentario;
                }
                

                $('#mdListaHora .modal-body').append(
                    '<div class="mb-2" onclick="editaHora(\''+y[0]+'-'+y[1]+'-'+y[2]+'\')">'+
                        '<strong>'+y[2] +' de '+meses[y[1]-1]+' de '+y[0]+'</strong>'+
                        '<br>'+
                        '<small>'+textoDescricao+'</small>'+
                    '</div>'
                );
            });

            $('#mdListaHora .modal-title').html('<strong>Horas do mês de '+ tabela.find('th:eq(0)').text()+'</strong>');
            $('#mdListaHora').modal();
        }
        
    }

    function editaHora(dia)
    {
        $('#mdAddHora [name="data"]').val(dia).trigger('change');
        $('#mdListaHora').modal('hide');
        setTimeout(function(){$('#mdAddHora').modal();}, 200);

    }

    function restartPlugins()
    {
        //$('[data-toggle="tooltip"]').tooltip('disable');
        //$('[data-toggle="popover"]').popover('disable');
        setTimeout(function(){
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({html:true, sanitize: false});
        }, 500);
    }

    

    
    $(document).ready(function(){
        
        getHoras(120);
        relatorioAnterior();
        relatorioAno();

        $(document).ajaxComplete(function() {
            //$('[data-toggle="popover"]').popover({html:true, sanitize: false});
            restartPlugins();
        });
        $(document).on('click', '#btAddHora',function(){
            if(horasRAW != undefined) {
                horasRAW.forEach(function(x){
                    if(x.data == $('#mdAddHora form [name="data"]').val()) {
                        let form = $('#mdAddHora form');


                        form.find('[name="hora"]').val(minutosToHora(x.hora));
                        form.find('[name="horaldc"]').val(minutosToHora(x.horaldc));
                        form.find('[name="publicacao"]').val(x.publicacao);
                        form.find('[name="videos"]').val(x.videos);
                        form.find('[name="revisitas"]').val(x.revisitas);
                        form.find('[name="comentario"]').val(x.comentario);
                    }
                });
            }
            $('#mdAddHora').modal();
        });

        $(document).on('change', '#mdAddHora form [name="data"]', function(){
            let c = false;
            let form = $('#mdAddHora form');

            if(horasRAW != undefined) {
                horasRAW.forEach(function(x){
                    if(x.data == $('#mdAddHora form [name="data"]').val()) {

                        form.find('[name="hora"]').val(minutosToHora(x.hora));
                        form.find('[name="horaldc"]').val(minutosToHora(x.horaldc));
                        form.find('[name="publicacao"]').val(x.publicacao);
                        form.find('[name="videos"]').val(x.videos);
                        form.find('[name="revisitas"]').val(x.revisitas);
                        form.find('[name="comentario"]').val(x.comentario);
                        c = true;
                    }
                });
            }

            if(c == false) {
                form.find('[name="hora"]').val('00:00');
                form.find('[name="horaldc"]').val('00:00');
                form.find('[name="publicacao"]').val('');
                form.find('[name="videos"]').val('');
                form.find('[name="revisitas"]').val('');
                form.find('[name="comentario"]').val('');
            }
        });
        
    });
    
</script>
@endsection