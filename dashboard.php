<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

// Verifica se o usuário está logado e tem empresa associada
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    header('Location: login.php');
    exit;
}
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
        /* --- Estilos dos filtros --- */
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
        /* Categorias e grids */
        .categoria-titulo {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #1e3a8a;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .categoria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        /* Cards de indicadores */
        .card-indicador {
            padding: 1rem;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            border-left: 4px solid #1e3a8a;
        }
        .card-indicador:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .card-indicador h4 {
            margin: 0 0 0.5rem 0;
            color: #1e3a8a;
            font-size: 1rem;
        }
        .card-indicador .valor {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .sem-indicadores {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        /* Status indicators */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .status-preenchido {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pendente {
            background-color: #fee2e2;
            color: #991b1b;
        }
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(30,58,138,0.3);
            border-radius: 50%;
            border-top-color: #1e3a8a;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        /* Status de atualização */
        .status-atualizacao {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        .btn-atualizar {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            border: 1px solid #1e3a8a;
            background-color: #1e3a8a;
            color: white;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        .btn-atualizar:hover {
            background-color: #1e40af;
        }
        /* Cards de resumo */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .card {
            padding: 1rem;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-top: 0;
            color: #1e3a8a;
            font-size: 1rem;
        }
        .card p {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0;
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

    <main class="container">
        <section class="dashboard-header">
            <h2>Painel de Indicadores</h2>
            <p>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>! <span id="ultimaAtualizacao"></span></p>
        </section>

        <section class="cards-grid" aria-label="Indicadores resumidos">
            <article class="card" tabindex="0" role="region" aria-labelledby="total-indicadores">
                <h3 id="total-indicadores">Total de Indicadores</h3>
                <p id="totalIndicadores"><span class="loading"></span></p>
            </article>
            <article class="card" tabindex="0" role="region" aria-labelledby="preenchidos-indicadores">
                <h3 id="preenchidos-indicadores">Indicadores Preenchidos</h3>
                <p id="preenchidosIndicadores"><span class="loading"></span></p>
            </article>
            <article class="card" tabindex="0" role="region" aria-labelledby="pendentes-indicadores">
                <h3 id="pendentes-indicadores">Indicadores Pendentes</h3>
                <p id="pendentesIndicadores"><span class="loading"></span></p>
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
            
            <div class="status-atualizacao">
                <button id="btnAtualizar" class="btn-atualizar">Atualizar</button>
                <span id="statusCarregamento" style="display: none;">
                    <span class="loading"></span> Atualizando...
                </span>
            </div>
        </section>

        <section class="cards-indicadores" aria-label="Lista de indicadores detalhados" id="cardsIndicadores">
            <p class="sem-indicadores">Carregando indicadores...</p>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let dadosOriginais = {};
        let chartIndicadores = null;
        let ultimaAtualizacao = null;

        // Função para formatar data/hora
        function formatarDataHora(data) {
            if (!data) return '';
            const options = { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit'
            };
            return new Date(data).toLocaleString('pt-BR', options);
        }

        // Função para mostrar status de carregamento
        function mostrarCarregamento(mostrar) {
            const elemento = document.getElementById('statusCarregamento');
            elemento.style.display = mostrar ? 'flex' : 'none';
            
            const btn = document.getElementById('btnAtualizar');
            btn.disabled = mostrar;
        }

        async function carregarDashboard() {
            try {
                mostrarCarregamento(true);
                const res = await fetch('dashboard-data.php', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error('Falha ao carregar dados');

                const data = await res.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Atualiza os totais
                document.getElementById('totalIndicadores').textContent = data.total;
                document.getElementById('preenchidosIndicadores').textContent = data.preenchidos;
                document.getElementById('pendentesIndicadores').textContent = data.pendentes;

                // Atualiza o gráfico
                atualizarGrafico(data);
                
                // Armazena os dados originais
                dadosOriginais = data.indicadores;
                
                // Preenche as opções de categorias
                preencherOpcoesCategorias(data.indicadores);
                
                // Preenche os cartões de indicadores
                preencherCartoes(data.indicadores);
                
                // Atualiza a informação da última atualização
                ultimaAtualizacao = new Date();
                document.getElementById('ultimaAtualizacao').textContent = `(Última atualização: ${formatarDataHora(ultimaAtualizacao)})`;
                
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
                document.getElementById('cardsIndicadores').innerHTML = 
                    '<p class="sem-indicadores">Erro ao carregar os dados. Tentando novamente...</p>';
            } finally {
                mostrarCarregamento(false);
            }
        }

        function atualizarGrafico(dados) {
            // Cria ou atualiza o canvas do gráfico
            let canvas = document.getElementById('indicadoresChart');
            
            if (!canvas) {
                canvas = document.createElement('canvas');
                canvas.id = 'indicadoresChart';
                document.querySelector('.card').appendChild(canvas);
            }
            
            const ctx = canvas.getContext('2d');
            
            // Destrói o gráfico anterior se existir
            if (chartIndicadores) {
                chartIndicadores.destroy();
            }
            
            // Cria o novo gráfico
            chartIndicadores = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Preenchidos', 'Pendentes'],
                    datasets: [{
                        label: 'Indicadores',
                        data: [dados.preenchidos, dados.pendentes],
                        backgroundColor: ['#4CAF50', '#F44336'],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#333',
                                font: { size: 14, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function preencherOpcoesCategorias(indicadoresPorCategoria) {
            const select = document.getElementById('filtroCategoria');
            select.innerHTML = '<option value="todas">Todas</option>';
            
            // Ordena as categorias alfabeticamente
            const categorias = Object.keys(indicadoresPorCategoria).sort();
            
            categorias.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                select.appendChild(opt);
            });
        }

        function preencherCartoes(indicadoresPorCategoria) {
            const filtroCategoria = document.getElementById('filtroCategoria').value;
            const filtroStatus = document.getElementById('filtroStatus').value;

            const container = document.getElementById('cardsIndicadores');
            container.innerHTML = '';

            const categorias = Object.keys(indicadoresPorCategoria);
            const categoriasFiltradas = categorias.filter(cat =>
                filtroCategoria === 'todas' || cat === filtroCategoria
            );

            if (categoriasFiltradas.length === 0) {
                container.innerHTML = '<p class="sem-indicadores">Nenhum indicador encontrado com os filtros selecionados.</p>';
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
                    card.setAttribute('aria-label', `Indicador ${ind.nome}, ${ind.valor !== null ? 'preenchido' : 'pendente'}`);

                    const statusBadge = ind.valor !== null ? 
                        '<span class="status-badge status-preenchido">Preenchido</span>' : 
                        '<span class="status-badge status-pendente">Pendente</span>';

                    card.innerHTML = `
                        <h4>${ind.nome} ${statusBadge}</h4>
                        <span class="valor">${ind.valor !== null ? ind.valor.toLocaleString('pt-BR', {
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

        // Event listeners para os filtros
        document.getElementById('filtroCategoria').addEventListener('change', () => {
            preencherCartoes(dadosOriginais);
        });

        document.getElementById('filtroStatus').addEventListener('change', () => {
            preencherCartoes(dadosOriginais);
        });

        // Botão de atualização manual
        document.getElementById('btnAtualizar').addEventListener('click', carregarDashboard);

        // Carrega os dados inicialmente
        document.addEventListener('DOMContentLoaded', carregarDashboard);

        // Atualiza os dados a cada 30 segundos
        setInterval(carregarDashboard, 30000);
    </script>
</body>
</html>