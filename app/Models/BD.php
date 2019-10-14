<?php
include_once('Model.php');

class BD extends Model {
    protected $backupFolder = __DIR__.'../../../backup';
    private $automatico = FALSE;
    protected $prefixTemp;
    private $erros = array();

    function __construct()
    {
        parent::__construct();
        if(!file_exists($this->backupFolder)) {
            mkdir($this->backupFolder);
        }
    }

    function get(string $nomeVariavel)
    {
        return $this->$nomeVariavel;
    }

    function getFiles()
    {
        $arquivos = scandir($this->backupFolder,1);
        foreach($arquivos as $key => $value) {
            if($value == '.' || $value == '..') {
                unset($arquivos[$key]);
            }
        }
        return $arquivos;
    }

    function getUltimoBackup()
    {
        // Ultimo backup manual
        $arquivos = scandir($this->backupFolder,1);
        $file = array();
        foreach($arquivos as $key => $value) {
            if($value != '.' && $value != '..' && substr($value, 0, 10) != 'AUTOMATIC-' && substr($value, -4, 4) == '.sql') {
                $file[$value] = filemtime($this->backupFolder.'/'.$value);
            }
        }
        arsort($file);
        
        $nome = '';

        foreach($file as $key => $value) {
            $nome = $key;
            break;
        }
        $nome = substr($nome, 0, -4);

        return $nome;
    }

    function deleteFile($nome)
    {
        if(substr($nome, -4) != '.sql') {
            return 'Esse não é um arquivo de backup válido. Somente arquivos com extensão .sql podem ser removidos.';
        }

        if(!file_exists($this->backupFolder.'/'. $nome)) {
            return 'Arquivo não encontrado! Atualize a página.';
        }

        $res = unlink($this->backupFolder.'/'. $nome);
        if($res === TRUE) {
            SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Arquivo <i>'.$nome.'</i> removido com sucesso.', 'tipo' => 'success'));
            
            $log = new LOG();
            $log->novo(LOG::TIPO_REMOVE, 'Arquivo de backup <i>'.$nome.'</i> removido.');
            return 'OK';
        } else {
            return 'A remoção do arquivo falhou...';
        }
    }

    function backupAutomatico()
    {
        $this->$automatico = TRUE;
        return $this->backup();
    }

