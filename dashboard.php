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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/dashboard.css" />
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --gray-light: #f3f4f6;
            --gray-medium: #e5e7eb;
            --gray-dark: #6b7280;
            --text-primary: #111827;
            --text-secondary: #4b5563;
        }
        
        /* Layout base */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-small {
            height: 50px;
        }
        
        nav a {
            margin-left: 1.5rem;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 600;
            transition: color 0.2s;
        }
        
        nav a:hover, nav a.active {
            color: var(--primary-color);
        }
        
        /* Dashboard header */
        .dashboard-header {
            margin-bottom: 2rem;
        }
        
        .dashboard-header h2 {
            font-size: 1.75rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .dashboard-header p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        /* Cards grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .card p {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }
        
        .card .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .card-total .card-icon { background-color: #e0e7ff; color: var(--primary-color); }
        .card-preenchidos .card-icon { background-color: #dcfce7; color: var(--success-color); }
        .card-pendentes .card-icon { background-color: #fee2e2; color: var(--danger-color); }
        .card-progresso .card-icon { background-color: #fef3c7; color: var(--warning-color); }
        
        /* Filtros */
        .filters-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        .filter-group select {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--gray-medium);
            background-color: white;
            font-family: inherit;
            font-size: 0.875rem;
            min-width: 160px;
        }
        
        .status-update {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .btn-refresh {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            background-color: var(--primary-color);
            color: white;
            font-family: inherit;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }
        
        .btn-refresh:hover {
            background-color: var(--primary-dark);
        }
        
        .loading-indicator {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Indicadores */
        .indicators-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        
        .category-section {
            margin-bottom: 2rem;
        }
        
        .category-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-medium);
        }
        
        .indicators-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        
        .indicator-card {
            padding: 1.25rem;
            border-radius: 0.5rem;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        
        .indicator-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .indicator-card.answered {
            border-left-color: var(--success-color);
        }
        
        .indicator-card.pending {
            border-left-color: var(--danger-color);
        }
        
        .indicator-card.partial {
            border-left-color: var(--warning-color);
        }
        
        .indicator-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .indicator-code {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 0.875rem;
        }
        
        .indicator-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }
        
        .status-answered {
            background-color: #dcfce7;
            color: var(--success-color);
        }
        
        .status-pending {
            background-color: #fee2e2;
            color: var(--danger-color);
        }
        
        .status-partial {
            background-color: #fef3c7;
            color: var(--warning-color);
        }
        
        .indicator-name {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .indicator-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .indicator-date {
            font-size: 0.75rem;
            color: var(--gray-dark);
            margin-top: 0.5rem;
        }
        
        .no-indicators {
            text-align: center;
            padding: 2rem;
            color: var(--gray-dark);
            grid-column: 1 / -1;
        }
        
        /* Progress chart */
        .chart-container {
            height: 300px;
            margin-bottom: 2rem;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .indicators-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-update {
                margin-left: 0;
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <header>
        <div style="display: flex; align-items: center;">
            <img src="assets/css/img/logo.png" alt="Logo" class="logo-small" />
            <h1 style="margin-left: 1rem;">Sistema GRI</h1>
        </div>
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
            <p>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>! <span id="last-update"></span></p>
        </section>

        <div class="cards-grid">
            <article class="card card-total">
                <div class="card-icon">
                    <i class="fas fa-chart-pie fa-lg"></i>
                </div>
                <h3>Total de Indicadores</h3>
                <p id="total-indicators">--</p>
            </article>
            
            <article class="card card-preenchidos">
                <div class="card-icon">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <h3>Preenchidos</h3>
                <p id="completed-indicators">--</p>
            </article>
            
            <article class="card card-pendentes">
                <div class="card-icon">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                </div>
                <h3>Pendentes</h3>
                <p id="pending-indicators">--</p>
            </article>
            
            <article class="card card-progresso">
                <div class="card-icon">
                    <i class="fas fa-tasks fa-lg"></i>
                </div>
                <h3>Progresso</h3>
                <p id="progress-percentage">--%</p>
            </article>
        </div>

        <div class="chart-container">
            <canvas id="indicatorsChart"></canvas>
        </div>

        <section class="filters-container">
            <div class="filter-group">
                <label for="category-filter">Categoria:</label>
                <select id="category-filter">
                    <option value="all">Todas</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status-filter">Status:</label>
                <select id="status-filter">
                    <option value="all">Todos</option>
                    <option value="completed">Preenchidos</option>
                    <option value="pending">Pendentes</option>
                </select>
            </div>
            
            <div class="status-update">
                <button id="refresh-btn" class="btn-refresh">
                    <i class="fas fa-sync-alt"></i> Atualizar
                </button>
                <div id="loading-indicator" class="loading-indicator">
                    <div class="loading-spinner"></div>
                    <span>Atualizando...</span>
                </div>
                <span id="update-time"></span>
            </div>
        </section>

        <section class="indicators-container">
            <div id="indicators-list">
                <p class="no-indicators">Carregando indicadores...</p>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variáveis globais
        let indicatorsData = {};
        let indicatorsChart = null;
        let lastUpdate = null;
        let socket = null;
        let isConnected = false;
        
        // Elementos do DOM
        const totalIndicatorsEl = document.getElementById('total-indicators');
        const completedIndicatorsEl = document.getElementById('completed-indicators');
        const pendingIndicatorsEl = document.getElementById('pending-indicators');
        const progressPercentageEl = document.getElementById('progress-percentage');
        const lastUpdateEl = document.getElementById('last-update');
        const updateTimeEl = document.getElementById('update-time');
        const refreshBtn = document.getElementById('refresh-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const indicatorsListEl = document.getElementById('indicators-list');
        const categoryFilter = document.getElementById('category-filter');
        const statusFilter = document.getElementById('status-filter');
        
        // Formatar data/hora
        function formatDateTime(date) {
            if (!date) return '';
            const options = { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            };
            return new Date(date).toLocaleString('pt-BR', options);
        }
        
        // Mostrar/ocultar loading
        function showLoading(show) {
            if (show) {
                refreshBtn.disabled = true;
                loadingIndicator.style.display = 'flex';
            } else {
                refreshBtn.disabled = false;
                loadingIndicator.style.display = 'none';
            }
        }
        
        // Conectar ao WebSocket
        function connectWebSocket() {
            const wsProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            const wsUrl = wsProtocol + window.location.host + '/ws/indicators';
            
            socket = new WebSocket(wsUrl);
            
            socket.onopen = function(e) {
                console.log('Conexão WebSocket estabelecida');
                isConnected = true;
            };
            
            socket.onmessage = function(event) {
                const data = JSON.parse(event.data);
                if (data.type === 'indicators_update') {
                    console.log('Atualização recebida via WebSocket');
                    updateDashboard(data.data);
                }
            };
            
            socket.onclose = function(event) {
                console.log('Conexão WebSocket fechada');
                isConnected = false;
                
                // Tentar reconectar após 5 segundos
                setTimeout(connectWebSocket, 5000);
            };
            
            socket.onerror = function(error) {
                console.error('Erro WebSocket:', error);
                isConnected = false;
            };
        }
        
        // Buscar dados do dashboard
        async function fetchDashboardData() {
            try {
                showLoading(true);
                
                const response = await fetch('api/dashboard.php', {
                    headers: {
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Falha ao carregar dados');
                }
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                return data;
            } catch (error) {
                console.error('Erro ao buscar dados:', error);
                indicatorsListEl.innerHTML = `
                    <p class="no-indicators">
                        Erro ao carregar dados. ${error.message}
                    </p>
                `;
                return null;
            } finally {
                showLoading(false);
            }
        }
        
        // Atualizar o dashboard com novos dados
        function updateDashboard(data) {
            if (!data) return;
            
            // Armazenar dados
            indicatorsData = data;
            lastUpdate = new Date();
            
            // Atualizar totais
            totalIndicatorsEl.textContent = data.total_indicators;
            completedIndicatorsEl.textContent = data.completed_indicators;
            pendingIndicatorsEl.textContent = data.pending_indicators;
            
            // Calcular e atualizar progresso
            const progress = Math.round((data.completed_indicators / data.total_indicators) * 100);
            progressPercentageEl.textContent = `${progress}%`;
            
            // Atualizar última atualização
            lastUpdateEl.textContent = `Última atualização: ${formatDateTime(lastUpdate)}`;
            updateTimeEl.textContent = formatDateTime(lastUpdate);
            
            // Atualizar gráfico
            updateChart(data);
            
            // Atualizar lista de indicadores
            updateIndicatorsList(data.indicators_by_category);
            
            // Atualizar opções de categoria
            updateCategoryOptions(data.indicators_by_category);
        }
        
        // Atualizar gráfico
        function updateChart(data) {
            const ctx = document.getElementById('indicatorsChart').getContext('2d');
            
            if (indicatorsChart) {
                indicatorsChart.destroy();
            }
            
            indicatorsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Preenchidos', 'Pendentes'],
                    datasets: [{
                        data: [data.completed_indicators, data.pending_indicators],
                        backgroundColor: ['#16a34a', '#dc2626'],
                        borderWidth: 1,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'Inter',
                                    size: 14
                                }
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
                    },
                    cutout: '70%'
                }
            });
        }
        
        // Atualizar lista de indicadores
        function updateIndicatorsList(indicatorsByCategory) {
            const categoryFilterValue = categoryFilter.value;
            const statusFilterValue = statusFilter.value;
            
            let html = '';
            
            // Filtrar categorias
            const categories = Object.keys(indicatorsByCategory)
                .filter(category => categoryFilterValue === 'all' || category === categoryFilterValue);
            
            if (categories.length === 0) {
                html = '<p class="no-indicators">Nenhum indicador encontrado com os filtros selecionados</p>';
            } else {
                categories.forEach(category => {
                    // Filtrar indicadores por status
                    const indicators = indicatorsByCategory[category]
                        .filter(indicator => {
                            if (statusFilterValue === 'completed') return indicator.status === 'completed';
                            if (statusFilterValue === 'pending') return indicator.status === 'pending';
                            return true;
                        });
                    
                    if (indicators.length === 0) return;
                    
                    html += `
                        <div class="category-section">
                            <h3 class="category-title">${category}</h3>
                            <div class="indicators-grid">
                                ${indicators.map(indicator => `
                                    <div class="indicator-card ${indicator.status}" 
                                         onclick="window.location.href='indicadores.php?indicador_id=${indicator.id}'"
                                         tabindex="0"
                                         role="button"
                                         aria-label="Indicador ${indicator.code} - ${indicator.name}, ${indicator.status === 'completed' ? 'preenchido' : 'pendente'}">
                                        <div class="indicator-header">
                                            <span class="indicator-code">${indicator.code}</span>
                                            <span class="indicator-status ${getStatusClass(indicator.status)}">
                                                ${getStatusText(indicator.status)}
                                            </span>
                                        </div>
                                        <h4 class="indicator-name">${indicator.name}</h4>
                                        <p class="indicator-value">${indicator.value || '—'}</p>
                                        ${indicator.updated_at ? `
                                            <p class="indicator-date">
                                                Atualizado em: ${formatDateTime(indicator.updated_at)}
                                            </p>
                                        ` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                });
            }
            
            indicatorsListEl.innerHTML = html;
        }
        
        // Obter classe CSS para status
        function getStatusClass(status) {
            switch (status) {
                case 'completed': return 'status-answered';
                case 'pending': return 'status-pending';
                default: return 'status-partial';
            }
        }
        
        // Obter texto para status
        function getStatusText(status) {
            switch (status) {
                case 'completed': return 'Preenchido';
                case 'pending': return 'Pendente';
                default: return 'Parcial';
            }
        }
        
        // Atualizar opções de categoria
        function updateCategoryOptions(indicatorsByCategory) {
            const categories = Object.keys(indicatorsByCategory).sort();
            
            categoryFilter.innerHTML = `
                <option value="all">Todas</option>
                ${categories.map(category => `
                    <option value="${category}">${category}</option>
                `).join('')}
            `;
        }
        
        // Carregar dados iniciais
        async function loadDashboard() {
            const data = await fetchDashboardData();
            if (data) {
                updateDashboard(data);
            }
        }
        
        // Event listeners
        categoryFilter.addEventListener('change', () => {
            updateIndicatorsList(indicatorsData.indicators_by_category);
        });
        
        statusFilter.addEventListener('change', () => {
            updateIndicatorsList(indicatorsData.indicators_by_category);
        });
        
        refreshBtn.addEventListener('click', loadDashboard);
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboard();
            connectWebSocket();
            
            // Atualizar a cada 30 segundos (fallback se WebSocket falhar)
            setInterval(async () => {
                if (!isConnected) {
                    console.log('Atualizando via polling (fallback)');
                    const data = await fetchDashboardData();
                    if (data) {
                        updateDashboard(data);
                    }
                }
            }, 30000);
        });
    </script>
</body>
</html>