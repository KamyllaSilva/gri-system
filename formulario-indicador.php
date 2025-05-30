<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];

// Recebe o id do indicador via GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Indicador inválido.");
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe e valida dados
    $nome = trim($_POST['nome'] ?? '');
    $valor = filter_var($_POST['valor'] ?? '', FILTER_VALIDATE_FLOAT);
    $status = ($_POST['status'] ?? '') === 'preenchido' ? 'preenchido' : 'pendente';

    if (empty($nome)) {
        $erro = "Nome não pode ficar vazio.";
    } elseif ($valor === false || $valor < 0) {
        $erro = "Valor inválido.";
    } else {
        // Atualiza no banco
        $stmt = $conn->prepare("UPDATE indicadores SET nome = ?, valor = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $nome, $valor, $status, $id);
        if ($stmt->execute()) {
            $sucesso = "Indicador atualizado com sucesso!";
        } else {
            $erro = "Erro ao atualizar: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Busca os dados atuais do indicador para preencher o formulário
$stmt = $conn->prepare("SELECT nome, valor, status FROM indicadores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nomeIndicador, $valorIndicador, $statusIndicador);
if (!$stmt->fetch()) {
    die("Indicador não encontrado.");
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Indicador - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            color: #333;
            padding: 20px;
            max-width: 600px;
            margin: 40px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;
        }

        h1 {
            color: #004080;
            margin-bottom: 30px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #004080;
        }

        input[type="text"],
        input[type="number"],
        select {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1.8px solid #007BFF;
            font-size: 1.1rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #004080;
            outline: none;
            box-shadow: 0 0 8px #004080aa;
        }

        button {
            background-color: #004080;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 12px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: background-color 0.25s;
        }

        button:hover {
            background-color: #003366;
        }

        .message {
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 10px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1.5px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
        }
    </style>
</head>
<body>

<h1>Editar Indicador</h1>

<?php if (!empty($erro)) : ?>
    <div class="message error"><?= htmlspecialchars($erro) ?></div>
<?php elseif (!empty($sucesso)) : ?>
    <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<form method="post" novalidate>
    <label for="nome">Nome do Indicador</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nomeIndicador) ?>" required autofocus />

    <label for="valor">Valor</label>
    <input type="number" id="valor" name="valor" value="<?= htmlspecialchars($valorIndicador) ?>" min="0" step="0.01" required />

    <label for="status">Status</label>
    <select id="status" name="status" required>
        <option value="preenchido" <?= $statusIndicador === 'preenchido' ? 'selected' : '' ?>>Preenchido</option>
        <option value="pendente" <?= $statusIndicador === 'pendente' ? 'selected' : '' ?>>Pendente</option>
    </select>

    <button type="submit">Salvar Alterações</button>
</form>

<p style="text-align:center; margin-top: 20px;">
    <a href="dashboard.php" style="color:#004080; text-decoration:none; font-weight:600;">&larr; Voltar ao Painel</a>
</p>

</body>
</html>
