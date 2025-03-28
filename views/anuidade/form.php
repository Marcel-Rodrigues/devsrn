<?php
require_once '../../config/database.php';
require_once '../../classes/Anuidade.php';
require_once '../../classes/Cobranca.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$anuidade = new Anuidade($conn);
$cobranca = new Cobranca($conn);
$msg = "";

$editarId = $_GET['editar'] ?? null;
$anuidadeEditar = null;

if ($editarId) {
    $anuidadeEditar = $anuidade->buscarPorId($editarId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ano = $_POST['ano'] ?? null;
        $valor = $_POST['valor'] ?? null;
        $dataVencimento = $_POST['data_vencimento_anuidade'] ?? null;

        if (empty($ano) || $valor <= 0) {
            throw new Exception("Preencha todos os campos corretamente.");
        }

        $anuidade->setAno($ano);
        $anuidade->setValor($valor);

        if (isset($_POST['gerar_automatico'])) {

            if ($anuidade->buscarPorAno($ano)) {
                $msg = "<div class='alert alert-warning'>Este ano já está cadastrado.</div>";
            } else {

                if ($anuidade->salvar()) {

                    $anuidadeModel = $anuidade->buscarPorAno($ano);

                    $associadosValidos = $cobranca->listarAssociadosParaAnuidade($ano, $anuidadeModel['id']);
                    $contador = 0;

                    foreach ($associadosValidos as $associado) {
                        $cobranca->setAssociadoId($associado['id']);
                        $cobranca->setAnuidadeId($anuidadeModel['id']);
                        $cobranca->setDataVencimento($dataVencimento);
                        $cobranca->setValor($valor);

                        if (!$cobranca->salvar()) {
                            throw new Exception("Erro ao gerar cobrança para associado ID {$associado['id']}");
                        }
                        $contador++;
                    }

                    $msg = "<div class='alert alert-success'>Anuidade e {$contador} cobranças geradas com sucesso!</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Erro ao cadastrar anuidade.</div>";
                }
            }
        } else {
            if (isset($_POST['editar_id'])) {
                if ($anuidade->atualizar($_POST['editar_id'])) {
                    header("Location: form.php?success=edit");
                    exit;
                } else {
                    throw new Exception("Erro ao atualizar anuidade.");
                }
            } else {
                if ($anuidade->buscarPorAno($ano)) {
                    $msg = "<div class='alert alert-warning'>Este ano já está cadastrado.</div>";
                } elseif ($anuidade->salvar()) {
                    header("Location: form.php?success=create");
                    exit;
                } else {
                    throw new Exception("Erro ao cadastrar anuidade.");
                }
            }
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>" . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$lista = $anuidade->listarTodas();
$totalAssociados = $anuidade->contarTodos();
$pendentes = $anuidade->contarPendentes();
$valores = $anuidade->calcularValores();
$valorPendente = $valores['pendente'] ?? 0;
$totalArrecadado = $valores['arrecadado'] ?? 0;

?>


<div class="container mt-7 pt-5">

    <h2 class="mb-4">Cadastrar Anuidade</h2>

    <?= $msg ?>

    <form method="post">
        <div class="col-12 d-flex">
            <div class="col-3">
                <label class="form-label">Ano:</label>
                <input type="number" name="ano" class="form-control ano" required min="1900" max="2100"
                    value="<?= $anuidadeEditar['ano'] ?? '' ?>" <?= $anuidadeEditar ? 'readonly' : '' ?>>
            </div>

            <div class="col-3">
                <label class="form-label">Valor (R$):</label>
                <input type="text" name="valor" class="form-control moeda" required
                    value="<?= isset($anuidadeEditar['valor']) ? number_format($anuidadeEditar['valor'], 2, ',', '.') : '' ?>">
            </div>

            <div class="col-6" style="margin-top: 30px;">
                <?php if ($anuidadeEditar): ?>
                    <input type="hidden" name="editar_id" value="<?= $anuidadeEditar['id'] ?>">
                    <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    <a href="form.php" class="btn btn-secondary">Cancelar</a>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Gerar Anuidade</button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#gerarCobrancaModal">
                        Gerar Anuidade e Cobranças
                    </button>
                    <a href="../../index.php" class="btn btn-secondary">Voltar</a>
                <?php endif; ?>
            </div>
        </div>

    </form>



    <?php if (count($lista) > 0): ?>
        <hr>
        <h4 class="mt-4">Anuidades Cadastradas</h4>
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <p><strong>Total de Cobranças:</strong> <?= $totalAssociados ?></p>
                    <p><strong>Cobranças Pendentes:</strong> <?= $pendentes ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <p><strong>Total Arrecadado:</strong> R$ <?= number_format($totalArrecadado, 2, ',', '.') ?></p>
                    <p><strong>Valor Pendente:</strong> R$ <?= number_format($valorPendente, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-sm mt-2">
            <thead>
                <tr>
                    <th>Ano</th>
                    <th>Valor</th>
                    <th>Total Associados</th>
                    <th>Pendentes</th>
                    <th>Valor Pendente</th>
                    <th>Total Arrecadado</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $a):
                    $resumo = $anuidade->obterResumoPorAno($a['ano']);
                ?>
                    <tr>
                        <td><?= $a['ano'] ?></td>
                        <td>R$ <?= number_format($a['valor'], 2, ',', '.') ?></td>
                        <td><?= $resumo['total_associados'] ?></td>
                        <td><?= $resumo['pendentes'] ?></td>
                        <td>R$ <?= number_format($resumo['valor_pendente'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($resumo['arrecadado'], 2, ',', '.') ?></td>
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

<div class="modal fade" id="gerarCobrancaModal" tabindex="-1" aria-labelledby="gerarCobrancaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="gerarCobrancaModalLabel">Gerar Cobranças</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ano" id="modalAno">
                    <input type="hidden" name="valor" id="modalValor">

                    <p>Deseja gerar cobranças para todos os associados filiados?</p>
                    <div class="mb-3">
                        <label for="dataVencimento" class="form-label">Data de Vencimento:</label>
                        <input type="date" name="data_vencimento_anuidade" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="gerar_automatico" class="btn btn-primary">Gerar Cobranças</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#gerarCobrancaModal').on('show.bs.modal', function (e) {
        document.getElementById('modalAno').value = document.querySelector('input[name="ano"]').value;
        document.getElementById('modalValor').value = document.querySelector('input[name="valor"]').value; 
    });
});
</script>

<?php include '../footer.php'; ?>