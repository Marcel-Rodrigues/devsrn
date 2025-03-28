<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$assoc = new Associado($conn);
$msg = "";

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID do associado não informado.</div>";
    include '../footer.php';
    exit;
}

$id = $_GET['id'];
$dados = $assoc->buscarPorId($id);

if (!$dados) {
    echo "<div class='alert alert-danger'>Associado não encontrado.</div>";
    include '../footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assoc->setNome($_POST['nome']);
    $assoc->setEmail($_POST['email']);
    $assoc->setCpf($_POST['cpf']);
    $assoc->setDataFiliacao($_POST['data_filiacao']);

    if ($assoc->atualizar($id)) {
        $msg = "<div class='alert alert-success'>Associado atualizado com sucesso!</div>";
        $dados = $assoc->buscarPorId($id);
    } else {
        $msg = "<div class='alert alert-danger'>Erro ao atualizar associado.</div>";
    }
}
?>
<div class="container mt-7 pt-5">

    <h2 class="mb-4">Editar Associado</h2>

    <?= $msg ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($dados['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="<?= $dados['email'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" class="form-control cpf" value="<?= $dados['cpf'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de Filiação</label>
            <input type="date" name="data_filiacao" class="form-control" value="<?= $dados['data_filiacao'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="lista.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php include '../footer.php'; ?>