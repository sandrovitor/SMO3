@extends('layouts.layoutindex')

@php
    $rota1 = $router->generate( 'registrosNovoPOST' );
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-info"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    $config = new Config();
    $social = $config->get('social');
    $campanha = $config->get('campanha');
    $hoje = new DateTime();

    if($surdoUnico == true) {
        $surOPTSelect = '';
        $surSELDisabled = 'disabled';
        $surInputAlternativo = '<input type="hidden" name="surdo" value="'.$surdoId.'">';
    } else {
        $surOPTSelect = 'selected';
        $surSELDisabled = '';
        $surInputAlternativo = '';
    }

    if((int)$_SESSION['nivel'] < 5) {
        $pubSELDisabled = 'disabled';
        $pubInputAlternativo = '<input type="hidden" name="publicador" value="'.$_SESSION['id'].'">';
    } else {
        $pubSELDisabled = '';
        $pubInputAlternativo = '';
    }
    
    //var_dump($campanha);
@endphp

@section ('paginaCorrente', 'Registros')

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item"><a href="/registros">Registros</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-6 col-sm-6">
            <a href="#" class="btn btn-block btn-primary active">Novo</a>
        </div>
        <div class="col-6 col-sm-6">
            <a href="/registros/buscar" class="btn btn-block btn-light">Buscar</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-xl-8 offset-xl-2">
            <div id="tela1" class="text-center tela">
                <h3><strong>Olá {{$uNome}}! Vou te ajudar a criar um novo registro.</strong></h3>
                <br>
                <h3>Quem foi o surdo que você visitou ou encontrou?<br>
                <small class="text-muted fs06">A lista abaixo está organizada por bairro e em ordem alfabética. Tá bem fácil...</small></h3>

                <select class="form-control form-control-sm" id="surdo" required {{$surSELDisabled}}>
                    <option disabled {{$surOPTSelect}}>- Escolha:</option>
                    @php
                        echo $surdos;
                    @endphp
                </select>
                {!!$surInputAlternativo!!}
                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary ml-auto px-4 btn-next">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>
                </div>
            </div>

            <div id="tela2" class="text-center tela" style="display:none;">
                <h3>Muito bem! Em que data você visitou <span class="surdoNome"></span>?<br>
                <small class="text-muted fs06">Se você não lembra da data, então pode escolher uma data nesta mesma semana.</small></h3>

                <input type="date" class="form-control form-control-sm" id="data" required>

                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary mr-auto px-4 btn-prev"><i class="fas fa-angle-left"></i> &nbsp; Voltar</button>
                    <button type="button" class="btn btn-primary ml-auto px-4 btn-next">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>
                </div>
            </div>

            <div id="tela3" class="text-center tela" style="display:none;">
                <h3>Certo. E você encontrou <span class="surdoNome"></span> no dia <span class="dataVisita"></span>?<br>
                <small class="text-muted fs06">Se você visitou e não encontrou, marque a opção "Não".</small></h3>

                <select class="form-control form-control-sm" id="encontrado" required>
                    <option disabled selected>- Escolha:</option>
                    <option value="yes">SIM, encontrei.</option>
                    <option value="not">NÃO, não encontrei.</option>
                </select>

                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary mr-auto px-4 btn-prev"><i class="fas fa-angle-left"></i> &nbsp; Voltar</button>
                    <button type="button" class="btn btn-primary ml-auto px-4 btn-next">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>
                </div>
            </div>

            @if ($campanha['ativo'] == 'yes' && $hoje >= $campanha['InicioDateTime'] && $hoje < $campanha['FinalDateTime'])
            <div id="tela4" class="text-center tela" style="display:none;">
                <h3>Ah, tem uma pergunta extra: você entregou a publicação da <strong>Campanha {{$campanha->nome}}</strong> ?<br>
                <small class="text-muted fs06">Se você deu pessoalmente, para alguém da familia ou no Correio, pode marca "Sim".</small></h3>

                <select class="form-control form-control-sm" id="campanha" required>
                    <option disabled selected>- Escolha:</option>
                    <option value="yes">SIM, entreguei.</option>
                    <option value="not">NÃO, não entreguei.</option>
                </select>

                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary mr-auto px-4 btn-prev"><i class="fas fa-angle-left"></i> &nbsp; Voltar</button>
                    <button type="button" class="btn btn-primary ml-auto px-4 btn-next">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>
                </div>
            </div>
            @endif

            <div id="tela5" class="text-center tela" style="display:none;">
                <h3>Ufa! Foram muitas perguntas, né?<br>
                Só mais uma: <strong>Como foi a visita?</strong><br>
                <small class="text-muted fs06">Conte o máximo de detalhes que conseguir.</small></h3>

                <textarea class="form-control" rows="4" id="texto" placeholder="Vida ter? Atenção como? Biblia ou Video passou qual? Você voltar visitar? Informações várias..." required></textarea>

                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary mr-auto px-4 btn-prev"><i class="fas fa-angle-left"></i> &nbsp; Voltar</button>
                    <button type="button" class="btn btn-primary ml-auto px-4 btn-next">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>
                </div>
            </div>
            
            <div id="tela10" class="text-center tela" style="display:none;">
                <h3>Terminamos!<br>
                <small class="text-muted fs06">Confira se todas as informações abaixo estão corretas antes de Salvar.</small></h3>

                <div class="shadow-sm border border-dark p-3 text-left">
                    <strong class="mr-3">Surdo:</strong> <span class="surdoNome"></span><br>
                    <strong class="mr-3">Data:</strong> <span class="dataVisita"></span><br>
                    <strong class="mr-3">Encontrado:</strong> <span class="surdoEncontrado"></span><br>
                    <strong class="mr-3">Campanha:</strong> <span class="campanha"><span class="badge badge-light">-</span></span><br>
                    <strong class="mr-3">Texto:</strong> <i>"<span class="textoVisita"></span>"</i><br>
                </div>

                <div class="d-flex mt-3">
                    <button type="button" class="btn btn-primary mr-auto px-4 btn-prev"><i class="fas fa-angle-left"></i> &nbsp; Voltar</button>
                    <!--<button type="button" class="btn btn-primary ml-auto px-4">Avançar &nbsp; <i class="fas fa-angle-right"></i></button>-->
                    <form method="POST" action="{{$rota1}}">
                        <input type="hidden" name="publicador" value="{{$_SESSION['id']}}">
                        <input type="hidden" name="data" value="">
                        <input type="hidden" name="surdo" value="">
                        <input type="hidden" name="texto" value="">
                        <button type="submit" class="btn btn-success ml-auto px-4">Salvar</button>
                    </form>
                </div>
            </div>



            @if ($campanha['ativo'] == 'yes' && $hoje >= $campanha['InicioDateTime'] && $hoje < $campanha['FinalDateTime'])
            
            @else

            @endif
        </div>
    </div>
