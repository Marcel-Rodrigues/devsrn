<?php
class Anuidade
{
    private $id;
    private $ano;
    private $valor;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAno()
    {
        return $this->ano;
    }
    public function setAno($ano)
    {
        $ano = (int) $ano;
        if ($ano < 1900 || $ano > 2100) {
            throw new Exception("Ano inválido.");
        }
        $this->ano = $ano;
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


    public function salvar()
    {

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

    public function buscarPorAno($ano)
    {
        $stmt = $this->conn->prepare("SELECT * FROM anuidade WHERE ano = ?");
        $stmt->execute([$ano]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodas()
    {
        $stmt = $this->conn->prepare("SELECT * FROM anuidade ORDER BY ano DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarNaoCobradas($associadoId, $dataFiliacao)
    {
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

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM anuidade WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id)
    {
        $stmt = $this->conn->prepare("UPDATE anuidade SET valor = ? WHERE id = ?");
        return $stmt->execute([$this->valor, $id]);
    }

    public function contarTodos()
    {
        $sql = "SELECT COUNT(*) as total FROM cobranca";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function contarPendentes()
    {
        $sql = "SELECT COUNT(*) as pendentes FROM cobranca WHERE status = 0";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['pendentes'] ?? 0;
    }

    public function calcularValores()
    {
        $sql = "SELECT 
                    SUM(CASE WHEN status = 0 THEN valor ELSE 0 END) AS pendente,
                    SUM(CASE WHEN status = 1 THEN valor ELSE 0 END) AS arrecadado
                FROM cobranca";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['pendente' => 0, 'arrecadado' => 0];
    }

    public function obterResumoPorAno($ano) {
        $sql = "SELECT 
                    COUNT(c.associado_id) AS total_associados,
                    SUM(CASE WHEN c.status = 0 THEN 1 ELSE 0 END) AS pendentes,
                    SUM(CASE WHEN c.status = 0 THEN c.valor ELSE 0 END) AS valor_pendente,
                    SUM(CASE WHEN c.status = 1 THEN c.valor ELSE 0 END) AS arrecadado
                FROM anuidade a
                LEFT JOIN cobranca c ON a.id = c.anuidade_id
                WHERE a.ano = :ano";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_associados' => 0, 
            'pendentes' => 0, 
            'valor_pendente' => 0, 
            'arrecadado' => 0
        ];
    }
}
