<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Profissional</title>
  <style>
    /* Variáveis CSS para tema e tipografia */
    :root {
      --color-bg: #1565c0;
      --color-bg-dark: #0d47a1;
      --color-text-light: #e3f2fd;
      --color-text-muted: #bbdefb;
      --color-primary: #81D4FA;
      --color-error: #ef5350;
      --font-family: 'Inter', sans-serif;
      --transition-speed: 0.3s;
      --border-radius: 12px;
      --shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    /* Reset e global */
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: var(--font-family);
      background-color: var(--color-bg);
      color: var(--color-text-light);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    a {
      color: var(--color-text-light);
      text-decoration: none;
    }
    a:focus, button:focus, [role="button"]:focus {
      outline: 3px solid var(--color-primary);
      outline-offset: 3px;
    }

    /* Cabeçalho */
    header {
      background-color: var(--color-bg-dark);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow);
    }
    header h1 {
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
    }

    /* Perfil com dropdown */
    .profile {
      position: relative;
      font-weight: 600;
      cursor: pointer;
      user-select: none;
      color: var(--color-text-muted);
    }
    .profile:focus-visible {
      outline: 3px solid var(--color-primary);
      outline-offset: 3px;
    }
    .profile > span {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      user-select: none;
    }
    .profile svg {
      width: 0.7rem;
      height: 0.7rem;
      fill: var(--color-text-muted);
      transition: transform var(--transition-speed);
    }
    .profile[aria-expanded="true"] svg {
      transform: rotate(180deg);
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      background: var(--color-bg-dark);
      border-radius: var(--border-radius);
      margin-top: 0.5rem;
      min-width: 160px;
      box-shadow: var(--shadow);
      z-index: 1000;
      padding: 0.25rem 0;
    }
    .dropdown[aria-hidden="false"] {
      display: block;
    }
    .dropdown a {
      display: block;
      padding: 0.75rem 1.25rem;
      font-size: 1rem;
      color: var(--color-text-light);
      transition: background-color var(--transition-speed);
    }
    .dropdown a:hover,
    .dropdown a:focus-visible {
      background-color: var(--color-primary);
      color: var(--color-bg-dark);
      outline: none;
    }

    /* Conteúdo principal */
    main {
      flex: 1;
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    /* Loading Spinner */
    #loading {
      margin: 3rem auto;
      text-align: center;
      font-size: 1.2rem;
      color: var(--color-text-muted);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
    }
    .spinner {
      border: 4px solid rgba(255,255,255,0.2);
      border-top-color: var(--color-primary);
      border-radius: 50%;
      width: 3rem;
      height: 3rem;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Cards grid */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
      gap: 2rem;
      outline: none;
    }
    .card-indicador {
      background-color: var(--color-bg-dark);
      padding: 2rem;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      transition: background-color var(--transition-speed), transform var(--transition-speed);
      user-select: none;
    }
    .card-indicador:hover,
    .card-indicador:focus-visible {
      background-color: var(--color-primary);
      color: var(--color-bg-dark);
      transform: translateY(-4px);
      outline: none;
    }
    .card-indicador p {
      font-size: 3rem;
      font-weight: 800;
      margin: 0 0 0.5rem;
      line-height: 1;
    }
    .card-indicador span {
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    /* Chart container */
    #chart-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: var(--color-bg-dark);
      border-radius: var(--border-radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    /* Texto acessível escondido */
    .sr-only {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0,0,0,0) !important;
      white-space: nowrap !important;
      border: 0 !important;
    }

    /* Responsividade fina */
    @media (max-width: 600px) {
      .card-indicador p {
        font-size: 2.5rem;
      }
      main {
        gap: 1.5rem;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>Dashboard Profissional</h1>
  <div class="profile" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Menu do usuário" id="profileMenuButton">
    <span>
      Usuário
      <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.353a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
    </span>
  </div>
  <nav id="profileDropdown" class="dropdown" aria-label="Configurações do usuário" aria-hidden="true">
    <a href="perfil.php" tabindex="-1">Perfil</a>
    <a href="configuracoes.php" tabindex="-1">Configurações</a>
    <a href="logout.php" tabindex="-1">Sair</a>
  </nav>
</header>

<main>
  <section id="loading" role="status" aria-live="polite">
    <div class="spinner" aria-hidden="true"></div>
    Carregando dados...
  </section>

  <section id="cards-container" class="cards-grid" aria-label="Indicadores principais" tabindex="0" hidden>
    <!-- Cards gerados dinamicamente -->
  </section>

  <section id="chart-container" aria-label="Gráfico de preenchimento de indicadores" tabindex="0" hidden>
    <canvas id="dashboardChart" role="img" aria-describedby="chartDescription"></canvas>
    <div id="chartDescription" class="sr-only">Gráfico de barras mostrando a quantidade de indicadores preenchidos e pendentes.</div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (() => {
    const profileBtn = document.getElementById('profileMenuButton');
    const profileDropdown = document.getElementById('profileDropdown');

    // Toggle dropdown acessível
    profileBtn.addEventListener('click', () => {
      const expanded = profileBtn.getAttribute('aria-expanded') === 'true';
      profileBtn.setAttribute('aria-expanded', String(!expanded));
      profileDropdown.setAttribute('aria-hidden', String(expanded));
      if (!expanded) {
        // Coloca foco no primeiro item do dropdown
        profileDropdown.querySelector('a').focus();
      }
    });

    // Fecha dropdown ao clicar fora
    document.addEventListener('click', (e) => {
      if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileBtn.setAttribute('aria-expanded', 'false');
        profileDropdown.setAttribute('aria-hidden', 'true');
      }
    });

    // Fecha dropdown com ESC
    profileDropdown.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        profileBtn.setAttribute('aria-expanded', 'false');
        profileDropdown.setAttribute('aria-hidden', 'true');
        profileBtn.focus();
      }
    });
  })();

  async function carregarDashboard() {
    const loading = document.getElementById('loading');
    const cardsContainer = document.getElementById('cards-container');
    const chartContainer = document.getElementById('chart-container');

    try {
      loading.hidden = false;
      cardsContainer.hidden = true;
      chartContainer.hidden = true;

      const res = await fetch('dashboard-data.php');
      if (!res.ok) throw new Error('Erro ao buscar dados');

      const data = await res.json();

      // Esconde loading e mostra conteúdo
      loading.hidden = true;
      cardsContainer.hidden = false;
      chartContainer.hidden = false;

      // Preenche cards
      cardsContainer.innerHTML = '';
      data.indicadores.forEach(({id, nome, preenchidos, pendentes}) => {
        const card = document.createElement('article');
        card.className = 'card-indicador';
        card.tabIndex = 0;
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `${nome}: ${preenchidos} preenchidos, ${pendentes} pendentes`);
        card.addEventListener('click', () => {
          window.location.href = `detalhes-indicador.php?id=${id}`;
        });
        card.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            card.click();
          }
        });
        card.innerHTML = `<p>${preenchidos}</p><span>${nome}</span>`;
        cardsContainer.appendChild(card);
      });

      // Gráfico
      const ctx = document.getElementById('dashboardChart').getContext('2d');

      // Destrói gráfico anterior se existir
      if (window.myChart) window.myChart.destroy();

      const totalPreenchidos = data.indicadores.reduce((acc, cur) => acc + cur.preenchidos, 0);
      const totalPendentes = data.indicadores.reduce((acc, cur) => acc + cur.pendentes, 0);

      if (totalPreenchidos + totalPendentes === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = "18px 'Inter', sans-serif";
        ctx.fillStyle = "var(--color-text-light)";
        ctx.textAlign = "center";
        ctx.fillText("Sem dados para exibir", ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
      }

      window.myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Preenchidos', 'Pendentes'],
          datasets: [{
            label: 'Quantidade',
            data: [totalPreenchidos, totalPendentes],
            backgroundColor: ['var(--color-primary)', 'var(--color-error)'].map(c => getComputedStyle(document.documentElement).getPropertyValue(c).trim()),
            borderRadius: 6,
            maxBarThickness: 60,
          }]
        },
        options: {
          responsive: true,
          animation: {duration: 800, easing: 'easeOutQuart'},
          plugins: {
            legend: {display: false},
            tooltip: {
              callbacks: {
                label: ctx => `${ctx.parsed.y} indicadores`
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {stepSize: 1, color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-light').trim()},
              grid: {
                color: 'rgba(255,255,255,0.2)'
              }
            },
            x: {
              ticks: {color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-light').trim()},
              grid: {
                display: false
              }
            }
          }
        }
      });

    } catch (error) {
      loading.innerHTML = `<div role="alert" style="color: var(--color-error); font-weight: 600;">
        Falha ao carregar dados. Por favor, tente novamente.
      </div>`;
      console.error(error);
    }
  }

  window.addEventListener('DOMContentLoaded', carregarDashboard);
</script>

</body>
</html>
