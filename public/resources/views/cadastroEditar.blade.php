@extends('layouts.layoutindex')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    
    //var_dump($surdo);
    $regiao = '';
@endphp

@section ('paginaCorrente', 'Cadastro')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item"><a href="/cadastro">Cadastro</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection


@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
<form method="post" action="{{$router->generate('cadastroEditarPOST')}}">
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
                            <input type="text" class="form-control text-uppercase" name="endereco" placeholder="RUA, Nº, COMPLEMENTO" maxlength="75" value="{{$surdo->endereco}}">
                        </div>
                        <div class="form-group">
                            <label>Ponto de referência</label>
                            <input type="text" class="form-control text-uppercase" name="pref" placeholder="PONTOS DE REFERÊNCIA" maxlength="90" value="{{$surdo->p_ref}}">
                        </div>
                        <div class="form-group">
                            <label>Parentes/Família</label>
                            <input type="text" class="form-control text-uppercase" name="familia" placeholder="PARENTES/FAMÍLIA" maxlength="75" value="{{$surdo->familia}}">
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control text-uppercase" name="obs" placeholder="INFORMAÇÕES RELEVANTES. MÁXIMO DE 75 LETRAS" rows="4" maxlength="75">{{$surdo->obs}}</textarea>
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
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="form-group">
                            <label>Turno</label>
                            <select class="form-control" name="turno">
                                <option></option>
                                <option value="MAN">MANHÃ</option>
                                <option value="TAR">TARDE</option>
                                <option value="NOIT">NOITE</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Telefone(s)</label>
                            <input type="text" class="form-control text-uppercase" name="tel" placeholder="NÚMEROS DE TELEFONE" maxlength="75" value="{{$surdo->tel}}">
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

            initMap(true, $('[name="gpsval"]').val());
        });
    </script>
@endsection