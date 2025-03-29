<?php
include 'views/header.php';

require_once 'config/database.php';
require_once 'classes/Cobranca.php';
require_once 'classes/Associado.php';

$db = new Database();
$conn = $db->getConnection();

$associado = new Associado($conn);
$cobranca = new Cobranca($conn);

$totalAssociados = $associado->qtdTotal();
$cobrancasAbertas = $cobranca->qtdPendente();
$cobrancasVencidas = $cobranca->qtdVencidas();
$cobrancasPagas = $cobranca->qtdPagas();
$totalArrecadado = $cobranca->totalArrecadado();
$totalAReceber = $cobranca->totalPendente();
$distribuicaoPagamentos = $cobranca->pagamentosPorAno();
?>

<div class="container-fluid">
    <h1 class="my-4">Dashboard - Gestão de Anuidades</h1>

    <div class="card mb-4 border-0 shadow-lg">
        <div class="card-header bg-primary text-white rounded-top-3">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Ações Rápidas</h5>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-6 col-xl-3 hover-effect">
                    <a href="views/associado/form.php" class="action-card d-block p-4 text-decoration-none">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1 text-dark">Cadastrar Associado</h6>
                                <small class="text-muted">Adicionar novo membro</small>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 hover-effect">
                    <a href="views/anuidade/form.php" class="action-card d-block p-4 text-decoration-none">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1 text-dark">Cadastrar Anuidade</h6>
                                <small class="text-muted">Definir valores anuais</small>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 hover-effect">
                    <a href="views/cobranca/form.php" class="action-card d-block p-4 text-decoration-none">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1 text-dark">Gerar Cobrança</h6>
                                <small class="text-muted">Emitir novos boletos</small>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 hover-effect">
                    <a href="views/situacao/pagamento.php" class="action-card d-block p-4 text-decoration-none">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1 text-dark">Situação de Pagamento</h6>
                                <small class="text-muted">Monitorar status</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Associados</h5>
                    <p class="card-text display-4 fs-1"><?= $totalAssociados ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Cobranças Abertas</h5>
                    <p class="card-text display-4 fs-1"><?= $cobrancasAbertas ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Vencidas</h5>
                    <p class="card-text display-4 fs-1"><?= $cobrancasVencidas ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Arrecadado Total</h5>
                    <p class="card-text display-4 fs-1">R$ <?= number_format($totalArrecadado, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Situação de Pagamentos
                </div>
                <div class="card-body">
                    <canvas id="chartPagamentos" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Arrecadação por Ano
                </div>
                <div class="card-body">
                    <canvas id="chartArrecadacao" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('chartPagamentos'), {
        type: 'doughnut',
        data: {
            labels: ['Pagas', 'Abertas', 'Vencidas'],
            datasets: [{
                data: [
                    <?= $cobrancasPagas ?>,
                    <?= $cobrancasAbertas ?>,
                    <?= $cobrancasVencidas ?>
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        }
    });

    new Chart(document.getElementById('chartArrecadacao'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($distribuicaoPagamentos, 'ano')) ?>,
            datasets: [{
                label: 'Arrecadação (R$)',
                data: <?= json_encode(array_column($distribuicaoPagamentos, 'total')) ?>,
                backgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'views/footer.php'; ?>