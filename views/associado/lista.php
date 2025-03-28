<?php
require_once '../../config/database.php';
require_once '../../classes/Associado.php';
include '../header.php';

$db = new Database();
$conn = $db->getConnection();
$assoc = new Associado($conn);
$lista = $assoc->listarTodos();
?>

<div class="container mt-7 pt-5">

<h2 class="mb-4">Lista de Associados</h2>

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
