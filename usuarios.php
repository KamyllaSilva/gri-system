<?php
session_start();
require_once 'includes/db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$erro = null;
$sucesso = null;

// EXCLUIR USUÁRIO via ?excluir=id
if (isset($_GET['excluir'])) {
    $excluir_id = (int) $_GET['excluir'];

    // Só excluir se o usuário for da mesma empresa
    $stmt = $pdo->prepare("SELECT empresa_id FROM usuarios WHERE id = ?");
    $stmt->execute([$excluir_id]);
    $userEmpresa = $stmt->fetchColumn();

    if ($userEmpresa == $empresa_id && $excluir_id != $_SESSION['usuario_id']) {
        $del = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $del->execute([$excluir_id]);
        $sucesso = "Usuário excluído com sucesso.";
    } else {
        $erro = "Não foi possível excluir esse usuário.";
    }
}

// ADICIONAR NOVO USUÁRIO via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if (!$nome || !$email || !$senha || !$tipo) {
        $erro = "Preencha todos os campos do formulário.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } else if (!in_array($tipo, ['admin', 'usuario'])) {
        $erro = "Tipo de usuário inválido.";
    } else {
        // Verifica se email já existe na empresa
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND empresa_id = ?");
        $check->execute([$email, $empresa_id]);
        if ($check->fetchColumn() > 0) {
            $erro = "Já existe um usuário com esse e-mail na sua empresa.";
        } else {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$nome, $email, $hashSenha, $tipo, $empresa_id]);
            $sucesso = "Usuário cadastrado com sucesso.";
        }
    }
}

// Buscar usuários da mesma empresa
$stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE empresa_id = ?");
$stmt->execute([$empresa_id]);
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Gerenciar Usuários - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        /* Seu CSS já enviado aqui */
        :root {
          --primary: #1e40af;
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

        /* HEADER */
        header {
          background-color: var(--primary);
          padding: 16px 32px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          box-shadow: var(--shadow);
          position: sticky;
          top: 0;
          z-index: 1000;
          flex-wrap: wrap;
        }

        header h1 {
          color: white;
          font-size: 1.5rem;
          font-weight: 600;
          flex-grow: 1;
          user-select: none;
        }

        nav {
          display: flex;
          gap: 1rem;
        }

        nav a {
          color: #e0e7ff;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.3s ease;
        }

        nav a:hover,
        nav a.active {
          color: #ffffff;
          text-decoration: underline;
        }

        /* Container principal */
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

        .welcome {
          text-align: center;
          margin-bottom: 25px;
          font-size: 1.1rem;
          color: var(--muted);
        }

        /* Mensagens */
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

        /* Tabela */
        table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 30px;
        }

        th, td {
          padding: 12px 15px;
          border-bottom: 1px solid #ddd;
          text-align: left;
          font-size: 0.95rem;
        }

        th {
          background-color: var(--primary);
          color: white;
          font-weight: 600;
        }

        tr:hover {
          background-color: #f0f8ff;
        }

        /* Ações */
        .actions {
          display: flex;
          gap: 12px;
        }

        .btn {
          padding: 8px 14px;
          border: none;
          border-radius: 8px;
          font-weight: 600;
          cursor: pointer;
          transition: background-color 0.3s ease;
          color: white;
          font-size: 14px;
          text-decoration: none;
          display: inline-block;
          text-align: center;
          user-select: none;
        }

        .btn-delete {
          background-color: #dc3545;
        }

        .btn-delete:hover {
          background-color: #a71d2a;
        }

        /* Formulário */
        form {
          max-width: 500px;
          margin: 0 auto;
          background: var(--card);
          padding: 32px;
          border-radius: var(--radius);
          box-shadow: var(--shadow);
          text-align: left;
        }

        form h2 {
          margin-top: 0;
          color: var(--primary);
          margin-bottom: 20px;
          text-align: center;
          font-size: 1.5rem;
        }

        form label {
          display: block;
          margin-bottom: 8px;
          font-weight: 600;
          color: var(--foreground);
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select {
          width: 100%;
          padding: 12px 14px;
          margin-bottom: 18px;
          border: 1px solid #cfd8e3;
          border-radius: var(--radius);
          font-size: 15px;
          transition: var(--transition);
        }

        form input:focus,
        form select:focus {
          border-color: var(--primary-light);
          box-shadow: 0 0 6px rgba(37, 99, 235, 0.2);
          outline: none;
        }

        form button {
          width: 100%;
          padding: 14px;
          background-color: var(--primary-light);
          color: #fff;
          border: none;
          border-radius: var(--radius);
          font-weight: 600;
          font-size: 1rem;
          cursor: pointer;
          transition: var(--transition);
        }

        form button:hover {
          background-color: var(--primary);
          transform: translateY(-2px);
        }

        @media (max-width: 600px) {
          th, td {
            padding: 10px 8px;
          }
          .actions {
            flex-direction: column;
            gap: 8px;
          }
          .btn {
            width: 100%;
          }
          .container {
            margin: 20px 15px;
            padding: 20px;
          }
          form {
            padding: 24px;
            margin: 20px 0;
          }
        }
    </style>
</head>
<body>

<header>
    <h1>Sistema GRI</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="usuarios.php" class="active">Usuários</a>
        <a href="logout.php" class="button">Sair</a>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">Gerenciar Usuários</h1>
    <p class="welcome">Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?>! Veja os usuários da sua empresa.</p>

    <?php if ($erro): ?>
        <div class="msg error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="msg success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($usuarios): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['tipo']) ?></td>
                    <td class="actions">
                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                            <a href="?excluir=<?= $u['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars($u['nome']) ?>?')">Excluir</a>
                        <?php else: ?>
                            <em>Você</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center; padding: 15px;">Nenhum usuário cadastrado nesta empresa.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <form method="post" action="">
        <h2>Adicionar Novo Usuário</h2>
        <input type="hidden" name="acao" value="adicionar" />

        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required />

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required />

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="" disabled selected>Selecione o tipo</option>
            <option value="admin">Administrador</option>
            <option value="usuario">Usuário</option>
        </select>

        <button type="submit">Cadastrar Usuário</button>
    </form>
</div>

</body>
</html>
