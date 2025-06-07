<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header id="mainHeader">
  <div class="container">
    <div class="logo">  
      <h1>GRI - Gestão</h1>
    </div>
    <nav>
      <ul>
        <li><a href="?page=dashboard">Dashboard</a></li>
        <li><a href="?page=indicadores">Indicadores</a></li>
        <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
          <li><a href="?page=usuarios">Usuários</a></li>
        <?php endif; ?>
        <li><a href="../../backend/routes/logout.php" class="logout">Sair</a></li>
      </ul>
    </nav>
  </div>
</header>
