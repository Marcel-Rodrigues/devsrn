<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Devs do RN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container shadow mt-5">

        <?php
        if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
            $current_page = basename($_SERVER['PHP_SELF']);
        ?>
            <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
                <div class="container shadow">
                    <a class="navbar-brand" href="../../index.php">Devs do RN</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= (strpos($_SERVER['REQUEST_URI'], 'associado') !== false) ? 'active' : '' ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    Associado
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item <?= ($current_page == 'form.php' && strpos($_SERVER['REQUEST_URI'], 'associado') !== false) ? 'active' : '' ?>" href="../../views/associado/form.php">Cadastrar Associado</a></li>
                                    <li><a class="dropdown-item <?= ($current_page == 'lista.php') ? 'active' : '' ?>" href="../../views/associado/lista.php">Listar Associados</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="../../views/anuidade/form.php" class="nav-link <?= ($current_page == 'form.php' && strpos($_SERVER['REQUEST_URI'], 'anuidade') !== false) ? 'active' : '' ?>">Anuidade</a>
                            </li>
                            <li class="nav-item">
                                <a href="../../views/cobranca/form.php" class="nav-link <?= ($current_page == 'form.php' && strpos($_SERVER['REQUEST_URI'], 'cobranca') !== false) ? 'active' : '' ?>">Gerar Cobrança</a>
                            </li>
                            <li class="nav-item">
                                <a href="../../views/situacao/pagamento.php" class="nav-link <?= ($current_page == 'pagamento.php') ? 'active' : '' ?>">Ver Situação</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


        <?php } ?>