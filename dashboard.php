<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel Profissional - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: #093170;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }

        header img.logo-small {
            height: 48px;
            filter: drop-shadow(0 0 3px rgba(0,0,0,0.5));
        }

        header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 2px;
            user-select: none;
            flex-grow: 1;
            margin-left: 15px;
            color: #BBDEFB;
        }

        nav a {
            color: #BBDEFB;
            font-weight: 600;
            margin-left: 25px;
            text-decoration: none;
            transition: color 0.3s ease;
            position: relative;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #64B5F6;
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        nav a:hover, nav a.active {
            color: #E3F2FD;
        }

        nav a:hover::after, nav a.active::after {
            width: 100%;
        }

        main.container {
            flex-grow: 1;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.35);
            backdrop-filter: blur(10px);
        }

        section.dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        section.dashboard-header h2 {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
            margin-bottom: 10px;
        }

        section.dashboard-header p {
            font-size: 1.1rem;
            color: #cce4ff;
            font-weight: 500;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .card {
            background: linear-gradient(145deg, #1E88E5, #1565C0);
            border-radius: 18px;
            padding: 30px 25px;
            box-shadow: 0 10px 15px rgba(0,0,0,0.3);
            text-align: center;
            cursor: default;
            user-select: none;
            color: #e3f2fd;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 25px rgba(0,0,0,0.45);
        }

        .card h3 {
            font-size: 1.6rem;
            margin-bottom: 12px;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .card p {
            font-size: 2.8rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #bbdefb;
            text-shadow: 2px 2px 10px rgba(255,255,255,0.6);
        }

        /* Cards indicadores (clicáveis) */
        .cards-indicadores {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .card-indicador {
            background: linear-gradient(145deg, #42A5F5, #1E88E5);
            border-radius: 16px;
            padding: 22px 18px;
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
            cursor: pointer;
            color: #f0f7ff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card-indicador:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 20px rgba(0,0,0,0.35);
            background: linear-gradient(145deg, #64B5F6, #1976D2);
        }

        .card-indicador h4 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 6px;
            user-select: text;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }

        .card-indicador span.valor {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #dbe9ff;
            user-select: text;
            text-shadow: 2px 2px 8px rgba(255,255,255,0.7);
        }

        canvas#indicadoresChart {
            display: block;
            max-width: 480px;
            margin: 0 auto 50px;
            filter: drop-shadow(0 3px 8px rgba(0,0,0,0.25));
            border-radius: 20px;
            background: rgba(255,255,255,0.15);
        }

        @media (max-width: 768px) {
            main.container {
                margin: 20px 15px 50px;
                padding: 20px;
            }
            header {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }
            nav a {
                margin-left: 15px;
                font-size: 0.95rem;
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
            <a href="logout.php">Sair</a>
        </nav>
    </header>

    <main class="container">
        <section class="dashboard-header">
            <h2>Painel de Indicadores</h2>
            <p>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>!</p>
        </section>

        <section class="cards-grid" aria-label="Indicadores resumidos">
            <article class="card" tabindex="0" role="region" aria-labelledby="total-indicadores">
                <h3 id="total-indicadores">Total de Indicadores</h3>
                <p id="totalIndicadores">–</p>
            </article>
            <article class="card" tabindex="0" role="region" aria-labelledby="preenchidos-indicadores">
                <h3 id="preenchidos-indicadores">Indicadores Preenchidos</h3>
                <p id="preenchidosIndicadores">–</p>
            </article>
            <article class="card" tabindex="0" role="region" aria-labelledby="pendentes-indicadores">
                <h3 id="pendentes-indicadores">Indicadores Pendentes</h3>
                <p id="pendentesIndicadores">–</p>
            </article>
        </section>

        <canvas id="indicadoresChart" role="img" aria-label="Gráfico de pizza dos indicadores preenchidos e pendentes"></canvas>

        <section class="cards-indicadores" aria-label="Lista de indicadores detalhados">
            <!-- Cards dos indicadores serão inseridos aqui pelo JS -->
        </section>
    </main>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function carregarDashboard() {
            try {
                const res = await fetch('dashboard-data.php');
                if (!res.ok) throw new Error('Falha ao carregar dados');

                const data = await res.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Atualiza os cards resumidos
                document.getElementById('totalIndicadores').textContent = data.total;
                document.getElementById('preenchidosIndicadores').textContent = data.preenchidos;
                document.getElementById('pendentesIndicadores').textContent = data.pendentes;

                // Atualiza gráfico
                const ctx = document.getElementById('indicadoresChart').getContext('2d');

                // Se já existir gráfico, destrói antes para evitar duplicação
                if (window.chartIndicadores) {
                    window.chartIndicadores.destroy();
                }

                window.chartIndicadores = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Preenchidos', 'Pendentes'],
                        datasets: [{
                            label: 'Indicadores',
                            data: [data.preenchidos, data.pendentes],
                            backgroundColor: ['#64B5F6', '#1565C0'],
                            borderColor: '#fff',
                            borderWidth: 2,
                            hoverOffset: 40,
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            animateScale: true,
                            duration: 1200,
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#E3F2FD',
                                    font: { size: 14, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.parsed} indicadores`
                                }
                            }
                        }
                    }
                });

                // Lista de indicadores detalhados
                const container = document.querySelector('.cards-indicadores');
                container.innerHTML = '';

                if (data.indicadores.length === 0) {
                    container.innerHTML = '<p style="text-align:center; color:#dbe9ff; font-style: italic;">Nenhum indicador encontrado.</p>';
                } else {
                    data.indicadores.forEach(ind => {
                        const card = document.createElement('article');
                        card.className = 'card-indicador';
                        card.setAttribute('tabindex', '0');
                        card.setAttribute('role', 'button');
                        card.setAttribute('aria-pressed', 'false');
                        card.setAttribute('aria-label', `Indicador ${ind.nome}, valor ${ind.valor}`);

                        card.innerHTML = `
                            <h4>${ind.nome}</h4>
                            <span class="valor">${ind.valor.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        `;

                        card.addEventListener('click', () => {
                            window.location.href = 'formulario-indicador.php?id=' + ind.id;
                        });
                        card.addEventListener('keydown', e => {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                card.click();
                            }
                        });

                        container.appendChild(card);
                    });
                }

            } catch (error) {
                console.error(error);
                alert('Erro ao carregar dados do painel.');
            }
        }

        // Carrega os dados ao abrir a página
        window.addEventListener('DOMContentLoaded', carregarDashboard);
    </script>
</body>
</html>