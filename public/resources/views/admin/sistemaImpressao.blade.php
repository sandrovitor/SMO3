    @extends('layouts.layoutadmin')

    @php
        $mensagemDeRetorno = '';
        if($smoMSG != false) {
            foreach($smoMSG as $s) {
                $mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
            }
        }

        
    @endphp

    @section ('paginaCorrente', 'Administração')

    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
        <li class="breadcrumb-item"><a href="{{$router->generate('admSistema')}}">Sistema</a></li>
        <li class="breadcrumb-item active">Impressão</li>
    @endsection

    @section ('mensagemDeRetorno', $mensagemDeRetorno)

    @section('conteudo')
        <div class="row">
            <div class="mb-2 col-12 col-md-5 col-lg-6">
                <label><strong>Opção de impressão</strong></label>
                <select name="tipo" class="form-control mb-3">
                    <option value="">Escolha:</option>
                    <option value="todos"> >> TODOS << </option>
                    <option value="bairro"> Por bairro </option>
                    <option value="intervalo"> Intervalo de mapas </option>
                    <option value="individual"> Mapas individuais </option>
                </select>
                <div id="tipoBairro">
                    <label><strong>ESCOLHA O BAIRRO</strong></label>
                    <select name="bairro" class="form-control">
                            <option value=""></option>
                    @php
                        $regiao_nome = '';
                    @endphp
                    @foreach($bairros as $b)
                        @if($regiao_nome == '')
                            @php
                            $regiao_nome = $b->regiao_nome;
                            @endphp
                            <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                        @elseif($regiao_nome != $b->regiao_nome)
                            @php
                            $regiao_nome = $b->regiao_nome;
                            @endphp
                            </optgroup>
                            <optgroup label="Região {{$b->regiao_numero}} - {{$b->regiao_nome}}">
                        @endif
                            <option value="{{$b->id}}">{{$b->bairro}}</option>
                    @endforeach
                            </optgroup>
                    </select>
                </div>
                <div id="tipoIntervalo">
                    <div class="row">
                        <div class="col-6">
                            <label><strong>DE:</strong></label>
                            <select class="form-control" name="mapa1">
                                <option value=""></option>
                                @php
                                    $mNome = '';
                                @endphp
                                @foreach($mapas as $m)
                                    @if($m->mapa != $mNome)
                                <option value="{{$m->mapa}}">{{$m->mapa}}</option>
                                    @php
                                        $mNome = $m->mapa;
                                    @endphp
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label><strong>ATÉ:</strong></label>
                            <select class="form-control" name="mapa2">
                                <option value=""></option>
                                @php
                                    $mNome = '';
                                @endphp
                                @foreach($mapas as $m)
                                    @if($m->mapa != $mNome)
                                <option value="{{$m->mapa}}">{{$m->mapa}}</option>
                                    @php
                                        $mNome = $m->mapa;
                                    @endphp
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="tipoIndividual">
                    <label><strong>ESCOLHA O(S) MAPA(S)</strong></label>
                    <select class="form-control" multiple name="mapaInd" size="10">
                    @php
                        $bNome = '';
                    @endphp
                    @foreach($mapas as $m)
                        @if($bNome == '')
                            @php
                            $bNome = $m->bairro;
                            @endphp
                            <optgroup label="{{$m->bairro}}">
                        @elseif($bNome != $m->bairro)
                            @php
                            $bNome = $m->bairro;
                            @endphp
                            </optgroup>
                            <optgroup label="{{$m->bairro}}">
                        @endif
                            <option value="{{$m->mapa}}">{{$m->mapa}}</option>
                    @endforeach
                            </optgroup>
                    </select>
                    <small class="text-secondary">Para selecionar múltiplos mapas, mantenha a tecla <kbd>Ctrl</kbd> pressionada.</small>
                </div>
            </div>
            <div class="mb-2 col-12 col-md-7 col-lg-6">
                <div class="row" id="divBtn">
                    <div class="col-6">
                        <h6 class="text-center mb-3"><strong>IMPRIMIR</strong></h6>
                        <button class="btn btn-block btn-sm btn-primary btnTog" disabled onclick="printMapas()">Imprimir mapas</button>
                        <button class="btn btn-block btn-sm btn-primary btnTog" disabled onclick="printReg()">Registro (frente)</button>
                        <button class="btn btn-block btn-sm btn-primary" onclick="printVerso()">Registro (verso)</button>
                        <button class="btn btn-block btn-sm btn-primary btnTog" disabled onclick="printQR()">Código QR</button>
                    </div>
                    <div class="col-6">
                        <h6 class="text-center mb-3"><strong>PDF</strong></h6>
                        <button class="btn btn-block btn-sm btn-info btnTog" disabled onclick="pdfMapas()">Gerar mapas</button>
                        <button class="btn btn-block btn-sm btn-info btnTog" disabled onclick="pdfReg()">Gerar registro (frente)</button>
                        <button class="btn btn-block btn-sm btn-info" onclick="pdfVerso()">Gerar registro (verso)</button>
                        <button class="btn btn-block btn-sm btn-info btnTog" disabled onclick="pdfQR()">Gerar Código QR</button>
                    </div>
                </div>
                
                <form action="{{$router->generate('admImpressao')}}" method="post" id="form_print" target="_blank">
                    <input type="hidden" name="fModo" value="PRINT"><!-- PRINTMAPAS, PRINTREG, PRINTQR ou PDFMAPAS, PDFREG, PDFQR -->
                    <input type="hidden" name="fTipo" value=""> <!-- todos, bairro, intervalo ou individual -->
                    <input type="hidden" name="fCampo1" value="">
                    <input type="hidden" name="fCampo2" value="">
                    <input type="hidden" name="fRegistro" value="not">
                    <input type="hidden" name="fQrcode" value="not">
			    </form>
            </div>
        </div>
    @endsection

    @section('script')
    <style>
    #tipoBairro,
    #tipoIntervalo,
    #tipoIndividual {
        display:none;
    }
    </style>
    <script>
        function validaPrint() {
            let tipo = $('[name="tipo"]').find(':selected').val();
            let bairro = $('[name="bairro"]').find(':selected').val();
            let mapa1 = $('[name="mapa1"]').find(':selected').val();
            let mapa2 = $('[name="mapa2"]').find(':selected').val();
            let mapaInd = $('[name="mapaInd"]').val();

            // TIPO DE IMPRESSÃO
            $('#form_print').find('[name="fTipo"]').val(tipo);

            if(tipo == "") {
                alert('Escolha uma opção de impressão');
                $('[name="tipo"]').focus();
                return false;
            } else if(tipo == 'bairro') { 
                /**
                 * BAIRRO
                 */
                if(bairro == '') {
                    alert('Escolha o bairro.');
                    return false;
                }
            
                $('#form_print').find('[name="fCampo1"]').val(bairro);
                $('#form_print').find('[name="fCampo2"]').val('');
            } else if(tipo == 'intervalo') { 
                /**
                 * INTERVALO
                 */
                if(mapa1 == '' || mapa2 == '') {
                    alert('Escolha um mapa pra iniciar e um mapa pra finalizar o intervalo.');
                    return false;
                }
            
                $('#form_print').find('[name="fCampo1"]').val(mapa1);
                $('#form_print').find('[name="fCampo2"]').val(mapa2);
            } else if(tipo == 'individual') { 
                /**
                 * INDIVIDUAL
                 */
                if(mapaInd.length == 0) {
                    alert('Escolha no mínimo um mapa.');
                    return false;
                }
            
                $('#form_print').find('[name="fCampo1"]').val(mapaInd.toString());
                $('#form_print').find('[name="fCampo2"]').val('');
            } else if(tipo == 'todos') {
                /**
                 * TODOS
                 */
                $('#form_print').find('[name="fCampo1"]').val('');
                $('#form_print').find('[name="fCampo2"]').val('');
            }
        }
        function printMapas() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PRINTMAPAS');
            $('#form_print').submit();
        }
        function printReg() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PRINTREG');
            $('#form_print').submit();
        }
        function printVerso() {}
        function printQR() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PRINTQR');
        }
        function pdfMapas() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PDFMAPAS');
            $('#form_print').submit();
        }
        function pdfReg() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PDFREG');
            $('#form_print').submit();
        }
        function pdfVerso() {}
        function pdfQR() {
            if(validaPrint() == false) { return false;}
            $('#form_print').find('[name="fModo"]').val('PDFQR');
        }
        $(document).ready(function(){
            $(document).on('change', '[name="tipo"]',function(){
                switch($(this).find(':selected').val()) {
                    case 'todos':
                        $('#tipoBairro, #tipoIntervalo, #tipoIndividual').slideUp('fast');
                        $('.btnTog').prop('disabled', false);
                        break;
                    
                    case 'bairro':
                        $('#tipoIntervalo, #tipoIndividual').slideUp('fast');
                        $('#tipoBairro').slideDown('fast');
                        if($('[name="bairro"]').find(':selected').val() != 0) {
                            $('.btnTog').prop('disabled', false);
                        } else {
                            $('.btnTog').prop('disabled', true);
                        }
                        break;

                    case 'intervalo':
                        $('#tipoBairro, #tipoIndividual').slideUp('fast');
                        $('#tipoIntervalo').slideDown('fast');
                        break;

                    case 'individual':
                        $('#tipoBairro, #tipoIntervalo').slideUp('fast');
                        $('#tipoIndividual').slideDown('fast');
                        break;

                    default:
                        $('#tipoBairro, #tipoIntervalo, #tipoIndividual').slideUp('fast');
                        $('.btnTog').prop('disabled', true);
                        break;
                }
            });

            $(document).on('change', '[name="bairro"]',function(){
                if($(this).find(':selected').val() != "") {
                    $('.btnTog').prop('disabled', false);
                } else {
                    $('.btnTog').prop('disabled', true);
                }
            });

            $(document).on('change', '[name="mapa1"]', function(){
                if($(this).find(':selected').val() != "") {
                    if($('[name="mapa2"]').find(':selected').val() == "") {
                        $('[name="mapa2"]').val($('[name="mapa1"]').find(':selected').val());
                    }
                    $('.btnTog').prop('disabled', false);
                } else {
                    $('.btnTog').prop('disabled', true);
                }
            });

            $(document).on('change', '[name="mapa2"]', function(){
                if($(this).find(':selected').val() != "") {
                    if($('[name="mapa1"]').find(':selected').val() == "") {
                        $('[name="mapa1"]').val($('[name="mapa2"]').find(':selected').val());
                    }
                    $('.btnTog').prop('disabled', false);
                } else {
                    $('.btnTog').prop('disabled', true);
                }
            });

            $(document).on('change', '[name="mapaInd"]', function(){
                if($(this).find(':selected').val() != "") {
                    $('.btnTog').prop('disabled', false);
                } else {
                    $('.btnTog').prop('disabled', true);
                }
            });
        });
    </script>
    @endsection