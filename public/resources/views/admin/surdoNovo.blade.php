@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    
    //var_dump($bairros);
    $regiao = '';
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admSurdo')}}">Surdos</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <form method="post" action="{{$router->generate('admSurdoNovoPOST')}}">
        <div class="row">
            <div class="col-12 col-lg-6 mb-2">
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="form-group">
                            <label>Nome do surdo</label>
                            <input type="text" class="form-control" name="nome" required placeholder="Nome do surdo" title="Somente letras, hífen e parênteses. Máximo 25 caracteres." pattern="[a-zA-ZÀ-ú- ()]{3,25}">
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
                                    <option value="{{$b->id}}">{{$b->bairro}}</option>

                                @endforeach
                                @if($regiao != '')
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Endereço</label>
                            <input type="text" class="form-control text-uppercase" name="endereco" placeholder="RUA, Nº, COMPLEMENTO" maxlength="75">
                        </div>
                        <div class="form-group">
                            <label>Ponto de referência</label>
                            <input type="text" class="form-control text-uppercase" name="pref" placeholder="PONTOS DE REFERÊNCIA" maxlength="90">
                        </div>
                        <div class="form-group">
                            <label>Parentes/Família</label>
                            <input type="text" class="form-control text-uppercase" name="familia" placeholder="PARENTES/FAMÍLIA" maxlength="75">
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control" name="obs text-uppercase" placeholder="INFORMAÇÕES RELEVANTES. MÁXIMO DE 75 LETRAS" rows="4" maxlength="75"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Localização GPS</label>
                            <input type="text" class="form-control disabled" name="gps" placeholder="Arraste o ícone no mapa" disabled>
                            <input type="hidden" name="gpsval" value="">
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
                            <input type="text" class="form-control text-uppercase" name="tel" placeholder="NÚMEROS DE TELEFONE" maxlength="75">
                            <small class="form-text text-muted">Ex.: "98888-1234 (MÃE); 99999-1234 (SURDO)"</small>
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-whatsapp"></i> Whatsapp</label>
                            <input type="text" class="form-control" name="whatsapp" placeholder="SOMENTE UM NÚMERO" title="Somente números de um telefone." pattern="[0-9]{0, 15}">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="text" class="form-control" name="facebook" placeholder="LINK DO PERFIL" maxlength="90">
                        </div>
                        <div class="form-group">
                            <label>Dia Melhor</label>
                            <div class="form-check-div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="1"> Domingo
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="2"> Segunda
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="3"> Terça
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="4"> Quarta
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="5"> Quinta
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="6"> Sexta
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="dia_melhor[]" value="7"> Sábado
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Cadastro Ativo?</label>
                            <select class="form-control" name="ativo" onchange="motivodivToggle()">
                                <option value="yes">SIM</option>
                                <option value="not">NÃO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cadastro Oculto?</label>
                            <select class="form-control" name="ocultar" onchange="motivodivToggle()">
                                <option value="not">NÃO</option>
                                <option value="yes">SIM</option>
                            </select>
                        </div>
                        <div class="form-group" id="motivo-div" style="display:none;">
                            <label>Motivo</label>
                            <textarea class="form-control text-uppercase" rows="4" name="motivo" maxlength="240" placeholder="JUSTIFIQUE A DESATIVAÇÃO (OU OCULTAMENTO) COM ATÉ 240 LETRAS."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 mb-2">
                <div id="mapsAPI"></div>
                <div class="my-2">
                    <button type="button" class="btn btn-success" onclick="initMap(true, '');">Recarregar mapa</button>
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
<style>

</style>
@endsection