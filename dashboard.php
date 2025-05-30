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
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            color: #333;
        }

        .header {
            background: #004080;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .header .logo-small {
            height: 50px;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .header nav a:hover {
            color: #a5cfff;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard h2 {
            font-size: 28px;
            color: #004080;
            margin-bottom: 30px;
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 20px;
            color: #004080;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 32px;
            font-weight: bold;
            color: #007BFF;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header nav {
                margin-top: 10px;
            }

            .header nav a {
                display: block;
                margin: 10px 0;
            }
        }

        /* Centralizar canvas do gráfico */
        #indicadoresChart {
            max-width: 400px;
            max-height: 400px;
            margin: 40px auto 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <header class="header">
        <img src="assets/img/logo.png" alt="Logo" class="logo-small" />
        <h1>Sistema GRI</h1>
        <nav>
            <a href="dashboard.php">Painel</a>
            <a href="indicadores.php">Indicadores</a>
            <a href="usuarios.php">Usuários</a>
            <a href="logout.php">Sair</a>
        </nav>
    </header>

    <main class="container">
        <section class="dashboard">
            <h2>Painel de Indicadores</h2>
            <div class="grid">
                <div class="card">
                    <h3>Total de Indicadores</h3>
                    <p id="totalIndicadores">-</p>
                </div>
                <div class="card">
                    <h3>Preenchidos</h3>
                    <p id="preenchidosIndicadores">-</p>
                </div>
                <div class="card">
                    <h3>Pendentes</h3>
                    <p id="pendentesIndicadores">-</p>
                </div>
            </div>

            <canvas id="indicadoresChart"></canvas>
        </section>
    </main>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        fetch('dashboard-data.php')
            .then(response => {
                if (!response.ok) throw new Error('Erro ao carregar dados');
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Atualiza os números nos cards
                document.getElementById('totalIndicadores').textContent = data.total;
                document.getElementById('preenchidosIndicadores').textContent = data.preenchidos;
                document.getElementById('pendentesIndicadores').textContent = data.pendentes;

                // Configura o gráfico de pizza (doughnut)
                const ctx = document.getElementById('indicadoresChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Preenchidos', 'Pendentes'],
                        datasets: [{
                            label: 'Indicadores',
                            data: [data.preenchidos, data.pendentes],
                            backgroundColor: ['#007BFF', '#B0C4DE'],
                            hoverOffset: 30,
                            borderWidth: 1,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#003366',
                                    font: {
                                        size: 14,
                                        weight: 'bold',
                                    }
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
            })
            .catch(err => console.error(err));
    </script>
</body>
</html>
