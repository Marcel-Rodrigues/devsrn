<?php
class Cobranca {
    private $associado_id;
    private $anuidade_id;
    private $data_vencimento;
    private $status = 0;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAssociadoId() { return $this->associado_id; }
    public function setAssociadoId($id) { $this->associado_id = (int) $id; }

    public function getAnuidadeId() { return $this->anuidade_id; }
    public function setAnuidadeId($id) { $this->anuidade_id = (int) $id; }

    public function getDataVencimento() { return $this->data_vencimento; }
    public function setDataVencimento($data) {
        $this->data_vencimento = date('Y-m-d', strtotime($data));
    }

    public function getStatus() { return $this->status; }
    public function setStatus($status) {
        $this->status = $status ? 1 : 0;
    }

    public function salvar() {
        $verifica = $this->conn->prepare("
            SELECT COUNT(*) FROM cobranca 
            WHERE associado_id = ? AND anuidade_id = ?
        ");
        $verifica->execute([$this->associado_id, $this->anuidade_id]);
        $existe = $verifica->fetchColumn();
    
        if ($existe > 0) {
            return false;
        }
    
        $sql = "INSERT INTO cobranca (associado_id, anuidade_id, data_vencimento, status)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $this->associado_id,
            $this->anuidade_id,
            $this->data_vencimento,
            $this->status
        ]);
    }
    

    public function listarPorAssociado($associado_id) {
        $sql = "SELECT c.*, a.ano, a.valor 
                FROM cobranca c
                JOIN anuidade a ON a.id = c.anuidade_id
                WHERE c.associado_id = ?
                ORDER BY a.ano DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$associado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoPaga() {
        $sql = "UPDATE cobranca 
                SET status = 1 
                WHERE associado_id = ? AND anuidade_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->associado_id, $this->anuidade_id]);
    }
}
