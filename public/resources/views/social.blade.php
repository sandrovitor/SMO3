@extends('layouts.layoutindex')

@section ('paginaCorrente', 'Redes Sociais')

@php
	$mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    $hoje = new DateTime();
    $diff = $hoje->diff($social['InicioDateTime']);

    //var_dump($social, $hoje);
    //var_dump($diff);
    //var_dump($surdos[1]);
	
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Redes Sociais</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    @if($social['ativo'] == 'not')
        <div class="text-center border border-dark mb-2 p-3 shadow-sm">
            <h2><i class="far fa-comment"></i> <strong>Evento não está agendado.</strong></h2>
        </div>
    @else
        @if($hoje > $social['FinalDateTime'])
        <div class="text-center border border-dark mb-2 p-3 shadow-sm">
            <h2><i class="far fa-comment"></i> <strong>O evento já acabou!</strong></h2>
            Esse evento finalizou em <strong>{{$social['FinalDateTime']->format('d/m/Y \à\s H:i')}}</strong>
        </div>
        @elseif($diff->days === 0 && $diff->invert === 0 && $diff->h < 24)
        <div class="text-center border border-primary bg-primary text-white mb-2 p-3 shadow-sm">
            <h2 class="mb-3"><i class="far fa-comment"></i> <strong>O evento está próximo!!</strong><br>
            <small>Quase lá!! Restam menos de {{$diff->h + 1}}h para o evento.</small></h2>
            Esse evento está agendado para <strong>{{$social['InicioDateTime']->format('d/m/Y \à\s H:i')}}</strong>
        </div>
        @elseif($hoje < $social['InicioDateTime'])
        <div class="text-center border border-info bg-info text-white mb-2 p-3 shadow-sm">
            <h2><i class="far fa-comment"></i> <strong>O evento está agendado!</strong></h2>
            Esse evento está agendado para <strong>{{$social['InicioDateTime']->format('d/m/Y \à\s H:i')}}</strong>
        </div>
        @else
        <div class="border border-secondary mb-2 p-3 shadow-sm d-flex">
            <div class="mr-2">
                <i class="far fa-comment"></i> <strong>REDES SOCIAIS</strong>
            </div>
            <div class="ml-auto pl-2">
                Encerra às <span class="badge badge-info" style="font-size:1rem;">{{$social['FinalDateTime']->format('d/m/Y \à\s H:i')}}</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card rounded-0">
                    <div class="card-header p-2 bg-dark text-white">
                        SURDOS
                    </div>
                    <div class="card-body p-2" style="max-height: calc(100vh - 280px); overflow-y:auto;">
                        <table class="table table-sm" id="listaSurdos">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th></th>
                                    <th>Bairro</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($surdos))
                            @foreach($surdos as $s)
                                <tr smo-sid="{{$s->id}}">
                                    <td>{{$s->nome}}</td>
                                    <td style="padding: .5rem; width: 105px; text-align:center;">
                                        @if($s->tel == '')
                                            <span class="border border-secondary p-1 text-muted"><i class="fas fa-mobile-alt"></i></span>
                                        @else
                                            <span class="border border-primary bg-primary text-white p-1 "><i class="fas fa-mobile-alt"></i></span>
                                        @endif

                                        
                                        @if($s->whats == '')
                                            <span class="border border-secondary p-1 m-1 text-muted"><i class="fab fa-whatsapp"></i></span>
                                        @else
                                            <span class="border border-primary bg-primary text-white p-1 m-1 "><i class="fab fa-whatsapp"></i></span>
                                        @endif

                                        @if($s->facebook == '')
                                            <span class="border border-secondary p-1 text-muted"><i class="fab fa-facebook-f"></i></span>
                                        @else
                                            <span class="border border-primary bg-primary text-white p-1"><i class="fab fa-facebook-f"></i></span>
                                        @endif
                                    </td>
                                    <td>{{$s->bairro}}</td>
                                </tr>
                            @endforeach
                            @else
                                <tr><td colspan="3">Nada encontrado</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card rounded-0">
                    <div class="card-header p-2 bg-dark text-white">
                        INFORMAÇÕES
                    </div>
                    <div class="card-body px-3 py-2" style="max-height: calc(100vh - 280px); overflow-y:auto;" id="info">
                        <div class="not-found d-none">
                            <h4 class="text-center"><i class="fas fa-times text-danger"></i> &nbsp; Surdo não encontrado</h4>
                        </div>

                        <div class="found d-none">
                            <div class="row">
                                <div class="col-12 col-xl-8">
                                    <h3><strong class="surdoNome"></strong> <small style="font-size: .875rem">[ID: <span class="surdoId"></span>]</small><br>
                                    <span class="surdoEncontrado"></span> <span class="surdoBe"></span></h3><hr>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="border border-info text-white rounded-lg text-center bg-info h-100 p-2">
                                                <i class="fab fa-facebook-f" style="font-size: 1.5rem;"></i><br><strong class="surdoFacebook"></strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border border-info text-white rounded-lg text-center bg-info h-100 p-2">
                                                <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i><br><strong class="surdoWhats"></strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border border-info text-white rounded-lg text-center bg-info h-100 p-2">
                                                <i class="fas fa-mobile-alt" style="font-size: 1.5rem;"></i><br><strong class="surdoTel"></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <dl>
                                                <dt>Endereço:</dt><dd class="surdoEndereco"></dd>
                                                <dt>Bairro:</dt><dd class="surdoBairro"></dd>
                                                <dt>Ponto de Referência:</dt><dd class="surdoPref"></dd>
                                                <dt>Família:</dt><dd class="surdoFamilia"></dd>
                                                <dt>Faixa Etária:</dt><dd class="surdoIdade"></dd>
                                                
                                            </dl>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <dl>
                                                <dt>Observações:</dt><dd class="surdoObs"></dd>
                                                <dt>Turno:</dt><dd class="surdoTurno"></dd>
                                                <dt>Hora Melhor:</dt><dd class="surdoHM"></dd>
                                                <dt>Dia Melhor:</dt><dd class="surdoDM"></dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <a href="/registros/novo/" target="_blank" class="btn btn-lg btn-primary btn-block novoRegistro">Novo registro</a>
                                    <a href="/registros/buscar/" target="_blank" class="btn btn-lg btn-primary btn-block verRegistro">Ver registros</a>
                                    <a href="/cadastro/editar/" target="_blank" class="btn btn-lg btn-primary btn-block editarCadastro">Editar cadastro</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
