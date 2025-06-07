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
<title>Sistema GRI | Gestão Profissional de Indicadores</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" />
</head>
<body>
<header>
  <h1>Sistema GRI</h1>
  <nav>
    <a href="login.php" class="button" aria-label="Acessar área restrita">Acessar Área Restrita</a>
  </nav>
</header>

<main>
  <section>
    <h2>
      <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="32" height="32"><path d="M3 17h2v-7H3v7zm4 0h2v-4H7v4zm4 0h2v-10h-2v10zm4 0h2v-2h-2v2zm4 0h2v-14h-2v14z"/></svg>
      O que são os Indicadores GRI?
    </h2>
    <p>Os Indicadores GRI (Global Reporting Initiative) são padrões internacionais para relatórios de sustentabilidade, permitindo que organizações monitorem e divulguem seus impactos econômicos, ambientais e sociais de forma transparente e padronizada.</p>
  </section>

  <section>
    <h2>
      <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="32" height="32"><path d="M11 17h2v2h-2zm0-10h2v8h-2zm1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
      Por que são importantes?
    </h2>
    <p>Esses indicadores ajudam empresas e instituições a aprimorar a governança, a responsabilidade socioambiental e a comunicação com seus públicos, alinhando suas ações aos objetivos globais de desenvolvimento sustentável.</p>
  </section>

  <section>
    <h2>
      <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="32" height="32"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15H9v-2h2v2zm0-4H9V7h2v6z"/></svg>
      Exemplo: Objetivos de Desenvolvimento Sustentável (ODS)
    </h2>
    <p>Os ODS, definidos pela ONU, são 17 objetivos que visam promover um futuro melhor para todos, incluindo temas como erradicação da pobreza, educação de qualidade, igualdade de gênero e combate às mudanças climáticas.</p>
    <ul>
      <li>ODS 4: Educação de Qualidade</li>
      <li>ODS 7: Energia Acessível e Limpa</li>
      <li>ODS 13: Ação contra a Mudança Global do Clima</li>
    </ul>
  </section>

  <section>
    <h2>
      <svg xmlns="http://www.w3.org/2000/svg" fill="#2563eb" viewBox="0 0 24 24" width="32" height="32"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/></svg>
      Como funciona o Sistema?
    </h2>
    <p>Nosso sistema oferece uma plataforma segura e intuitiva para gerenciar os Indicadores GRI da sua organização. Você poderá cadastrar e acompanhar indicadores, visualizar relatórios e gráficos que facilitam a tomada de decisões estratégicas.</p>
    <ul>
      <li>Cadastramento e atualização de indicadores</li>
      <li>Visualização de indicadores preenchidos e pendentes</li>
      <li>Relatórios dinâmicos e gráficos interativos</li>
      <li>Controle de acesso por usuários autorizados</li>
    </ul>
    <p>Para acessar a plataforma, clique no botão “Acessar Área Restrita” no topo desta página e faça seu login.</p>
  </section>
</main>

<footer>
  &copy; <?= date('Y') ?> Sistema GRI — Projeto Acadêmico &mdash; Desenvolvido por você
</footer>
</body>
</html>
