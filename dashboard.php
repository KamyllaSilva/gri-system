<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel Profissional - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/dashboard.css" />
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
            <!-- Indicadores detalhados via JS -->
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function carregarDashboard() {
            try {
                const res = await fetch('dashboard-data.php', {
                    method: 'GET',
                    credentials: 'same-origin',
                });

                if (!res.ok) throw new Error('Falha ao carregar dados');

                const data = await res.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                document.getElementById('totalIndicadores').textContent = data.total;
                document.getElementById('preenchidosIndicadores').textContent = data.preenchidos;
                document.getElementById('pendentesIndicadores').textContent = data.pendentes;

                atualizarGrafico(data);
                preencherCartoes(data.indicadores);
            } catch (error) {
                console.error(error);
                alert('Erro ao carregar dados do painel.');
            }
        }

        function atualizarGrafico(dados) {
            const ctx = document.getElementById('indicadoresChart').getContext('2d');
            if (window.chartIndicadores) window.chartIndicadores.destroy();

            window.chartIndicadores = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Preenchidos', 'Pendentes'],
                    datasets: [{
                        label: 'Indicadores',
                        data: [dados.preenchidos, dados.pendentes],
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
                                color: '#333',
                                font: { size: 14, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }

        function preencherCartoes(indicadores) {
            const container = document.querySelector('.cards-indicadores');
            container.innerHTML = '';

            if (indicadores.length === 0) {
                container.innerHTML = '<p class="sem-indicadores">Nenhum indicador encontrado.</p>';
            } else {
                indicadores.forEach(ind => {
                    const card = document.createElement('article');
                    card.className = 'card-indicador';
                    card.setAttribute('tabindex', '0');
                    card.setAttribute('role', 'button');
                    card.setAttribute('aria-label', `Indicador ${ind.nome}, valor ${ind.valor}`);

                    card.innerHTML = `
                        <h4>${ind.nome}</h4>
                        <span class="valor">${ind.valor.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}</span>
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
        }

        window.addEventListener('DOMContentLoaded', carregarDashboard);
    </script>
</body>
</html>
