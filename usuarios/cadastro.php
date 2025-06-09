session_start();
require_once '../includes/db.php';

$erro = '';
$sucesso = '';

// Verifica se está logado e se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Busca empresas já cadastradas
$empresas = $pdo->query("SELECT id, nome FROM empresas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];  // texto puro
    $tipo = $_POST['tipo'] ?? 'user';  // tipo vindo do form ou default user
    $empresa_id = $_POST['empresa_id'] ?? null;
    $nova_empresa = trim($_POST['nova_empresa'] ?? '');

    // Cadastrar nova empresa, se houver
    if (!empty($nova_empresa)) {
        $stmt = $pdo->prepare("INSERT INTO empresas (nome) VALUES (?)");
        if ($stmt->execute([$nova_empresa])) {
            $empresa_id = $pdo->lastInsertId();
        } else {
            $erro = "Erro ao cadastrar nova empresa.";
        }
    }

    if (empty($empresa_id)) {
        $erro = "Você precisa selecionar ou cadastrar uma empresa.";
    }

    // Verifica se o email já existe
    $verifica = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->execute([$email]);
    if ($verifica->rowCount() > 0) {
        $erro = "Este e-mail já está cadastrado.";
    }

    // Cadastra o usuário se não houver erros
    if (empty($erro)) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, empresa_id, tipo) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nome, $email, $senha, $empresa_id, $tipo])) {
            $sucesso = "Usuário cadastrado com sucesso.";
        } else {
            $erro = "Erro ao cadastrar usuário.";
        }
    }
}
