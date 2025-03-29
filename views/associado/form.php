<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$associado = new Associado($conn);
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $email = $_POST['email'];
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);

        $validaEmail = false;

        if ($associado->buscarPorEmail($email) && $associado->buscarPorCpf($cpf)) {
            $msg = "<div class='alert alert-warning'>Estes email e cpf já estão cadastrados.</div>";
        } else if ($associado->buscarPorEmail($email)) {
            $msg = "<div class='alert alert-warning'>Este email já está cadastrado.</div>";
        } else if ($associado->buscarPorCpf($cpf)) {
            $msg = "<div class='alert alert-warning'>Este cpf já está cadastrado.</div>";
        } else {

            $associado->setNome($_POST['nome']);
            $associado->setEmail($_POST['email']);
            $associado->setCpf($_POST['cpf']);
            $associado->setDataFiliacao($_POST['data_filiacao']);

            if ($associado->salvar()) {
                $msg = "<div class='alert alert-success'>Associado cadastrado com sucesso!</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Erro ao cadastrar associado.</div>";
            }
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-warning'>" . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-7 pt-5">

    <h2 class="mb-4">Cadastrar Associado</h2>

    <?= $msg ?>

    <form method="post">
        <div class="col-12 d-flex">
            <div class="col-6">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="col-2">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" autocomplete="off" class="form-control" required>
            </div>

            <div class="col-2">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" autocomplete="off" class="form-control cpf" required>
            </div>

            <div class="col-2">
                <label class="form-label">Data de Filiação</label>
                <input type="date" name="data_filiacao" class="form-control" required>
            </div>

        </div>

        <div class="col-12" ;>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="../../index.php" class="btn btn-secondary">Voltar</a>
        </div>


    </form>

</div>

<?php include '../footer.php'; ?>