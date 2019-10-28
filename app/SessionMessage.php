<?php

class SessionMessage
{
    static function ler()
    {
        @session_start();
        if(isset($_SESSION['smoMsgRetorno']) && $_SESSION['smoMsgRetorno'] != '') {
            $smoMSG = $_SESSION['smoMsgRetorno'];
            SessionMessage::delete();
            return $smoMSG;
        } else {
            return false;
        }
    }

    static function lerFormatado() {
        /*
         * Retorna STRING formatada ou FALSE se não tiver mensagem
         */
        @session_start();
        if(isset($_SESSION['smoMsgRetorno']) && $_SESSION['smoMsgRetorno'] != '') {
            $smoMSG = $_SESSION['smoMsgRetorno'];
            $retorno = '';

            foreach($smoMSG as $x) {
                if($x['tipo'] == '' || !isset($x['tipo'])) {
                    $retorno .= '<div class="alert alert-info"><strong>'.$x['titulo'].'</strong> '.$x['texto'].'</div>';
                } else {
                    $retorno .= '<div class="alert alert-'.$x['tipo'].'"><strong>'.$x['titulo'].'</strong> '.$x['texto'].'</div>';
                }
            }

            SessionMessage::delete();
            return $retorno;
        } else {
            return false;
        }
    }

    static function novo(array $mensagem)
    {
        /*
         * ARRAY $mensagem:
         * titulo   => Titulo da mensagem de notificação
         * texto    => Corpo da mensagem de notificação
         * tipo     => Grau do alerta: info, success, warning, danger, primary.
         */
        @session_start();
        if(!isset($_SESSION['smoMsgRetorno']) || $_SESSION['smoMsgRetorno'] == '')  { // Não há mensagem
            $smo = array();
        } else {
            $smo = $_SESSION['smoMsgRetorno'];
        }

        if(!is_array($mensagem)) {
            // Enviado somente mensagem. Formata como array.
            $x = $mensagem;
            $mensagem = array(
                'titulo' => 'Notificação: ',
                'texto' => $x,
                'tipo' => 'info'
            );
        } else {
            if(!isset($mensagem['titulo'])) {
                $mensagem['titulo'] = 'Notificação: ';
            }
            if(!isset($mensagem['texto'])) {
                $mensagem['texto'] = 'Alguma notificação foi enviada pela página anterior, mas... eu esqueci!';
            }
            if(!isset($mensagem['tipo'])) {
                $mensagem['tipo'] = 'info';
            }
        }

        array_push($smo, $mensagem);
        $_SESSION['smoMsgRetorno'] = $smo;

    }

    static function delete()
    {
        @session_start();
        $_SESSION['smoMsgRetorno'] = null;
        unset($_SESSION['smoMsgRetorno']);
    }
}