@extends('layouts.layoutindex')

@section ('paginaCorrente', 'Registros')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Registros</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-6 col-sm-6">
            <a href="/registros/novo" class="btn btn-block btn-light">Novo</a>
        </div>
        <div class="col-6 col-sm-6">
            <a href="/registros/buscar" class="btn btn-block btn-light">Buscar</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <h4><strong>Últimos registros (...)</strong></h4><br>
            <div class="d-flex flex-wrap justify-content-center" id="ultimos"></div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            setTimeout(function(){registroUltimos();}, 500);
        });
    </script>
    <style>
        #ultimos .registro-card {
            width: 350px;
        }
    </style>
@endsection