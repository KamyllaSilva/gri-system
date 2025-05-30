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
<title>Painel - Sistema GRI</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

<style>
  * {
    box-sizing: border-box;
    margin: 0; padding: 0;
  }
  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a2a66, #004080);
    min-height: 100vh;
    color: #f0f4ff;
    display: flex;
    flex-direction: column;
  }
  header {
    background: #003366cc;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.4);
  }
  header h1 {
    font-weight: 700;
    font-size: 1.8rem;
    letter-spacing: 1.5px;
    text-shadow: 1px 1px 3px #0009;
  }
  nav a {
    color: #bcd4ff;
    font-weight: 600;
    margin-left: 25px;
    text-decoration: none;
    position: relative;
    padding-bottom: 4px;
  }
  nav a:hover, nav a.active {
    color: #e6f0ff;
  }
  nav a::after {
    content: "";
    position: absolute;
    left: 0; bottom: 0;
    width: 0;
    height: 3px;
    background: #66b0ff;
    transition: 0.3s ease;
  }
  nav a:hover::after, nav a.active::after {
    width: 100%;
  }
  main.container {
    max-width: 1300px;
    margin: 40px auto;
    padding: 0 20px 60px;
    flex-grow: 1;
  }
  h2 {
    text-align: center;
    font-size: 2.8rem;
    margin-bottom: 40px;
    text-shadow: 0 0 10px #7ab8ff;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
    gap: 30px;
  }
  .card {
    background: #0c3a8d;
    border-radius: 18px;
    padding: 30px;
    box-shadow:
      0 8px 15px rgb(0 98 255 / 0.6),
      inset 0 0 20px rgb(0 130 255 / 0.5);
    color: #d0e6ff;
    cursor: pointer;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    user-select: none;
  }
  .card:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow:
      0 12px 20px rgb(0 128 255 / 0.8),
      inset 0 0 25px rgb(0 160 255 / 0.7);
  }
  .card svg {
    width: 50px;
    height: 50px;
    margin-bottom: 18px;
    fill: #66b0ff;
    filter: drop-shadow(0 0 5px #3399ffaa);
  }
  .card h3 {
    font-weight: 700;
    font-size: 1.6rem;
    margin-bottom: 12px;
    text-align: center;
  }
  .card p {
    font-size: 3.2rem;
    font-weight: 900;
    color: #99ccff;
    letter-spacing: 1.1px;
    text-shadow: 0 0 12px #77aaffbb;
  }
  #indicadoresChart {
    max-width: 600px;
    max-height: 600px;
    margin: 50px auto 0;
    display: block;
    filter: drop-shadow(0 0 10px #66aaffcc);
  }
  footer {
    text-align: center;
    padding: 15px;
    font-size: 0.9rem;
    color: #aacceeaa;
  }
  /* Smooth number animation */
  .number-animate {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  }
  @media(max-width: 600px) {
    .card p {
      font-size: 2.4rem;
    }
  }
</style>
</head>
<body>

<header>
  <h1>Sistema GRI</h1>
  <nav>
    <a href="dashboard.php" class="active">Painel</a>
    <a href="indicadores.php">Indicadores</a>
    <a href="usuarios.php">Usuários</a>
    <a href="logout.php">Sair</a>
  </nav>
</header>

<main class="container">
  <h2>Painel de Indicadores</h2>
  <div class="grid" id="cardsContainer">
    <!-- Cards serão criados dinamicamente aqui -->
  </div>

  <canvas id="indicadoresChart"></canvas>
</main>

<footer>
  © 2025 Sistema GRI - Desenvolvido com 💙
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Função para animar contagem de números
  function animateNumber(element, start, end, duration = 1000) {
    let startTimestamp = null;
    const step = timestamp => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      element.textContent = Math.floor(progress * (end - start) + start);
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };
    window.requestAnimationFrame(step);
  }

  // Cria cards dinamicamente com dados recebidos
  function renderCards(indicadores) {
    const container = document.getElementById('cardsContainer');
    container.innerHTML = '';

    indicadores.forEach(ind => {
      const card = document.createElement('div');
      card.className = 'card';
      card.title = `Clique para editar o indicador "${ind.nome}"`;

      // Ícone SVG genérico (pode trocar conforme tipo)
      card.innerHTML = `
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm1 15h-2v-2h2zm0-4h-2V7h2z"/>
        </svg>
        <h3>${ind.nome}</h3>
        <p class="number-animate">0</p>
      `;

      card.onclick = () => {
        window.location.href = `formulario_indicador.php?id=${ind.id}`;
      };

      container.appendChild(card);

      // Animar o número do indicador (exemplo: valor atual)
      animateNumber(card.querySelector('p'), 0, ind.valor || 0);
    });
  }

  // Variável para armazenar o gráfico
  let chartInstance;

  // Atualiza dashboard com dados da API
  function updateDashboard() {
    fetch('dashboard-data.php')
      .then(res => {
        if (!res.ok) throw new Error('Erro ao carregar dados');
        return res.json();
      })
      .then(data => {
        if(data.error){
          alert(data.error);
          return;
        }

        // Atualiza cards dos indicadores
        renderCards(data.indicadores);

        // Atualiza gráfico donut
        const ctx = document.getElementById('indicadoresChart').getContext('2d');
        const chartData = {
          labels: ['Preenchidos', 'Pendentes'],
          datasets: [{
            label: 'Indicadores',
            data: [data.preenchidos, data.pendentes],
            backgroundColor: ['#66b0ff', '#224477'],
            hoverOffset: 35,
            borderWidth: 2,
            borderColor: '#0c2a66',
          }]
        };

        if(chartInstance){
          chartInstance.data = chartData;
          chartInstance.update();
        } else {
          chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: chartData,
            options: {
              responsive: true,
              cutout: '65%',
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    color: '#cde0ff',
                    font: { size: 15, weight: 'bold' },
                  }
                },
                tooltip: {
                  backgroundColor: '#0c2a66',
                  titleColor: '#aaddff',
                  bodyColor: '#d0e6ff',
                  cornerRadius: 6,
                  padding: 12,
                  callbacks: {
                    label: ctx => `${ctx.label}: ${ctx.parsed} indicadores`
                  }
                }
              }
            }
          });
        }
      })
      .catch(err => {
        console.error('Erro no fetch:', err);
      });
  }

  // Atualiza a cada 10 segundos para parecer "em tempo real"
  updateDashboard();
  setInterval(updateDashboard, 10000);

</script>
</body>
</html>
