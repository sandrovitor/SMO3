@extends('layouts.layoutindex')

@section ('paginaCorrente', 'Início')

@section('breadcrumb')
        <li class="breadcrumb-item active">Início</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-12 col-sm-12">
            <h3><strong>Bem vindo {{$uNome}}.</strong></h3>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="consulta">
                <div class="index_item">
                    <div class="index_item_caption">
                        <span class="glyphicon glyphicon-search"></span><br>
                        CONSULTA
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="registros">
                <div class="index_item">
                    <div class="index_item_caption">
                        <span class="glyphicon glyphicon-list-alt"></span><br>
                        REGISTROS
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="cadastro">
                <div class="index_item">
                    <div class="index_item_caption">
                        <span class="glyphicon glyphicon-edit"></span><br>
                        CADASTRO
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="tpessoal">
                <div class="index_item">
                    <div class="index_item_caption">
                        <span class="glyphicon glyphicon-pushpin"></span><br>
                        TERRITÓRIO PESSOAL
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="social">
                <div class="index_item">
                    <div class="index_item_caption">
                        <i class="fa fa-comment"></i><br>
                        REDES SOCIAIS
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <a href="campanha">
                <div class="index_item">
                    <div class="index_item_caption">
                        <span class="glyphicon glyphicon-briefcase"></span><br>
                        CAMPANHA
                    </div>
                </div>
            </a>
        </div>
    </div>
    <br>
    @if($_SESSION['nivel'] >= 5)
    <hr>
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card" id="pend-card">
                <div class="card-header py-1" data-toggle="collapse" data-target="#pend-body" style="cursor:pointer;"><strong>PENDÊNCIAS</strong></div>
                <div class="card-body p-2 collapse fade" id="pend-body">
                    <!--<a href="#"><div class="alert alert-info py-2 px-3"><strong>Titulo</strong> Conteúdo....... </div></a>-->
                </div>
            </div>

            
        </div>
    </div>
    @endif
@endsection
