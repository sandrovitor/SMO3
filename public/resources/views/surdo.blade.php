@extends('layouts.layoutindex')

@section('estiloPersonalizado')
    <link href="/css/layoutConsulta.css" rel="stylesheet">
@endsection

@php
    //var_dump($surdo);

    if($surdo != null) {
        // Se a variável não for nula

        if($surdo->p_ref == '') { $surdo->p_ref = '-'; }
        if($surdo->familia == '') { $surdo->familia = '-'; }
        if($surdo->facebook == '') { $surdo->facebook = '-'; }
        if($surdo->whats == '') { $surdo->whats = '-'; }
        if($surdo->tel == '') { $surdo->tel = '-'; }
        if($surdo->idade == '') { $surdo->idade = '-'; }
        if($surdo->obs == '') { $surdo->obs = '-'; }
        if($surdo->turno == '') { $surdo->turno = '-'; }
        if($surdo->hora_melhor == '') { $surdo->hora_melhor = '-'; }

        if($surdo->dia_melhor != '') {
            $x = explode('|', $surdo->dia_melhor);
            $cDias = '';
            
            foreach($x as $y) {
                switch($y) {
                    case "1":
                        $cDias .= 'Domingo; ';
                        break;
                    
                    case '2':
                        $cDias .= 'Segunda; ';
                        break;
                
                    case '3':
                        $cDias .= 'Terça; ';
                        break;
                        
                    case '4':
                        $cDias .= 'Quarta; ';
                        break;
                        
                    case '5':
                        $cDias .= 'Quinta; ';
                        break;
                        
                    case '6':
                        $cDias .= 'Sexta; ';
                        break;

                    case '7':
                        $cDias .= 'Sábado; ';
                        break;
                }
            }

            $cDias = substr($cDias, 0, -2);
        } else {
            $cDias = '-';
        }
        
        if((bool)$surdo->be == TRUE && $surdo->resp_id != '') {
            $cBE = '<span class="badge badge-success" style="font-size: 1rem"><i class="fas fa-heart"></i> &nbsp; '.$surdo->resp.'</span>';
        } else {
            $cBE = '<span class="badge badge-secondary" style="font-size: 1rem"><i class="far fa-heart"></i> &nbsp; NÃO</span>';
        }

        // BADGES

        if(($surdo->ativo == "1" || $surdo->ativo == true) && ($surdo->ocultar == "0" || $surdo->ocultar == false)) { // ATIVO e VISÍVEL
            $bAtivo = '<span class="badge badge-success" data-toggle="tooltip" title="ATIVO!"><i class="fas fa-star"></i> ATIVO</span>';
            $motivo = '';
        } else if(($surdo->ativo == "1" || $surdo->ativo == true) && ($surdo->ocultar == "1" || $surdo->ocultar == true)) { // ATIVO e OCULTO
            $bAtivo = '<span class="badge badge-info" data-toggle="tooltip" title="Oculto"><i class="far fa-star-half"></i> OCULTO</span>';
            $motivo = '<div class="bg-info text-white text-center py-2 px-3"><strong>MOTIVO:</strong> <i>"'. $surdo->motivo .'"</i></div>';
        } else { // DESATIVADO
            $bAtivo = '<span class="badge badge-danger" data-toggle="tooltip" title="Desativado"><i class="far fa-star"></i> DESATIVADO</span>';
            $motivo = '<div class="bg-danger text-white text-center py-2 px-3"><strong>MOTIVO:</strong> <i>"'. $surdo->motivo .'"</i></div>';
        }
        
        if($surdo->be == "1") { // BIBLIA ESTUDA
            $bBE = ' <span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda!"><i class="fas fa-heart"></i> BÍBLIA ESTUDA</span>';
            $bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>';
        } else if($surdo->be == "0" && $surdo->encontrado == "1") { // ENCONTRADO
            $bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i> NÃO ESTUDA</span>';
            $bEncontrado = ' <span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>';
        } else { // NÃO ENCONTRADO
            $bBE = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i> NÃO ESTUDA</span>';
            $bEncontrado = ' <span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i> NÃO ENCONTRADO</span>';
        }

        $nome = $surdo->nome;
    } else {
        $nome = '';
    }
