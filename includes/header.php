<?php
if (!isset($_SESSION)) session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : "Sistema GRI" ?></title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<header class="site-header" role="banner">
    <h1><?= isset($page_title) ? htmlspecialchars($page_title) : "Sistema GRI" ?></h1>
    <?php if (isset($_SESSION['user_nome'])): ?>
        <nav role="navigation" aria-label="Menu principal">
            <span>Olá, <strong><?= htmlspecialchars($_SESSION['user_nome']) ?></strong></span>
            <a href="../user/indicators.php">Indicadores</a>
            <a href="../logout.php">Sair</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container" role="main" tabindex="-1">
