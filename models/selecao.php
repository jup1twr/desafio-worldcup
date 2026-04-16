<?php 
   class Selecao {
    // public = qualquer um pode acessar.
    // private = apenas a própria classe acessa

    private $conn;
    private $table_name = 'selecoes';

    public $id_selecao;
    public $nome_selecao;
    public $grupo_selecao;
    public $titulos_mundiais;
    public $criado_em; 

    // criando um novo objeto
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo normal, inserir na tabela
    public function create(){
        $query = "INSERT INTO " . $this->table_name . "
          SET nome_selecao = :nome_selecao, 
              grupo_selecao = :grupo_selecao, 
              titulos_mundiais = :titulos_mundiais, 
              criado_em = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->nome_selecao = htmlspecialchars(strip_tags($this->nome_selecao));
        $this->grupo_selecao = htmlspecialchars(strip_tags($this->grupo_selecao));
        $this->titulos_mundiais = htmlspecialchars(strip_tags($this->titulos_mundiais));

        $stmt->bindParam(':nome_selecao', $this->nome_selecao);
        $stmt->bindParam(':grupo_selecao', $this->grupo_selecao);
        $stmt->bindParam(':titulos_mundiais', $this->titulos_mundiais);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read () {
        $query = "SELECT ...";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_selecao = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_selecao = $row['id_selecao'];
            $this->nome_selecao = $row['nome_selecao'];
            $this->grupo_selecao = $row['grupo_selecao'];
            $this->titulos_mundiais = $row['titulos_mundiais'];
            $this->criado_em = $row['criado_em'];
            return true;
        }
        return false;
    }
        public function update($id_selecao) {
            $query = "UPDATE " . $this->table_name . " 
                SET nome_selecao = :nome_selecao, 
                    grupo_selecao = :grupo_selecao, 
                    titulos_mundiais = :titulos_mundiais 
                WHERE id_selecao = :id";

            $stmt = $this->conn->prepare($query);

        $this->nome_selecao = htmlspecialchars(strip_tags($this->nome_selecao));
        $this->grupo_selecao = htmlspecialchars(strip_tags($this->grupo_selecao));
        $this->titulos_mundiais = htmlspecialchars(strip_tags($this->titulos_mundiais));
        
        $stmt->bindParam(":nome_selecao", $this->nome_selecao);
        $stmt->bindParam(":grupo_selecao", $this->grupo_selecao);
        $stmt->bindParam(":titulos_mundiais", $this->titulos_mundiais);
        $stmt->bindParam(":id", $id_selecao);
        
        return $stmt->execute();
    }

        public function delete($id) {

        $query = "DELETE FROM " . $this->table_name . " WHERE id_selecao = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
        }
    }
?>