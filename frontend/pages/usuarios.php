<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../public/index.php?page=dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Gerenciar Usuários - GRI</title>
</head>
<body>
  <h1>Usuários da Empresa: <?= $_SESSION['empresa_nome'] ?></h1>

  <form method="POST" action="../../backend/routes/cadastrar_usuario.php">
    <h2>Novo Usuário</h2>
    <input type="text" name="nome" placeholder="Nome completo" required />
    <input type="email" name="email" placeholder="E-mail" required />
    <input type="password" name="senha" placeholder="Senha" required />
    <select name="tipo">
      <option value="usuario">Usuário</option>
      <option value="admin">Administrador</option>
    </select>
    <button type="submit">Cadastrar</button>
  </form>

  <h2>Usuários Cadastrados</h2>
  <?php include '../../backend/routes/listar_usuarios.php'; ?>

  <p><a href="../../public/index.php?page=dashboard">← Voltar ao Dashboard</a></p>
</body>
</html>
