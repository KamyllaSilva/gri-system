<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // ✅ LOGIN OK — Preenche a sessão com todos os dados necessários
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_tipo'] = $usuario['tipo']; // 'admin' ou 'usuario'
        $_SESSION['empresa_id']   = $usuario['empresa_id'];

        // Redireciona conforme o tipo de usuário
        if ($usuario['tipo'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/indicators.php");
        }
        exit();
    } else {
        // Erro de autenticação
        header("Location: ../index.php?erro=1");
        exit();
    }
}
?>