    function backup()
    {
        if($this->automatico === TRUE) {
            // Verifica existência de arquivos de backup automático e etc...
        }

        // INICIA EXPORTAÇÃO
        $data_sql = new DateTime();
        $data_sql_string = $data_sql->format('d-M-Y');

        // Inicia o arquivo com comentários

        $sql = '
-- BACKUP DO BANCO DE DADOS
-- NOME DO BANCO: '.$this->banco.'
-- DATA DO BACKUP: '.$data_sql_string.'
        
-- ATENÇÃO: ESTE BACKUP SÓ FUNCIONA NA RESTAURAÇÃO PELO SISTEMA. ELE NÃO FUNCIONA AO USAR SGBD, COMO PHPMYADMIN, POIS O SCRIPT POSSUI UMA CODIFICAÇÃO PARTICULAR.
        
';

        $abc = $this->pdo->query('SHOW TABLES');
        $tabelas = array();
        while($reg = $abc->fetch(PDO::FETCH_NUM)) {
            array_push($tabelas, $reg[0]);
        }

        foreach($tabelas as $t) {
            $abc = $this->pdo->query('SHOW CREATE TABLE `'.$t.'`');
            $reg = $abc->fetch(PDO::FETCH_NUM);
            $sql .= '

-- CRIAÇÃO DA TABELA '.$t.'
            '.$reg[1].';';
        }

        $sql.='



    -- ##################### DADOS DAS TABELAS #####################
        ';

        // Escreve conteudo das tabelas
        foreach($tabelas as $x) {
            $abc = $this->pdo->query('SELECT * FROM `'.$x.'` WHERE 1');
            if($abc->rowCount() > 0) { // Se tiver dado dentro da tabela
                // Busca nome de todas as colunas
                $abc = $this->pdo->query('SHOW COLUMNS FROM `'.$x.'`');
                // Inicia sintaxe do INSERT
                $sql.='
                
                
INSERT INTO `'.$x.'` (';
                
                // Laço para escrever cada nome de coluna
                while($reg = $abc->fetch(PDO::FETCH_NUM)) {
                    $sql.='`'.$reg[0].'`, ';
                }
                
                // Remove última virgula da string e continua escrevendo valores
                $sql = substr($sql, 0, -2);
                $sql.=') VALUES
    ';
                
                // adapta query para tabelas do Assistente do Ministério
                if(substr($x, 0, 3) == 'ma_') {
                    $abc = $this->pdo->query('SELECT * FROM `'.$x.'` WHERE 1');
                } else {
                    $abc = $this->pdo->query('SELECT * FROM `'.$x.'` WHERE 1 ORDER BY id ASC');
                }
                // Executa laço para ler cada linha da tabela do banco de dados
                while($reg = $abc->fetch(PDO::FETCH_NUM)) {
                    // Inicia linha
                    $sql.='(';
                    foreach($reg as $y) {
                        // Verifica se o campo é numerico ou string
                        if(is_numeric($y) == TRUE) {
                            // Se for numero, escreve sem aspas
                            $sql.= $y.',';
                        } else{
                            // Se for string, escreve com aspas
                            // Remove pontos e vírgulas, para não causar problema na restauração e adiciona barras invertidas.
                            // Substitui ';' por '|'
                            $sql .= "'".str_replace(';', '|', addslashes($y))."',";
                        }
                    }
                    
                    // Remove ultima vigula e fecha a linha com parenteses
                    $sql = substr($sql, 0, -1) . '),
    ';
                }
                
                // Ao término da tabela, remove ultima virgula e adiciona um ponto e virgula.
                $sql = substr($sql, 0,strrpos($sql,',')) . ';';
            }
        }

        /**
         * SALVA O ARQUIVO
         */

        if($this->automatico === TRUE) {
            $nome = 'AUTOMATIC-'.$this->banco;
            $logStr = 'backup automático realizado.';
        } else {
            $nome = $this->banco;
            $logStr = 'backup manual realizado.';
        }
        $arqNome = $nome .'--'. $data_sql->format('Y-m-d_H-i').'.sql';

        // Verifica se o arquivo existe
        $handler = fopen($this->backupFolder.'/'.$arqNome, 'w');
        fwrite($handler, $sql);
        fclose($handler);

        /**
         * REGISTRA EVENTO NO LOG.
         */

        $log = new LOG();
        $log->novo(LOG::TIPO_SISTEMA, $logStr);
        
    }

    function restaura($arqNome)
    {
        // Procura arquivo
        if(!file_exists($this->backupFolder.'/'.$arqNome)) {
            return 'ARQUIVO NÃO ENCONTRADO! Abortado.';
        }


        // Gera prefixo de tabela.
        $this->prefixTemp = strtolower($this->randChar(5, TRUE));
        // Lista tabelas em um array
        $tabelas = array();
        $abc = $this->pdo->query('SHOW TABLES');
        while($reg = $abc->fetch(PDO::FETCH_NUM)) {
            array_push($tabelas, $reg[0]);
        }

        
        // Altera o nome das tabelas, usando o prefixo, como backup.
        foreach($tabelas as $tab) {
            try {
                $abc = $this->pdo->query('RENAME TABLE '.$tab.' TO '.$this->prefixTemp.'_'.$tab.';');
            } catch(PDOException $e) {
                array_push($this->erros, $e);
            }
        }
        

        // Checa se houve erros na mudança de nome
        if(count($this->erros) > 0) {
            $x = 'FALHA GRAVE: Consulte desenvolvedor.'."\n\n";
            foreach($this->erros as $y) {
                $x.= 'Erro: '.$y->getMessage()."\n";
            }

            return $x;
        }
        

        // Lê conteúdo do backup
        $arqConteudo = file_get_contents($this->backupFolder.'/'.$arqNome);

        // Separa as querys no arquivo.
        $sql = explode(';',$arqConteudo);
        // Executa as querys de restauração do banco de dados
        foreach($sql as $s) {
            try {
                $abc = $this->pdo->query($s);
            } catch(PDOException $e) {
                array_push($this->erros, $e);
            }
        }
        
        /** 
         * Checa se houve erros nas querys 
         */
        if(count($this->erros) > 0) {
            $x = 'Houve erros na restauração:'."\n\n";
            foreach($this->erros as $y) {
                $x.= 'Erro: '.$y->getMessage()."\n";
            }

            $x .= "\n".'Desfazendo alterações.';

            // DESFAZ AS ALTERAÇÕES (remove as tabelas e renomeia as tabelas prefixadas)
            // Exclui as tabelas novas
            foreach($tabelas as $t) {
                $abc = $this->pdo->query('DROP TABLE '.$t);
            }

            // Renomeia as tabelas prefixadas
            foreach($tabelas as $tab) {
                try {
                    $abc = $this->pdo->query('RENAME TABLE '.$this->prefixTemp.'_'.$tab.' TO '.$tab.';');
                } catch(PDOException $e) {
                    array_push($this->erros, $e);
                }
            }
        
            return $x;
        }

        // Verifica se todas as tabelas foram lançadas
        $novas = array();
        $abc = $this->pdo->query('SHOW TABLES');
        while($reg = $abc->fetch(PDO::FETCH_NUM)) {
            array_push($novas, $reg[0]);
        }
        
        if(count($novas) < count($tabelas)) {
            // Caso a quantidade de tabelas novas seja menor que a quantidade existente,
            // identifica as tabelas faltantes e faz uma cópia.
            $diff = array_diff($tabelas, $novas);
            foreach($diff as $t) {
                $abc = $this->pdo->query('CREATE TABLE '.$t.' SELECT * FROM '.$this->prefixTemp.'_'.$t.';');
            }

        }

        // Exclui as tabelas de backup
        foreach($tabelas as $t) {
            $abc = $this->pdo->query('DROP TABLE '.$this->prefixTemp.'_'.$t);
        }

        SessionMessage::novo(array('titulo' => 'Sucesso!', 'texto' => 'Backup do banco de dados restaurado com sucesso.', 'tipo' => 'success'));
        $log = new LOG();
        $log->novo(LOG::TIPO_SISTEMA, 'Backup do banco de dados restaurado com sucesso.');
        return 'OK';
    }


    function teste() {
    }
}