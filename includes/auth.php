<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) 
    session_start();

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';


    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        header("Location: ../login.php?erro=1");
        exit();
    }

    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];
        $_SESSION['empresa_id'] = (int)$usuario['empresa_id'];

        
        if ($usuario['tipo'] === 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        
        usleep(500000); 
        header("Location: ../login.php?erro=1");
        exit();
    }
}
