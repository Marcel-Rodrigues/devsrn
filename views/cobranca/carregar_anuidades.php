<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
require_once '../../classes/Anuidade.php';

$db = new Database();
$conn = $db->getConnection();

$assoc = new Associado($conn);
$anuidade = new Anuidade($conn);

$associadoId = isset($_GET['associado_id']) ? $_GET['associado_id'] : null;
$anuidadesDisponiveis = [];

if ($associadoId) {
    $associado = $assoc->buscarPorId($associadoId);
    if ($associado) {
        $dataFiliacao = $associado['data_filiacao'];

        $anuidadesDisponiveis = $anuidade->listarNaoCobradas($associadoId, $dataFiliacao);
    }
}

if (count($anuidadesDisponiveis) > 0) {
    foreach ($anuidadesDisponiveis as $anuidade) {
        echo "<option value=\"{$anuidade['id']}\">{$anuidade['ano']} - R$ " . number_format($anuidade['valor'], 2, ',', '.') . "</option>";
    }
} else {
    echo "<option value=\"\">Nenhuma anuidade disponível</option>";
}
