<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<header>
    <img src="assets/img/logo.png" alt="Logo" class="logo-small" />
    <h1>Sistema GRI</h1>
    <nav>
        <a href="dashboard.php" class="button">Painel</a>
        <a href="indicadores.php" class="button">Indicadores</a>
        <a href="usuarios.php" class="button">Usuários</a>
        <a href="logout.php" class="button">Sair</a>
    </nav>
</header>
