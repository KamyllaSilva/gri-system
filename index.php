<?php
session_start();
require_once 'includes/db.php';

$page_title = "Login - Sistema GRI";

if (isset($_SESSION['user_id'])) {
    // Já logado, redireciona para a área do usuário
    header("Location: user/indicators.php");
    exit();
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || empty($senha)) {
        $erro = "Preencha email e senha corretamente.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, senha_hash, tipo FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo'];
            header("Location: user/indicators.php");
            exit();
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}
require_once 'includes/header.php';
?>

<h2 class="page-title">Login</h2>

<?php if ($erro): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="POST" novalidate aria-describedby="login-desc">
    <p id="login-desc" style="margin-bottom: 20px; color:#555;">
        Entre com suas credenciais para acessar o sistema.
    </p>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="seu@email.com" required autofocus>

    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" placeholder="Sua senha" required>

    <button type="submit" aria-label="Entrar no sistema">Entrar</button>
</form>

<?php require_once 'includes/footer.php'; ?>
