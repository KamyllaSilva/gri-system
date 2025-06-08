<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) 
    session_start();

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Validação básica do email e senha
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        header("Location: ../login.php?erro=1");
        exit();
    }

    // Busca usuário pelo email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica usuário e senha
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login OK: definir variáveis de sessão
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];
        $_SESSION['empresa_id'] = (int)$usuario['empresa_id'];

        // Redireciona conforme tipo de usuário
        if ($usuario['tipo'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/dashboard.php");
        }
        exit();
    } else {
        // Falha na autenticação: pausa para mitigar força bruta
        usleep(500000); // 0,5 segundos
        header("Location: ../login.php?erro=1");
        exit();
    }
}
