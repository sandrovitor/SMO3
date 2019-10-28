@extends('layouts.layoutindex')

@section('estiloPersonalizado')
    <link href="/css/layoutConsulta.css" rel="stylesheet">
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
                        <button type="submit" class="btn btn-primary btn-sm" onclick="consultaPesquisa()"><span class="glyphicon glyphicon-search"></span> Pesquisar</button> &nbsp;
                        <button type="button" class="btn btn-info btn-sm" onclick="$('#modLegenda').modal('show');"><span class="glyphicon glyphicon-info-sign"></span> Legenda</button>
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
    <div class="modal fade" id="modLegenda">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title font-weight-bold">Legenda</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Os surdos possuem novas classificações e novos ícones de status. Veja:<br><br>
                    <div class="row">
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-success" data-toggle="tooltip" title="ATIVO!"><i class="fas fa-star"></i></span></h4>
                                <strong>Surdo ativo</strong>.<br> Todos podem visitar normalmente!
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-info" data-toggle="tooltip" title="Oculto"><i class="far fa-star-half"></i></span></h4>
                                <strong>Surdo oculto</strong>.<br> Há algum motivo para o surdo estar oculto.
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-danger" data-toggle="tooltip" title="Desativado"><i class="far fa-star"></i></span></h4>
                                <strong>Surdo desativado</strong>.<br> Esses surdos foram removidos do mapa por um motivo particular. Não visite esses surdos sem autorização do SS!
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i></span></h4>
                                <strong>Surdo não encontrado</strong>.<br> Surdos que ainda não foi encontrado na pregação.
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-light text-primary" data-toggle="tooltip" title="ENCONTRADO!"><i class="fas fa-check-double"></i></span></h4>
                                <strong>Surdo encontrado</strong>.<br> Esses surdos foram encontrados durante a pregação. Que bom!
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-light text-muted" data-toggle="tooltip" title="Não estuda Bíblia"><i class="far fa-heart"></i></span></h4>
                                <strong>Não estuda a Bíblia</strong>.<br> Surdo disponível para estudar a Bíblia. Tente marcar uma revisita.
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 text-center">
                            <div class="border border-dark p-2 mb-3 rounded-sm">
                                <h4><span class="badge badge-light text-danger" data-toggle="tooltip" title="Bíblia Estuda!"><i class="fas fa-heart"></i></span></h4>
                                <strong>Estuda a Bíblia</strong>.<br> Surdo já estuda a Bíblia com alguém. Que bom!
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5 class="text-center font-weight-bold">Qualquer dúvida que surgir, não hesite em contatar um ancião ou administrador para te ajudar.</h5>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="backTop"><span class="glyphicon glyphicon-chevron-up"></span></div>
@endsection