// frontend/js/empresa.js
document.getElementById('empresaForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const nome = document.getElementById('nome').value;
  const cnpj = document.getElementById('cnpj').value;
  const endereco = document.getElementById('endereco').value;

  const resposta = await fetch('/api/cadastrar_empresa.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({nome, cnpj, endereco})
  });

  const resultado = await resposta.json();
  const mensagem = document.getElementById('mensagem');

  if (resposta.ok) {
    mensagem.style.color = 'green';
    mensagem.textContent = resultado.message;
  } else {
    mensagem.textContent = resultado.error;
  }
});
