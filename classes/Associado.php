<?php
class Associado {
    private $id;
    private $nome;
    private $email;
    private $cpf;
    private $data_filiacao;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() { return $this->id; }

    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = trim($nome); }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = trim($email); }

    public function getCpf() { return $this->cpf; }
    public function setCpf($cpf) {
        $this->cpf = preg_replace('/\D/', '', $cpf);
    }

    public function getDataFiliacao() { return $this->data_filiacao; }
    public function setDataFiliacao($data) {
        $dataFormatada = date('Y-m-d', strtotime($data));
        $this->data_filiacao = $dataFormatada;
    }

    public function salvar() {
        $sql = "INSERT INTO associado (nome, email, cpf, data_filiacao) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $this->nome,
            $this->email,
            $this->cpf,
            $this->data_filiacao
        ]);
    }

    public function atualizar($id) {
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

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM associado ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCpf($cpf) {
        $stmt = $this->conn->prepare("SELECT * FROM associado WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
