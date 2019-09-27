@extends('layouts.layoutindex')

@section('estiloPersonalizado')
    <link href="css/layoutConsulta.css" rel="stylesheet">
@endsection

@section ('paginaCorrente', 'Consulta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Consulta</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-12 col-sm-12 col-md-5 col-lg-3 col-xl-2" id="filtro">
            <div class="card">
                <div class="card-header">
                        <strong>FILTROS</strong>
                </div>
                <div class="card-body">
                    <form action="#" method="get" onsubmit="return false;">
                        <div class="card">
                            <div class="card-body bg-light">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" id="filtroNome" name="filtroNome" class="form-control form-control-sm">
                                </div>
                                <div class="form-group">
                                    <label>Bairro</label>
                                    <select id="filtroBairro" name="filtroBairro" class="form-control form-control-sm">
                                        <option value="">- Escolha:</option>
                                        @php 
                                                echo $bairros
                                        @endphp
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Turno</label>
                                    <select id="filtroTurno" name="filtroTurno" class="form-control form-control-sm">
                                        <option value="">- Escolha:</option>
                                        <option value="MAN">MANHÃ:</option>
                                        <option value="TAR">TARDE</option>
                                        <option value="NOI">NOITE</option>
                                        <option value="IND">INDEFINIDO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Idade</label>
                                    <select id="filtroIdade" name="filtroIdade" class="form-control form-control-sm">
                                        <option value="">- Escolha:</option>
                                        <option value="CRI">CRIANÇA</option>
                                        <option value="JOV">JOVEM</option>
                                        <option value="ADU">ADULTO</option>
                                        <option value="IDO">IDOSO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Bíblia Estuda Já</label>
                                    <select id="filtroBE" name="filtroBE" class="form-control form-control-sm">
                                        <option value="">- Escolha:</option>
                                        <option value="YES">SIM</option>
                                        <option value="NOT">NÃO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ocultos</label>
                                    <select id="filtroOculto" name="filtroOculto" class="form-control form-control-sm">
                                        <option value="AMBOS">Mostrar ambos</option>
                                        <option value="YES">Somente ocultos</option>
                                        <option value="NOT" selected>Somente visíveis</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Encontrado</label>
                                    <select id="filtroEncontrado" name="filtroEncontrado" class="form-control form-control-sm">
                                        <option value="">- Escolha:</option>
                                        <option value="YES">SIM</option>
                                        <option value="NOT">NÃO</option>
                                    </select>
                                </div>
                                <hr>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input id="filtroDesativado" name="filtroDesativado" type="checkbox" class="form-check-input">Incluir desativados na pesquisa
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" onclick="consultaPesquisa()"><span class="glyphicon glyphicon-search"></span> Pesquisar</button>
                    </form>
                        
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-7 col-lg-4 col-xl-4" id="resultado">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-6" id="resultado-info">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
    <div class="backTop"><span class="glyphicon glyphicon-chevron-up"></span></div>
@endsection