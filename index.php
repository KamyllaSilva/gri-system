<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema GRI | Gestão de Indicadores</title>
    <meta name="description" content="Sistema GRI - Gestão profissional de indicadores de sustentabilidade" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" />
    <style>
        :root {
            --azul: #004080;
            --azul-claro: #2563eb;
            --fundo: #f1f5f9;
            --branco: #ffffff;
            --texto: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--fundo);
            color: var(--texto);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: var(--azul);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--branco);
            color: var(--azul);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
        }

        .button:hover {
            background-color: var(--azul-claro);
            color: white;
            transform: translateY(-1px);
        }

        main {
            flex: 1;
            max-width: 960px;
            margin: 40px auto;
            padding: 20px;
        }

        section {
            background-color: var(--branco);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            transition: box-shadow 0.3s ease;
        }

        section:hover {
            box-shadow: 0 6px 32px rgba(0,0,0,0.08);
        }

        section h2 {
            display: flex;
            align-items: center;
            font-size: 1.4rem;
            color: var(--azul);
            margin-bottom: 15px;
            gap: 10px;
        }

        section p, section ul {
            font-size: 1rem;
            color: #444;
        }

        section ul {
            margin-top: 10px;
            padding-left: 20px;
        }

        section ul li {
            margin-bottom: 6px;
        }

        footer {
            background-color: var(--azul);
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                text-align: center;
            }

            main {
                padding: 15px;
            }

            .button {
                padding: 10px 16px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Sistema GRI</h1>
    <a href="login.php" class="button">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="20" height="20" viewBox="0 0 24 24">
            <path d="M10 17l5-5-5-5v10zM19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
        </svg>
        Acessar Área Restrita
    </a>
</header>

<main>
    <section>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="24" height="24">
                <path d="M3 17h2v-7H3v7zm4 0h2v-4H7v4zm4 0h2v-10h-2v10zm4 0h2v-2h-2v2zm4 0h2v-14h-2v14z"/>
            </svg>
            O que são os Indicadores GRI?
        </h2>
        <p>Os Indicadores GRI são padrões internacionais que promovem transparência e responsabilidade corporativa em relação aos impactos econômicos, sociais e ambientais das organizações.</p>
    </section>

    <section>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="24" height="24">
                <path d="M11 17h2v2h-2zm0-10h2v8h-2zm1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
            </svg>
            Por que são importantes?
        </h2>
        <p>Esses indicadores auxiliam empresas a monitorarem e comunicarem seu desempenho sustentável, promovendo melhores decisões e reputação institucional.</p>
    </section>

    <section>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="24" height="24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15H9v-2h2v2zm0-4H9V7h2v6z"/>
            </svg>
            Exemplo: ODS da ONU
        </h2>
        <p>Os Objetivos de Desenvolvimento Sustentável (ODS) incluem ações globais para erradicar pobreza, promover educação, igualdade e proteção ambiental.</p>
        <ul>
            <li>ODS 4: Educação de Qualidade</li>
            <li>ODS 7: Energia Limpa</li>
            <li>ODS 13: Ação Climática</li>
        </ul>
    </section>

    <section>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="24" height="24">
                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/>
            </svg>
            Como funciona o Sistema?
        </h2>
        <p>Plataforma segura para gestão e monitoramento dos indicadores GRI com funcionalidades como:</p>
        <ul>
            <li>Cadastro e atualização de indicadores</li>
            <li>Dashboard com status e gráficos</li>
            <li>Controle de usuários e permissões</li>
        </ul>
    </section>
</main>

<footer>
    &copy; <?= date('Y') ?> Sistema GRI — Projeto Acadêmico
</footer>

</body>
</html>
