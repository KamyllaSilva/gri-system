// frontend/js/usuario.js
document.getElementById('usuarioForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const nome = document.getElementById('nome').value;
  const email = document.getElementById('email').value;
  const senha = document.getElementById('senha').value;
  const empresa_id = document.getElementById('empresa_id').value;

  const resposta = await fetch('/api/cadastrar_usuario.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({nome, email, senha, empresa_id})
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
