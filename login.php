<?php
declare(strict_types=1);
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && $senha ===  $usuario['senha']) {
            session_regenerate_id(true); // Segurança contra fixação de sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome']        = $usuario['nome'];
            $_SESSION['tipo']        = $usuario['tipo'];
            $_SESSION['empresa_id']  = $usuario['empresa_id'];

            header("Location: dashboard.php"); // Corrigido para caminho absoluto na raiz
            exit;
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
        url('assets/css/img/belas-florestas-na-primavera.jpg') no-repeat center center;
    background-size: cover;
    background-attachment: scroll;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    color: #1e293b;
        }

        .login-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 380px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #004080;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #004080;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0066cc;
        }

        .error {
            background: #ffdede;
            color: #a70000;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .register-link a {
            color: #004080;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Sistema GRI</h2>

    <?php if ($erro): ?>
        <div class="error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['senha_alterada'])): ?>
    <div class="success">Senha redefinida com sucesso. Faça login.</div>
    <?php endif; ?>


    <form method="POST" autocomplete="off">
        <input type="email" name="email" placeholder="Seu e-mail" required autofocus>
        <input type="password" name="senha" placeholder="Sua senha" required>
        <button type="submit">Entrar</button>
    </form>
    <div class="register-link">
    <a href="esqueci_senha.php">Esqueci minha senha</a>
    </div>


</div>

</body>
</html>
