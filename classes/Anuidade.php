<?php
class Anuidade {
    private $id;
    private $ano;
    private $valor;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() { return $this->id; }

    public function getAno() { return $this->ano; }
    public function setAno($ano) {
        $ano = (int) $ano;
        if ($ano < 1900 || $ano > 2100) {
            throw new Exception("Ano inválido.");
        }
        $this->ano = $ano;
    }

    public function getValor() { return $this->valor; }
    public function setValor($valor) {
        $valor = str_replace(['R$', '.', ' '], '', $valor);
        $valor = str_replace(',', '.', $valor);
        $this->valor = number_format((float)$valor, 2, '.', '');
    }
    

    public function salvar() {

        $verifica = $this->conn->prepare("SELECT COUNT(*) FROM anuidade WHERE ano = ?");
        $verifica->execute([$this->ano]);
        $existe = $verifica->fetchColumn();
    
        if ($existe > 0) {
            return false; 
        }
    
        $sql = "INSERT INTO anuidade (ano, valor) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->ano, $this->valor]);
    }

    public function buscarPorAno($ano) {
        $stmt = $this->conn->prepare("SELECT * FROM anuidade WHERE ano = ?");
        $stmt->execute([$ano]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodas() {
        $stmt = $this->conn->prepare("SELECT * FROM anuidade ORDER BY ano DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarNaoCobradas($associadoId, $dataFiliacao) {
        $query = "
            SELECT a.id, a.ano, a.valor
            FROM anuidade a
            LEFT JOIN cobranca c ON a.id = c.anuidade_id AND c.associado_id = :associado_id
            WHERE c.anuidade_id IS NULL 
            AND a.ano >= YEAR(:data_filiacao)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':associado_id', $associadoId);
        $stmt->bindParam(':data_filiacao', $dataFiliacao);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
