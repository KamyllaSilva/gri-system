<?php
session_start();
$erro = $_GET['erro'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- opcional -->
    <style>
        body {
            background: linear-gradient(to right, #004080, #007BFF);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #004080;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #004080;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0066cc;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
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
        <div class="error">Usuário ou senha inválidos.</div>
    <?php endif; ?>

    <form action="includes/auth.php" method="POST">
        <input type="email" name="email" placeholder="Seu e-mail" required>
        <input type="password" name="senha" placeholder="Sua senha" required>
        <button type="submit">Entrar</button>
    </form>

    <div class="register-link">
        Ainda não tem conta? <a href="register.php">Cadastre-se</a>
    </div>
</div>

</body>
</html>
