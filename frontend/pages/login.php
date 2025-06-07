<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Login GRI</title></head>
<body>
  <h1>Login</h1>
  <?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>
  <form method="POST" action="../../backend/routes/login.php">
    <label>Email:<br>
      <input type="email" name="email" required>
    </label><br><br>
    <label>Senha:<br>
      <input type="password" name="senha" required>
    </label><br><br>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>