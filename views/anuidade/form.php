<?php
require_once '../../config/database.php';
require_once '../../classes/Anuidade.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$anuidade = new Anuidade($conn);
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ano = $_POST['ano'];
        $valor = $_POST['valor'];

        if ($anuidade->buscarPorAno($ano)) {
            $msg = "<div class='alert alert-warning'>Este ano já está cadastrado.</div>";
        } else {
            $anuidade->setAno($ano);
            $anuidade->setValor($valor);

            if ($anuidade->salvar()) {
                $msg = "<div class='alert alert-success'>Anuidade cadastrada com sucesso!</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Erro ao cadastrar anuidade.</div>";
            }
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-warning'>" . $e->getMessage() . "</div>";
    }
}

$lista = $anuidade->listarTodas();
?>

<div class="container mt-7 pt-5">

    <h2 class="mb-4">Cadastrar Anuidade</h2>

    <?= $msg ?>

    <form method="post">
        <div class="col-12 d-flex">
            <div class="col-4">
                <label class="form-label">Ano:</label>
                <input type="number" name="ano" class="form-control ano" required min="1900" max="2100">
            </div>

            <div class="col-5">
                <label class="form-label">Valor (R$):</label>
                <input type="text" name="valor" class="form-control moeda" required>
            </div>

            <div class="col-3" style="margin-top: 30px";>
                <button type="submit" class="btn btn-primary">Gerar Anuidade</button>
                <a href="../../index.php" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

    </form>

    <?php if (count($lista) > 0): ?>
        <hr>
        <h4 class="mt-4">Anuidades Cadastradas</h4>
        <table class="table table-bordered table-sm mt-2">
            <thead>
                <tr>
                    <th>Ano</th>
                    <th>Valor</th>
                    <th>Data de Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $a): ?>
                    <tr>
                        <td><?= $a['ano'] ?></td>
                        <td>R$ <?= number_format($a['valor'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($a['data_cadastro'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>