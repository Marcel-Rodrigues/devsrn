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
$anuidadeModel = new Anuidade($conn);

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['gerar_manual'])) {
        $cobranca->setAssociadoId($_POST['associado_id']);
        $cobranca->setAnuidadeId($_POST['anuidade_id']);
        $cobranca->setDataVencimento($_POST['data_vencimento']);

        if ($cobranca->salvar()) {
            $msg = "<div class='alert alert-success'>Cobrança gerada com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao gerar cobrança (já existe?).</div>";
        }
    } elseif (isset($_POST['gerar_automatico'])) {
        $anoAnuidade = $_POST['ano_anuidade'];
        $dataVencimento = $_POST['data_vencimento_anuidade'];
        
        $anuidade = $anuidadeModel->buscarPorAno($anoAnuidade);

        

        if (!$anuidade) {
            $msg = "<div class='alert alert-danger'>Anuidade do ano selecionado não encontrada.</div>";
        } else {
            $anuidadeId = $anuidade['id'];
            $contador = 0;

            $associadosValidos = $cobranca->listarAssociadosParaAnuidade($anoAnuidade, $anuidadeId);

            foreach ($associadosValidos as $associado) {
                $valorBruto = $anuidade['valor'];

                $cobranca->setAssociadoId($associado['id']);
                $cobranca->setAnuidadeId($anuidadeId);
                $cobranca->setDataVencimento($dataVencimento);
                $cobranca->setValor((float)str_replace(',', '.', $valorBruto));

                if ($cobranca->salvar()) {
                    $contador++;
                }
            }

            if ($contador > 0) {
                $msg = "<div class='alert alert-success'>{$contador} anuidades geradas com sucesso!</div>";
            } else {
                $msg = "<div class='alert alert-warning'>Nenhuma anuidade foi gerada.</div>";
            }
        }
    }
}

$associados = $assoc->listarTodos();
$anuidades = $anuidadeModel->listarTodas();
?>

<div class="container mt-7 pt-5">
    <h2 class="mb-4">Gerar Cobrança</h2>
    <?= $msg ?>

    <div class="btn-group mb-3">
        <button type="button" class="btn btn-outline-primary active" id="btnManual" onclick="toggleModo('manual')">Gerar Manualmente</button>
        <button type="button" class="btn btn-outline-primary" id="btnAutomatico" onclick="toggleModo('automatico')">Gerar Para Todos Associados</button>
    </div>

    <!-- Opção Manual -->
    <form method="post" id="formManual">
        <input type="hidden" name="gerar_manual" value="1">
        <div class="row col-12 d-flex">
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

            <div class="col-3" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Gerar</button>
            </div>
        </div>
    </form>

    <!-- Opção Automática -->
    <form method="post" id="formAutomatico" style="display: none;">
        <input type="hidden" name="gerar_automatico" value="1">
        <div class="row">
            <div class="col-5">
                <label class="form-label">Ano da Anuidade</label>
                <select name="ano_anuidade" class="form-select" required>
                    <option value="">Selecione</option>
                    <?php foreach ($anuidades as $a): ?>
                        <option value="<?= $a['ano'] ?>"><?= $a['ano'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-4">
                <label class="form-label">Data de Vencimento</label>
                <input type="date" name="data_vencimento_anuidade" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-3" style="margin-top: 30px;">
                <button type="submit" class="btn btn-success">Gerar para Todos</button>
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

    function toggleModo(modo) {
        if (modo === 'manual') {
            document.getElementById('formManual').style.display = 'block';
            document.getElementById('formAutomatico').style.display = 'none';
            document.getElementById('btnManual').classList.add('active');
            document.getElementById('btnAutomatico').classList.remove('active');
        } else {
            document.getElementById('formManual').style.display = 'none';
            document.getElementById('formAutomatico').style.display = 'block';
            document.getElementById('btnManual').classList.remove('active');
            document.getElementById('btnAutomatico').classList.add('active');
        }
    }
</script>

<?php include '../footer.php'; ?>
