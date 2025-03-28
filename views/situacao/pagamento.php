<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
require_once '../../classes/Cobranca.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();

$assoc = new Associado($conn);
$cobrancaObj = new Cobranca($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['associado_id'], $_POST['anuidade_id'])) {
    try {
        if (isset($_POST['acao'], $_POST['associado_id'], $_POST['anuidade_id'])) {
            $cobrancaObj->setAssociadoId($_POST['associado_id']);
            $cobrancaObj->setAnuidadeId($_POST['anuidade_id']);

            switch ($_POST['acao']) {
                case 'marcar_paga':
                    $cobrancaObj->marcarComoPaga();
                    $msg = "<div class='alert alert-success'>Cobrança marcada como paga!</div>";
                    break;

                case 'excluir':
                    if ($cobrancaObj->excluir()) {
                        $msg = "<div class='alert alert-success'>Cobrança excluída com sucesso!</div>";
                    } else {
                        throw new Exception("Erro ao excluir cobrança");
                    }
                    break;
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>" . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$associados = $assoc->listarTodos();
?>

<div class="container mt-7 pt-5">

    <h2 class="mb-4">Situação de Pagamento dos Associados</h2>

    <?php if (count($associados) > 0): ?>
        <?php foreach ($associados as $a): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <strong><?= htmlspecialchars($a['nome']) ?></strong> (<?= $a['email'] ?>) - Filiação: <?= date('d/m/Y', strtotime($a['data_filiacao'])) ?>
                </div>
                <div class="card-body">
                    <?php
                    $cobrancas = $cobrancaObj->listarPorAssociado($a['id']);

                    $valorTotal = 0;
                    $anuidadesPendentes = [];
                    foreach ($cobrancas as $c) {
                        if (!$c['status']) {
                            $valorTotal += $c['valor'];
                            $anuidadesPendentes[] = $c['ano'];
                        }
                    }

                    if (count($cobrancas) === 0): ?>
                        <p class="text-muted">Nenhuma cobrança registrada.</p>
                    <?php else: ?>
                        <table class="table table-bordered table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Ano</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Pagamento</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cobrancas as $c): ?>
                                    <tr>
                                        <td><?= $c['ano'] ?></td>
                                        <td>R$ <?= number_format($c['valor'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($c['data_vencimento'])) ?></td>
                                        <td><?= !empty($c['data_pagamento']) ? date('d/m/Y', strtotime($c['data_pagamento'])) : '' ?></td>
                                        <td>
                                            <?php if ($c['status']): ?>
                                                <span class="badge bg-success">Paga</span>
                                            <?php elseif ($c['data_vencimento'] && strtotime($c['data_vencimento']) < time()): ?>
                                                <span class="badge bg-danger">Vencido</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Em aberto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$c['status']): ?>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="associado_id" value="<?= $a['id'] ?>">
                                                    <input type="hidden" name="anuidade_id" value="<?= $c['anuidade_id'] ?>">
                                                    <input type="hidden" name="acao" value="marcar_paga">
                                                    <button type="submit" class="btn btn-sm btn-success">Marcar como paga</button>
                                                </form>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta cobrança?');">
                                                    <input type="hidden" name="associado_id" value="<?= $a['id'] ?>">
                                                    <input type="hidden" name="anuidade_id" value="<?= $c['anuidade_id'] ?>">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                                </form>
                                            <?php else: ?>
                                                <i class="text-muted">—</i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (!empty($anuidadesPendentes)): ?>
                            <div class="mt-3 alert alert-warning">
                                <strong>Checkout:</strong>
                                Anuidades em aberto: <?= implode(', ', $anuidadesPendentes) ?> <br>
                                Valor total devido: <strong>R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 alert alert-success">
                                <strong>Pagamento em dia!</strong> Nenhuma anuidade pendente.
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Nenhum associado encontrado.</div>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>