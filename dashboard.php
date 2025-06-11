<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel Profissional - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 20px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        header img.logo-small {
            height: 50px;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }
        nav a.active {
            color: #004080;
            font-weight: 700;
        }
        h1, h2 {
            color: #004080;
        }
        .cards-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            text-align: center;
            cursor: default;
        }
        .card h3 {
            margin-bottom: 12px;
        }
        .card p {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        .cards-indicadores {
            display: grid;
            grid-template-columns: repeat(auto-fill,minmax(150px,1fr));
            gap: 15px;
        }
        .card-indicador {
            background: white;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
            user-select: none;
        }
        .card-indicador:hover, .card-indicador:focus {
            box-shadow: 0 0 12px #42a5f5;
            outline: none;
        }
        .card-indicador h4 {
            margin: 0 0 10px 0;
            font-weight: 600;
            color: #004080;
        }
        .card-indicador .valor {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }
        .sem-indicadores {
            font-style: italic;
            color: #666;
        }
        canvas#indicadoresChart {
            max-width: 100%;
            margin-bottom: 40px;
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

    <main>
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
            <!-- Indicadores detalhados via JS -->
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function carregarDashboard() {
            try {
                const res = await fetch('dashboard-data.php', { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Falha ao carregar dados');

                const data = await res.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                document.getElementById('totalIndicadores').textContent = data.total;
                document.getElementById('preenchidosIndicadores').textContent = data.preenchidos;
                document.getElementById('pendentesIndicadores').textContent = data.pendentes;

                atualizarGrafico(data.preenchidos, data.pendentes);
                preencherCartoes(data.indicadores);
            } catch (e) {
                console.error(e);
                alert('Erro ao carregar dados do painel.');
            }
        }

        function atualizarGrafico(preenchidos, pendentes) {
            const ctx = document.getElementById('indicadoresChart').getContext('2d');
            if (window.chartIndicadores) window.chartIndicadores.destroy();

            window.chartIndicadores = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Preenchidos', 'Pendentes'],
                    datasets: [{
                        label: 'Indicadores',
                        data: [preenchidos, pendentes],
                        backgroundColor: ['#42A5F5', '#90CAF9'],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 40,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 14, weight: '600' },
                                color: '#004080'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.parsed}`
                            }
                        }
                    }
                }
            });
        }

        function preencherCartoes(indicadores) {
            const container = document.querySelector('.cards-indicadores');
            container.innerHTML = '';

            if (!indicadores || indicadores.length === 0) {
                container.innerHTML = '<p class="sem-indicadores">Nenhum indicador encontrado.</p>';
                return;
            }

            indicadores.forEach(ind => {
                const div = document.createElement('article');
                div.className = 'card-indicador';
                div.tabIndex = 0;
                div.setAttribute('role', 'button');
                div.setAttribute('aria-pressed', 'false');
                div.title = `Indicador: ${ind.nome}, Status: ${ind.status}, Valor: ${ind.valor}`;

                div.innerHTML = `
                    <h4>${ind.nome}</h4>
                    <p class="valor">${ind.valor || '(sem valor)'}</p>
                    <p>Status: <strong>${ind.status || 'pendente'}</strong></p>
                `;

                // Exemplo: clicar abre formulário do indicador (substituir pela rota correta)
                div.addEventListener('click', () => {
                    // Redireciona para a página do formulário do indicador
                    window.location.href = 'formulario-indicador.php?id=' + encodeURIComponent(ind.id);
                });

                container.appendChild(div);
            });
        }

        window.addEventListener('load', carregarDashboard);
    </script>
</body>
</html>
