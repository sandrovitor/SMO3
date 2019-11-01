<?php
include_once('Model.php');

class MA extends Model {
    protected $maGeral = 'ma_geral';
    protected $pdo;
    protected $id;
    protected $maTable;


    function __construct(int $usuarioId)
    {
        parent::__construct();
        $this->id = $usuarioId;
        $this->maTable = 'ma_'.$usuarioId;

    }

    function getHoras(int $qtd)
    {
        $abc = $this->pdo->query('SELECT * FROM '.$this->maTable.' ORDER BY data DESC LIMIT 0, '.$qtd);
        if($abc->rowCount() > 0) {
            return json_encode($abc->fetchAll(PDO::FETCH_OBJ));
        } else {
            return '{0}';
        }
    }

    function getHorasAll()
    {
        $abc = $this->pdo->query('SELECT * FROM '.$this->maTable.' ORDER BY data DESC');
        if($abc->rowCount() > 0) {
            return json_encode($abc->fetchAll(PDO::FETCH_OBJ));
        } else {
            return '{0}';
        }
    }

    function getHoraMes(DateTime $def)
    {
        $data = new DateTime($def->format('Y-m-d'));
        $ano1 = $data->format('Y');
        $mes1 = $data->format('m');

        $data->add(new DateInterval('P1M'));
        $ano2 = $data->format('Y');
        $mes2 = $data->format('m');

        $d1 = $ano1.'-'.$mes1.'-01'; // DATA INICIO
        $d2 = $ano2.'-'.$mes2.'-01'; // DATA FIM

        // Busca resultado do mês e ano informado
        $abc = $this->pdo->query('SELECT * FROM '.$this->maTable.' WHERE data >= "'.$d1.'" AND data < "'.$d2.'" ORDER BY data DESC');
        if($abc->rowCount() == 0) {
            return false;
        } else {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        }

    }

    function getRelatorioAno(int $ano)
    {
        $abc = $this->pdo->prepare('SELECT SUM(hora) as hora, SUM(horaldc) as horaldc, SUM(publicacao) as publicacao, SUM(videos) as videos, SUM(revisitas) as revisitas FROM '.$this->maTable.' WHERE data >= :d_ini AND data < :d_fim');
        $abc->bindValue(':d_ini', ($ano-1).'-09-01', PDO::PARAM_STR);
        $abc->bindValue(':d_fim', ($ano).'-09-01', PDO::PARAM_STR);
        $abc->execute();

        $ret = (array)$abc->fetch(PDO::FETCH_OBJ);

        foreach($ret as $key => $valor) {
            if($valor == null) {
                $ret[$key] = 0;
            }
        }

        $ret = (object) $ret;
        return json_encode($ret);

    }

    function setHora($r)
    {
        $data = new DateTime($r['dia']);
        if($r['hora'] == '') {$r['hora'] = '00:00';}
        if($r['horaldc'] == '') {$r['horaldc'] = '00:00';}


        $h = explode(':', $r['hora']);
        $hora = ((int)$h[0]*60) + (int)$h[1];

        $h = explode(':', $r['horaldc']);
        $horaldc = ((int)$h[0]*60) + (int)$h[1];

        $publicacao = (int)$r['publicacao'];
        $videos = (int)$r['videos'];
        $revisitas = (int)$r['revisitas'];
        $comentario = addslashes(substr($r['comentario'],0, 200));

        // Busca se há registro com essa data
        $abc = $this->pdo->prepare('SELECT * FROM '.$this->maTable.' WHERE data = :d');
        $abc->bindValue(':d', $data->format('Y-m-d'), PDO::PARAM_STR);
        $abc->execute();
        if($abc->rowCount() > 0) {
            // Atualiza
            $reg = $abc->fetch(PDO::FETCH_OBJ);

            if($hora == 0 && $horaldc == 0 && $publicacao == 0 && $videos == 0 && $revisitas == 0 && $comentario == '') {
                // Valores vazios. Apaga linha
                $abc = $this->pdo->prepare('DELETE FROM '.$this->maTable.' WHERE data = :d');
                $abc->bindValue(':d', $data->format('Y-m-d'), PDO::PARAM_STR);
                $abc->execute();
            } else {
                // Atualiza com novos valores
                $abc = $this->pdo->prepare('UPDATE '.$this->maTable.' SET
                hora = '.$hora.',
                horaldc = '.$horaldc.',
                publicacao = '.$publicacao.',
                videos = '.$videos.',
                revisitas = '.$revisitas.',
                comentario = "'.$comentario.'"
                WHERE data = :d');
                $abc->bindValue(':d', $data->format('Y-m-d'), PDO::PARAM_STR);
                $abc->execute();
            }
        } else {
            // Insere novo
            $abc = $this->pdo->query('INSERT INTO '.$this->maTable.' (data, hora, horaldc, publicacao, videos, revisitas, comentario) VALUES ( 
                "'.$data->format('Y-m-d').'", '.$hora.', '.$horaldc.', '.$publicacao.', '.$videos.', '.$revisitas.', "'.$comentario.'"
            )');
        }

        return true;
    }

}