@endsection

@section('script')
<style>
    small.fs06 {
        font-size: .6em;
    }
</style>
<script>
$(document).ready(function(){
    $(document).on('click', '.btn-next',function(){
        let telaAtual = $(this).parents('.tela').eq(0);
        let telaProx = telaAtual.next();
        //console.log(telaAtual, telaProx);

        console.log(telaAtual.find('[required]'));
        if(!telaAtual.find('[required]').val()) {
            //alert('Você precisa escolher uma opção!');
            telaAtual.find('[required]').focus();
        } else {
            telaAtual.fadeOut('fast',function(){
                telaProx.delay(300).fadeIn('fast');
            });
        }
    });

    $(document).on('click', '.btn-prev',function(){
        let telaAtual = $(this).parents('.tela').eq(0);
        let telaAnt = telaAtual.prev();

        telaAtual.fadeOut('fast',function(){
            telaAnt.delay(300).fadeIn('fast');
        });
    });

    $(document).on('change', '#surdo',function(){
        let valor = $(this).find(':selected').val();
        let texto = $(this).find(':selected').text();
        $('.surdoNome').text(texto);
        $('form input[name="surdo"]').val(valor);
    });

    $(document).on('change', '#data',function(){
        let valor = $(this).val();
        let dataVisita = valor.split('-');
        dataVisita = dataVisita[2] +'/'+ dataVisita[1] +'/'+ dataVisita[0];
        $('.dataVisita').text(dataVisita);
        $('form input[name="data"]').val(valor);

        console.log(dataVisita);
    });

    $(document).on('change', '#campanha',function(){
        let valor = $(this).find(':selected').val();
        if(valor == 'yes') {
            $('.campanha').html('<span class="badge badge-success"><i class="fas fa-check"></i> SIM</span>');
            $('form').append('<input type="hidden" name="campanha" value="campanha">');
        } else {
            $('.campanha').html('<span class="badge badge-light text-muted"><i class="fas fa-check"></i> NÃO</span>');
            $('form input[name="campanha"]').remove();
        }
    });

    $(document).on('change', '#encontrado',function(){
        let valor = $(this).find(':selected').val();
        if(valor == 'yes') {
            $('.surdoEncontrado').html('<span class="badge badge-light text-primary"><i class="fas fa-check-double"></i> ENCONTRADO</span>');
            $('form').append('<input type="hidden" name="encontrado" value="encontrado">');
        } else {
            $('.surdoEncontrado').html('<span class="badge badge-light text-muted"><i class="fas fa-check"></i> NÃO ENCONTRADO</span>');
            $('form input[name="encontrado"]').remove();
        }
    });

    $(document).on('change', '#texto',function(){
        let valor = $(this).val();

        $('.textoVisita').text(valor);
        $('form input[name="texto"]').val(valor);
    });
    
});
</script>
@endsection