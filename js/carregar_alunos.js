document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de alunos carregado');
    carregarAlunos();
});

const url = 'data/carregar_dados'


const dadosAlunosFallback = {
    alunos: [
        {
            "foto": "img/placeholderjpg",
            "nome": "Carlos Silva",
            "idade": 15,
            "faixa": "Verde",
            "tempo": "2 anos"
        },
        {
            "foto": "img/placeholderjpg",
            "nome": "Ana Costa",
            "idade": 12,
            "faixa": "Amarela",
            "tempo": "1 ano"
        }
    ]
};

function carregarAlunos() {

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(dados => {
            console.log('JSON carregado com sucesso');
            if (dados.alunos && dados.alunos.length > 0) {
                preencherTabelaAlunos(dados.alunos);
            } else {
                throw new Error('JSON não contém dados de alunos');
            }
        })
        .catch(erro => {
            console.warn('Não foi possível carregar o JSON, usando dados embutidos:', erro);
            preencherTabelaAlunos(dadosAlunosFallback.alunos);
        });
}

function preencherTabelaAlunos(alunos) {
    const tabela = document.getElementById('tabela-alunos');
    if (!tabela) {
        console.error('Tabela de alunos não encontrada - verifique se o ID está correto');
        return;
    }

    let tbody = tabela.querySelector('tbody');
    if (!tbody) {
        tbody = document.createElement('tbody');
        tabela.appendChild(tbody);
    }
    
    tbody.innerHTML = '';

    alunos.forEach((aluno, index) => {
        const linha = document.createElement('tr');
        const fotoUrl = aluno.foto || `img/aluno${index + 1}.jpg`;
        
        linha.innerHTML = `
            <td><img src="${fotoUrl}" alt="Foto de ${aluno.nome}" class="foto-aluno" onerror="this.src='img/placeholder.jpg'"></td>
            <td>${aluno.nome}</td>
            <td>${aluno.idade} anos</td>
            <td><div class="faixa-aluno" style="background-color: ${getCorFaixa(aluno.faixa)}">${aluno.faixa}</div></td>
            <td>${aluno.tempo}</td>
        `;
        
        tbody.appendChild(linha);
    });

    console.log('Tabela de alunos preenchida com', alunos.length, 'alunos');
}

function getCorFaixa(faixa) {
    const cores = {
        'Branca': 'white',
        'Amarela': 'yellow',
        'Verde': 'green',
        'Azul': 'blue',
        'Vermelha': 'red',
        'Preta': 'black'
    };
    return cores[faixa] || 'gray';
}
