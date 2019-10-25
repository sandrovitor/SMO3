@extends('layouts.layoutindex')

@php
    $tpstr = '';
@endphp

@section ('paginaCorrente', 'Território Pessoal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Início</a></li>
    <li class="breadcrumb-item active">Território Pessoal</li>
@endsection

@section('conteudo')
    <div class="row">
        <div class="col-12">
            <h5><strong>MEU TERRITÓRIO PESSOAL</strong></h5><hr>
            <div id="tpessoal">
                @if($surdos == '')
                    <h5>Vazio...</h5>
                @elseif($surdos != '')
                    <div id="sanfona">

                    @foreach ($surdos as $chave => $a)
                        @php
                            if($a->endereco == '') {$a->endereco = '-';}
                            if($a->p_ref == '') {$a->p_ref = '-';}
                            if($a->familia == '') {$a->familia = '-';}
                            if($a->whats == '') {$a->whats = '-';}
                            if($a->tel == '') {$a->tel = '-';}
                            if($a->facebook == '') {$a->facebook = '-';}
                            if($a->obs == '') {$a->obs = '-';}
                            if($a->gps == '') {$gpsDisabled = 'disabled';} else {$gpsDisabled = '';}
                            $be = '';
                            if($a->be == '1') {
                                $be = '<span class="badge badge-light text-danger" style="font-size: 1rem;"><i class="fas fa-heart"></i> BÍBLIA ESTUDA</span>';
                            } else {
                                $be = '<span class="badge badge-light text-muted" style="font-size: 1rem;"><i class="far fa-heart"></i> NÃO ESTUDA</span>';
                            }
                            if($a->encontrado == '0' && $a->be == '0') {
                                $encontradoLogo = '<span class="badge badge-light text-muted" data-toggle="tooltip" title="Não encontrado"><i class="fas fa-check"></i></span>';
                            } else {
                                $encontradoLogo = '<span class="badge badge-light text-primary" data-toggle="tooltip" title="Encontrado"><i class="fas fa-check-double"></i></span>';
                            }

                        @endphp
                        <div class="card">
                            <div class="card-header py-1">
                                <a class="card-link" data-toggle="collapse" href="#surdo-{{$chave}}">
                                    {{$a->nome}} {!! $encontradoLogo !!}
                                    
                                </a>
                                &nbsp; &nbsp;<span class="text-muted">[{{$a->bairro}}]</span>
                            </div>
                            <div id="surdo-{{$chave}}" class="collapse" data-parent="#sanfona">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <dl>
                                                <dt>Endereço:</dt>
                                                <dd>{{$a->endereco}}</dd>
                                                <dt>Ponto de Referência:</dt>
                                                <dd>{{$a->p_ref}}</dd>
                                                <dt>Família</dt>
                                                <dd>{{$a->familia}}</dd>
                                                <dt>Observações:</dt>
                                                <dd>{{$a->obs}}</dd>
                                                <dt>Telefones</dt>
                                                <dd>{{$a->tel}} / {{$a->whats}} (WhatsApp)</dd>
                                            </dl>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <dl>
                                                <dt>Faixa Etária:</dt>
                                                <dd>{{$a->idade}}</dd>
                                                <dt>Observações:</dt>
                                                <dd>{{$a->obs}}</dd>
                                                <dt>Bíblia Estuda Já:</dt>
                                                <dd>{!! $be !!}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="card-footer py-2">
                                    <div class="btn-group">
                                        <a href="https://www.google.com/maps?q={{$a->gps}}" target="_blank" class="btn btn-primary btn-sm {{$gpsDisabled}}">Rota</a> 
                                        <a href="/surdo/{{$a->id}}" class="btn btn-primary btn-sm">Mais Informações</a>
                                        <a href="/registros/novo/{{$a->id}}" class="btn btn-primary btn-sm">Novo Registro</a>
                                        <a href="/cadastro/editar/{{$a->id}}" target="_blank" class="btn btn-primary btn-sm">Editar</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    </div>
                @endif
                
            
            </div>
        </div>
    </div>
@endsection