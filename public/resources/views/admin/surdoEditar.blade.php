@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    //var_dump($bairros);
    //var_dump($surdo);
    $bairro = '';
    if($surdo->ativo == '1') {
        $ativo = 'yes';
    } else {
        $ativo = 'not';
    }

    if($surdo->ocultar == '1') {
        $ocultar = 'yes';
    } else {
        $ocultar = 'not';
    }
    
    $dmClass = array(0 => '', 1 => '', 2 => '', 3 => '',  4 => '', 5 => '', 6 => '');
    if($surdo->dia_melhor != '') {
        $dia_melhor = explode('|', $surdo->dia_melhor);
        //var_dump($dia_melhor);
        foreach($dia_melhor as $d) {
            switch($d) {
                case '1':
                    $dmDom = 'checked="checked"';
                    $dmClass[0] = 'active';
                    break;

                case '2':
                    $dmSeg = 'checked="checked"';
                    $dmClass[1] = 'active';
                    break;

                case '3':
                    $dmTer = 'checked="checked"';
                    $dmClass[2] = 'active';
                    break;

                case '4':
                    $dmQua = 'checked="checked"';
                    $dmClass[3] = 'active';
                    break;

                case '5':
                    $dmQui = 'checked="checked"';
                    $dmClass[4] = 'active';
                    break;

                case '6':
                    $dmSex = 'checked="checked"';
                    $dmClass[5] = 'active';
                    break;

                case '7':
                    $dmSab = 'checked="checked"';
                    $dmClass[6] = 'active';
                    break;
            }
        }
    }
    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSurdo')}}">Surdos</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 mb-2">
            <div class="bg-light p-2 d-flex justify-content-center">
                @if($surdo->ativo == '1' && $surdo->ocultar == '0')
                <button type="button" class="btn btn-danger"><i class="far fa-star"></i> Desativar</button> &nbsp;
                <button type="button" class="btn btn-info"><i class="far fa-star-half"></i> Ocultar</button>
                @elseif($surdo->ativo == '1' && $surdo->ocultar == '1')
                <button type="button" class="btn btn-danger"><i class="far fa-star"></i> Desativar</button> &nbsp;
                <button type="button" class="btn btn-dark"><i class="fas fa-star"></i> Re-exibir</button> 
                @else
                <button type="button" class="btn btn-success"><i class="fas fa-star"></i> Ativar</button> &nbsp;
                @endif
                
            </div>
        </div>
    </div>
    <form method="post" action="{{$router->generate('admSurdoSalva')}}">
        <div class="row">
            <div class="col-12 col-lg-6 mb-2">
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="form-group">
                            <label>Nome do surdo</label>
                            <input type="text" class="form-control" name="nome" required placeholder="Nome do surdo" title="Somente letras, hífen e parênteses. Máximo 25 caracteres." pattern="[a-zA-ZÀ-ú- ()]{3,25}" value="{{$surdo->nome}}">
                            <input type="hidden" class="form-control" name="id" value="{{$surdo->id}}">
                        </div>
                        <div class="form-group">
                            <label>Bairro</label>
                            <select class="form-control" name="bairro" required>
                                <option></option>
                                @foreach($bairros as $b)
                                    @if($regiao == '')
                                    <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                    @php($regiao = $b->regiao_numero)

                                    @elseif($regiao != $b->regiao_numero)
                                    </optgroup>
                                    <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                                    @php($regiao = $b->regiao_numero)
                                    @endif

                                    @if($b->id == $surdo->bairro_id)
                                    <option value="{{$b->id}}" selected>{{$b->bairro}}</option>
                                    @else
                                    <option value="{{$b->id}}">{{$b->bairro}}</option>
                                    @endif

                                @endforeach
                                @if($regiao != '')
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Endereço</label>
                            <input type="text" class="form-control" name="endereco" placeholder="RUA, Nº, COMPLEMENTO" maxlength="75" value="{{$surdo->endereco}}">
                        </div>
                        <div class="form-group">
                            <label>Ponto de referência</label>
                            <input type="text" class="form-control" name="pref" placeholder="PONTOS DE REFERÊNCIA" maxlength="90" value="{{$surdo->p_ref}}">
                        </div>
                        <div class="form-group">
                            <label>Parentes/Família</label>
                            <input type="text" class="form-control" name="familia" placeholder="PARENTES/FAMÍLIA" maxlength="75" value="{{$surdo->familia}}">
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control" name="obs" placeholder="INFORMAÇÕES RELEVANTES. MÁXIMO DE 75 LETRAS" rows="4" maxlength="75">{{$surdo->obs}}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Localização GPS</label>
                            <input type="text" class="form-control disabled" name="gps" data-gpsdisabled="true" placeholder="Arraste o ícone no mapa" disabled value="{{$surdo->gps}}">
                            <input type="hidden" name="gpsval" value="{{$surdo->gps}}">
                        </div>
                        <div class="form-group">
                            <label>Faixa Etária</label>
                            <select class="form-control" name="idade">
                                <option></option>
                                <option value="">Não sei</option>
                                <option value="ADULTO">ADULTO</option>
                                <option value="CRIANÇA">CRIANÇA</option>
                                <option value="JOVEM">JOVEM</option>
                                <option value="IDOSO">IDOSO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Turno</label>
                            <select class="form-control" name="turno">
                                <option></option>
                                <option value="MAN">MANHÃ</option>
                                <option value="TAR">TARDE</option>
                                <option value="NOIT">NOITE</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="form-group">
                            <label>Telefone(s)</label>
                            <input type="text" class="form-control" name="tel" placeholder="NÚMEROS DE TELEFONE" maxlength="75" value="{{$surdo->tel}}">
                            <small class="form-text text-muted">Ex.: "98888-1234 (MÃE); 99999-1234 (SURDO)"</small>
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-whatsapp"></i> Whatsapp</label>
                            <input type="text" class="form-control" name="whatsapp" placeholder="SOMENTE UM NÚMERO" title="Somente números de um telefone." pattern="[0-9]{0, 15}" value="{{$surdo->whats}}">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="text" class="form-control" name="facebook" placeholder="LINK DO PERFIL" maxlength="90" value="{{$surdo->facebook}}">
                        </div>
                        <div class="form-group">
                            <label>Dia Melhor</label>
                            <div class="form-check-div">
                                <div class="form-check {{$dmClass[0]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="1" {!!$dmDom or ''!!}> Domingo
                                </div>
                                <div class="form-check {{$dmClass[1]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="2" {!!$dmSeg or ''!!}> Segunda
                                </div>
                                <div class="form-check {{$dmClass[2]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="3" {!!$dmTer or ''!!}> Terça
                                </div>
                                <div class="form-check {{$dmClass[3]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="4" {!!$dmQua or ''!!}> Quarta
                                </div>
                                <div class="form-check {{$dmClass[4]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="5" {!!$dmQui or ''!!}> Quinta
                                </div>
                                <div class="form-check {{$dmClass[5]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="6" {!!$dmSex or ''!!}> Sexta
                                </div>
                                <div class="form-check {{$dmClass[6]}}">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="7" {!!$dmSab or ''!!}> Sábado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 mb-2">
                <div id="mapsAPI"></div>
                <div class="my-2">
                    <button type="button" class="btn btn-success" onclick="initMap(true, $('[name=gpsval]').val());">Recarregar mapa</button>
                    <button type="button" class="btn btn-primary" onclick="getGPSAtual()">Usar minha localização*</button>
                    <small class="form-text text-muted">* Para tal, você precisa autorizar que o SMO tenha acesso à sua localização.</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-success">Salvar</button>
                <button type="reset" class="btn btn-warning">Resetar</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('[name="idade"]').find('[value^="{{substr($surdo->idade, 0, 4)}}"]').prop('selected',true);
            $('[name="turno"]').find('[value^="{{substr($surdo->turno, 0, 3)}}"]').prop('selected',true);
            $('[name="ativo"]').find('[value="{{$ativo}}"]').prop('selected',true);
            $('[name="ocultar"]').find('[value="{{$ocultar}}"]').prop('selected',true);

            initMap(true, $('[name="gpsval"]').val());

            if('{{$ativo}}' == 'not' || '{{$ocultar}}' == 'yes') {
                $('#motivo-div').slideDown();
            }
        });
    </script>
@endsection