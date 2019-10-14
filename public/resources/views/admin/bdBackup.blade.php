@extends('layouts.layoutadmin')

@php
    $mensagemDeRetorno = '';
    if($smoMSG != false) {
		foreach($smoMSG as $s) {
			$mensagemDeRetorno .= '<div class="alert alert-'.$s['tipo'].'"><strong>'. $s['titulo']. '</strong> '. $s['texto'].'</div>';
		}
	}

    $arquivos = $bd->getFiles();
    $arqAut = array();
    $arqMan = array();
    foreach($arquivos as $a) {
        if(substr($a, strrpos($a,'.')) == '.sql') {
            if(substr($a,0,9) == 'AUTOMATIC') {
                array_push($arqAut, $a);
            } else {
                array_push($arqMan, $a);
            }
        }
        
    }

    
    
@endphp

@section ('paginaCorrente', 'Administração')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{$router->generate('admIndex')}}">Administração</a></li>
    <li class="breadcrumb-item"><a href="{{$router->generate('admBd')}}">Banco de Dados</a></li>
    <li class="breadcrumb-item active">Backup e Restauração</li>
@endsection

@section ('mensagemDeRetorno', $mensagemDeRetorno)

@section('conteudo')
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header px-3 py-2">
                    <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">Backup</a>
                </div>
                <div class="card-body px-3 py-2 collapse show">
                    Para fazer um backup manual e padrão do banco de dados, bastar clicar no botão abaixo.<br><br>
                    <strong>OBS.:</strong> Um backup automático é realizado semanalmente. Lembre-se de guardar os backups manuais em lugar seguro e apagar os backups antigos.
                    <button type="button" class="btn btn-dark btn-block" onclick="fazerBackup()"><i class="fas fa-file-export"></i>&nbsp; FAZER BACKUP</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header px-3 py-2">
                    <a href="javascript:void(0)" onclick="cardBodyCollapse(this)">Restauração</a>
                </div>
                <div class="card-body px-3 py-2 collapse show">
                    <div>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Arquivo</th>
                                    <th colspan="2">DATA</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($arqAut) == 0)
                                <tr>
                                    <td colspan="3" class="text-center">Vazio</td>
                                </tr>
                            @else
                            @foreach($arqAut as $arq)
                            @php
                                $a = explode('--', $arq);
                                $a[0] = str_replace('AUTOMATIC-', '',$a[0]);
                                
                                $dataStr = str_replace('.sql', '',$a[1]);
                                $dataStr = explode('_', $dataStr);
                                $dataStr[1] = str_replace('-', ':', $dataStr[1]);
                                $dataStr = implode(' ', $dataStr);
                                $d = new DateTime($dataStr);
                                
                                $arqSemExtensao = substr($arq, 0, -4);
                            @endphp
                                <tr>
                                    <td><span class="badge badge-info">AUTOMÁTICO</span> <a href="javascript:void(0)" onclick="restaura('{{$arq}}')">{{$a[0]}}</a></td>
                                    <td>{{$d->format('d/m/Y H:i')}}</td>
                                    <td class="text-center">
                                        <a href="/admin/bd/download/{{$arqSemExtensao}}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Download"><i class="fas fa-download"></i></a>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Apagar" onclick="apagarBackup('{{$arq}}');"><i class="fas fa-eraser"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div>
                    <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Arquivo</th>
                                    <th colspan="2">DATA</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($arqMan) == 0)
                                <tr>
                                    <td colspan="3" class="text-center">Vazio</td>
                                </tr>
                            @else
                            @foreach($arqMan as $arq)
                            @php
                                $a = explode('--', $arq);
                                
                                $dataStr = str_replace('.sql', '',$a[1]);
                                $dataStr = explode('_', $dataStr);
                                $dataStr[1] = str_replace('-', ':', $dataStr[1]);
                                $dataStr = implode(' ', $dataStr);
                                $d = new DateTime($dataStr);

                                $arqSemExtensao = substr($arq, 0, -4);
                            @endphp
                                <tr>
                                    <td><span class="badge badge-dark">MANUAL</span> <a href="javascript:void(0)" onclick="restaura('{{$arq}}')">{{$a[0]}}</a></td>
                                    <td>{{$d->format('d/m/Y H:i')}}</td>
                                    <td class="text-center">
                                        <a href="/admin/bd/download/{{$arqSemExtensao}}" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Download"><i class="fas fa-download"></i></a>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Apagar" onclick="apagarBackup('{{$arq}}');"><i class="fas fa-eraser"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
    //echo $bd->restaura('smo--2019-10-08_11-37.sql');
    @endphp
@endsection

@section('script')
<script>
    function fazerBackup()
    {
        $.post('{{$router->generate("admFunctions")}}',{
            funcao: 'setBackupManual'
        },function(data){
            if(data == '') {
                alert('Servidor retornou um mensagem inválida.');
            } else {
                if(data.length > 50) {
                    alert(data);
                    return false;
                } 
                var popout = window.open('/admin/bd/download/'+data, '_blank', 'width=450, height=200');
                console.log(data);
                console.log('/admin/bd/download/'+data);
                //setTimeout(function(){popout.close()}, 1000);
            }
        });
    }

    function apagarBackup(nome)
    {
        $.post('{{$router->generate("admFunctions")}}',{
            funcao: 'setBackupDelete',
            nome: nome
        },function(data){
            if(data == 'OK') {
                location.reload();
            } else {
                alert(data);
            }
        });
    }

    function restaura(nome)
    {
        $.post('{{$router->generate("admFunctions")}}',{
            funcao: 'setRestauraBackup',
            nome: nome
        },function(data){
            if(data == 'OK') {
                location.reload();
            } else {
                alert(data);
            }
        });
    }
</script>
@endsection