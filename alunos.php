<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'escola_taekwondo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar alunos do banco de dados
    $stmt = $pdo->query('SELECT * FROM alunos ORDER BY nome');
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro_bd = "Erro ao conectar com o banco de dados: " . $e->getMessage();
    $alunos = [];
}

// Função para obter cor da faixa
function getCorFaixa($faixa) {
    $cores = [
        'Branca' => 'white',
        'Amarela' => 'yellow',
        'Verde' => 'green',
        'Azul' => 'blue',
        'Vermelha' => 'red',
        'Preta' => 'black'
    ];
    return $cores[$faixa] ?? 'gray';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos - Escola de Taekwondo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/alunos.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="alunos.php">Alunos</a></li>
                    <li><a href="tabela.html">Faixas</a></li>
                    <li><a href="sac.html">SAC</a></li>
                    <li><a href="integrantes.html">Integrantes</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="content-section">
            <h1>Nossos Alunos</h1>
            <p>Conheça nossos talentosos alunos e suas conquistas:</p>
            
            <?php if (isset($erro_bd)): ?>
                <div class="erro"><?= $erro_bd ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                <div class="sucesso">Operação realizada com sucesso!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
                <div class="erro">Erro ao realizar a operação. Tente novamente.</div>
            <?php endif; ?>
            
            <!-- Botão para adicionar novo aluno -->
            <a href="adicionar_aluno.php" class="btn btn-adicionar"> Adicionar Novo Aluno</a>
            
            <table id="tabela-alunos">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Idade</th>
                        <th>Faixa</th>
                        <th>Tempo de Prática</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alunos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">
                                Nenhum aluno cadastrado no momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td>
                                <?php if (filter_var($aluno['foto'], FILTER_VALIDATE_URL)): ?>
                                    <!-- Se for uma URL externa -->
                                    <img src="<?= htmlspecialchars($aluno['foto']) ?>" 
                                         alt="Foto de <?= htmlspecialchars($aluno['nome']) ?>" 
                                         class="foto-aluno"
                                         onerror="this.src='img/placeholder.jpg'">
                                <?php else: ?>
                                    <!-- Se for uma imagem local -->
                                    <img src="uploads/<?= htmlspecialchars($aluno['foto']) ?>" 
                                         alt="Foto de <?= htmlspecialchars($aluno['nome']) ?>" 
                                         class="foto-aluno"
                                         onerror="this.src='img/placeholder.jpg'">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($aluno['nome']) ?></td>
                            <td><?= htmlspecialchars($aluno['idade']) ?> anos</td>
                            <td>
                                <div class="faixa-aluno" 
                                     style="background-color: <?= getCorFaixa($aluno['faixa']) ?>; 
                                            color: <?= in_array($aluno['faixa'], ['Branca', 'Amarela']) ? 'black' : 'white' ?>;">
                                    <?= htmlspecialchars($aluno['faixa']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($aluno['tempo']) ?></td>
                            <td class="acoes">
                                <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-editar"> Editar</a>
                                <a href="excluir_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-excluir" 
                                   onclick="return confirm('Tem certeza que deseja excluir o aluno <?= htmlspecialchars(addslashes($aluno['nome'])) ?>?')">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Escola de Taekwondo - Todos os direitos reservados</p>
        </div>
    </footer>

    <script src="js/tema.js"></script>
</body>
</html>