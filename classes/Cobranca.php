<?php
class Cobranca
{
    private $associado_id;
    private $anuidade_id;
    private $valor;
    private $data_vencimento;
    private $data_pagamento;
    private $status = 0;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAssociadoId()
    {
        return $this->associado_id;
    }
    public function setAssociadoId($id)
    {
        $this->associado_id = (int) $id;
    }

    public function getAnuidadeId()
    {
        return $this->anuidade_id;
    }
    public function setAnuidadeId($id)
    {
        $this->anuidade_id = (int) $id;
    }

    public function getValor()
    {
        return $this->valor;
    }
    public function setValor($valor)
    {
        $valor = str_replace(['R$', '.', ' '], '', $valor);
        $valor = str_replace(',', '.', $valor);
        $this->valor = number_format((float)$valor, 2, '.', '');
    }

    public function getDataVencimento()
    {
        return $this->data_vencimento;
    }
    public function setDataVencimento($data)
    {
        $this->data_vencimento = date('Y-m-d', strtotime($data));
    }

    public function getDataPagamento()
    {
        return $this->data_pagamento;
    }
    public function setDataPagamento($data)
    {
        $this->data_pagamento = date('Y-m-d H:i:s', strtotime($data));
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status ? 1 : 0;
    }

    public function salvar()
    {
        $verifica = $this->conn->prepare("
            SELECT COUNT(*) FROM cobranca 
            WHERE associado_id = ? AND anuidade_id = ?
        ");
        $verifica->execute([$this->associado_id, $this->anuidade_id]);
        if ($verifica->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO cobranca (associado_id, anuidade_id, valor, data_vencimento, status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $this->associado_id,
            $this->anuidade_id,
            $this->valor,
            $this->data_vencimento,
            $this->status
        ]);
    }


    public function listarPorAssociado($associado_id)
    {
        $sql = "SELECT c.*, a.ano 
                FROM cobranca c
                JOIN anuidade a ON a.id = c.anuidade_id
                WHERE c.associado_id = ?
                ORDER BY a.ano DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$associado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoPaga()
    {
        $sql = "UPDATE cobranca 
                SET status = 1, data_pagamento = NOW()
                WHERE associado_id = ? AND anuidade_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->associado_id, $this->anuidade_id]);
    }

    public function excluir()
    {
        $sql = "DELETE FROM cobranca 
            WHERE associado_id = ? AND anuidade_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->associado_id, $this->anuidade_id]);
    }

    public function listarAssociadosParaAnuidade($anoAnuidade, $idAnuidade)
    {
        $dataMinima = $anoAnuidade . '-12-31';

        $query = "SELECT a.*
                  FROM associado a
                  LEFT JOIN cobranca c ON a.id = c.associado_id AND c.anuidade_id = :idAnuidade
                  WHERE c.anuidade_id IS NULL AND data_filiacao <= :data_filiacao_minima";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_filiacao_minima', $dataMinima);
        $stmt->bindParam(':idAnuidade', $idAnuidade);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function qtdPendente() {
        $sql = "SELECT COUNT(*) FROM cobranca 
                WHERE status = 0";
        return $this->conn->query($sql)->fetchColumn();
    }

    public function qtdVencidas() {
        $sql = "SELECT COUNT(*) FROM cobranca 
                WHERE status = 0 AND data_vencimento < CURDATE()";
        return $this->conn->query($sql)->fetchColumn();
    }

    public function qtdPagas() {
        $sql = "SELECT COUNT(*) FROM cobranca 
                WHERE status = 1";
        return $this->conn->query($sql)->fetchColumn();
    }
    
    public function totalArrecadado() {
        $sql = "SELECT SUM(valor) FROM cobranca WHERE status = 1";
        return $this->conn->query($sql)->fetchColumn() ?: 0;
    }
    
    public function totalPendente() {
        $sql = "SELECT SUM(valor) FROM cobranca WHERE status = 0";
        return $this->conn->query($sql)->fetchColumn() ?: 0;
    }
    
    public function pagamentosPorAno() {
        $sql = "SELECT a.ano, SUM(c.valor) as total 
                FROM cobranca c
                JOIN anuidade a ON c.anuidade_id = a.id
                WHERE c.status = 1
                GROUP BY a.ano
                ORDER By a.ano ASC"    ;
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
