<style>
  /* Container padrão centralizado */
  .container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 15px;
  }

  /* Header */
  #mainHeader {
    background-color: #1e3a8a;
    color: white;
    padding: 15px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }

  #mainHeader .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  #mainHeader .logo h1 {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 700;
    font-size: 1.8rem;
  }

  #mainHeader nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px;
  }

  #mainHeader nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
  }

  #mainHeader nav ul li a:hover,
  #mainHeader nav ul li a.logout {
    color: #ff7961;
    font-weight: 700;
  }

  /* Footer */
  #mainFooter {
    background-color: #1e3a8a;
    color: white;
    text-align: center;
    padding: 15px 0;
    margin-top: 50px;
    font-size: 0.9rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  /* Container centralizado e com largura limitada */
  #dashboardGraficos {
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background: #f0f7ff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  /* Canvas responsivo */
  #dashboardGraficos canvas {
    width: 100% !important; /* importante para forçar responsividade */
    height: 300px !important;
    margin-bottom: 40px;
    border: 1px solid #c1d3f8;
    border-radius: 5px;
    background-color: white;
  }

  /* Títulos dos gráficos */
  h2 {
    color: #1e3a8a;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
</style>
