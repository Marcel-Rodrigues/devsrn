<?php
class Associado
{
    private $id;
    private $nome;
    private $email;
    private $cpf;
    private $data_filiacao;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($nome)
    {
        $this->nome = trim($nome);
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = trim($email);
    }

    public function getCpf()
    {
        return $this->cpf;
    }
    public function setCpf($cpf)
    {
        $this->cpf = preg_replace('/\D/', '', $cpf);
    }

    public function getDataFiliacao()
    {
        return $this->data_filiacao;
    }
    public function setDataFiliacao($data)
    {
        $dataFormatada = date('Y-m-d', strtotime($data));
        $this->data_filiacao = $dataFormatada;
    }

    public function salvar()
    {
        $sql = "INSERT INTO associado (nome, email, cpf, data_filiacao) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $sucesso = $stmt->execute([
            $this->nome,
            $this->email,
            $this->cpf,
            $this->data_filiacao
        ]);

        if ($sucesso) {
            $idAssociado = $this->conn->lastInsertId();
            $anoFiliacao = date('Y', strtotime($this->data_filiacao));
            $this->criarCobrancas($idAssociado, $anoFiliacao);
        }

        return $sucesso;
    }

    public function atualizar($id)
    {
        $sql = "UPDATE associado SET nome = ?, email = ?, cpf = ?, data_filiacao = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $this->nome,
            $this->email,
            $this->cpf,
            $this->data_filiacao,
            $id
        ]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos($filtro = '', $ordenacao = 'nome ASC')
    {
        $sql = "SELECT * FROM associado WHERE 1";
        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE :filtro OR email LIKE :filtro OR cpf LIKE :filtro)";
        }
        $sql .= " ORDER BY " . $ordenacao;

        $stmt = $this->conn->prepare($sql);

        if (!empty($filtro)) {
            $stmt->bindValue(':filtro', "%$filtro%", PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCpf($cpf)
    {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function qtdTotal()
    {
        return $this->conn->query("SELECT COUNT(*) FROM associado")->fetchColumn();
    }

    private function criarCobrancas($idAssociado, $anoFiliacao)
    {
        $sql = "SELECT id, ano, valor FROM anuidade WHERE ano >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$anoFiliacao]);
        $anuidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlInsert = "INSERT INTO cobranca (associado_id, anuidade_id, valor, data_vencimento, status) VALUES (?, ?, ?, ?, 0)";
        $stmtInsert = $this->conn->prepare($sqlInsert);

        foreach ($anuidades as $anuidade) {
            $vencimento = $anuidade['ano'] . '-12-31';
            $stmtInsert->execute([
                $idAssociado,
                $anuidade['id'],
                $anuidade['valor'],
                date('Y-m-d', strtotime($vencimento)),
            ]);
        }
    }
}
