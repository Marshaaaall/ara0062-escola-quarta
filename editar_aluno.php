<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'escola_taekwondo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;
$erro = '';

if (!$id) {
    header('Location: alunos.php');
    exit;
}

// Buscar aluno existente
$stmt = $pdo->prepare('SELECT * FROM alunos WHERE id = ?');
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    header('Location: alunos.php');
    exit;
}

// Criar pasta de uploads se não existir
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $faixa = $_POST['faixa'] ?? '';
    $tempo = $_POST['tempo'] ?? '';
    
    // Manter a foto atual por padrão
    $foto = $aluno['foto'];
    
    // Se foi enviada uma URL
    if (!empty($_POST['foto_url'])) {
        $foto = $_POST['foto_url'];
    }
    
    // Se foi feito upload de arquivo
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['foto_upload'];
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($extensao), $extensoes_permitidas)) {
            // Gerar nome único para o arquivo
            $nome_arquivo = uniqid() . '_' . time() . '.' . $extensao;
            $caminho_arquivo = 'uploads/' . $nome_arquivo;
            
            if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
                $foto = $nome_arquivo;
                
                // Excluir a imagem antiga se não for uma URL e não for a placeholder
                if (!filter_var($aluno['foto'], FILTER_VALIDATE_URL) && $aluno['foto'] !== 'placeholder.jpg') {
                    @unlink('uploads/' . $aluno['foto']);
                }
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erro = "Formato de arquivo não permitido. Use JPG, PNG, GIF ou WebP.";
        }
    }

    if (empty($erro)) {
        if (!empty($nome) && !empty($idade) && !empty($faixa) && !empty($tempo)) {
            try {
                $stmt = $pdo->prepare('UPDATE alunos SET foto = ?, nome = ?, idade = ?, faixa = ?, tempo = ? WHERE id = ?');
                if ($stmt->execute([$foto, $nome, $idade, $faixa, $tempo, $id])) {
                    header('Location: alunos.php?sucesso=1');
                    exit;
                } else {
                    $erro = "Erro ao atualizar aluno.";
                }
            } catch (PDOException $e) {
                $erro = "Erro ao atualizar aluno: " . $e->getMessage();
            }
        } else {
            $erro = "Por favor, preencha todos os campos obrigatórios.";
        }
    }
}

// Determinar o tipo da imagem atual
$imagem_atual_tipo = filter_var($aluno['foto'], FILTER_VALIDATE_URL) ? 'url' : 'upload';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno - Escola de Taekwondo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/alunos.css">
    <link rel="stylesheet" href="css/form-alunos.css">
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
            <h1>Editar Aluno</h1>

            <?php if (!empty($erro)): ?>
                <div class="erro"><?= $erro ?></div>
            <?php endif; ?>

            <div class="current-image">
                <h3>Imagem Atual:</h3>
                <?php if ($imagem_atual_tipo === 'url'): ?>
                    <img src="<?= htmlspecialchars($aluno['foto']) ?>" 
                         alt="Foto atual de <?= htmlspecialchars($aluno['nome']) ?>"
                         class="preview-image">
                    <p><small>(URL externa)</small></p>
                <?php else: ?>
                    <img src="uploads/<?= htmlspecialchars($aluno['foto']) ?>" 
                         alt="Foto atual de <?= htmlspecialchars($aluno['nome']) ?>"
                         class="preview-image"
                         onerror="this.src='img/placeholder.jpg'">
                    <p><small>(Imagem local)</small></p>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="form-container">
                <div class="form-group">
                    <h3>Alterar foto (opcional):</h3>
                    <div class="upload-options">
                        <div class="upload-option">
                            <h3>📷 Usar URL do Google</h3>
                            <p>Cole a URL de uma imagem da web</p>
                            <input type="url" id="foto_url" name="foto_url" 
                                   class="url-input" 
                                   placeholder="https://exemplo.com/imagem.jpg">
                            <small>Formatos: JPG, PNG, GIF, WebP</small>
                        </div>
                        
                        <div class="upload-option">
                            <h3>📁 Fazer Upload</h3>
                            <p>Envie uma imagem do seu computador</p>
                            <input type="file" id="foto_upload" name="foto_upload" 
                                   class="file-input" 
                                   accept="image/*">
                            <small>Formatos: JPG, PNG, GIF, WebP (máx. 5MB)</small>
                        </div>
                    </div>
                    
                    <div class="preview-container">
                        <img id="preview" class="preview-image" alt="Pré-visualização da nova imagem" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome completo:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="idade">Idade:</label>
                    <input type="number" id="idade" name="idade" value="<?= htmlspecialchars($aluno['idade']) ?>" min="5" max="100" required>
                </div>
                
                <div class="form-group">
                    <label for="faixa">Faixa:</label>
                    <select id="faixa" name="faixa" required>
                        <option value="">Selecione a faixa</option>
                        <option value="Branca" <?= $aluno['faixa'] == 'Branca' ? 'selected' : '' ?>>Branca</option>
                        <option value="Amarela" <?= $aluno['faixa'] == 'Amarela' ? 'selected' : '' ?>>Amarela</option>
                        <option value="Verde" <?= $aluno['faixa'] == 'Verde' ? 'selected' : '' ?>>Verde</option>
                        <option value="Azul" <?= $aluno['faixa'] == 'Azul' ? 'selected' : '' ?>>Azul</option>
                        <option value="Vermelha" <?= $aluno['faixa'] == 'Vermelha' ? 'selected' : '' ?>>Vermelha</option>
                        <option value="Preta" <?= $aluno['faixa'] == 'Preta' ? 'selected' : '' ?>>Preta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tempo">Tempo de Prática:</label>
                    <input type="text" id="tempo" name="tempo" value="<?= htmlspecialchars($aluno['tempo']) ?>" placeholder="ex: 2 anos" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-form btn-salvar">Salvar Alterações</button>
                    <a href="alunos.php" class="btn-form btn-cancelar-form">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Escola de Taekwondo - Todos os direitos reservados</p>
        </div>
    </footer>

    <script src="js/form-alunos.js"></script>
</body>
</html>