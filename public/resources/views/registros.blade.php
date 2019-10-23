@extends('layouts.layoutindex')

@php
        $mensagemDeRetorno = '';
        if($smoMSG != false) {
            foreach($smoMSG as $s) {
                $mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
            }
        }
@endphp

@section ('paginaCorrente', 'Registros')

@section ('mensagemDeRetorno', $mensagemDeRetorno)

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