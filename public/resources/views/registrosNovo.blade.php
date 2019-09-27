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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                            <form class="form-rounded" method="POST" action="{{$rota1}}">
                                <div class="form-group row">
                                    <label class="col-12 col-sm-4 text-sm-right">Publicador:</label>
                                    <div class="col-12 col-sm-8">
                                        <select class="form-control form-control-sm" id="publicador" name="publicador" {{$pubSELDisabled}}>
                                            @php
                                                echo $publicadores;
                                            @endphp
                                        </select>
                                        {!!$pubInputAlternativo!!}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-12 col-sm-4 text-sm-right">Data da Visita:</label>
                                    <div class="col-12 col-sm-8">
                                        <input type="date" class="form-control form-control-sm" id="data" name="data" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-12 col-sm-4 text-sm-right">Surdo:</label>
                                    <div class="col-12 col-sm-8">
                                        <select class="form-control form-control-sm" id="surdo" name="surdo" required {{$surSELDisabled}}>
                                            <option disabled {{$surOPTSelect}}>- Escolha:</option>
                                            @php
                                                echo $surdos;
                                            @endphp
                                        </select>
                                        {!!$surInputAlternativo!!}
                                    </div>
                                </div>

                                <hr>
                                <div class="form-group row">
                                    <div class="col-12 col-sm-8 offset-sm-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="encontrado" name="encontrado" value="encontrado">
                                            <label class="custom-control-label" for="encontrado">Surdo encontrado</label><br>
                                            <small>[Se encontrou o surdo e pregou para ele, marque acima! Se não encontrou ou encontrou mais não pregou, deixe desmarcado.]</small>
                                        </div><br>

                                        @if ($campanha['ativo'] == 'yes' && $hoje >= $campanha['InicioDateTime'] && $hoje < $campanha['FinalDateTime'])
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="campanha" name="campanha" value="campanha" checked="checked">
                                            <label class="custom-control-label" for="campanha">Campanha 
                                            @php
                                            echo $campanha['nome'];
                                            @endphp
                                            </label><br>
                                            <small>[Surdo foi contatado durante a campanha especial!]</small>
                                        </div><br>
                                        @endif

                                        @if ($social['ativo'] == 'yes' && $hoje >= $social['InicioDateTime'] && $hoje < $social['FinalDateTime'])
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="social" name="social" value="social" checked="checked">
                                            <label class="custom-control-label" for="social">Contatado via Redes Sociais</label><br>
                                            <small>[Se o surdo foi contatado por redes sociais: WhatsApp, Facebook, Instagram Direct, Messenger, entre outros.]</small>
                                        </div><br>
                                        @endif

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="be" name="be" value="be">
                                            <label class="custom-control-label" for="be">Eu responsável ensinar</label><br>
                                            <small>[Se você é o responsável em ensinar esse surdo, marque! Se você não é o responsável ou parou de ensinar, desmarque essa opção.]</small>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="form-group row">
                                    <label class="col-12 col-sm-4 text-sm-right">Texto:</label>
                                    <div class="col-12 col-sm-8">
                                        <textarea class="form-control form-control-sm" id="texto" name="texto" rows="4" placeholder="Vida ter? Atenção como? Biblia ou Video passou qual? Você voltar visitar? Informações várias..." required></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-12 text-right">
                                        <button type="submit" id="butSave" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection