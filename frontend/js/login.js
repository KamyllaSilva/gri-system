// frontend/js/login.js
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const email = document.getElementById('email').value;
  const senha = document.getElementById('senha').value;

  const resposta = await fetch('/api/login.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({email, senha})
  });

  const resultado = await resposta.json();
  const mensagem = document.getElementById('mensagem');

  if (resposta.ok) {
    mensagem.style.color = 'green';
    mensagem.textContent = resultado.message;
    // redirecionar se quiser
  } else {
    mensagem.textContent = resultado.error;
  }
});
