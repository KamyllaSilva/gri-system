<?php
session_start();
require_once 'includes/db.php';

$erro = null;
$sucesso = null;

// CADASTRAR NOVA EMPRESA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');

    if (!$nome || !$cnpj) {
        $erro = "Preencha todos os campos.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
            $stmt->execute([$nome, $cnpj]);
            $sucesso = "Empresa cadastrada com sucesso!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Já existe uma empresa com esse CNPJ.";
            } else {
                $erro = "Erro ao cadastrar: " . $e->getMessage();
            }
        }
    }
}

// BUSCAR TODAS AS EMPRESAS
$stmt = $pdo->query("SELECT id, nome, cnpj, created_at FROM empresas ORDER BY created_at DESC");
$empresas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Empresas - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
          --primary: #1e3a8a;
          --primary-light: #2563eb;
          --background: #f9fbff;
          --foreground: #1a1a1a;
          --muted: #6c757d;
          --card: #ffffff;
          --shadow: 0 4px 20px rgba(30, 64, 175, 0.1);
          --radius: 12px;
          --transition: all 0.3s ease;
        }

        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Inter', sans-serif;
          background: var(--background);
          color: var(--foreground);
          line-height: 1.6;
          -webkit-font-smoothing: antialiased;
          scroll-behavior: smooth;
          min-height: 100vh;
          padding-bottom: 40px;
        }

        header {
            background-color: var(--primary);
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        nav a {
            color: #e0e7ff;
            text-decoration: none;
            font-weight: 500;
            margin-left: 1rem;
        }

        nav a:hover,
        nav a.active {
            color: #fff;
            text-decoration: underline;
        }

        .container {
            background: var(--card);
            padding: 32px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 900px;
            margin: 40px auto;
        }

        h1.page-title {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .msg {
            padding: 14px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            text-align: center;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .msg.error {
            background: #ffdede;
            color: #a70000;
            box-shadow: 0 0 8px #a70000aa;
        }

        .msg.success {
            background: #d1ffd8;
            color: #228a22;
            box-shadow: 0 0 8px #228a2288;
        }

        form {
            max-width: 500px;
            margin: 0 auto 40px;
            background: var(--card);
            padding: 32px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: var(--radius);
            font-size: 15px;
        }

        form button {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-light);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        form button:hover {
            background-color: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f0f8ff;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 20px 10px;
            }

            form {
                padding: 20px;
            }

            table, th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<header>
        <img src="assets/css/img/logo.png" alt="Logo" class="logo-small" />
        <h1>Sistema GRI</h1>
        <nav>
            <a href="dashboard.php" class="active">Painel</a>
            <a href="indicadores.php">Indicadores</a>
            <a href="usuarios.php">Usuários</a>
            <a href="empresas.php">Empresas</a>
            <a href="logout.php">Sair</a>
        </nav>
    </header>

<div class="container">
    <h1 class="page-title">Cadastro de Empresa</h1>

    <?php if ($erro): ?>
        <div class="msg error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="msg success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="acao" value="adicionar" />

        <label for="nome">Nome da Empresa</label>
        <input type="text" id="nome" name="nome" required />

        <label for="cnpj">CNPJ</label>
        <input type="text" id="cnpj" name="cnpj" required placeholder="00.000.000/0000-00" />

        <button type="submit">Cadastrar Empresa</button>
    </form>

    <h2 style="text-align:center; color: var(--primary); margin-bottom: 1rem;">Empresas Cadastradas</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>CNPJ</th>
                <th>Data de Cadastro</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($empresas): ?>
                <?php foreach ($empresas as $empresa): ?>
                    <tr>
                        <td><?= htmlspecialchars($empresa['nome']) ?></td>
                        <td><?= htmlspecialchars($empresa['cnpj']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($empresa['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">Nenhuma empresa cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
