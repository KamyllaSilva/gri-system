const API_URL = 'http://localhost:8080/api/indicadores'; // Ajuste para seu backend

const form = document.getElementById('formIndicador');
const tabelaCorpo = document.querySelector('#tabelaIndicadores tbody');
const btnCancelar = document.getElementById('btnCancelar');

function limparForm() {
    form.reset();
    form.id.value = '';
    btnCancelar.style.display = 'none';
}

function preencherForm(indicador) {
    form.id.value = indicador.id;
    form.codigo.value = indicador.codigo;
    form.descricao.value = indicador.descricao;
    form.resposta.value = indicador.resposta;
    form.evidencias.value = indicador.evidencias;
    btnCancelar.style.display = 'inline-block';
}

function carregarIndicadores() {
    fetch(API_URL)
        .then(res => res.json())
        .then(data => {
            tabelaCorpo.innerHTML = '';
            data.forEach(ind => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ind.id}</td>
                    <td>${ind.codigo}</td>
                    <td>${ind.descricao}</td>
                    <td>${ind.resposta || ''}</td>
                    <td>${ind.evidencias || ''}</td>
                    <td>
                        <button onclick="editar(${ind.id})">Editar</button>
                        <button onclick="excluir(${ind.id})">Excluir</button>
                    </td>
                `;
                tabelaCorpo.appendChild(tr);
            });
        });
}

function editar(id) {
    fetch(`${API_URL}/${id}`)
        .then(res => res.json())
        .then(data => preencherForm(data));
}

function excluir(id) {
    if (confirm('Confirma exclusão?')) {
        fetch(`${API_URL}/${id}`, { method: 'DELETE' })
            .then(res => res.json())
            .then(() => carregarIndicadores());
    }
}

form.addEventListener('submit', e => {
    e.preventDefault();

    const id = form.id.value;
    const data = {
        codigo: form.codigo.value,
        descricao: form.descricao.value,
        resposta: form.resposta.value,
        evidencias: form.evidencias.value,
    };

    if (id) {
        fetch(`${API_URL}/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(res => res.json())
          .then(() => {
              limparForm();
              carregarIndicadores();
          });
    } else {
        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(res => res.json())
          .then(() => {
              limparForm();
              carregarIndicadores();
          });
    }
});

btnCancelar.addEventListener('click', () => limparForm());

carregarIndicadores();
