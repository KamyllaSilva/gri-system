<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sistema GRI | Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<header>
  <h1>Login</h1>
</header>

<main>
  <?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['success'])): ?>
  <p style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>
  
  <form method="POST" action="backend/routes/login.php">
    <label>Email:<br>
      <input type="email" name="email" required>
    </label><br><br>
    <label>Senha:<br>
      <input type="password" name="senha" required>
    </label><br><br>
    <button type="submit">Entrar</button>
  </form>
  
  <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
</main>
</body>
</html>
