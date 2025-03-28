<?php
require_once '../../config/database.php';
require_once '../../classes/Anuidade.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$anuidade = new Anuidade($conn);
$msg = "";

$editarId = $_GET['editar'] ?? null;
$anuidadeEditar = null;

if ($editarId) {
    $anuidadeEditar = $anuidade->buscarPorId($editarId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ano = $_POST['ano'];
        $valor = $_POST['valor'];

        $anuidade->setAno($ano);
        $anuidade->setValor($valor);

        if (isset($_POST['editar_id'])) {
            if ($anuidade->atualizar($_POST['editar_id'])) {
                // $msg = "<div class='alert alert-success'>Anuidade atualizada com sucesso!</div>";
                header("Location: form.php");
                exit;
            } else {
                $msg = "<div class='alert alert-danger'>Erro ao atualizar anuidade.</div>";
            }
        } else {
            if ($anuidade->buscarPorAno($ano)) {
                $msg = "<div class='alert alert-warning'>Este ano já está cadastrado.</div>";
            } elseif ($anuidade->salvar()) {
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
                <input type="number" name="ano" class="form-control ano" required min="1900" max="2100"
                    value="<?= $anuidadeEditar['ano'] ?? '' ?>" <?= $anuidadeEditar ? 'readonly' : '' ?>>
            </div>

            <div class="col-5">
                <label class="form-label">Valor (R$):</label>
                <input type="text" name="valor" class="form-control moeda" required
                    value="<?= isset($anuidadeEditar['valor']) ? number_format($anuidadeEditar['valor'], 2, ',', '.') : '' ?>">
            </div>

            <div class="col-3" style="margin-top: 30px;">
                <?php if ($anuidadeEditar): ?>
                    <input type="hidden" name="editar_id" value="<?= $anuidadeEditar['id'] ?>">
                    <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    <a href="form.php" class="btn btn-secondary">Cancelar</a>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Gerar Anuidade</button>
                    <a href="../../index.php" class="btn btn-secondary">Voltar</a>
                <?php endif; ?>
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
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $a): ?>
                    <tr>
                        <td><?= $a['ano'] ?></td>
                        <td>R$ <?= number_format($a['valor'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($a['data_cadastro'])) ?></td>
                        <td>
                            <a href="form.php?editar=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>