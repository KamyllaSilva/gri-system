<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_nome'] = $usuario['nome'];
            $_SESSION['user_tipo'] = $usuario['tipo'];
            $_SESSION['empresa_id'] = $usuario['empresa_id'];

            // Redireciona conforme o tipo de usuário
            if ($usuario['tipo'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/indicators.php");
            }
            exit();
        } else {
            // Erro de login
            header("Location: ../index.php?erro=1");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
}
