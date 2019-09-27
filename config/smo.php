<?php
$app;
$file_env = '../.env';
if(file_exists($file_env)){
    $f = fopen($file_env, 'r');
    if(filesize($file_env) == 0) { // Se tamanho do arquivo for igual a zero
        exit('SMO diz: Verifique o arquivo de configuração do sistema.');
    }

    $c = fread($f, filesize($file_env));
    $c = explode("\n",$c);
    foreach($c as $linha) {
        $d = explode("=", $linha);
        $app[$d[0]] = $d[1];
    }
} else {
    exit('SMO diz: O sistema não está configurado corretamente.');
}