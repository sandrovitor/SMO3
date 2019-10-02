<?php
include_once('Model.php');

class Bairro extends Model {
    protected $pdo;


    function __construct()
    {
        parent::__construct();
    }

    
    function listaBairro()
    {
        $abc = $this->pdo->query('SELECT ter.id, ter.bairro, ter.regiao as regiao_numero, (SELECT config.valor FROM config WHERE config.opcao = CONCAT("regiao_", regiao_numero)) as regiao_nome FROM ter WHERE 1 ORDER BY ter.regiao ASC, ter.bairro ASC');
        if($abc->rowCount() > 0) {
            return $abc->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    function novo(string $nome, int $regiao)
    {
        // Checa se bairro já existe nessa região.
        $abc = $this->pdo->prepare('SELECT * FROM ter WHERE bairro = :b AND regiao = :r');
        $abc->bindValue(':b', $nome, PDO::PARAM_STR);
        $abc->bindValue(':r', $regiao, PDO::PARAM_INT);
        $abc->execute();


        if($abc->rowCount() > 0) {
            return 'Bairro já existe nessa região.';
        } else {
            try {
                // Insere bairro no BD
                $abc = $this->pdo->prepare('INSERT INTO `ter` (id, bairro, regiao) VALUES (null, :b, :r)');
                $abc->bindValue(':b', $nome, PDO::PARAM_STR);
                $abc->bindValue(':r', $regiao, PDO::PARAM_INT);
                $abc->execute();
                
                return true;
            } catch(PDOException $e) {
                return $e->getMessage();
            }
        }

    }

    function edita(int $bairroId, string $nome, int $regiao)
    {
        // Checa se bairro ainda existe.
        $abc = $this->pdo->prepare('SELECT * FROM ter WHERE id = :id');
        $abc->bindValue(':id', $bairroId, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() > 0) {
            try {
                // Faz a atualização
                $abc = $this->pdo->prepare('UPDATE `ter` SET `bairro` = :b, `regiao` = :r WHERE `id` = :id');
                $abc->bindValue(':b', $nome, PDO::PARAM_STR);
                $abc->bindValue(':r', $regiao, PDO::PARAM_INT);
                $abc->bindValue(':id', $bairroId, PDO::PARAM_INT);
                $abc->execute();

                return true;
            } catch(PDOException $e) {
                return $e->getMessage();
            }

            return true;
        } else {
            return 'Esse bairro não existe. Por favor, atualize a página.';
        }
    }

    function remove(int $bairroId)
    {
        // Checa se bairro ainda existe.
        $abc = $this->pdo->prepare('SELECT * FROM ter WHERE id = :id');
        $abc->bindValue(':id', $bairroId, PDO::PARAM_INT);
        $abc->execute();

        if($abc->rowCount() > 0) {
            try {
                // REMOVE o bairro
                $abc = $this->pdo->prepare('DELETE FROM `ter` WHERE `id` = :id');
                $abc->bindValue(':id', $bairroId, PDO::PARAM_INT);
                $abc->execute();

                return true;
            } catch(PDOException $e) {
                return $e->getMessage();
            }

            return true;
        } else {
            return 'Esse bairro não existe. Por favor, atualize a página.';
        }
    }
}