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
    <link rel="stylesheet" href="assets/css/dashboard.css" />
    <style>
        /* --- Adicionado: Estilo dos filtros --- */
        .filtros-dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #1e3a8a;
            font-weight: 500;
        }
        .filtros-dashboard label {
            margin-right: 0.3rem;
        }
        .filtros-dashboard select {
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
        }
        /* Para melhor separar as categorias no painel */
        .categoria-titulo {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #1e3a8a;
            font-weight: 600;
        }
        .categoria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
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

        <!-- FILTROS: categoria e status -->
        <section class="filtros-dashboard" aria-label="Filtros de indicadores">
            <label for="filtroCategoria">Categoria:</label>
            <select id="filtroCategoria" aria-controls="cardsIndicadores">
                <option value="todas">Todas</option>
            </select>

            <label for="filtroStatus">Status:</label>
            <select id="filtroStatus" aria-controls="cardsIndicadores">
                <option value="todos">Todos</option>
                <option value="preenchidos">Preenchidos</option>
                <option value="pendentes">Pendentes</option>
            </select>
        </section>

        <section class="cards-indicadores" aria-label="Lista de indicadores detalhados" id="cardsIndicadores">
            <!-- Indicadores detalhados via JS -->
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let dadosOriginais = {};

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
                dadosOriginais = data.indicadores;
                preencherOpcoesCategorias(data.indicadores);
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

        function preencherOpcoesCategorias(indicadoresPorCategoria) {
            const select = document.getElementById('filtroCategoria');
            select.innerHTML = '<option value="todas">Todas</option>';
            Object.keys(indicadoresPorCategoria).forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                select.appendChild(opt);
            });
        }

        function preencherCartoes(indicadoresPorCategoria) {
    const filtroCategoria = document.getElementById('filtroCategoria').value;
    const filtroStatus = document.getElementById('filtroStatus').value;

    const container = document.querySelector('.cards-indicadores');
    container.innerHTML = '';

    const categorias = Object.keys(indicadoresPorCategoria);
    const categoriasFiltradas = categorias.filter(cat =>
        filtroCategoria === 'todas' || cat === filtroCategoria
    );

    if (categoriasFiltradas.length === 0) {
        container.innerHTML = '<p class="sem-indicadores">Nenhum indicador encontrado.</p>';
        return;
    }

    categoriasFiltradas.forEach(categoria => {
        const indicadores = indicadoresPorCategoria[categoria].filter(ind => {
            if (filtroStatus === 'preenchidos') return ind.valor !== null;
            if (filtroStatus === 'pendentes') return ind.valor === null;
            return true;
        });

        if (indicadores.length === 0) return;

        const titulo = document.createElement('h3');
        titulo.textContent = categoria;
        titulo.className = 'categoria-titulo';
        container.appendChild(titulo);

        const grid = document.createElement('div');
        grid.className = 'categoria-grid';

        indicadores.forEach(ind => {
            const card = document.createElement('article');
            card.className = 'card-indicador';
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'button');
            card.setAttribute('aria-label', `Indicador ${ind.nome}, valor ${ind.valor !== null ? ind.valor : 'não preenchido'}`);

            card.innerHTML = `
                <h4>${ind.nome}</h4>
                <span class="valor">${ind.valor !== null ? ind.valor.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) : '—'}</span>
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

            grid.appendChild(card);
        });

        container.appendChild(grid);
    });
}


        document.getElementById('filtroCategoria').addEventListener('change', () => {
            preencherCartoes(dadosOriginais);
        });

        document.getElementById('filtroStatus').addEventListener('change', () => {
            preencherCartoes(dadosOriginais);
        });

        window.addEventListener('DOMContentLoaded', carregarDashboard);
        setInterval(carregarDashboard, 10000);
    </script>
</body>
</html>
