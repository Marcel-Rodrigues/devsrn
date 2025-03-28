<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();
$assoc = new Associado($conn);

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';
$ordenacao = isset($_GET['ordenacao']) ? $_GET['ordenacao'] : 'nome ASC';

$lista = $assoc->listarTodos($filtro, $ordenacao);

?>

<div class="container mt-7 pt-5">

    <h2 class="mb-4">Lista de Associados</h2>

    <form method="GET" class="mb-3">
        <div class="row col-12 d-flex">
            <div class="col-7">
                <label class="form-label">Filtro</label>
                <input type="text" name="filtro" class="form-control" placeholder="Filtrar por Nome, Email ou CPF" value="<?= htmlspecialchars($filtro) ?>">
            </div>
            <div class="col-3">
            <label class="form-label">Ordenação</label>
                <select name="ordenacao" class="form-control">
                    <option value="nome ASC" <?= $ordenacao == 'nome ASC' ? 'selected' : '' ?>>Nome (A-Z)</option>
                    <option value="nome DESC" <?= $ordenacao == 'nome DESC' ? 'selected' : '' ?>>Nome (Z-A)</option>
                    <option value="data_filiacao ASC" <?= $ordenacao == 'data_filiacao ASC' ? 'selected' : '' ?>>Data de Filiação (Mais Antigos)</option>
                    <option value="data_filiacao DESC" <?= $ordenacao == 'data_filiacao DESC' ? 'selected' : '' ?>>Data de Filiação (Mais Recentes)</option>
                </select>
            </div>
            <div class="col-2" style="margin-top: 30px">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="index.php" class="btn btn-secondary">Limpar</a>
            </div>
        </div>
    </form>

    <?php if (count($lista) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Data de Filiação</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nome']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $a['cpf']) ?></td>
                        <td><?= date('d/m/Y', strtotime($a['data_filiacao'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($a['data_cadastro'])) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Nenhum associado encontrado.</div>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>