@endphp

@section ('paginaCorrente', 'Surdo '.$nome)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item"><a href="/consulta">Consulta</a></li>
    <li class="breadcrumb-item active">Surdo</li>
@endsection

@section('conteudo')
    @if($surdo == null)
    <div class="row">
        <div class="col-12">
            <h4 class="text-center"><i class="fas fa-times text-danger"></i> &nbsp; Surdo não encontrado</h4>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12 col-lg-6">
            <h3><strong>{{$surdo->nome}}</strong> <small style="font-size: .875rem">[ID: {{$surdo->id}}]</small><br>
            {!!$bAtivo!!} {!!$bEncontrado!!} {!!$bBE!!}</h3> {!!$motivo!!}<hr>
            <div class="row">
                <div class="col-12 col-md-6">
                    <dl>
                        <dt>Endereço:</dt><dd>{{$surdo->endereco}}</dd>
                        <dt>Bairro:</dt><dd>{{$surdo->bairro}}</dd>
                        <dt>Ponto de Referência:</dt><dd>{{$surdo->p_ref}}</dd>
                        <dt>Família:</dt><dd>{{$surdo->familia}}</dd>
                        <dt><i class="fab fa-facebook-f"></i> Facebook:</dt><dd>{{$surdo->facebook}}</dd>
                        <dt><i class="fab fa-whatsapp"></i> Whatsapp:</dt><dd>{{$surdo->whats}}</dd>
                        <dt>Telefone(s):</dt><dd>{{$surdo->tel}}</dd>
                        
                    </dl>
                </div>
                <div class="col-12 col-md-6">
                    <dl>
                        <dt>Faixa Etária:</dt><dd>{{$surdo->idade}}</dd>
                        <dt>Observações:</dt><dd>{{$surdo->obs}}</dd>
                        <dt>Turno:</dt><dd>{{$surdo->turno}}</dd>
                        <dt>Hora Melhor:</dt><dd>{{$surdo->hora_melhor}}</dd>
                        <dt>Dia Melhor:</dt><dd>{{$cDias}}</dd>
                        <dt>Bíblia Estuda:</dt><dd>{!!$cBE!!}</dd>
                    </dl>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div><button type="button" class="btn btn-sm btn-info btn-block" onclick="$('#mapsAPI').slideToggle();">Mostrar / esconder mapa</button></div>
                    <!--<input type="hidden" name="gps" data-gpsdisabled="true">-->
                    <div id="mapsAPI" style="display:none"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header"><a href="#" data-toggle="collapse" data-target="#registros"><strong>ÚLTIMOS REGISTROS (...)</strong></a></div>
                <div class="card-body collapse fade" id="registros" style="overflow-y:auto; max-height: 70vh; height:100%;">
                    <div id="resultadobusca"></div>
                </div>
            </div>

            <div class="btn-group btn-block">
                <a href="/registros/novo/{{$surdo->id}}" class="btn btn-primary">Novo registro</a>
                <a href="/cadastro/editar/{{$surdo->id}}" class="btn btn-info">Editar</a>
            </div>
        </div>
    </div>
    <div class="backTop"><span class="glyphicon glyphicon-chevron-up"></span></div>
    @endif
@endsection

@section('script')
<script>
    $(document).ready(function(){
        setTimeout(function(){initMap(true, '{{$surdo->gps}}');}, 700);

        $.post('/registros/buscar/surdo/{{$surdo->id}}/publicador/0/limit=0-3&order=new', {}, function(data){
            if(isJson(data)) {
                resultado = JSON.parse(data);
                console.log(resultado);

                for(i=0; i<resultado.length; i++) {
                    var temp = resultado[i];
                    var x = temp.data_visita.split('-');
                    var data_formatada = x[2]+'/'+x[1]+'/'+x[0];
                    $('#resultadobusca').append(getRegistroFormatado(temp.id, temp.mapa_id, temp.nome, temp.bairro, data_formatada, temp.encontrado, temp.publicador, temp.texto));
                }
            }
        });
    });
</script>
@endsection