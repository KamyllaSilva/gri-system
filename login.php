<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - Sistema GRI</title>
<link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
  <main>
    <h1>Login</h1>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="backend/routes/login.php">
      <label>Email:<br><input type="email" name="email" required autofocus></label><br><br>
      <label>Senha:<br><input type="password" name="senha" required></label><br><br>
      <button type="submit">Entrar</button>
    </form>
  </main>
</body>
</html>
