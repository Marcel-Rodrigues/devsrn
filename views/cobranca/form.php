<?php
require_once '../../config/database.php';
require_once '../../classes/Cobranca.php';
require_once '../../classes/Associado.php';
require_once '../../classes/Anuidade.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$assoc = new Associado($conn);
$anuidade = new Anuidade($conn);
$cobranca = new Cobranca($conn);

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cobranca->setAssociadoId($_POST['associado_id']);
    $cobranca->setAnuidadeId($_POST['anuidade_id']);
    $cobranca->setDataVencimento($_POST['data_vencimento']);

    if ($cobranca->salvar()) {
        $msg = "<div class='alert alert-success'>Cobrança gerada com sucesso!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Erro ao gerar cobrança (já existe?).</div>";
    }
}

$associados = $assoc->listarTodos();
$anuidades = $anuidade->listarTodas();
?>

<div class="container mt-7 pt-5">

    <h2 class="mb-4">Gerar Cobrança</h2>

    <?= $msg ?>

    <form method="post">
        <div class="col-12 d-flex">
            <div class="col-5">
                <label class="form-label">Associado</label>
                <select name="associado_id" class="form-select" required onchange="carregarAnuidades()">
                    <option value="">Selecione</option>
                    <?php foreach ($associados as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nome']) ?> (<?= $a['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-2">
                <label class="form-label">Anuidade</label>
                <select name="anuidade_id" class="form-select" required>
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="col-2">
                <label class="form-label">Data de Vencimento</label>
                <input type="date" name="data_vencimento" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-3" style="margin-top: 30px" ;>
                <button type="submit" class="btn btn-primary">Gerar Anuidade</button>
                <a href="../../index.php" class="btn btn-secondary">Voltar</a>
            </div>
        </div>


    </form>

</div>

<script>
    function carregarAnuidades() {
        const associadoId = document.querySelector('select[name="associado_id"]').value;

        if (associadoId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'carregar_anuidades.php?associado_id=' + associadoId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.querySelector('select[name="anuidade_id"]').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            document.querySelector('select[name="anuidade_id"]').innerHTML = '<option value="">Selecione</option>';
        }
    }
</script>

<?php include '../footer.php'; ?>