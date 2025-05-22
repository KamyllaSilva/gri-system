<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_nome = htmlspecialchars($_SESSION['user_nome']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $page_title ?? "Sistema GRI"; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        /* Header básico com flexbox */
        header {
            background-color: #004080;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        header a.logout {
            color: #ff5c5c;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
        }
        header a.logout:hover {
            text-decoration: underline;
        }
        main {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $page_title ?? "Sistema GRI"; ?></h1>
        <nav>
            <span>Olá, <strong><?php echo $user_nome; ?></strong></span> |
            <a href="../includes/logout.php" class="logout" aria-label="Sair do sistema">Sair</a>
        </nav>
    </header>
    <main>
