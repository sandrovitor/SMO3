<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> <!-- Font Awesome 5 -->
<link href="/css/glyphicon.css" rel="stylesheet">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link href="/css/impressao_temafixo.css" rel="stylesheet">
<link rel="stylesheet" id="mapastema" href="/css/{{$estilo}}">
<title>SMO :: IMPRESSÃO</title>
<style>
.badge-be {
    background-color: #f8f9fa;
    color: #dc3545;
    font-weight:bold;
}
</style>

</head>
<body>
    <div class="hidden-print">
        <h5><a href="javascript:void(0)" onclick="$('.conteudo_options').slideToggle(); if($('h5 .moreless').text() == '[-]') {$('h5 .moreless').text('[+]');} else {$('h5 .moreless').text('[-]');}"><span class="moreless">[-]</span> OPÇÕES PRÉ-IMPRESSÃO</a></h5>
        <div class="conteudo_options">
            
            <hr>
            <div class="item_mestre">
                <strong>ESQUEMA DE CORES:</strong>  
            </div>
            <div class="item_escravo1" style="display: flex; flex-wrap: wrap;">
                <a href="/css/impressao_estilo1.css" class="trocartema"><img src="/images/estilo_1.png"></a>
                <a href="/css/impressao_estilo2.css" class="trocartema"><img src="/images/estilo_2.png"></a>
                <a href="/css/impressao_estilo3.css" class="trocartema"><img src="/images/estilo_3.png"></a>
                <a href="/css/impressao_estilo4.css" class="trocartema"><img src="/images/estilo_4.png"></a>
            </div>
            <br>
        
            <div class="item_mestre">
                <strong>FONTE:</strong> 
            </div>
            <div class="item_escravo1">
                <select id="fonte_select">
                    <option selected="selected" value="Arial">Arial (padrão)</option>
                    <option value="Calibri">Calibri</option>
                    <option value="Comic Sans MS">Comic Sans MS</option>
                    <option value="Courier">Courier</option>
                    <option value="Tahoma">Tahoma</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Verdana">Verdana</option>

                </select>
                <button type="button" onclick="window.print()" style=""><i class="fa fa-print"></i> Imprimir</button>
                <button type="button" onclick="salvaEstilo()"><i class="fa fa-save"></i> Salvar Estilo</button>
            </div>
            
        </div>
    </div>

    {!!$html!!}



    <script>
        function salvaEstilo() {
            let valor = $('.trocartema.active').prop('href');
            valor = valor.substr(valor.lastIndexOf('/')+1);
            
            $.post('{{$router->generate("admFunctions")}}',{
                funcao: 'setConfigEstilo',
                valor: valor
            },function(){});
            
        }
        $(document).ready(function(){
            /*
            * REMOVE PROPAGANDA 000WEBHOST
            */
            if($('a[href*="000webhost.com"]').length > 0) {
                $('a[href*="000webhost.com"]').parent('div').hide()
            }
            /*
            * REMOVE PROPAGANDA 000WEBHOST
            */
            
            /*
            * Encontrar o estilo setado
            */
            
            $('.trocartema[href="'+$('#mapastema').attr('href')+'"]').addClass('active');
            
            
            /*
            * TROCAR TEMA DOS MAPAS
            */
            $(document).on('click', '.trocartema',function(){
                var obj = $(this);
                var atributo = $(this).attr('href');
                
                $('.trocartema').removeClass('active');
                obj.addClass('active');
                
                
                $('#mapastema').attr('href',atributo);
                return false;
            });
            
            $(document).on('change','#fonte_select',function(){
                var atributo = $(this).find(':selected').val();
                $('.page').css('font-family',atributo);
            });
        });
    </script>
</body>
</html>