@endsection

@section('script')
<style>
    tr.selected {
        background-color: #b3ffec;
    }
</style>
<script>
var surdosJSON = {!!json_encode($surdos)!!};

function loadInfo(sid)
{
    let info = $('#info');
    let s = null;

    $('.found, .not-found').addClass('d-none');
    surdosJSON.forEach(function(surdo){
        if(surdo.id == sid) {
            s = surdo;
        }
    });

    if(s == null) {
        $('.not-found').removeClass('d-none');
    } else {
        $('.surdoNome').text(s.nome);
        $('.surdoId').text(s.id);
        if(s.encontrado == '0') {
            $('.surdoEncontrado').html('<span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i> NÃO ENCONTRADO</span>');
        } else{
            $('.surdoEncontrado').html('<span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i> ENCONTRADO</span>');
        }
        if(s.be == '0') {
            $('.surdoBe').html('<span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i> NÃO ESTUDA</span>');
        } else {
            $('.surdoBe').html('<span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda!"><i class="fas fa-heart"></i> BÍBLIA ESTUDA</span>');
        }
        $('.surdoEndereco').text((function(){
            if(s.endereco == '') { return '-'; } else { return s.endereco; }
        })());
        $('.surdoBairro').text(s.bairro);
        $('.surdoPref').text((function(){
            if(s.p_ref == '') { return '-'; } else { return s.p_ref; }
        })());
        $('.surdoFamilia').text((function(){
            if(s.familia == '') { return '-'; } else { return s.familia; }
        })());
        $('.surdoIdade').text(s.idade);
        $('.surdoObs').text((function(){
            if(s.obs == '') { return '-'; } else { return s.obs; }
        })());
        $('.surdoTurno').text((function(){
            if(s.turno == '') { return '-'; } else { return s.turno; }
        })());
        $('.surdoHM').text((function(){
            if(s.hora_melhor == '') { return '-'; } else { return s.hora_melhor; }
        })());
        $('.surdoDM').text((function(){
            if(s.dia_melhor == '') { return '-'; } else { 
                let diam = s.dia_melhor.split('|');
                diam.forEach(function(d, index){
                    switch(d) {
                        case '1': diam[index] = 'Dom'; break;
                        case '2': diam[index] = 'Seg'; break;
                        case '3': diam[index] = 'Ter'; break;
                        case '4': diam[index] = 'Qua'; break;
                        case '5': diam[index] = 'Qui'; break;
                        case '6': diam[index] = 'Sex'; break;
                        case '7': diam[index] = 'Sáb'; break;
                    }
                });

                diam = diam.join('; ');
                
                return diam;
            }
        })());

        $('.novoRegistro').attr('href', '/registros/novo/'+s.id);
        $('.verRegistro').attr('href', '/registros/buscar/'+s.id);
        $('.editarCadastro').attr('href', '/cadastro/editar/'+s.id);

        
        $('.surdoFacebook').html((function(){
            if(s.facebook == '') { return '-';} else {return '<a href="'+s.facebook+'" target="_blank" class="btn btn-light"> Abrir </a>'; }
        })());
        $('.surdoWhats').text((function(){
            if(s.whats == '') { return '-';} else {return s.whats; }
        })());
        $('.surdoTel').text((function(){
            if(s.tel == '') { return '-';} else {return s.tel; }
        })());
        $('.found').removeClass('d-none');
    }

}

$(document).ready(function(){
    $(document).on('click', '#listaSurdos tbody tr', function(){
        $('#listaSurdos .selected').removeClass('selected');
        let linha = $(event.target).parents('tr:eq(0)');
        let sid = linha.attr('smo-sid');
        linha.addClass('selected');

        loadInfo(sid);
        location.href = "#info";
    });
});
</script>
@endsection