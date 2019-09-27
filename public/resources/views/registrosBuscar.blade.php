@extends('layouts.layoutindex')

@section ('paginaCorrente', 'Registros')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item"><a href="/registros">Registros</a></li>
    <li class="breadcrumb-item active">Buscar</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-6 col-sm-6">
            <a href="/registros/novo" class="btn btn-block btn-light">Novo</a>
        </div>
        <div class="col-6 col-sm-6">
            <a href="#" class="btn btn-block btn-primary active">Buscar</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-lg-3 col-xl-3">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <form method="post" action="#" onsubmit="registroBusca(); return false;">
                                        <div class="form-group">
                                            <label>Surdos</label>
                                            <select class="form-control" name="surdo" id="surdo">
                                                <option selected value="0">- Escolha:</option>
                                            @php
                                                echo $surdos;
                                            @endphp
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Publicadores</label>
                                            <select class="form-control" name="publicador" id="publicador">
                                                <option selected value="0">- Escolha:</option>
                                                @php
                                                    echo $publicadores;
                                                @endphp
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-search"></span> Pesquisar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <hr class="hidden hidden-xl">
                        </div>
                        <div class="col-12 col-sm-12 col-lg-9 col-xl-9" id="resultadobusca">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="backTop"><span class="glyphicon glyphicon-chevron-up"></span></div>
@endsection

@if($surdoid != '')
    @section('script')
        <script>
            $(document).ready(function(){
                registroBusca();
            });
        </script>
    @endsection
@endif